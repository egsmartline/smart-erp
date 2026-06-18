<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function format($amount, ?string $currency = null): string
    {
        $currency = $currency ?? session('display_currency', 'EGP');
        $symbol = $currency === 'USD' ? '$' : 'ج.م';
        return number_format((float)$amount, 2) . ' ' . $symbol;
    }

    public static function convert($amount, string $from, string $to): float
    {
        if ($from === $to) return (float)$amount;
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        $rate = \App\Models\Currency::where('tenant_id', $tenantId)
            ->where('code', $to)->value('exchange_rate') ?? 1;
        return round((float)$amount * $rate, 2);
    }

    public static function getSymbol(string $currency = 'EGP'): string
    {
        return $currency === 'USD' ? '$' : 'ج.م';
    }
}
