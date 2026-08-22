@extends('layouts.app')

@section('title', 'Edit Produk')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Produk</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></div>
                    <div class="breadcrumb-item">Edit</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Edit Produk: {{ $product->name }}</h2>
                <p class="section-lead">Perbarui data produk makanan atau minuman di bawah ini.</p>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="card">
                            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card-header">
                                    <h4>Form Edit Produk</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="category_id">Kategori <span class="text-danger">*</span></label>
                                        <a href="{{ route('categories.create') }}" target="_blank" class="float-right" style="font-size: 0.85rem;"><i class="fas fa-plus"></i> Kategori Baru</a>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-tag"></i>
                                                </div>
                                            </div>
                                            <select 
                                                class="form-control @error('category_id') is-invalid @enderror" 
                                                name="category_id" 
                                                id="category_id" 
                                                required>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="name">Nama Produk <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-utensils"></i>
                                                </div>
                                            </div>
                                            <input type="text" 
                                                class="form-control @error('name') is-invalid @enderror" 
                                                name="name" 
                                                id="name" 
                                                value="{{ old('name', $product->name) }}" 
                                                placeholder="Contoh: Nasi Goreng Spesial" 
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
                                                placeholder="Keterangan bahan atau rasa produk..." 
                                                style="height: auto;">{{ old('description', $product->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="price">Harga Produk (Rp) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <strong>Rp</strong>
                                                </div>
                                            </div>
                                            <input type="number" 
                                                class="form-control @error('price') is-invalid @enderror" 
                                                name="price" 
                                                id="price" 
                                                value="{{ old('price', $product->price) }}" 
                                                placeholder="Contoh: 25000" 
                                                min="0"
                                                required>
                                            @error('price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="stock">Stok <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-boxes"></i>
                                                </div>
                                            </div>
                                            <input type="number" 
                                                class="form-control @error('stock') is-invalid @enderror" 
                                                name="stock" 
                                                id="stock" 
                                                value="{{ old('stock', $product->stock) }}" 
                                                min="0" 
                                                required>
                                            @error('stock')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>



                                    <div class="form-group">
                                        <label>Gambar Saat Ini</label>
                                        <div class="mb-3">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" 
                                                    alt="{{ $product->name }}" 
                                                    class="img-thumbnail" 
                                                    style="max-width: 150px; object-fit: cover;">
                                            @else
                                                <div class="text-muted" style="font-size: 0.85rem;">
                                                    <i class="fas fa-info-circle mr-1"></i>Produk ini belum memiliki gambar.
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="image">Ubah Gambar Produk</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            </div>
                                            <input type="file" 
                                                class="form-control @error('image') is-invalid @enderror" 
                                                name="image" 
                                                id="image" 
                                                accept="image/*">
                                            @error('image')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">Format: jpeg, png, jpg, svg. Maksimal 2MB. Biarkan kosong jika tidak ingin mengubah gambar.</small>
                                        <div class="mt-3" id="image-preview-container" style="display: none;">
                                            <p class="mb-1 text-muted" style="font-size: 0.85rem;">Gambar Baru:</p>
                                            <img id="image-preview" src="" alt="Preview" class="img-thumbnail" style="max-height: 150px; border-radius: 8px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-danger mr-2">Batal</a>
                                    <button class="btn btn-primary" type="submit" id="btn-submit">Perbarui Produk</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image Preview
        const imageInput = document.getElementById('image');
        const previewContainer = document.getElementById('image-preview-container');
        const previewImage = document.getElementById('image-preview');

        if(imageInput) {
            imageInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.style.display = 'block';
                    }
                    reader.readAsDataURL(e.target.files[0]);
                } else {
                    previewContainer.style.display = 'none';
                }
            });
        }

    });
</script>
@endpush
