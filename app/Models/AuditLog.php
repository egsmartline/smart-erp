<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'action',
        'model',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function scopeForTenant($query, ?int $tenantId = null)
    {
        return $query->where('tenant_id', $tenantId ?? tenant('id'));
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): ?string
    {
        return match ($this->model) {
            SalesInvoice::class => route('sales-invoices.show', $this->model_id),
            PurchaseInvoice::class => route('purchase-invoices.show', $this->model_id),
            Customer::class => route('customers.show', $this->model_id),
            Supplier::class => route('suppliers.show', $this->model_id),
            Item::class => route('items.show', $this->model_id),
            Payment::class => route('payments.show', $this->model_id),
            Expense::class => route('expenses.show', $this->model_id),
            Quotation::class => route('quotations.show', $this->model_id),
            SalesReturn::class => route('sales-returns.show', $this->model_id),
            PurchaseReturn::class => route('purchase-returns.show', $this->model_id),
            default => null,
        };
    }

    public function getFormattedOldAttribute(): ?array
    {
        return $this->old_values ? self::formatValues($this->model, $this->old_values) : null;
    }

    public function getFormattedNewAttribute(): ?array
    {
        return $this->new_values ? self::formatValues($this->model, $this->new_values) : null;
    }

    public static function formatValues(string $modelClass, array $values): array
    {
        static $cache = [];
        $labels = self::fieldLabels($modelClass);

        $result = [];
        foreach ($values as $key => $value) {
            $label = $labels[$key] ?? $key;
            $resolved = self::resolveValue($modelClass, $key, $value, $cache);
            $result[] = ['label' => $label, 'value' => $resolved];
        }
        return $result;
    }

    protected static function fieldLabels(string $modelClass): array
    {
        return match ($modelClass) {
            SalesInvoice::class, PurchaseInvoice::class => [
                'invoice_number' => 'رقم الفاتورة',
                'customer_id' => 'العميل',
                'supplier_id' => 'المورد',
                'warehouse_id' => 'المستودع',
                'date' => 'التاريخ',
                'subtotal' => 'المجموع الفرعي',
                'discount_percent' => 'نسبة الخصم',
                'discount_amount' => 'قيمة الخصم',
                'tax_percent' => 'نسبة الضريبة',
                'tax_amount' => 'قيمة الضريبة',
                'shipping_cost' => 'تكلفة الشحن',
                'total' => 'الإجمالي',
                'paid_amount' => 'المدفوع',
                'due_amount' => 'المتبقي',
                'status' => 'الحالة',
                'payment_status' => 'حالة الدفع',
                'currency_id' => 'العملة',
                'exchange_rate' => 'سعر الصرف',
                'notes' => 'ملاحظات',
                'reference' => 'المرجع',
                'cashier_id' => 'الكاشير',
            ],
            SalesInvoiceLine::class, PurchaseInvoiceLine::class => [
                'item_id' => 'الصنف',
                'description' => 'الوصف',
                'quantity' => 'الكمية',
                'unit_price' => 'سعر الوحدة',
                'unit_cost' => 'تكلفة الوحدة',
                'discount_percent' => 'نسبة الخصم',
                'discount_amount' => 'قيمة الخصم',
                'tax_rate' => 'نسبة الضريبة',
                'tax_amount' => 'قيمة الضريبة',
                'subtotal' => 'المجموع الفرعي',
                'total' => 'الإجمالي',
                'warehouse_id' => 'المستودع',
                'expiry_date' => 'تاريخ الانتهاء',
                'serial_numbers' => 'الأرقام التسلسلية',
            ],
            Customer::class, Supplier::class => [
                'name' => 'الاسم',
                'name_ar' => 'الاسم بالعربية',
                'email' => 'البريد الإلكتروني',
                'phone' => 'الهاتف',
                'mobile' => 'الجوال',
                'address' => 'العنوان',
                'city' => 'المدينة',
                'country' => 'الدولة',
                'tax_number' => 'الرقم الضريبي',
                'credit_limit' => 'الحد الائتماني',
                'balance' => 'الرصيد',
                'is_active' => 'نشط',
                'notes' => 'ملاحظات',
            ],
            Item::class => [
                'category_id' => 'التصنيف',
                'unit_id' => 'الوحدة',
                'name' => 'الاسم',
                'name_ar' => 'الاسم بالعربية',
                'sku' => 'رمز الصنف',
                'barcode' => 'الباركود',
                'description' => 'الوصف',
                'cost_price' => 'سعر التكلفة',
                'purchase_currency_id' => 'عملة الشراء',
                'selling_price' => 'سعر البيع',
                'sales_currency_id' => 'عملة البيع',
                'minimum_stock' => 'الحد الأدنى',
                'maximum_stock' => 'الحد الأقصى',
                'reorder_level' => 'مستوى إعادة الطلب',
                'tax_rate' => 'نسبة الضريبة',
                'is_active' => 'نشط',
            ],
            Payment::class => [
                'payment_number' => 'رقم الدفعة',
                'date' => 'التاريخ',
                'type' => 'النوع',
                'customer_id' => 'العميل',
                'supplier_id' => 'المورد',
                'account_id' => 'الحساب',
                'treasury_id' => 'الخزينة',
                'bank_account_id' => 'الحساب البنكي',
                'amount' => 'المبلغ',
                'payment_method' => 'طريقة الدفع',
                'currency_id' => 'العملة',
                'exchange_rate' => 'سعر الصرف',
                'amount_in_currency' => 'المبلغ بالعملة',
                'reference' => 'المرجع',
                'notes' => 'ملاحظات',
                'status' => 'الحالة',
            ],
            Expense::class => [
                'employee_id' => 'الموظف',
                'expense_number' => 'رقم المصروف',
                'date' => 'التاريخ',
                'category' => 'التصنيف',
                'amount' => 'المبلغ',
                'status' => 'الحالة',
                'description' => 'الوصف',
            ],
            default => [],
        };
    }

    protected static function resolveValue(string $modelClass, string $key, mixed $value, array &$cache): mixed
    {
        if ($value === null || $value === '' || is_bool($value)) {
            return match ($value) {
                true => 'نعم',
                false => 'لا',
                null, '' => '-',
            };
        }

        if (str_ends_with($key, '_id')) {
            $rel = match ($key) {
                'customer_id' => Customer::class,
                'supplier_id' => Supplier::class,
                'item_id' => Item::class,
                'warehouse_id' => \App\Models\Warehouse::class,
                'currency_id' => Currency::class,
                'purchase_currency_id' => Currency::class,
                'sales_currency_id' => Currency::class,
                'category_id' => ItemCategory::class,
                'unit_id' => ItemUnit::class,
                'cashier_id' => User::class,
                'account_id' => Account::class,
                'treasury_id' => CashTreasury::class,
                'bank_account_id' => BankAccount::class,
                'employee_id' => \App\Models\Employee::class,
                default => null,
            };

            if ($rel !== null) {
                $cacheKey = "{$rel}:{$value}";
                if (!isset($cache[$cacheKey])) {
                    $instance = $rel::find($value);
                    $cache[$cacheKey] = match ($rel) {
                        Customer::class, Supplier::class => $instance?->name ?? $instance?->name_ar ?? "#{$value}",
                        Item::class => $instance?->name ?? $instance?->name_ar ?? "#{$value}",
                        Currency::class => $instance?->name ?? "#{$value}",
                        Warehouse::class, ItemCategory::class, ItemUnit::class,
                        Account::class, CashTreasury::class, BankAccount::class => $instance?->name ?? "#{$value}",
                        User::class => $instance?->name ?? "#{$value}",
                        default => "#{$value}",
                    } ?? "#{$value}";
                }
                return $cache[$cacheKey];
            }
        }

        if (str_ends_with($key, 'amount') || str_ends_with($key, 'total') || str_ends_with($key, 'price') || str_ends_with($key, 'cost') || in_array($key, ['subtotal', 'paid_amount', 'due_amount', 'balance', 'credit_limit', 'shipping_cost'])) {
            return number_format((float) $value, 2) . ' ريال';
        }

        if (in_array($key, ['date', 'expiry_date']) && $value) {
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return $value;
            }
        }

        if (str_ends_with($key, '_rate') || in_array($key, ['tax_percent', 'discount_percent'])) {
            return number_format((float) $value, 2) . '%';
        }

        if ($key === 'exchange_rate') {
            return number_format((float) $value, 6);
        }

        if ($key === 'status') {
            return match ($value) {
                'draft' => 'مسودة',
                'posted' => 'مرحل',
                'paid' => 'مدفوع',
                'partial' => 'مدفوع جزئياً',
                'void' => 'ملغي',
                'pending' => 'معلق',
                'approved' => 'معتمد',
                'rejected' => 'مرفوض',
                default => $value,
            };
        }

        if ($key === 'payment_status') {
            return match ($value) {
                'pending' => 'معلق',
                'partial' => 'مدفوع جزئياً',
                'paid' => 'مدفوع',
                default => $value,
            };
        }

        if ($key === 'type') {
            return match ($value) {
                'in' => 'قبض',
                'out' => 'صرف',
                'sales' => 'مبيعات',
                'purchase' => 'مشتريات',
                default => $value,
            };
        }

        if ($key === 'is_active' || $key === 'has_serial_numbers' || $key === 'has_expiry_date') {
            return $value ? 'نعم' : 'لا';
        }

        if ($key === 'payment_method') {
            return match ($value) {
                'cash' => 'نقداً',
                'check' => 'شيك',
                'bank_transfer' => 'تحويل بنكي',
                'credit_card' => 'بطاقة ائتمان',
                default => $value,
            };
        }

        return (string) $value;
    }
}
