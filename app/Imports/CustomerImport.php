<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow
{
    private $tenantId;

    public function __construct()
    {
        $this->tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
    }

    public function model(array $row)
    {
        return new Customer([
            'tenant_id' => $this->tenantId,
            'name' => $row['name'] ?? $row['اسم العميل'] ?? '',
            'name_ar' => $row['name_ar'] ?? $row['الاسم بالعربي'] ?? '',
            'email' => $row['email'] ?? $row['البريد'] ?? null,
            'phone' => $row['phone'] ?? $row['الهاتف'] ?? null,
            'address' => $row['address'] ?? $row['العنوان'] ?? null,
            'tax_number' => $row['tax_number'] ?? $row['الرقم الضريبي'] ?? null,
            'credit_limit' => $row['credit_limit'] ?? $row['حد الائتمان'] ?? 0,
            'is_active' => true,
        ]);
    }
}
