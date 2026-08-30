@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Pesanan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pesanan</a></div>
                    <div class="breadcrumb-item">Detail</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Detail Pesanan: #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h2>
                <p class="section-lead">Informasi lengkap rincian item, pajak, dan metode pembayaran pesanan.</p>

                <div class="row">
                    <!-- Ringkasan Transaksi -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Informasi Transaksi</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">No Pesanan</span>
                                        <strong>#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Kasir</span>
                                        <strong>{{ $order->cashier->name ?? '-' }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Waktu</span>
                                        <strong>{{ \Carbon\Carbon::parse($order->transaction_time)->format('d M Y H:i') }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Metode Bayar</span>
                                        <strong class="text-uppercase">{{ $order->payment_method }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if(strtolower($order->payment_method) === 'qris' && $order->payment_token)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h4>Detail QRIS Xendit</h4>
                            </div>
                            <div class="card-body text-center">
                                @php
                                    $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($order->payment_token);
                                @endphp
                                <img src="{{ $qrImageUrl }}" alt="QRIS Code" class="img-fluid rounded mb-3" style="border: 2px solid #eaeaea; padding: 10px; max-width: 250px;">
                                <p class="text-muted mb-0" style="font-size: 12px; word-break: break-all;">
                                    <strong>QR String:</strong><br>
                                    {{ $order->payment_token }}
                                </p>
                            </div>
                        </div>
                        @endif

                        <div class="card mt-3">
                            <div class="card-header">
                                <h4>Rincian Biaya</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span>Rp {{ number_format($order->sub_total, 0, ',', '.') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                                        <span class="text-muted">Diskon</span>
                                        <span class="text-danger">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                                        <span class="text-muted">Biaya Kirim (Ongkir)</span>
                                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                                        <span class="text-muted">Biaya Layanan</span>
                                        <span>Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                                        <span class="text-muted">Pajak</span>
                                        <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0 pt-3 border-top">
                                        <strong style="font-size: 1.1rem;">Grand Total</strong>
                                        <strong class="text-success" style="font-size: 1.1rem;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('orders.index') }}" class="btn btn-block btn-outline-primary"> kembali ke Daftar Pesanan</a>
                            </div>
                        </div>
                    </div>

                    <!-- Item Menu yang Dibeli -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Daftar Menu yang Dibeli</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;">No</th>
                                                <th>Gambar</th>
                                                <th>Nama Menu</th>
                                                <th>Harga Satuan</th>
                                                <th class="text-center">Jumlah</th>
                                                <th class="text-right">Total Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->items as $item)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if ($item->product && $item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                                alt="{{ $item->product->name }}" 
                                                                class="rounded" 
                                                                style="width: 50px; height: 50px; object-fit: cover; border: 1.5px solid var(--sp-border);">
                                                        @else
                                                            <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                                style="width: 50px; height: 50px; border: 1.5px solid var(--sp-border); font-size: 0.75rem; color: var(--sp-text-muted);">
                                                                <i class="fas fa-utensils fa-lg"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $item->product->name ?? 'Menu Terhapus' }}</strong>
                                                        <small class="d-block text-muted">{{ $item->product->category->name ?? '' }}</small>
                                                        @if ($item->note)
                                                            <div class="mt-1">
                                                                <span class="badge badge-warning text-dark" style="font-size: 11px; padding: 3px 8px;">
                                                                    <i class="fas fa-comment-alt mr-1"></i>Catatan: {{ $item->note }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-right"><strong>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
