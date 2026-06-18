<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencySwitcherController extends Controller
{
    public function switch(Request $request, string $currency)
    {
        $request->validate(['currency' => 'required|in:EGP,USD']);
        session(['display_currency' => $currency]);
        return back()->with('success', 'تم تغيير العملة بنجاح');
    }
}
