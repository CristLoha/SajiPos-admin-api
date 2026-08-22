<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $order->receipt_token }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .receipt-container {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0 0 5px;
            font-size: 24px;
        }
        .header p {
            margin: 2px 0;
            font-size: 14px;
            color: #666;
        }
        .details {
            margin-bottom: 20px;
            font-size: 14px;
        }
        .details table {
            width: 100%;
        }
        .details th {
            text-align: left;
            font-weight: normal;
            color: #666;
        }
        .items table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items th, .items td {
            padding: 8px 0;
            font-size: 14px;
            border-bottom: 1px dashed #eee;
        }
        .items th {
            text-align: left;
            border-bottom: 1px dashed #ccc;
        }
        .summary table {
            width: 100%;
            font-size: 14px;
        }
        .summary td {
            padding: 4px 0;
        }
        .summary .total {
            font-weight: bold;
            font-size: 16px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 5px;
            font-size: 14px;
        }
        .btn-secondary {
            background: #6c757d;
        }

        /* Mode Print */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="header text-center">
        @if(isset($setting) && $setting->show_logo_on_receipt && $setting->logo_url)
            <img src="{{ asset('storage/' . $setting->logo_url) }}" alt="Logo" style="max-width: 80px; margin-bottom: 10px;">
        @endif
        <h1>{{ $setting->shop_name ?? 'Toko Kita' }}</h1>
        @if(!isset($setting) || $setting->show_address_on_receipt)
            <p>{{ $setting->shop_address ?? 'Alamat Toko' }}</p>
        @endif
        @if(!isset($setting) || $setting->show_phone_on_receipt)
            <p>{{ $setting->shop_phone ?? 'Telp: 00000' }}</p>
        @endif
    </div>

    <div class="details">
        <table>
            <tr>
                <th>No. Trx</th>
                <td class="text-right">{{ $order->id }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td class="text-right">{{ date('d M Y H:i', strtotime($order->transaction_time)) }}</td>
            </tr>
            <tr>
                <th>Kasir</th>
                <td class="text-right">{{ $order->cashier->name ?? 'Kasir' }}</td>
            </tr>
        </table>
    </div>

    <div class="items">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Item' }}<br><small>Rp {{ number_format($item->price, 0, ',', '.') }}</small></td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($order->sub_total, 0, ',', '.') }}</td>
            </tr>
            @if($order->discount_amount > 0)
            <tr>
                <td>Diskon ({{ $order->discount_name }})</td>
                <td class="text-right">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($order->shipping_cost > 0)
            <tr>
                <td>Ongkir</td>
                <td class="text-right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($order->service_charge > 0)
            <tr>
                <td>Layanan</td>
                <td class="text-right">Rp {{ number_format($order->service_charge, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($order->tax > 0)
            <tr>
                <td>Pajak</td>
                <td class="text-right">Rp {{ number_format($order->tax, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total">
                <td>Total</td>
                <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Pembayaran</td>
                <td class="text-right">{{ strtoupper($order->payment_method) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>Simpan struk ini sebagai bukti pembayaran yang sah.</p>
    </div>

    <div class="actions no-print">
        <button onclick="window.print()" class="btn">Print / Simpan Gambar</button>
        <a href="{{ route('receipt.pdf', $order->receipt_token) }}" class="btn btn-secondary">Download PDF</a>
    </div>
</div>

</body>
</html>
