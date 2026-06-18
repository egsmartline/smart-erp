<?php

namespace App\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Account::where('tenant_id', session('current_tenant_id'))->get();
    }

    public function headings(): array
    {
        return ['الكود', 'اسم الحساب', 'النوع', 'الحالة'];
    }

    public function map($account): array
    {
        return [
            $account->code,
            $account->name,
            $account->type,
            $account->is_active ? 'نشط' : 'غير نشط',
        ];
    }
}
