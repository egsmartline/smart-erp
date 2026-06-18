<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Item::where('tenant_id', session('current_tenant_id'))->get();
    }

    public function headings(): array
    {
        return ['اسم الصنف', 'الكود', 'الباركود', 'سعر التكلفة', 'سعر البيع', 'الحد الادنى', 'الحالة'];
    }

    public function map($item): array
    {
        return [
            $item->name,
            $item->sku,
            $item->barcode,
            $item->cost_price,
            $item->selling_price,
            $item->minimum_stock,
            $item->is_active ? 'نشط' : 'غير نشط',
        ];
    }
}
