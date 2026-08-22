<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicReceiptController extends Controller
{
    public function show(string $receiptToken)
    {
        $order = Order::where('receipt_token', $receiptToken)
            ->with(['items.product', 'cashier'])
            ->firstOrFail();
            
        $setting = Setting::first();

        return view('receipt.public', compact('order', 'setting'));
    }

    public function downloadPdf(string $receiptToken)
    {
        $order = Order::where('receipt_token', $receiptToken)
            ->with(['items.product', 'cashier'])
            ->firstOrFail();

        $setting = Setting::first();

        $pdf = Pdf::loadView('receipt.public', compact('order', 'setting'));

        return $pdf->download("struk-{$order->receipt_token}.pdf");
    }
}
