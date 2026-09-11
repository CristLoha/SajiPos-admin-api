<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header-container { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        .header-container h1 { margin: 0; color: #667eea; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .header-container p { margin: 5px 0 0; color: #666; font-size: 13px; }
        
        h3 { background-color: #667eea; color: #fff; padding: 8px 12px; margin-top: 30px; margin-bottom: 15px; font-size: 14px; border-radius: 4px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #ddd; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; color: #333; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .total-row th, .total-row td { background-color: #e2e8f0; color: #1a202c; font-size: 14px; border-top: 2px solid #667eea; }
        
        /* Zebra striping untuk tabel rincian transaksi */
        .table-striped tbody tr:nth-child(odd) { background-color: #ffffff; }
        .table-striped tbody tr:nth-child(even) { background-color: #f8f9fa; }
        
        .text-zero { color: #a0aec0; font-style: italic; }
        
        .footer { margin-top: 40px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 10px; color: #718096; text-align: right; }
    </style>
</head>
<body>

    <div class="header-container">
        <h1>SajiPOS</h1>
        <p><strong>LAPORAN PENJUALAN OUTLET</strong></p>
        <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    <h3>1. Ringkasan Pendapatan</h3>
    <table border="1">
        <tr>
            <th width="50%">Total Transaksi</th>
            <td class="text-right">{{ number_format($summary->total_transactions, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Subtotal</th>
            <td class="text-right">Rp {{ number_format($summary->total_sub_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Diskon</th>
            <td class="text-right {!! $summary->total_discounts == 0 ? 'text-zero' : '' !!}">
                Rp {{ number_format($summary->total_discounts, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <th>Total Pajak</th>
            <td class="text-right {!! $summary->total_taxes == 0 ? 'text-zero' : '' !!}">
                Rp {{ number_format($summary->total_taxes, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <th>Total Service Charge</th>
            <td class="text-right {!! $summary->total_service_charges == 0 ? 'text-zero' : '' !!}">
                Rp {{ number_format($summary->total_service_charges, 0, ',', '.') }}
            </td>
        </tr>
        <tr class="total-row">
            <th><strong>TOTAL PENDAPATAN BERSIH</strong></th>
            <td class="text-right"><strong>Rp {{ number_format($summary->total_revenue, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    <h3>2. Metode Pembayaran</h3>
    <table border="1" class="table-striped">
        <thead>
            <tr>
                <th style="background-color: #667eea; color: #fff;">Metode Pembayaran</th>
                <th class="text-center" style="background-color: #667eea; color: #fff;">Jumlah Transaksi</th>
                <th class="text-right" style="background-color: #667eea; color: #fff;">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paymentMethods as $pm)
            <tr>
                <td>{{ strtoupper($pm->payment_method) }}</td>
                <td class="text-center">{{ $pm->count }}</td>
                <td class="text-right">Rp {{ number_format($pm->revenue, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Belum ada data pembayaran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3>3. Rincian Transaksi</h3>
    <table border="1" class="table-striped">
        <thead>
            <tr>
                <th style="background-color: #667eea; color: #fff;">Waktu Transaksi</th>
                <th style="background-color: #667eea; color: #fff;">No. Struk</th>
                <th style="background-color: #667eea; color: #fff;">Kasir</th>
                <th style="background-color: #667eea; color: #fff;">Pembayaran</th>
                <th class="text-right" style="background-color: #667eea; color: #fff;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ \Carbon\Carbon::parse($order->transaction_time)->format('d/m/Y H:i') }}</td>
                <td>ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $order->cashier->name ?? 'Unknown' }}</td>
                <td>{{ strtoupper($order->payment_method) }}</td>
                <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada data transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Digenerate pada {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }} oleh {{ auth()->user()->name ?? 'Sistem SajiPOS' }}
    </div>

</body>
</html>
