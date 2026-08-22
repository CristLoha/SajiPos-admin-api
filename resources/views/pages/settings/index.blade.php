@extends('layouts.app')

@section('title', 'Pengaturan Toko')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Pengaturan Toko</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Pengaturan Toko</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Profil Toko</h2>
                <p class="section-lead">
                    Perbarui informasi identitas toko Anda.
                </p>

                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-7 col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h4>Form Profil Toko</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="shop_name">Nama Toko <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-store"></i></span>
                                            </div>
                                            <input id="shop_name" type="text"
                                                class="form-control @error('shop_name') is-invalid @enderror"
                                                name="shop_name" value="{{ old('shop_name', $setting?->shop_name) }}" required
                                                placeholder="Masukkan nama toko" oninput="updatePreview()">
                                        </div>
                                        @error('shop_name')
                                            <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="shop_phone">Nomor Telepon <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            </div>
                                            <input id="shop_phone" type="text"
                                                class="form-control @error('shop_phone') is-invalid @enderror"
                                                name="shop_phone" value="{{ old('shop_phone', $setting?->shop_phone) }}" required
                                                placeholder="Masukkan nomor telepon" oninput="updatePreview()">
                                        </div>
                                        @error('shop_phone')
                                            <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="shop_address">Alamat Toko <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            </div>
                                            <textarea id="shop_address"
                                                class="form-control @error('shop_address') is-invalid @enderror"
                                                name="shop_address" required style="height: 100px;"
                                                placeholder="Masukkan alamat toko" oninput="updatePreview()">{{ old('shop_address', $setting?->shop_address) }}</textarea>
                                        </div>
                                        <div class="mt-2 text-right">
                                            <button type="button" class="btn btn-sm btn-info" id="btn-get-location">
                                                <i class="fas fa-location-arrow mr-1"></i> Deteksi Otomatis dari GPS
                                            </button>
                                        </div>
                                        @error('shop_address')
                                            <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="logo">Logo Toko (Opsional)</label>
                                        <input id="logo" type="file" class="form-control @error('logo') is-invalid @enderror" name="logo" accept="image/*" onchange="previewLogo(event)">
                                        <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                                        @if($setting && $setting->logo_url)
                                            <div class="mt-2">
                                                <img id="logo-preview-img" src="{{ asset('storage/' . $setting->logo_url) }}" alt="Logo" style="max-height: 50px;">
                                            </div>
                                        @else
                                            <div class="mt-2">
                                                <img id="logo-preview-img" src="" alt="Logo" style="max-height: 50px; display:none;">
                                            </div>
                                        @endif
                                        @error('logo')
                                            <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr>
                                    <h5>Info yang Tampil di Struk</h5>
                                    <p class="text-muted text-small">Pilih informasi mana saja yang ingin ditampilkan pada struk digital pelanggan.</p>
                                    
                                    <ul class="list-group mb-4">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Nama Toko</strong>
                                                <div class="text-muted small">Wajib tampil sebagai identitas.</div>
                                            </div>
                                            <div>
                                                <span class="badge badge-success">Selalu Tampil</span>
                                            </div>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Nomor Telepon</strong>
                                                <div class="text-muted small" id="preview_phone_label">{{ old('shop_phone', $setting?->shop_phone) ?: '-' }}</div>
                                            </div>
                                            <label class="custom-switch mt-2">
                                                <input type="checkbox" name="show_phone_on_receipt" id="show_phone_on_receipt" class="custom-switch-input" {{ (old('show_phone_on_receipt', $setting?->show_phone_on_receipt ?? true)) ? 'checked' : '' }} onchange="updatePreview()">
                                                <span class="custom-switch-indicator"></span>
                                            </label>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Alamat Toko</strong>
                                                <div class="text-muted small" id="preview_address_label" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ old('shop_address', $setting?->shop_address) ?: '-' }}</div>
                                            </div>
                                            <label class="custom-switch mt-2">
                                                <input type="checkbox" name="show_address_on_receipt" id="show_address_on_receipt" class="custom-switch-input" {{ (old('show_address_on_receipt', $setting?->show_address_on_receipt ?? true)) ? 'checked' : '' }} onchange="updatePreview()">
                                                <span class="custom-switch-indicator"></span>
                                            </label>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Logo Toko</strong>
                                                <div class="text-muted small">Tampilkan logo di bagian atas struk.</div>
                                            </div>
                                            <label class="custom-switch mt-2">
                                                <input type="checkbox" name="show_logo_on_receipt" id="show_logo_on_receipt" class="custom-switch-input" {{ (old('show_logo_on_receipt', $setting?->show_logo_on_receipt ?? false)) ? 'checked' : '' }} onchange="updatePreview()">
                                                <span class="custom-switch-indicator"></span>
                                            </label>
                                        </li>
                                    </ul>

                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save mr-2"></i>Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-5 col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h4>Live Preview Struk</h4>
                            </div>
                            <div class="card-body bg-light" style="display: flex; justify-content: center; padding: 30px;">
                                <!-- Mockup Struk -->
                                <div style="background: #fff; width: 100%; max-width: 320px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); font-family: 'Courier New', Courier, monospace; color: #333; text-align: center;">
                                    
                                    <div id="preview-logo-container" style="{{ (old('show_logo_on_receipt', $setting?->show_logo_on_receipt ?? false)) ? '' : 'display:none;' }} margin-bottom: 10px;">
                                        @if($setting && $setting->logo_url)
                                            <img id="preview-receipt-logo" src="{{ asset('storage/' . $setting->logo_url) }}" style="max-width: 60px;">
                                        @else
                                            <img id="preview-receipt-logo" src="" style="max-width: 60px; display:none;">
                                        @endif
                                    </div>

                                    <h4 id="preview-shop-name" style="margin: 0 0 5px; font-size: 1.2rem; font-weight: bold; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">{{ old('shop_name', $setting?->shop_name) ?: 'Nama Toko' }}</h4>
                                    
                                    <div id="preview-shop-address-container" style="{{ (old('show_address_on_receipt', $setting?->show_address_on_receipt ?? true)) ? '' : 'display:none;' }}">
                                        <p id="preview-shop-address" style="margin: 2px 0; font-size: 0.85rem; color: #555; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">{{ old('shop_address', $setting?->shop_address) ?: 'Alamat Toko' }}</p>
                                    </div>
                                    
                                    <div id="preview-shop-phone-container" style="{{ (old('show_phone_on_receipt', $setting?->show_phone_on_receipt ?? true)) ? '' : 'display:none;' }}">
                                        <p id="preview-shop-phone" style="margin: 2px 0; font-size: 0.85rem; color: #555; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">{{ old('shop_phone', $setting?->shop_phone) ?: 'Telp: 0000000000' }}</p>
                                    </div>

                                    <div style="border-bottom: 1px dashed #ccc; margin: 15px 0;"></div>
                                    
                                    <div style="text-align: left; font-size: 0.85rem;">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>No. Trx</span>
                                            <span>#12345</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>Tanggal</span>
                                            <span>{{ date('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>

                                    <div style="border-bottom: 1px dashed #ccc; margin: 15px 0;"></div>

                                    <div style="text-align: left; font-size: 0.85rem;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                            <span>Nasi Goreng x2</span>
                                            <span>Rp 40.000</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>Es Teh Manis x2</span>
                                            <span>Rp 10.000</span>
                                        </div>
                                    </div>

                                    <div style="border-bottom: 1px dashed #ccc; margin: 15px 0;"></div>

                                    <div style="text-align: left; font-size: 0.9rem; font-weight: bold;">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>TOTAL</span>
                                            <span>Rp 50.000</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<!-- Modal Pilih Alamat -->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addressModalLabel">Pilih Format Alamat</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="list-group" id="address-options-list">
            <!-- Pilihan alamat akan di-inject ke sini via JS -->
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    function updatePreview() {
        // Update Teks Preview
        const nameVal = document.getElementById('shop_name').value || 'Nama Toko';
        const phoneVal = document.getElementById('shop_phone').value || 'Telp: 0000000000';
        const addressVal = document.getElementById('shop_address').value || 'Alamat Toko';

        document.getElementById('preview-shop-name').innerText = nameVal;
        document.getElementById('preview-shop-phone').innerText = phoneVal;
        document.getElementById('preview-shop-address').innerText = addressVal;

        // Update Label Kecil di List Toggles
        document.getElementById('preview_phone_label').innerText = phoneVal;
        document.getElementById('preview_address_label').innerText = addressVal;

        // Toggle Visibilitas Elemen Preview Struk
        const showPhone = document.getElementById('show_phone_on_receipt').checked;
        const showAddress = document.getElementById('show_address_on_receipt').checked;
        const showLogo = document.getElementById('show_logo_on_receipt').checked;

        document.getElementById('preview-shop-phone-container').style.display = showPhone ? 'block' : 'none';
        document.getElementById('preview-shop-address-container').style.display = showAddress ? 'block' : 'none';
        document.getElementById('preview-logo-container').style.display = showLogo ? 'block' : 'none';
    }

    function previewLogo(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img1 = document.getElementById('logo-preview-img');
                img1.src = e.target.result;
                img1.style.display = 'block';

                const img2 = document.getElementById('preview-receipt-logo');
                img2.src = e.target.result;
                img2.style.display = 'inline-block';
            }
            reader.readAsDataURL(file);
        }
    }

    document.getElementById('btn-get-location').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        
        if (!navigator.geolocation) {
            alert('Geolokasi tidak didukung oleh browser Anda.');
            return;
        }

        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mendapatkan lokasi...';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            // Menggunakan API Nominatim dengan parameter addressdetails=1
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}&addressdetails=1`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        const addr = data.address;
                        const variations = [];
                        
                        // Variasi 1: Alamat Super Lengkap (Bawaan API)
                        variations.push(data.display_name);

                        // Variasi 2: Tingkat Jalan / Gedung + Kota
                        const streetLevel = [addr.road, addr.suburb, addr.city || addr.town || addr.village].filter(Boolean).join(', ');
                        if (streetLevel && !variations.includes(streetLevel)) variations.push(streetLevel);

                        // Variasi 3: Tingkat Kecamatan + Kota + Provinsi
                        const districtLevel = [addr.suburb, addr.city_district, addr.city || addr.town || addr.village, addr.state].filter(Boolean).join(', ');
                        if (districtLevel && !variations.includes(districtLevel)) variations.push(districtLevel);

                        // Render ke dalam modal
                        const listGroup = document.getElementById('address-options-list');
                        listGroup.innerHTML = ''; // Kosongkan list sebelumnya
                        
                        variations.forEach((text, index) => {
                            const btnOpt = document.createElement('button');
                            btnOpt.type = 'button';
                            btnOpt.className = 'list-group-item list-group-item-action';
                            btnOpt.innerHTML = `<strong>Opsi ${index + 1}:</strong><br><small>${text}</small>`;
                            
                            btnOpt.onclick = function() {
                                document.getElementById('shop_address').value = text;
                                $('#addressModal').modal('hide'); // Tutup modal (asumsi pakai jQuery/Bootstrap)
                            };
                            
                            listGroup.appendChild(btnOpt);
                        });

                        // Tampilkan modal
                        $('#addressModal').modal('show');
                        
                    } else {
                        alert('Gagal mendapatkan alamat dari koordinat.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memproses lokasi.');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }, function(error) {
            alert('Akses lokasi ditolak atau gagal mendeteksi lokasi.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
</script>
@endpush
