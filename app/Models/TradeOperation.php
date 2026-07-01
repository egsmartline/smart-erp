<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeOperation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'type', 'operation_number', 'date', 'status',
        'party_id', 'party_name', 'party_type',
        'country', 'port_of_loading', 'port_of_discharge', 'incoterm',
        'currency_id', 'exchange_rate', 'total_value',
        'shipping_method', 'container_number', 'vessel_name',
        'bill_of_lading_number', 'etd_date', 'eta_date',
        'lc_number', 'lc_issuing_bank', 'lc_beneficiary_bank', 'lc_type',
        'lc_amount', 'lc_issue_date', 'lc_expiry_date',
        'customs_value', 'customs_duty_amount', 'shipping_cost',
        'insurance_cost', 'inspection_cost', 'other_costs',
        'notes', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'etd_date' => 'date',
        'eta_date' => 'date',
        'lc_issue_date' => 'date',
        'lc_expiry_date' => 'date',
        'exchange_rate' => 'decimal:4',
        'total_value' => 'decimal:2',
        'lc_amount' => 'decimal:2',
        'customs_value' => 'decimal:2',
        'customs_duty_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'insurance_cost' => 'decimal:2',
        'inspection_cost' => 'decimal:2',
        'other_costs' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function party()
    {
        if ($this->party_type === 'customer') {
            return $this->belongsTo(Customer::class, 'party_id');
        }
        if ($this->party_type === 'supplier') {
            return $this->belongsTo(Supplier::class, 'party_id');
        }
        return null;
    }

    public function scopeImportOperation($query)
    {
        return $query->where('type', 'import');
    }

    public function scopeExportOperation($query)
    {
        return $query->where('type', 'export');
    }

    public function scopeForTenant($query)
    {
        return $query->where('tenant_id', session('current_tenant_id') ?? auth()->user()->tenant_id);
    }

    public static function generateNumber($type)
    {
        $prefix = $type === 'import' ? 'IMP' : 'EXP';
        $lastOp = self::where('type', $type)
            ->where('tenant_id', session('current_tenant_id') ?? auth()->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->first();
        $nextNumber = $lastOp ? intval(substr($lastOp->operation_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function totalCosts()
    {
        return ($this->customs_duty_amount ?? 0) + ($this->shipping_cost ?? 0) + ($this->insurance_cost ?? 0) + ($this->inspection_cost ?? 0) + ($this->other_costs ?? 0);
    }

    public function netProfit()
    {
        if ($this->type === 'export') {
            return $this->total_value - $this->totalCosts();
        }
        return 0;
    }
}