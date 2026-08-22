@extends('layouts.app')

@section('title', 'Diskon')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Manajemen Diskon</h1>
                @if (in_array(auth()->user()->roles, ['admin', 'staff', 'user']))
                    <div class="section-header-button">
                        <a href="{{ route('discounts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Tambah Diskon
                        </a>
                    </div>
                @endif
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('discounts.index') }}">Diskon</a></div>
                    <div class="breadcrumb-item">Semua Diskon</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>
                <h2 class="section-title">Diskon</h2>
                <p class="section-lead">
                    Kelola semua promo dan diskon POS restoran di sini.
                </p>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Semua Diskon</h4>
                            </div>
                            <div class="card-body">
                                <div class="float-right mb-3">
                                    <form method="GET" action="{{ route('discounts.index') }}" class="w-100">
                                        <div class="row m-0 justify-content-end">
                                            <div class="col-md-4 pl-0 mb-2">
                                                <select name="status_filter" class="form-control" onchange="this.form.submit()">
                                                    <option value="">Semua Status</option>
                                                    <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="expired" {{ request('status_filter') == 'expired' ? 'selected' : '' }}>Expired</option>
                                                    <option value="inactive" {{ request('status_filter') == 'inactive' ? 'selected' : '' }}>Non-aktif</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 pr-0 mb-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Cari nama atau kode promo..." name="name" value="{{ request('name') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary px-3"><i class="fas fa-search"></i> Cari</button>
                                                        @if(request('name') || request('status_filter'))
                                                            <a href="{{ route('discounts.index') }}" class="btn btn-secondary px-3"><i class="fas fa-sync"></i> Reset</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="clearfix mb-3"></div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;">No</th>
                                                <th>Nama Promo</th>
                                                <th>Deskripsi</th>
                                                <th>Tipe</th>
                                                <th>Nilai Diskon</th>
                                                <th class="text-center">Status</th>
                                                <th>Tanggal Kedaluwarsa</th>
                                                <th class="text-center" style="width: 180px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($discounts as $discount)
                                                <tr>
                                                    <td class="text-center">{{ $discounts->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        <strong>{{ $discount->name }}</strong>
                                                        @if($discount->code)
                                                            <div class="text-muted small">Kode: <code>{{ $discount->code }}</code></div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $discount->description ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge badge-light" style="border: 1px solid var(--sp-border); font-weight: 500;">
                                                            {{ $discount->type == 'percentage' ? 'Persentase (%)' : 'Potongan Tetap (Fixed)' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($discount->type == 'percentage')
                                                            {{ number_format($discount->value, 0) }}%
                                                        @else
                                                            Rp {{ number_format($discount->value, 0, ',', '.') }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($discount->status == 'active')
                                                            <span class="badge badge-success">Aktif</span>
                                                        @elseif ($discount->status == 'upcoming')
                                                            <span class="badge badge-warning">Akan Datang</span>
                                                        @elseif ($discount->status == 'expired')
                                                            <span class="badge badge-danger">Kedaluwarsa</span>
                                                        @else
                                                            <span class="badge badge-secondary">Non-aktif</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($discount->expired_date)
                                                            <span class="{{ \Carbon\Carbon::parse($discount->expired_date)->isPast() ? 'text-danger font-weight-bold' : '' }}">
                                                                {{ \Carbon\Carbon::parse($discount->expired_date)->format('d M Y') }}
                                                                @if (\Carbon\Carbon::parse($discount->expired_date)->isPast())
                                                                    <small class="d-block">(Expired)</small>
                                                                @endif
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (in_array(auth()->user()->roles, ['admin', 'staff', 'user']))
                                                            <div class="d-flex justify-content-center">
                                                                <a href="{{ route('discounts.edit', $discount->id) }}"
                                                                    class="btn btn-sm btn-info btn-icon mr-1"
                                                                    data-toggle="tooltip" title="Edit Diskon">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>

                                                                <form action="{{ route('discounts.destroy', $discount->id) }}"
                                                                    method="POST" class="ml-1"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus diskon ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-sm btn-danger btn-icon"
                                                                        data-toggle="tooltip" title="Hapus Diskon">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @else
                                                            <span class="text-muted"><i class="fas fa-lock"></i> Tidak ada akses</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        <i class="fas fa-percent fa-2x mb-2 d-block" style="opacity: 0.3;"></i>
                                                        Belum ada data diskon.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $discounts->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
