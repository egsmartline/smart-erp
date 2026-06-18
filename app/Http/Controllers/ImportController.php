<?php

namespace App\Http\Controllers;

use App\Imports\CustomerImport;
use App\Imports\SupplierImport;
use App\Imports\ItemImport;
use App\Imports\AccountImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends TenantAwareController
{
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

        try {
            match ($request->type) {
                'customers' => Excel::import(new CustomerImport, $request->file('file')),
                'suppliers' => Excel::import(new SupplierImport, $request->file('file')),
                'items' => Excel::import(new ItemImport, $request->file('file')),
                'accounts' => Excel::import(new AccountImport, $request->file('file')),
            };
            return back()->with('success', 'تم استيراد البيانات بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'خطأ في الاستيراد: ' . $e->getMessage()]);
        }
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
