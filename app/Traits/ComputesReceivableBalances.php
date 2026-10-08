<?php

namespace App\Traits;

use App\Models\Currency;

/**
 * حساب أرصدة المديونية بالعملة، من غير ما يتم صب عملة في عملة
 * (يعني 103 دولار ما تتجمعش مع 105,000 جنيه).
 */
trait ComputesReceivableBalances
{
    /**
     * رمز الحالة لكل عملة للمنشأة الحالية: ['3' => 'EGP', '4' => 'USD']
     */
    protected function currencyCodeMap(int $tenantId): array
    {
        return Currency::where('tenant_id', $tenantId)
            ->whereNull('deleted_at')
            ->pluck('code', 'id')
            ->all();
    }

    /**
     * العملة الأساسية للمنشأة (للي مالوش عملة محددة زي إشعارات الخصم).
     */
    protected function baseCurrencyCode(int $tenantId): string
    {
        $base = Currency::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();

        return $base?->code ?? 'EGP';
    }

    /**
     * رصيد العميل كـ map: ['EGP' => 105000.0, 'USD' => 103.0]
     *
     * الدوال الحالة نفسها (salesInvoices/payments/discountNotes) لازم تكون
     * محمّلة قبل الاستدعاء، وsalesInvoices لازم تكون مصفاة من 'voided'.
     */
    protected function receivableByCurrency($customer, array $currencyCodes, string $baseCode): array
    {
        $balances = [];

        $openingBal = (float) ($customer->opening_balance ?? 0);
        $openingSigned = $customer->opening_balance_type === 'credit' ? -$openingBal : $openingBal;

        $openingCode = $customer->openingBalanceCurrency?->code;
        if ($openingCode === null && !empty($customer->opening_balance_currency_id)) {
            $openingCode = $currencyCodes[$customer->opening_balance_currency_id] ?? null;
        }
        $openingCode = $openingCode ?: $baseCode;

        $balances[$openingCode] = ($balances[$openingCode] ?? 0) + $openingSigned;

        foreach ($customer->salesInvoices ?? [] as $inv) {
            if ($inv->status === 'voided') {
                continue;
            }
            $code = $currencyCodes[$inv->currency_id] ?? $baseCode;
            $balances[$code] = ($balances[$code] ?? 0) + (float) $inv->total;
        }

        foreach ($customer->payments ?? [] as $pay) {
            $code = $currencyCodes[$pay->currency_id] ?? $baseCode;
            $amount = (float) $pay->amount;
            if ($pay->type === 'receipt') {
                $amount = -$amount;
            }
            $balances[$code] = ($balances[$code] ?? 0) + $amount;
        }

        foreach ($customer->discountNotes ?? [] as $dn) {
            $balances[$baseCode] = ($balances[$baseCode] ?? 0) - (float) $dn->amount;
        }

        return $balances;
    }

    /**
     * مجموع أرصدة المديونية لكل عملة، على خريطة ['EGP' => 1234.5, ...]
     */
    protected function totalByCurrency($rows, string $field = 'currency_balances'): array
    {
        $totals = [];
        foreach ($rows as $row) {
            foreach ($row->{$field} ?? [] as $code => $amount) {
                $totals[$code] = ($totals[$code] ?? 0) + (float) $amount;
            }
        }

        return $totals;
    }
}