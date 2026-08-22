@extends('layouts.app')

@section('title', 'Detail Diskon')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Diskon</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('discounts.index') }}">Diskon</a></div>
                    <div class="breadcrumb-item">Detail</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Detail Diskon: {{ $discount->name }}</h2>
                <p class="section-lead">Informasi detail promo diskon yang terdaftar.</p>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Informasi Diskon</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-md">
                                        <tbody>
                                            <tr>
                                                <th>Nama Promo</th>
                                                <td>{{ $discount->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Kode Promo</th>
                                                <td><code>{{ $discount->code ?? '-' }}</code></td>
                                            </tr>
                                            <tr>
                                                <th>Deskripsi</th>
                                                <td>{{ $discount->description ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tipe Diskon</th>
                                                <td>
                                                    @if($discount->type == 'percentage')
                                                        Persentase (%)
                                                    @else
                                                        Potongan Tetap (Rupiah)
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Nilai Diskon</th>
                                                <td>
                                                    @if($discount->type == 'percentage')
                                                        {{ $discount->value }}%
                                                    @else
                                                        Rp {{ number_format($discount->value, 0, ',', '.') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    @if($discount->status == 'active')
                                                        <span class="badge badge-success">Aktif</span>
                                                    @else
                                                        <span class="badge badge-danger">Non-aktif</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Kedaluwarsa</th>
                                                <td>
                                                    @if($discount->expired_date)
                                                        {{ \Carbon\Carbon::parse($discount->expired_date)->format('d F Y') }}
                                                    @else
                                                        <span class="badge badge-info">Berlaku Selamanya</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Dibuat Pada</th>
                                                <td>{{ $discount->created_at->format('d F Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Diperbarui Pada</th>
                                                <td>{{ $discount->updated_at->format('d F Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('discounts.index') }}" class="btn btn-secondary mr-2">Kembali</a>
                                <a href="{{ route('discounts.edit', $discount->id) }}" class="btn btn-primary">Edit Diskon</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
