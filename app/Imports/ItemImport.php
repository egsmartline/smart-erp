<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Currency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

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
        HeadingRowFormatter::default('none');
    }

    public function model(array $row)
    {
        $name = $row['اسم الصنف'] ?? $row['اسم السلعة'] ?? $row['name'] ?? $row['Item Name'] ?? '';
        if (empty($name)) {
            return null;
        }

        $categoryName = $row['التصنيف'] ?? $row['category'] ?? null;
        $categoryId = null;
        if ($categoryName) {
            if (!isset($this->categoryCache[$categoryName])) {
                $cat = ItemCategory::where('tenant_id', $this->tenantId)
                    ->where('name', $categoryName)->first();
                $this->categoryCache[$categoryName] = $cat?->id;
            }
            $categoryId = $this->categoryCache[$categoryName];
        }

        $unitName = $row['الوحدة'] ?? $row['unit'] ?? null;
        $unitId = null;
        if ($unitName) {
            if (!isset($this->unitCache[$unitName])) {
                $u = ItemUnit::where('tenant_id', $this->tenantId)
                    ->where('name', $unitName)->first();
                $this->unitCache[$unitName] = $u?->id;
            }
            $unitId = $this->unitCache[$unitName];
        }

        return new Item([
            'tenant_id' => $this->tenantId,
            'name' => $name,
            'sku' => $row['رمز الصنف (SKU)'] ?? $row['الكود'] ?? $row['رمز الصنف'] ?? $row['sku'] ?? $row['code'] ?? null,
            'barcode' => $row['الباركود'] ?? $row['barcode'] ?? null,
            'category_id' => $categoryId,
            'unit_id' => $unitId,
            'cost_price' => $row['سعر الشراء'] ?? $row['سعر التكلفة'] ?? $row['cost_price'] ?? $row['purchase price'] ?? 0,
            'purchase_currency_id' => $this->resolveCurrency($row['عملة الشراء'] ?? $row['purchase_currency'] ?? null),
            'selling_price' => $row['سعر البيع'] ?? $row['selling_price'] ?? $row['sale price'] ?? 0,
            'sales_currency_id' => $this->resolveCurrency($row['عملة البيع'] ?? $row['sales_currency'] ?? null),
            'tax_rate' => $row['نسبة الضريبة %'] ?? $row['نسبة الضريبة'] ?? $row['tax_rate'] ?? $row['tax %'] ?? 0,
            'minimum_stock' => $row['الحد الأدنى'] ?? $row['الحد الادنى'] ?? $row['minimum_stock'] ?? $row['min stock'] ?? 0,
            'maximum_stock' => $row['الحد الأقصى'] ?? $row['الحد الاقصى'] ?? $row['maximum_stock'] ?? $row['max stock'] ?? 0,
            'reorder_level' => $row['مستوى إعادة الطلب'] ?? $row['reorder_level'] ?? $row['reorder level'] ?? 0,
            'opening_stock' => $row['الرصيد الافتتاحي'] ?? $row['opening_stock'] ?? $row['opening stock'] ?? 0,
            'has_serial_numbers' => in_array($row['يتطلب أرقام تسلسلية'] ?? $row['has_serial_numbers'] ?? 'لا', ['نعم', 'yes', '1', 1], true),
            'has_expiry_date' => in_array($row['له تاريخ صلاحية'] ?? $row['has_expiry_date'] ?? 'لا', ['نعم', 'yes', '1', 1], true),
            'description' => $row['الوصف'] ?? $row['description'] ?? null,
            'is_active' => in_array($row['الحالة'] ?? $row['status'] ?? $row['is_active'] ?? 'نشط', ['نشط', 'active', '1', 1], true),
        ]);
    }

    private function resolveCurrency($code)
    {
        if (!$code) return null;
        if (!isset($this->currencyCache[$code])) {
            $c = Currency::where('tenant_id', $this->tenantId)
                ->where('code', $code)->first();
            $this->currencyCache[$code] = $c?->id;
        }
        return $this->currencyCache[$code];
    }
}
