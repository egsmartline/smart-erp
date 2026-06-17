<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentTermController extends Controller
{
    public function index()
    {
        $paymentTerms = PaymentTerm::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->paginate(20);

        return view('payment-terms.index', compact('paymentTerms'));
    }

    public function create()
    {
        return view('payment-terms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'days_net' => 'required|integer|min:0',
            'days_discount' => 'nullable|integer|min:0',
            'note' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = Auth::user()->tenant_id;
        $validated['is_active'] = $request->boolean('is_active', true);

        PaymentTerm::create($validated);

        return redirect()->route('payment-terms.index')
            ->with('success', 'تم إنشاء شروط الدفع بنجاح');
    }

    public function show(PaymentTerm $paymentTerm)
    {
        return view('payment-terms.show', compact('paymentTerm'));
    }

    public function edit(PaymentTerm $paymentTerm)
    {
        return view('payment-terms.edit', compact('paymentTerm'));
    }

    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'days_net' => 'required|integer|min:0',
            'days_discount' => 'nullable|integer|min:0',
            'note' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $paymentTerm->update($validated);

        return redirect()->route('payment-terms.index')
            ->with('success', 'تم تحديث شروط الدفع بنجاح');
    }

    public function destroy(PaymentTerm $paymentTerm)
    {
        $paymentTerm->delete();

        return redirect()->route('payment-terms.index')
            ->with('success', 'تم حذف شروط الدفع بنجاح');
    }
}
