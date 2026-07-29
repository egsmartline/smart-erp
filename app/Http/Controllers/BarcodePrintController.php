<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodePrintController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(Item::class)
            ->with('category', 'unit', 'warehouses')
            ->where(function ($q) {
                $q->whereNotNull('barcode')->where('barcode', '!=', '')
                  ->orWhereNotNull('sku')->where('sku', '!=', '');
            });

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->orderBy('name')->get();

        $generator = new BarcodeGeneratorPNG();

        $barcodes = $items->map(function ($item) use ($generator) {
            $code = $item->barcode ?: $item->sku;
            $barcodeData = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 50);
            $item->barcode_base64 = base64_encode($barcodeData);
            $item->display_barcode = $code;
            return $item;
        });

        $categories = $this->tenantQuery(\App\Models\ItemCategory::class)->orderBy('name')->get();

        return view('barcodes.index', compact('barcodes', 'categories'));
    }

    public function print(Request $request)
    {
        $items = [];
        $generator = new BarcodeGeneratorPNG();

        $raw = $request->query('d', '');
        if (!$raw) {
            return view('barcodes.print', compact('items'));
        }

        foreach (explode(',', $raw) as $part) {
            $segments = explode(':', $part);
            $id = (int)($segments[0] ?? 0);
            $qty = max(1, (int)($segments[1] ?? 1));
            if (!$id) continue;

            $item = $this->tenantQuery(Item::class)->find($id);
            if (!$item) continue;

            $code = $item->barcode ?: $item->sku;
            $barcodeData = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 50);

            $items[] = [
                'name' => $item->name,
                'code' => $code,
                'sku' => $item->sku && $item->sku !== $code ? $item->sku : null,
                'barcode_base64' => base64_encode($barcodeData),
                'qty' => $qty,
            ];
        }

        return view('barcodes.print', compact('items'));
    }

    public function printCode(Request $request)
    {
        $code = $request->query('code', '');
        $qty = max(1, (int)$request->query('qty', 1));
        $name = $request->query('name', $code);

        if (!$code) {
            return response('No code provided', 400);
        }

        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 50);

        $items[] = [
            'name' => $name,
            'code' => $code,
            'sku' => null,
            'barcode_base64' => base64_encode($barcodeData),
            'qty' => $qty,
        ];

        return view('barcodes.print', compact('items'));
    }
}