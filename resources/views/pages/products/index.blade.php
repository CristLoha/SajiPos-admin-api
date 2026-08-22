@extends('layouts.app')

@section('title', 'Produk')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Manajemen Produk</h1>
                @if (in_array(auth()->user()->roles, ['admin', 'staff', 'user']))
                    <div class="section-header-button">
                        <a href="{{ route('discounts.index') }}" class="btn btn-outline-primary mr-2">
                            <i class="fas fa-percent mr-1"></i>Kelola Diskon
                        </a>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Tambah Produk
                        </a>
                    </div>
                @endif
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></div>
                    <div class="breadcrumb-item">Semua Produk</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>
                <h2 class="section-title">Produk</h2>
                <p class="section-lead">
                    Kelola semua produk makanan, minuman, dan item POS restoran di sini.
                </p>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Semua Produk</h4>
                            </div>
                            <div class="card-body">
                                    <form method="GET" action="{{ route('products.index') }}" class="w-100">
                                        <div class="row m-0">
                                            <div class="col-md-3 pl-0 mb-2">
                                                <select name="category_id" class="form-control" onchange="this.form.submit()">
                                                    <option value="">Semua Kategori</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 pl-0 mb-2">
                                                <select name="stock_status" class="form-control" onchange="this.form.submit()">
                                                    <option value="">Semua Stok</option>
                                                    <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Tersedia (> 5)</option>
                                                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Menipis (1 - 5)</option>
                                                    <option value="empty" {{ request('stock_status') == 'empty' ? 'selected' : '' }}>Habis (0)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 pr-0 mb-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Cari produk..." name="name" value="{{ request('name') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                                        @if(request('name') || request('category_id') || request('stock_status'))
                                                            <a href="{{ route('products.index') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                <div class="clearfix mb-3"></div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;">No</th>
                                                <th>Gambar</th>
                                                <th>Nama Produk</th>
                                                <th>Kategori</th>
                                                <th>Harga</th>
                                                <th class="text-center">Stok</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center" style="width: 180px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($products as $product)
                                                <tr>
                                                    <td class="text-center">{{ $products->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        @if ($product->image)
                                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                                alt="{{ $product->name }}" 
                                                                class="rounded" 
                                                                style="width: 50px; height: 50px; object-fit: cover; border: 1.5px solid var(--sp-border);">
                                                        @else
                                                            <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                                style="width: 50px; height: 50px; border: 1.5px solid var(--sp-border); font-size: 0.75rem; color: var(--sp-text-muted);">
                                                                <i class="fas fa-utensils fa-lg"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td><strong>{{ $product->name }}</strong></td>
                                                    <td>
                                                        <span class="badge badge-light" style="border: 1px solid var(--sp-border); font-weight: 500;">
                                                            {{ $product->category->name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($product->is_campaign_active)
                                                            <div class="d-flex flex-column align-items-start py-1">
                                                                <div class="text-muted mb-1" style="text-decoration: line-through; font-size: 0.85rem; line-height: 1;">
                                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                                </div>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="font-weight-bold text-success mr-2">
                                                                        Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                                                    </div>
                                                                    <span class="badge badge-warning" style="font-size: 0.65rem; padding: 4px 6px; letter-spacing: 0.5px;">PROMO</span>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="py-2">
                                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($product->stock == 0)
                                                            <span class="badge badge-danger" data-toggle="tooltip" title="Stok Habis!">{{ $product->stock }}</span>
                                                        @elseif($product->stock <= 5)
                                                            <span class="badge badge-warning" data-toggle="tooltip" title="Stok Menipis! Segera Restock.">{{ $product->stock }}</span>
                                                        @else
                                                            {{ $product->stock }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($product->stock == 0)
                                                            <span class="badge badge-danger">Habis</span>
                                                        @else
                                                            <span class="badge badge-{{ $product->status ? 'success' : 'secondary' }}">
                                                                {{ $product->status ? 'Tersedia' : 'Nonaktif' }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (in_array(auth()->user()->roles, ['admin', 'staff', 'user']))
                                                            <div class="d-flex justify-content-center">
                                                                <a href="{{ route('products.edit', $product->id) }}"
                                                                    class="btn btn-sm btn-info btn-icon mr-1"
                                                                    data-toggle="tooltip" title="Edit Produk">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>

                                                                <form action="{{ route('products.destroy', $product->id) }}"
                                                                    method="POST" class="ml-1"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-sm btn-danger btn-icon"
                                                                        data-toggle="tooltip" title="Hapus Produk">
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
                                                        <i class="fas fa-utensils fa-2x mb-2 d-block" style="opacity: 0.3;"></i>
                                                        Belum ada data produk.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right mt-4">
                                    {{ $products->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
