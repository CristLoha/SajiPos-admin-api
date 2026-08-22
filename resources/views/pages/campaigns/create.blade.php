@extends('layouts.app')

@section('title', 'Tambah Campaign')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('campaigns.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>Tambah Campaign Baru</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></div>
                    <div class="breadcrumb-item">Tambah Campaign</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Tambah Campaign</h2>
                <p class="section-lead">
                    Di halaman ini Anda dapat menambahkan data campaign / flash sale (Harga coret) baru.
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Form Campaign</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('campaigns.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nama Campaign</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tanggal Mulai</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                                name="start_date" value="{{ old('start_date') }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tanggal Selesai</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                                name="end_date" value="{{ old('end_date') }}" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tipe Diskon</label>
                                        <div class="col-sm-12 col-md-7">
                                            <select class="form-control selectric @error('discount_type') is-invalid @enderror"
                                                name="discount_type" required>
                                                <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Persentase (%)</option>
                                                <option value="nominal" {{ old('discount_type') == 'nominal' ? 'selected' : '' }}>Potongan Tetap (Nominal)</option>
                                            </select>
                                            @error('discount_type')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nilai Diskon</label>
                                        <div class="col-sm-12 col-md-7">
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('discount_value') is-invalid @enderror"
                                                    name="discount_value" value="{{ old('discount_value') }}" min="0" required>
                                                @error('discount_value')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">Isikan nominal atau persentase angka (contoh: 20 untuk 20% atau 15000 untuk potongan 15.000)</small>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Pilih Produk</label>
                                        <div class="col-sm-12 col-md-7">
                                            <div class="list-group" style="max-height: 350px; overflow-y: auto; border: 1px solid var(--sp-border); border-radius: 5px;">
                                                @foreach ($products as $product)
                                                    <label class="list-group-item list-group-item-action d-flex align-items-center mb-0" style="cursor: pointer;">
                                                        <div class="custom-control custom-checkbox mr-3">
                                                            <input type="checkbox" name="products[]" value="{{ $product->id }}" class="custom-control-input" id="prod_{{ $product->id }}">
                                                            <label class="custom-control-label" for="prod_{{ $product->id }}"></label>
                                                        </div>
                                                        @if ($product->image)
                                                            <img src="{{ asset('storage/' . $product->image) }}" class="rounded mr-3" style="width: 45px; height: 45px; object-fit: cover; border: 1px solid #eee;">
                                                        @else
                                                            <div class="rounded bg-light d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; border: 1px solid #eee; color: #ccc;">
                                                                <i class="fas fa-utensils"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0 text-dark">{{ $product->name }}</h6>
                                                            <small class="text-muted font-weight-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</small>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <small class="form-text text-muted mt-2"><i class="fas fa-info-circle"></i> Centang produk yang ingin diikutkan dalam campaign ini.</small>
                                            @error('products')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status</label>
                                        <div class="col-sm-12 col-md-7">
                                            <select class="form-control selectric @error('is_active') is-invalid @enderror"
                                                name="is_active" required>
                                                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non-aktif</option>
                                            </select>
                                            @error('is_active')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                        <div class="col-sm-12 col-md-7">
                                            <button class="btn btn-primary">Buat Campaign</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
