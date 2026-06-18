<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Customer::where('tenant_id', session('current_tenant_id'))->get();
    }

    public function headings(): array
    {
        return ['اسم العميل', 'الاسم بالعربي', 'البريد', 'الهاتف', 'العنوان', 'الرقم الضريبي', 'حد الائتمان', 'الحالة'];
    }

    public function map($customer): array
    {
        return [
            $customer->name,
            $customer->name_ar,
            $customer->email,
            $customer->phone,
            $customer->address,
            $customer->tax_number,
            $customer->credit_limit,
            $customer->is_active ? 'نشط' : 'غير نشط',
        ];
    }
}
