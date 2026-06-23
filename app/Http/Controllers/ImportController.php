<?php

namespace App\Http\Controllers;

use App\Imports\CustomerImport;
use App\Imports\SupplierImport;
use App\Imports\ItemImport;
use App\Imports\AccountImport;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Currency;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends TenantAwareController
{
    private $categoryCache = [];
    private $unitCache = [];
    private $currencyCache = [];

    public function index()
    {
        return view('import.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'type' => 'required|in:customers,suppliers,items,accounts',
        ]);

        if ($request->type === 'items') {
            return $this->importItems($request);
        }

        try {
            match ($request->type) {
                'customers' => Excel::import(new CustomerImport, $request->file('file')),
                'suppliers' => Excel::import(new SupplierImport, $request->file('file')),
                'accounts' => Excel::import(new AccountImport, $request->file('file')),
            };
            return back()->with('success', 'تم استيراد البيانات بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'خطأ في الاستيراد: ' . $e->getMessage()]);
        }
    }

    private function importItems(Request $request)
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->path());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows)) {
                return back()->withErrors(['error' => 'الملف فارغ']);
            }

            // First row = headers, build column map
            $headers = array_map('trim', $rows[0]);
            $colMap = $this->buildItemColMap($headers);
            $count = 0;

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $name = $this->getVal($row, $colMap, 'name');
                if (empty($name)) continue;

                $categoryName = $this->getVal($row, $colMap, 'category');
                $categoryId = null;
                if ($categoryName && !isset($this->categoryCache[$categoryName])) {
                    $cat = ItemCategory::where('tenant_id', $tenantId)
                        ->where('name', $categoryName)->first();
                    $this->categoryCache[$categoryName] = $cat?->id;
                }
                $categoryId = $this->categoryCache[$categoryName] ?? null;

                $unitName = $this->getVal($row, $colMap, 'unit');
                $unitId = null;
                if ($unitName && !isset($this->unitCache[$unitName])) {
                    $u = ItemUnit::where('tenant_id', $tenantId)
                        ->where('name', $unitName)->first();
                    $this->unitCache[$unitName] = $u?->id;
                }
                $unitId = $this->unitCache[$unitName] ?? null;

                Item::create([
                    'tenant_id' => $tenantId,
                    'name' => $name,
                    'sku' => $this->getVal($row, $colMap, 'sku'),
                    'barcode' => $this->getVal($row, $colMap, 'barcode'),
                    'category_id' => $categoryId,
                    'unit_id' => $unitId,
                    'cost_price' => $this->getVal($row, $colMap, 'cost_price') ?? 0,
                    'purchase_currency_id' => $this->resolveCurrency($tenantId, $this->getVal($row, $colMap, 'purchase_currency')),
                    'selling_price' => $this->getVal($row, $colMap, 'selling_price') ?? 0,
                    'sales_currency_id' => $this->resolveCurrency($tenantId, $this->getVal($row, $colMap, 'sales_currency')),
                    'tax_rate' => $this->getVal($row, $colMap, 'tax_rate') ?? 0,
                    'minimum_stock' => $this->getVal($row, $colMap, 'minimum_stock') ?? 0,
                    'maximum_stock' => $this->getVal($row, $colMap, 'maximum_stock') ?? 0,
                    'reorder_level' => $this->getVal($row, $colMap, 'reorder_level') ?? 0,
                    'opening_stock' => $this->getVal($row, $colMap, 'opening_stock') ?? 0,
                    'has_serial_numbers' => in_array($this->getVal($row, $colMap, 'has_serial_numbers') ?? 'لا', ['نعم', 'yes', '1', 1], true),
                    'has_expiry_date' => in_array($this->getVal($row, $colMap, 'has_expiry_date') ?? 'لا', ['نعم', 'yes', '1', 1], true),
                    'description' => $this->getVal($row, $colMap, 'description'),
                    'is_active' => in_array($this->getVal($row, $colMap, 'is_active') ?? 'نشط', ['نشط', 'active', '1', 1], true),
                ]);
                $count++;
            }

            return back()->with('success', "تم استيراد $count صنف بنجاح");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'خطأ: ' . $e->getMessage()]);
        }
    }

    private function getVal($row, $colMap, $field)
    {
        $idx = $colMap[$field] ?? null;
        if ($idx === null) return null;
        return $row[$idx] ?? null;
    }

    private function buildItemColMap($headers)
    {
        $map = [
            'اسم الصنف' => 'name', 'اسم السلعة' => 'name', 'name' => 'name', 'item name' => 'name',
            'الكود' => 'sku', 'رمز الصنف' => 'sku', 'رمز الصنف (SKU)' => 'sku', 'sku' => 'sku', 'code' => 'sku',
            'الباركود' => 'barcode', 'barcode' => 'barcode',
            'التصنيف' => 'category', 'category' => 'category',
            'الوحدة' => 'unit', 'unit' => 'unit',
            'سعر الشراء' => 'cost_price', 'سعر التكلفة' => 'cost_price', 'cost_price' => 'cost_price', 'cost price' => 'cost_price',
            'عملة الشراء' => 'purchase_currency', 'purchase_currency' => 'purchase_currency',
            'سعر البيع' => 'selling_price', 'selling_price' => 'selling_price', 'sale price' => 'selling_price',
            'عملة البيع' => 'sales_currency', 'sales_currency' => 'sales_currency',
            'نسبة الضريبة %' => 'tax_rate', 'نسبة الضريبة' => 'tax_rate', 'tax_rate' => 'tax_rate', 'tax %' => 'tax_rate',
            'الحد الأدنى' => 'minimum_stock', 'الحد الادنى' => 'minimum_stock', 'minimum_stock' => 'minimum_stock',
            'الحد الأقصى' => 'maximum_stock', 'الحد الاقصى' => 'maximum_stock', 'maximum_stock' => 'maximum_stock',
            'مستوى إعادة الطلب' => 'reorder_level', 'reorder_level' => 'reorder_level',
            'الرصيد الافتتاحي' => 'opening_stock', 'opening_stock' => 'opening_stock',
            'يتطلب أرقام تسلسلية' => 'has_serial_numbers', 'has_serial_numbers' => 'has_serial_numbers',
            'له تاريخ صلاحية' => 'has_expiry_date', 'has_expiry_date' => 'has_expiry_date',
            'الوصف' => 'description', 'description' => 'description',
            'الحالة' => 'is_active', 'status' => 'is_active', 'is_active' => 'is_active',
        ];

        $result = [];
        foreach ($headers as $idx => $header) {
            $lower = mb_strtolower(trim($header));
            if (isset($map[$lower])) {
                $result[$map[$lower]] = $idx;
            } elseif (isset($map[$header])) {
                $result[$map[$header]] = $idx;
            }
        }
        return $result;
    }

    private function resolveCurrency($tenantId, $code)
    {
        if (!$code) return null;
        if (!isset($this->currencyCache[$code])) {
            $c = Currency::where('tenant_id', $tenantId)->where('code', $code)->first();
            $this->currencyCache[$code] = $c?->id;
        }
        return $this->currencyCache[$code];
    }

    public function export($type)
    {
        $filename = match ($type) {
            'customers' => 'العملاء.xlsx',
            'suppliers' => 'الموردين.xlsx',
            'items' => 'الأصناف.xlsx',
            'accounts' => 'دليل الحسابات.xlsx',
        };

        return match ($type) {
            'customers' => Excel::download(new \App\Exports\CustomerExport, $filename),
            'suppliers' => Excel::download(new \App\Exports\SupplierExport, $filename),
            'items' => Excel::download(new \App\Exports\ItemExport, $filename),
            'accounts' => Excel::download(new \App\Exports\AccountExport, $filename),
        };
    }
}
