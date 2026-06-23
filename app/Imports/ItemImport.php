<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Currency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ItemImport implements ToModel, WithStartRow
{
    private $tenantId;
    private $categoryCache;
    private $unitCache;
    private $currencyCache;
    private $colMap;
    private $headerRead;

    private static $KNOWN_HEADERS = [
        'اسم الصنف' => 'name',
        'اسم السلعة' => 'name',
        'name' => 'name',
        'item name' => 'name',
        'الكود' => 'sku',
        'رمز الصنف' => 'sku',
        'رمز الصنف (SKU)' => 'sku',
        'sku' => 'sku',
        'code' => 'sku',
        'الباركود' => 'barcode',
        'barcode' => 'barcode',
        'التصنيف' => 'category',
        'category' => 'category',
        'الوحدة' => 'unit',
        'unit' => 'unit',
        'سعر الشراء' => 'cost_price',
        'سعر التكلفة' => 'cost_price',
        'cost_price' => 'cost_price',
        'cost price' => 'cost_price',
        'purchase price' => 'cost_price',
        'عملة الشراء' => 'purchase_currency',
        'purchase_currency' => 'purchase_currency',
        'سعر البيع' => 'selling_price',
        'selling_price' => 'selling_price',
        'selling price' => 'selling_price',
        'sale price' => 'selling_price',
        'عملة البيع' => 'sales_currency',
        'sales_currency' => 'sales_currency',
        'نسبة الضريبة' => 'tax_rate',
        'نسبة الضريبة %' => 'tax_rate',
        'tax_rate' => 'tax_rate',
        'tax rate' => 'tax_rate',
        'tax %' => 'tax_rate',
        'الحد الأدنى' => 'minimum_stock',
        'الحد الادنى' => 'minimum_stock',
        'minimum_stock' => 'minimum_stock',
        'min stock' => 'minimum_stock',
        'الحد الأقصى' => 'maximum_stock',
        'الحد الاقصى' => 'maximum_stock',
        'maximum_stock' => 'maximum_stock',
        'max stock' => 'maximum_stock',
        'مستوى إعادة الطلب' => 'reorder_level',
        'reorder_level' => 'reorder_level',
        'reorder level' => 'reorder_level',
        'الرصيد الافتتاحي' => 'opening_stock',
        'opening_stock' => 'opening_stock',
        'opening stock' => 'opening_stock',
        'يتطلب أرقام تسلسلية' => 'has_serial_numbers',
        'has_serial_numbers' => 'has_serial_numbers',
        'له تاريخ صلاحية' => 'has_expiry_date',
        'has_expiry_date' => 'has_expiry_date',
        'expiry date' => 'has_expiry_date',
        'الوصف' => 'description',
        'description' => 'description',
        'الحالة' => 'is_active',
        'status' => 'is_active',
        'is_active' => 'is_active',
        'active' => 'is_active',
    ];

    public function __construct()
    {
        $this->tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        $this->categoryCache = [];
        $this->unitCache = [];
        $this->currencyCache = [];
        $this->colMap = [];
        $this->headerRead = false;
    }

    public function startRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        if (!$this->headerRead) {
            $this->headerRead = true;
            $this->colMap = $this->buildColMap($row);
            return null;
        }

        $name = $this->get($row, 'name');
        if (empty($name)) {
            return null;
        }

        $categoryName = $this->get($row, 'category');
        $categoryId = null;
        if ($categoryName) {
            if (!isset($this->categoryCache[$categoryName])) {
                $cat = ItemCategory::where('tenant_id', $this->tenantId)
                    ->where('name', $categoryName)->first();
                $this->categoryCache[$categoryName] = $cat?->id;
            }
            $categoryId = $this->categoryCache[$categoryName];
        }

        $unitName = $this->get($row, 'unit');
        $unitId = null;
        if ($unitName) {
            if (!isset($this->unitCache[$unitName])) {
                $u = ItemUnit::where('tenant_id', $this->tenantId)
                    ->where('name', $unitName)->first();
                $this->unitCache[$unitName] = $u?->id;
            }
            $unitId = $this->unitCache[$unitName];
        }

        $pcc = $this->get($row, 'purchase_currency');
        $purchaseCurrencyId = null;
        if ($pcc) {
            if (!isset($this->currencyCache[$pcc])) {
                $c = Currency::where('tenant_id', $this->tenantId)
                    ->where('code', $pcc)->first();
                $this->currencyCache[$pcc] = $c?->id;
            }
            $purchaseCurrencyId = $this->currencyCache[$pcc];
        }

        $scc = $this->get($row, 'sales_currency');
        $salesCurrencyId = null;
        if ($scc) {
            if (!isset($this->currencyCache[$scc])) {
                $c = Currency::where('tenant_id', $this->tenantId)
                    ->where('code', $scc)->first();
                $this->currencyCache[$scc] = $c?->id;
            }
            $salesCurrencyId = $this->currencyCache[$scc];
        }

        $hasSerial = $this->get($row, 'has_serial_numbers');
        $hasExpiry = $this->get($row, 'has_expiry_date');
        $isActive = $this->get($row, 'is_active');

        return new Item([
            'tenant_id' => $this->tenantId,
            'name' => $name,
            'sku' => $this->get($row, 'sku'),
            'barcode' => $this->get($row, 'barcode'),
            'category_id' => $categoryId,
            'unit_id' => $unitId,
            'cost_price' => $this->get($row, 'cost_price') ?? 0,
            'purchase_currency_id' => $purchaseCurrencyId,
            'selling_price' => $this->get($row, 'selling_price') ?? 0,
            'sales_currency_id' => $salesCurrencyId,
            'tax_rate' => $this->get($row, 'tax_rate') ?? 0,
            'minimum_stock' => $this->get($row, 'minimum_stock') ?? 0,
            'maximum_stock' => $this->get($row, 'maximum_stock') ?? 0,
            'reorder_level' => $this->get($row, 'reorder_level') ?? 0,
            'opening_stock' => $this->get($row, 'opening_stock') ?? 0,
            'has_serial_numbers' => in_array($hasSerial, ['نعم', 'yes', '1', 1], true),
            'has_expiry_date' => in_array($hasExpiry, ['نعم', 'yes', '1', 1], true),
            'description' => $this->get($row, 'description'),
            'is_active' => in_array($isActive, ['نشط', 'active', '1', 1], true),
        ]);
    }

    private function get(array $row, string $field)
    {
        if (!isset($this->colMap[$field])) {
            return null;
        }
        $idx = $this->colMap[$field];
        return $row[$idx] ?? null;
    }

    private function buildColMap(array $row): array
    {
        $map = [];
        foreach ($row as $idx => $header) {
            if ($header === null || $header === '') continue;
            $normalized = trim(mb_strtolower((string)$header));
            if (isset(self::$KNOWN_HEADERS[$normalized])) {
                $field = self::$KNOWN_HEADERS[$normalized];
                $map[$field] = $idx;
            } else {
                foreach (self::$KNOWN_HEADERS as $known => $field) {
                    $knownNorm = trim(mb_strtolower($known));
                    if ($normalized === $knownNorm) {
                        $map[$field] = $idx;
                        break;
                    }
                }
            }
        }
        return $map;
    }
}
