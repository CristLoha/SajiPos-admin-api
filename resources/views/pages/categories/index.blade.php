@extends('layouts.app')

@section('title', 'Kategori')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Manajemen Kategori</h1>
                @if (in_array(auth()->user()->roles, ['admin', 'staff', 'user']))
                    <div class="section-header-button">
                        <a href="{{ route('categories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Tambah Kategori
                        </a>
                    </div>
                @endif
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('categories.index') }}">Kategori</a></div>
                    <div class="breadcrumb-item">Semua Kategori</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>
                <h2 class="section-title">Kategori</h2>
                <p class="section-lead">
                    Kelola semua kategori produk sistem, termasuk menambah, mengedit, dan menghapus.
                </p>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Semua Kategori</h4>
                            </div>
                            <div class="card-body">
                                <div class="float-right mb-3">
                                    <form method="GET" action="{{ route('categories.index') }}">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Cari kategori..."
                                                name="name" value="{{ request('name') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
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
                                                <th>Nama Kategori</th>
                                                <th>Deskripsi</th>
                                                <th>Dibuat</th>
                                                <th class="text-center" style="width: 180px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($categories as $category)
                                                <tr>
                                                    <td class="text-center">{{ $categories->firstItem() + $loop->index }}</td>
                                                    <td><strong>{{ $category->name }}</strong></td>
                                                    <td>{{ $category->description ?? '-' }}</td>
                                                    <td>{{ $category->created_at->format('d M Y') }}</td>
                                                    <td class="text-center">
                                                        @if (in_array(auth()->user()->roles, ['admin', 'staff', 'user']))
                                                            <div class="d-flex justify-content-center">
                                                                <a href="{{ route('categories.edit', $category->id) }}"
                                                                    class="btn btn-sm btn-info btn-icon mr-1"
                                                                    data-toggle="tooltip" title="Edit Kategori">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>

                                                                <form action="{{ route('categories.destroy', $category->id) }}"
                                                                    method="POST" class="ml-1"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-sm btn-danger btn-icon"
                                                                        data-toggle="tooltip" title="Hapus Kategori">
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
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <i class="fas fa-tags fa-2x mb-2 d-block" style="opacity: 0.3;"></i>
                                                        Belum ada data kategori.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $categories->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
