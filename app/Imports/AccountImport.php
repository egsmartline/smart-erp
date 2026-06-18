<?php

namespace App\Imports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AccountImport implements ToModel, WithHeadingRow
{
    private $tenantId;

    public function __construct()
    {
        $this->tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
    }

    public function model(array $row)
    {
        return new Account([
            'tenant_id' => $this->tenantId,
            'code' => $row['code'] ?? $row['الكود'] ?? '',
            'name' => $row['name'] ?? $row['اسم الحساب'] ?? '',
            'type' => $row['type'] ?? $row['النوع'] ?? 'asset',
            'is_active' => true,
        ]);
    }
}
