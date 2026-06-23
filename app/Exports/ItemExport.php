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
        return Item::with(['category', 'unit', 'purchaseCurrency', 'salesCurrency'])
            ->where('tenant_id', session('current_tenant_id'))
            ->get();
    }

    public function headings(): array
    {
        return [
            'اسم الصنف', 'رمز الصنف (SKU)', 'الباركود',
            'التصنيف', 'الوحدة',
            'سعر الشراء', 'عملة الشراء', 'سعر البيع', 'عملة البيع',
            'نسبة الضريبة %',
            'الحد الأدنى', 'الحد الأقصى', 'مستوى إعادة الطلب', 'الرصيد الافتتاحي',
            'يتطلب أرقام تسلسلية', 'له تاريخ صلاحية',
            'الوصف', 'الحالة',
        ];
    }

    public function map($item): array
    {
        return [
            $item->name,
            $item->sku,
            $item->barcode,
            $item->category?->name,
            $item->unit?->name,
            $item->cost_price,
            $item->purchaseCurrency?->code,
            $item->selling_price,
            $item->salesCurrency?->code,
            $item->tax_rate,
            $item->minimum_stock,
            $item->maximum_stock,
            $item->reorder_level,
            $item->opening_stock,
            $item->has_serial_numbers ? 'نعم' : 'لا',
            $item->has_expiry_date ? 'نعم' : 'لا',
            $item->description,
            $item->is_active ? 'نشط' : 'غير نشط',
        ];
    }
}
