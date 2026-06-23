<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Currency;
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

    public function model(array $row)
    {
        $categoryId = null;
        $categoryName = $row['التصنيف'] ?? $row['category'] ?? null;
        if ($categoryName) {
            if (!isset($this->categoryCache[$categoryName])) {
                $cat = ItemCategory::where('tenant_id', $this->tenantId)
                    ->where('name', $categoryName)->first();
                $this->categoryCache[$categoryName] = $cat?->id;
            }
            $categoryId = $this->categoryCache[$categoryName];
        }

        $unitId = null;
        $unitName = $row['الوحدة'] ?? $row['unit'] ?? null;
        if ($unitName) {
            if (!isset($this->unitCache[$unitName])) {
                $u = ItemUnit::where('tenant_id', $this->tenantId)
                    ->where('name', $unitName)->first();
                $this->unitCache[$unitName] = $u?->id;
            }
            $unitId = $this->unitCache[$unitName];
        }

        $purchaseCurrencyId = null;
        $pcc = $row['عملة الشراء'] ?? $row['purchase_currency'] ?? null;
        if ($pcc) {
            if (!isset($this->currencyCache[$pcc])) {
                $c = Currency::where('tenant_id', $this->tenantId)
                    ->where('code', $pcc)->first();
                $this->currencyCache[$pcc] = $c?->id;
            }
            $purchaseCurrencyId = $this->currencyCache[$pcc];
        }

        $salesCurrencyId = null;
        $scc = $row['عملة البيع'] ?? $row['sales_currency'] ?? null;
        if ($scc) {
            if (!isset($this->currencyCache[$scc])) {
                $c = Currency::where('tenant_id', $this->tenantId)
                    ->where('code', $scc)->first();
                $this->currencyCache[$scc] = $c?->id;
            }
            $salesCurrencyId = $this->currencyCache[$scc];
        }

        $hasSerial = $row['يتطلب أرقام تسلسلية'] ?? $row['has_serial_numbers'] ?? null;
        $hasExpiry = $row['له تاريخ صلاحية'] ?? $row['has_expiry_date'] ?? null;
        $isActive = $row['الحالة'] ?? $row['status'] ?? null;

        return new Item([
            'tenant_id' => $this->tenantId,
            'name' => $row['اسم الصنف'] ?? $row['name'] ?? '',
            'sku' => $row['رمز الصنف (SKU)'] ?? $row['sku'] ?? $row['الكود'] ?? null,
            'barcode' => $row['الباركود'] ?? $row['barcode'] ?? null,
            'category_id' => $categoryId,
            'unit_id' => $unitId,
            'cost_price' => $row['سعر الشراء'] ?? $row['cost_price'] ?? $row['سعر التكلفة'] ?? 0,
            'purchase_currency_id' => $purchaseCurrencyId,
            'selling_price' => $row['سعر البيع'] ?? $row['selling_price'] ?? 0,
            'sales_currency_id' => $salesCurrencyId,
            'tax_rate' => $row['نسبة الضريبة %'] ?? $row['tax_rate'] ?? 0,
            'minimum_stock' => $row['الحد الأدنى'] ?? $row['minimum_stock'] ?? $row['الحد الادنى'] ?? 0,
            'maximum_stock' => $row['الحد الأقصى'] ?? $row['maximum_stock'] ?? 0,
            'reorder_level' => $row['مستوى إعادة الطلب'] ?? $row['reorder_level'] ?? 0,
            'opening_stock' => $row['الرصيد الافتتاحي'] ?? $row['opening_stock'] ?? 0,
            'has_serial_numbers' => in_array($hasSerial, ['نعم', 'yes', '1', 1], true),
            'has_expiry_date' => in_array($hasExpiry, ['نعم', 'yes', '1', 1], true),
            'description' => $row['الوصف'] ?? $row['description'] ?? null,
            'is_active' => in_array($isActive, ['نشط', 'active', '1', 1], true),
        ]);
    }
}
