<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Supplier::where('tenant_id', session('current_tenant_id'))->get();
    }

    public function headings(): array
    {
        return ['اسم المورد', 'الاسم بالعربي', 'البريد', 'الهاتف', 'العنوان', 'الرقم الضريبي', 'الحالة'];
    }

    public function map($supplier): array
    {
        return [
            $supplier->name,
            $supplier->name_ar,
            $supplier->email,
            $supplier->phone,
            $supplier->address,
            $supplier->tax_number,
            $supplier->is_active ? 'نشط' : 'غير نشط',
        ];
    }
}
