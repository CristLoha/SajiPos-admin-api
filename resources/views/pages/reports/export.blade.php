<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h2, h3 { text-align: center; margin: 5px 0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-box { margin-bottom: 20px; border: 1px solid #000; padding: 10px; }
    </style>
</head>
<body>

    <h2>Laporan Penjualan SajiPOS</h2>
    <p class="text-center">Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>

    <h3>1. Ringkasan Pendapatan</h3>
    <table>
        <tr>
            <th>Total Transaksi</th>
            <td class="text-right">{{ number_format($summary->total_transactions, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Subtotal</th>
            <td class="text-right">Rp {{ number_format($summary->total_sub_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Diskon</th>
            <td class="text-right">Rp {{ number_format($summary->total_discounts, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Pajak</th>
            <td class="text-right">Rp {{ number_format($summary->total_taxes, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Service Charge</th>
            <td class="text-right">Rp {{ number_format($summary->total_service_charges, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th><strong>TOTAL PENDAPATAN</strong></th>
            <td class="text-right"><strong>Rp {{ number_format($summary->total_revenue, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    <h3>2. Metode Pembayaran</h3>
    <table>
        <thead>
            <tr>
                <th>Metode</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentMethods as $pm)
            <tr>
                <td>{{ strtoupper($pm->payment_method) }}</td>
                <td class="text-center">{{ $pm->count }}</td>
                <td class="text-right">Rp {{ number_format($pm->revenue, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>3. Rincian Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>No. Struk</th>
                <th>Kasir</th>
                <th>Pembayaran</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ \Carbon\Carbon::parse($order->transaction_time)->format('d/m/Y H:i') }}</td>
                <td>{{ $order->receipt_number }}</td>
                <td>{{ $order->cashier->name ?? 'Unknown' }}</td>
                <td>{{ strtoupper($order->payment_method) }}</td>
                <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
