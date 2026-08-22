@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Kategori</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('categories.index') }}">Kategori</a></div>
                    <div class="breadcrumb-item">Edit</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Edit Kategori: {{ $category->name }}</h2>
                <p class="section-lead">Perbarui informasi kategori produk di bawah ini.</p>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="card">
                            <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card-header">
                                    <h4>Form Edit Kategori</h4>
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
                                                value="{{ old('name', $category->name) }}" 
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
                                                style="height: auto;">{{ old('description', $category->description) }}</textarea>
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
                                    <button class="btn btn-primary" type="submit">Perbarui Kategori</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
