@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Kategori</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('categories.index') }}">Kategori</a></div>
                    <div class="breadcrumb-item">Tambah</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Tambah Kategori Baru</h2>
                <p class="section-lead">Lengkapi formulir di bawah ini untuk membuat kategori produk baru.</p>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="card">
                            <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-header">
                                    <h4>Form Kategori</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Nama Kategori <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-tag"></i>
                                                </div>
                                            </div>
                                            <input type="text" 
                                                class="form-control @error('name') is-invalid @enderror" 
                                                name="name" 
                                                id="name" 
                                                value="{{ old('name') }}" 
                                                placeholder="Contoh: Makanan Utama" 
                                                required>
                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Deskripsi</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-align-left"></i>
                                                </div>
                                            </div>
                                            <textarea 
                                                class="form-control @error('description') is-invalid @enderror" 
                                                name="description" 
                                                id="description" 
                                                rows="3" 
                                                placeholder="Keterangan singkat tentang kategori ini..." 
                                                style="height: auto;">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>


                                </div>
                                <div class="card-footer text-right">
                                    <a href="{{ route('categories.index') }}" class="btn btn-outline-danger mr-2">Batal</a>
                                    <button class="btn btn-primary" type="submit">Simpan Kategori</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
