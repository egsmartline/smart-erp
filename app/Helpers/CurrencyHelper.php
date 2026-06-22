<?php

namespace App\Helpers;

use App\Models\Currency;

class CurrencyHelper
{
    public static function format($amount, $currency = null): string
    {
        $symbol = 'ج.م';
        if ($currency instanceof Currency) {
            $symbol = $currency->symbol;
        } elseif (is_numeric($currency)) {
            $c = Currency::find($currency);
            if ($c) $symbol = $c->symbol;
        } elseif (is_string($currency)) {
            $c = Currency::where('code', $currency)->first();
            if ($c) $symbol = $c->symbol;
        } else {
            $default = Currency::where('is_default', true)->first();
            if ($default) $symbol = $default->symbol;
        }
        return number_format((float)$amount, 2) . ' ' . $symbol;
    }

    public static function getSymbol($currency = null): string
    {
        if ($currency instanceof Currency) {
            return $currency->symbol;
        }
        if (is_numeric($currency)) {
            $c = Currency::find($currency);
            return $c ? $c->symbol : 'ج.م';
        }
        if (is_string($currency)) {
            $c = Currency::where('code', $currency)->first();
            return $c ? $c->symbol : 'ج.م';
        }
        $default = Currency::where('is_default', true)->first();
        return $default ? $default->symbol : 'ج.م';
    }
}
