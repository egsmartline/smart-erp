<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements ToModel, WithHeadingRow
{
    private $tenantId;

    public function __construct()
    {
        $this->tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
    }

    public function model(array $row)
    {
        return new Supplier([
            'tenant_id' => $this->tenantId,
            'name' => $row['name'] ?? $row['اسم المورد'] ?? '',
            'name_ar' => $row['name_ar'] ?? $row['الاسم بالعربي'] ?? '',
            'email' => $row['email'] ?? $row['البريد'] ?? null,
            'phone' => $row['phone'] ?? $row['الهاتف'] ?? null,
            'address' => $row['address'] ?? $row['العنوان'] ?? null,
            'tax_number' => $row['tax_number'] ?? $row['الرقم الضريبي'] ?? null,
            'is_active' => true,
        ]);
    }
}
