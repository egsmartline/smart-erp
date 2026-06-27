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

            $headers = $rows[0];
            $colMap = $this->buildItemColMap($headers);
            $count = 0;

            $debug = [
                'headers_raw' => $headers,
                'headers_trimmed' => array_map('trim', $headers),
                'colMap' => $colMap,
                'first_data_row' => $rows[1] ?? null,
            ];

            $skipped = 0;
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

                $sku = $this->getVal($row, $colMap, 'sku');
                if ($sku && Item::where('tenant_id', $tenantId)->where('sku', $sku)->withTrashed()->exists()) {
                    $base = $sku;
                    $n = 1;
                    while (Item::where('tenant_id', $tenantId)->where('sku', $base . '-' . $n)->withTrashed()->exists()) {
                        $n++;
                    }
                    $sku = $base . '-' . $n;
                    $skipped++;
                }

                Item::create([
                    'tenant_id' => $tenantId,
                    'name' => $name,
                    'sku' => $sku,
                    'barcode' => $this->getVal($row, $colMap, 'barcode'),
                    'category_id' => $categoryId,
                    'unit_id' => $unitId,
                    'cost_price' => $this->parseNum($this->getVal($row, $colMap, 'cost_price')),
                    'purchase_currency_id' => $this->resolveCurrency($tenantId, $this->getVal($row, $colMap, 'purchase_currency')),
                    'selling_price' => $this->parseNum($this->getVal($row, $colMap, 'selling_price')),
                    'sales_currency_id' => $this->resolveCurrency($tenantId, $this->getVal($row, $colMap, 'sales_currency')),
                    'tax_rate' => $this->parseNum($this->getVal($row, $colMap, 'tax_rate')),
                    'minimum_stock' => $this->parseNum($this->getVal($row, $colMap, 'minimum_stock')),
                    'maximum_stock' => $this->parseNum($this->getVal($row, $colMap, 'maximum_stock')),
                    'reorder_level' => $this->parseNum($this->getVal($row, $colMap, 'reorder_level')),
                    'opening_stock' => $this->parseNum($this->getVal($row, $colMap, 'opening_stock')),
                    'has_serial_numbers' => in_array(mb_strtolower(trim($this->getVal($row, $colMap, 'has_serial_numbers') ?? 'لا')), ['نعم', 'yes', '1', 1, 'true'], true),
                    'has_expiry_date' => in_array(mb_strtolower(trim($this->getVal($row, $colMap, 'has_expiry_date') ?? 'لا')), ['نعم', 'yes', '1', 1, 'true'], true),
                    'description' => $this->getVal($row, $colMap, 'description'),
                    'is_active' => in_array(mb_strtolower(trim($this->getVal($row, $colMap, 'is_active') ?? 'نشط')), ['نشط', 'active', '1', 1, 'true'], true),
                ]);
                $count++;
            }

            $msg = "تم استيراد $count صنف بنجاح";
            if ($skipped > 0) {
                $msg .= " (تم تعديل $skipped كود مكرر تلقائياً)";
            }
            return back()->with('success', $msg)->with('import_debug', $debug);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'خطأ: ' . $e->getMessage()]);
        }
    }

    private function parseNum($val)
    {
        if ($val === null || $val === '') return 0;
        if (is_numeric($val)) return $val;
        $cleaned = preg_replace('/[^0-9\.\-]/', '', $val);
        return is_numeric($cleaned) ? $cleaned : 0;
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
            'الكود' => 'sku', 'رمز الصنف' => 'sku', 'sku' => 'sku', 'code' => 'sku',
            'رمز الصنف (sku)' => 'sku', 'رمز الصنف (Sku)' => 'sku',
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

        $normalized = [];
        foreach ($map as $headerText => $field) {
            $normalized[$this->normalize($headerText)] = $field;
        }

        $result = [];
        foreach ($headers as $idx => $header) {
            if ($header === null) continue;
            $key = $this->normalize($header);
            if (isset($normalized[$key])) {
                $result[$normalized[$key]] = $idx;
            }
        }
        return $result;
    }

    private function normalize($str)
    {
        $str = trim($str);
        // Remove BOM and other invisible characters
        $str = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $str);
        // Normalize whitespace
        $str = preg_replace('/\s+/u', ' ', $str);
        // Lowercase
        $str = mb_strtolower($str, 'UTF-8');
        // Remove diacritics (Arabic tashkeel)
        $str = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $str);
        return $str;
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
