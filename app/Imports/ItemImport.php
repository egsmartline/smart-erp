<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Currency;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemImport implements ToModel, WithHeadingRow
{
    private $tenantId;
    private $categoryCache;
    private $unitCache;
    private $currencyCache;

    public function __construct()
    {
        $this->tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        $this->categoryCache = [];
        $this->unitCache = [];
        $this->currencyCache = [];
    }

    private function val(array $row, array $keys)
    {
        foreach ($keys as $k) {
            if (isset($row[$k]) && $row[$k] !== '') {
                return $row[$k];
            }
            $slug = Str::slug($k, '_');
            if (isset($row[$slug]) && $row[$slug] !== '') {
                return $row[$slug];
            }
        }
        return null;
    }

    public function model(array $row)
    {
        $name = $this->val($row, ['اسم الصنف', 'name']);
        if (empty($name)) {
            return null;
        }

        $categoryName = $this->val($row, ['التصنيف', 'category']);
        $categoryId = null;
        if ($categoryName) {
            if (!isset($this->categoryCache[$categoryName])) {
                $cat = ItemCategory::where('tenant_id', $this->tenantId)
                    ->where('name', $categoryName)->first();
                $this->categoryCache[$categoryName] = $cat?->id;
            }
            $categoryId = $this->categoryCache[$categoryName];
        }

        $unitName = $this->val($row, ['الوحدة', 'unit']);
        $unitId = null;
        if ($unitName) {
            if (!isset($this->unitCache[$unitName])) {
                $u = ItemUnit::where('tenant_id', $this->tenantId)
                    ->where('name', $unitName)->first();
                $this->unitCache[$unitName] = $u?->id;
            }
            $unitId = $this->unitCache[$unitName];
        }

        $pcc = $this->val($row, ['عملة الشراء', 'purchase_currency']);
        $purchaseCurrencyId = null;
        if ($pcc) {
            if (!isset($this->currencyCache[$pcc])) {
                $c = Currency::where('tenant_id', $this->tenantId)
                    ->where('code', $pcc)->first();
                $this->currencyCache[$pcc] = $c?->id;
            }
            $purchaseCurrencyId = $this->currencyCache[$pcc];
        }

        $scc = $this->val($row, ['عملة البيع', 'sales_currency']);
        $salesCurrencyId = null;
        if ($scc) {
            if (!isset($this->currencyCache[$scc])) {
                $c = Currency::where('tenant_id', $this->tenantId)
                    ->where('code', $scc)->first();
                $this->currencyCache[$scc] = $c?->id;
            }
            $salesCurrencyId = $this->currencyCache[$scc];
        }

        $hasSerial = $this->val($row, ['يتطلب أرقام تسلسلية', 'has_serial_numbers']);
        $hasExpiry = $this->val($row, ['له تاريخ صلاحية', 'has_expiry_date']);
        $isActive = $this->val($row, ['الحالة', 'status', 'is_active']);

        return new Item([
            'tenant_id' => $this->tenantId,
            'name' => $name,
            'sku' => $this->val($row, ['رمز الصنف (SKU)', 'sku', 'الكود']),
            'barcode' => $this->val($row, ['الباركود', 'barcode']),
            'category_id' => $categoryId,
            'unit_id' => $unitId,
            'cost_price' => $this->val($row, ['سعر الشراء', 'cost_price', 'سعر التكلفة']) ?? 0,
            'purchase_currency_id' => $purchaseCurrencyId,
            'selling_price' => $this->val($row, ['سعر البيع', 'selling_price']) ?? 0,
            'sales_currency_id' => $salesCurrencyId,
            'tax_rate' => $this->val($row, ['نسبة الضريبة %', 'tax_rate']) ?? 0,
            'minimum_stock' => $this->val($row, ['الحد الأدنى', 'minimum_stock', 'الحد الادنى']) ?? 0,
            'maximum_stock' => $this->val($row, ['الحد الأقصى', 'maximum_stock']) ?? 0,
            'reorder_level' => $this->val($row, ['مستوى إعادة الطلب', 'reorder_level']) ?? 0,
            'opening_stock' => $this->val($row, ['الرصيد الافتتاحي', 'opening_stock']) ?? 0,
            'has_serial_numbers' => in_array($hasSerial, ['نعم', 'yes', '1', 1], true),
            'has_expiry_date' => in_array($hasExpiry, ['نعم', 'yes', '1', 1], true),
            'description' => $this->val($row, ['الوصف', 'description']),
            'is_active' => in_array($isActive, ['نشط', 'active', '1', 1], true),
        ]);
    }
}
