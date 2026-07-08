<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends TenantAwareController
{
    public function salesInvoice(SalesInvoice $invoice)
    {
        if ($invoice->tenant_id !== $this->getTenantId()) abort(403);
        $invoice->load(['customer', 'lines.item']);
        $company = Company::where('tenant_id', $this->getTenantId())->first();
        $pdf = Pdf::loadView('pdf.sales-invoice', compact('invoice', 'company'));
        $pdf->setPaper('a4');
        if (request()->has('print')) {
            return $pdf->stream("فاتورة-بيع-{$invoice->invoice_number}.pdf");
        }
        return $pdf->download("فاتورة-بيع-{$invoice->invoice_number}.pdf");
    }

    public function purchaseInvoice(PurchaseInvoice $invoice)
    {
        if ($invoice->tenant_id !== $this->getTenantId()) abort(403);
        $invoice->load(['supplier', 'lines.item']);
        $company = Company::where('tenant_id', $this->getTenantId())->first();
        $pdf = Pdf::loadView('pdf.purchase-invoice', compact('invoice', 'company'));
        $pdf->setPaper('a4');
        if (request()->has('print')) {
            return $pdf->stream("فاتورة-شراء-{$invoice->invoice_number}.pdf");
        }
        return $pdf->download("فاتورة-شراء-{$invoice->invoice_number}.pdf");
    }

    public function purchaseOrder(PurchaseOrder $order)
    {
        if ($order->tenant_id !== $this->getTenantId()) abort(403);
        $order->load(['supplier', 'lines.item']);
        $company = Company::where('tenant_id', $this->getTenantId())->first();
        $pdf = Pdf::loadView('pdf.purchase-order', compact('order', 'company'));
        $pdf->setPaper('a4');
        return $pdf->download("أمر-شراء-{$order->order_number}.pdf");
    }

    public function quotation(Quotation $quotation)
    {
        if ($quotation->tenant_id !== $this->getTenantId()) abort(403);
        $quotation->load(['customer', 'lines.item']);
        $company = Company::where('tenant_id', $this->getTenantId())->first();
        $pdf = Pdf::loadView('pdf.quotation', compact('quotation', 'company'));
        $pdf->setPaper('a4');
        return $pdf->download("عرض-سعر-{$quotation->quote_number}.pdf");
    }
}
