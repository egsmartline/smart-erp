<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemImport implements ToModel, WithHeadingRow
{
    private $tenantId;

    public function __construct()
    {
        $this->tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
    }

    public function model(array $row)
    {
        return new Item([
            'tenant_id' => $this->tenantId,
            'name' => $row['name'] ?? $row['اسم الصنف'] ?? '',
            'sku' => $row['sku'] ?? $row['الكود'] ?? null,
            'barcode' => $row['barcode'] ?? $row['الباركود'] ?? null,
            'cost_price' => $row['cost_price'] ?? $row['سعر التكلفة'] ?? 0,
            'selling_price' => $row['selling_price'] ?? $row['سعر البيع'] ?? 0,
            'minimum_stock' => $row['minimum_stock'] ?? $row['الحد الادنى'] ?? 0,
            'is_active' => true,
        ]);
    }
}
