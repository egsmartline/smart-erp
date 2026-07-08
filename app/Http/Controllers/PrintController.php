<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;

class PrintController extends TenantAwareController
{
    public function salesInvoice(SalesInvoice $invoice)
    {
        if ($invoice->tenant_id !== $this->getTenantId()) abort(403);
        $invoice->load(['customer', 'currency', 'lines.item']);
        $company = \App\Models\Company::where('tenant_id', $this->getTenantId())->first();
        return view('print.sales-invoice', compact('invoice', 'company'));
    }
}
