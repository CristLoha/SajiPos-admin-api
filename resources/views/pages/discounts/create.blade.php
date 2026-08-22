@extends('layouts.app')

@section('title', 'Tambah Diskon')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Diskon</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('discounts.index') }}">Diskon</a></div>
                    <div class="breadcrumb-item">Tambah</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Tambah Diskon Baru</h2>
                <p class="section-lead">Buat promo diskon baru untuk dipasang di aplikasi POS Kasir.</p>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="card">
                            <form action="{{ route('discounts.store') }}" method="POST">
                                @csrf
                                <div class="card-header">
                                    <h4>Form Diskon</h4>
                                </div>
                                <div class="card-body">
                                    <!-- Rekomendasi Diskon -->
                                    <div class="mb-4">
                                        <label class="d-block text-muted" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">💡 Template Rekomendasi</label>
                                        <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                                            <button type="button" class="btn btn-sm btn-outline-info btn-template" data-type="fixed" data-value="10000" data-min="50000" data-max="" data-name="Diskon Goceng (Min. 50rb)">Potongan 10rb (Min. 50rb)</button>
                                            <button type="button" class="btn btn-sm btn-outline-success btn-template" data-type="percentage" data-value="20" data-min="0" data-max="20000" data-name="Diskon 20% (Max. 20rb)">Diskon 20% (Max 20rb)</button>
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-template" data-type="percentage" data-value="50" data-min="100000" data-max="50000" data-name="Flash Sale 50% (Sultan)">Flash Sale 50% (Sultan)</button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="name">Nama Promo/Diskon <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-percent"></i>
                                                </div>
                                            </div>
                                            <input type="text" 
                                                class="form-control @error('name') is-invalid @enderror" 
                                                name="name" 
                                                id="name" 
                                                value="{{ old('name') }}" 
                                                placeholder="Contoh: Welcome WCB" 
                                                required>
                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="code">Kode Promo (Optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-barcode"></i>
                                                </div>
                                            </div>
                                            <input type="text" 
                                                class="form-control @error('code') is-invalid @enderror" 
                                                name="code" 
                                                id="code" 
                                                value="{{ old('code') }}" 
                                                placeholder="Contoh: WCB20"
                                                style="text-transform: uppercase;">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button" id="btn-generate-code">Generate</button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Kosongkan agar diskon otomatis tersedia untuk dipilih kasir. Isi jika ingin digunakan sebagai voucher manual.</small>
                                        @error('code')
                                            <div class="invalid-feedback" style="display: block;">
                                                {{ $message }}
                                            </div>
                                        @enderror
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
                                                placeholder="Detail promosi..." 
                                                style="height: auto;">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="type">Tipe Diskon <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-sliders-h"></i>
                                                </div>
                                            </div>
                                            <select 
                                                class="form-control @error('type') is-invalid @enderror" 
                                                name="type" 
                                                id="type" 
                                                required>
                                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Potongan Tetap (Rupiah)</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="value">Nilai Diskon <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-coins"></i>
                                                </div>
                                            </div>
                                            <input type="number" 
                                                class="form-control @error('value') is-invalid @enderror" 
                                                name="value" 
                                                id="value" 
                                                value="{{ old('value') }}" 
                                                placeholder="Contoh: 10 untuk 10% atau 10000 untuk Rp 10.000" 
                                                min="0"
                                                required>
                                            @error('value')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="max_discount">Maksimal Diskon (Rp)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Rp</div>
                                                    </div>
                                                    <input type="number" class="form-control @error('max_discount') is-invalid @enderror" name="max_discount" id="max_discount" value="{{ old('max_discount') }}" placeholder="Contoh: 20000" min="0">
                                                    @error('max_discount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <small class="form-text text-muted">Batas maksimal potongan untuk tipe Persentase. Kosongkan jika tanpa batas.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="min_transaction">Minimal Transaksi (Rp)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Rp</div>
                                                    </div>
                                                    <input type="number" class="form-control @error('min_transaction') is-invalid @enderror" name="min_transaction" id="min_transaction" value="{{ old('min_transaction') }}" placeholder="Contoh: 50000" min="0">
                                                    @error('min_transaction')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <small class="form-text text-muted">Syarat minimal belanja. Kosongkan jika tanpa syarat.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Status Diskon <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-toggle-on"></i>
                                                </div>
                                            </div>
                                            <select 
                                                class="form-control @error('status') is-invalid @enderror" 
                                                name="status" 
                                                id="status" 
                                                required>
                                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Non-aktif</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="start_date">Tanggal Mulai Berlaku</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                            </div>
                                            <input type="date" 
                                                class="form-control @error('start_date') is-invalid @enderror" 
                                                name="start_date" 
                                                id="start_date" 
                                                value="{{ old('start_date') }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">Bisa dikosongkan jika promo langsung berlaku sekarang.</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="expired_date">Tanggal Kedaluwarsa</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-calendar-times"></i>
                                                </div>
                                            </div>
                                            <input type="date" 
                                                class="form-control @error('expired_date') is-invalid @enderror" 
                                                name="expired_date" 
                                                id="expired_date" 
                                                value="{{ old('expired_date') }}">
                                            @error('expired_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">Kosongkan jika diskon berlaku selamanya.</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="quota">Kuota Penggunaan (Optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                            </div>
                                            <input type="number" class="form-control @error('quota') is-invalid @enderror" name="quota" id="quota" value="{{ old('quota') }}" placeholder="Contoh: 100" min="0">
                                            @error('quota')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">Batas maksimal diskon ini bisa dipakai oleh semua pelanggan. Kosongkan jika unlimited.</small>
                                    </div>

                                    <div class="alert alert-light mt-4 mb-3" style="border: 1px dashed var(--sp-border); background: #f8f9fd;">
                                        <h6 class="alert-heading text-primary mb-1" style="font-size: 0.9rem;"><i class="fas fa-eye"></i> Simulasi Info di Mesin Kasir</h6>
                                        <p class="mb-0 text-dark" id="preview-text" style="font-size: 0.95rem;">Mulai isi form untuk melihat preview.</p>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <a href="{{ route('discounts.index') }}" class="btn btn-outline-danger mr-2">Batal</a>
                                    <button class="btn btn-primary" type="submit">Simpan Diskon</button>
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
        const typeSelect = document.getElementById('type');
        const valueInput = document.getElementById('value');
        
        function updateValueValidation() {
            const maxDiscountInput = document.getElementById('max_discount');
            const minTransactionInput = document.getElementById('min_transaction');
            const maxDiscountLabel = document.querySelector('label[for="max_discount"]');
            
            if (typeSelect.value === 'percentage') {
                valueInput.setAttribute('max', '100');
                valueInput.setAttribute('placeholder', 'Contoh: 10 untuk 10%');
                if (parseInt(valueInput.value) > 100) {
                    valueInput.value = 100;
                }
                
                // ACTIVE PROTECTION: Persentase > 50% WAJIB ada batas maksimal
                if (parseInt(valueInput.value) > 50) {
                    maxDiscountInput.setAttribute('required', 'required');
                    if (!maxDiscountLabel.querySelector('.text-danger')) {
                        maxDiscountLabel.innerHTML += ' <span class="text-danger">*</span>';
                    }
                    maxDiscountLabel.nextElementSibling.nextElementSibling.innerHTML = 'Wajib diisi karena diskon di atas 50% (mencegah rugi bandar).';
                } else {
                    maxDiscountInput.removeAttribute('required');
                    if (maxDiscountLabel.querySelector('.text-danger')) {
                        maxDiscountLabel.querySelector('.text-danger').remove();
                    }
                    maxDiscountLabel.nextElementSibling.nextElementSibling.innerHTML = 'Batas maksimal potongan untuk tipe Persentase. Kosongkan jika tanpa batas.';
                }
                
                minTransactionInput.removeAttribute('min');
            } else {
                valueInput.removeAttribute('max');
                valueInput.setAttribute('placeholder', 'Contoh: 10000 untuk Rp 10.000');
                
                // ACTIVE PROTECTION: Potongan tetap tidak wajib ada batas maksimal
                maxDiscountInput.removeAttribute('required');
                if (maxDiscountLabel.querySelector('.text-danger')) {
                    maxDiscountLabel.querySelector('.text-danger').remove();
                }
                maxDiscountLabel.nextElementSibling.nextElementSibling.innerHTML = 'Batas maksimal potongan untuk tipe Persentase. Kosongkan jika tanpa batas.';
                
                // Minimal transaksi disarankan >= nilai diskon
                const val = parseInt(valueInput.value) || 0;
                minTransactionInput.setAttribute('min', val);
            }
        }

        typeSelect.addEventListener('change', updateValueValidation);
        valueInput.addEventListener('input', function() {
            if (typeSelect.value === 'percentage' && parseInt(this.value) > 100) {
                this.value = 100;
            }
        });
        const btnGenerate = document.getElementById('btn-generate-code');
        const codeInput = document.getElementById('code');
        const nameInput = document.getElementById('name');
        const previewText = document.getElementById('preview-text');

        if(btnGenerate) {
            btnGenerate.addEventListener('click', function() {
                let baseName = nameInput.value.trim().toUpperCase();
                
                if (baseName.length > 0) {
                    // Filter kata umum agar kode lebih bermakna
                    const skipWords = ['SPESIAL', 'SPECIAL', 'PROMO', 'DISKON', 'DISCOUNT', 'WELCOME', 'GEBYAR', 'EXTRA'];
                    let words = baseName.replace(/[^A-Z0-9 ]/g, '').split(/\s+/);
                    
                    let filteredWords = words.filter(w => !skipWords.includes(w) && w.length > 0);
                    if(filteredWords.length === 0) filteredWords = words; // fallback
                    
                    // Gabungkan max 2 kata (kata utama + angka/tahun)
                    baseName = filteredWords.slice(0, 2).join('');
                    if(baseName.length > 15) baseName = baseName.substring(0, 15);
                } else {
                    baseName = 'PROMO';
                }

                // Suffix cukup 2 angka acak
                const chars = '0123456789';
                let randomSuffix = '';
                for (let i = 0; i < 2; i++) {
                    randomSuffix += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                
                codeInput.value = baseName + '-' + randomSuffix;
                updatePreview();
            });
        }

        // Live Preview
        function updatePreview() {
            let name = nameInput.value || '[Nama Diskon]';
            let val = valueInput.value || '0';
            let code = codeInput.value ? `Kode: <strong>${codeInput.value.toUpperCase()}</strong>` : '<em>(Tersedia Otomatis / Tanpa Kode)</em>';
            
            let valStr = typeSelect.value === 'percentage' ? `${val}%` : `Rp ${parseInt(val || 0).toLocaleString('id-ID')}`;
            previewText.innerHTML = `<strong>${name}</strong>: Potongan ${valStr} <br> <span class="text-muted" style="font-size: 0.85rem;">${code}</span>`;
        }

        nameInput.addEventListener('input', updatePreview);
        codeInput.addEventListener('input', updatePreview);

        typeSelect.addEventListener('change', updatePreview);
        valueInput.addEventListener('input', updatePreview);

        updateValueValidation();
        // Date validation
        const startDateInput = document.getElementById('start_date');
        const expiredDateInput = document.getElementById('expired_date');

        function updateDateConstraints() {
            // Update expired_date min
            if (startDateInput.value) {
                expiredDateInput.setAttribute('min', startDateInput.value);
                if (expiredDateInput.value && expiredDateInput.value < startDateInput.value) {
                    expiredDateInput.value = startDateInput.value;
                }
            } else {
                const today = new Date().toISOString().split('T')[0];
                expiredDateInput.setAttribute('min', today);
            }

            // Update start_date max
            if (expiredDateInput.value) {
                startDateInput.setAttribute('max', expiredDateInput.value);
                if (startDateInput.value && startDateInput.value > expiredDateInput.value) {
                    startDateInput.value = expiredDateInput.value;
                }
            } else {
                startDateInput.removeAttribute('max');
            }
        }

        startDateInput.addEventListener('change', updateDateConstraints);
        expiredDateInput.addEventListener('change', updateDateConstraints);
        // Initialize on load
        updateDateConstraints();

        // Template Recommendation logic
        document.querySelectorAll('.btn-template').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('name').value = this.dataset.name;
                typeSelect.value = this.dataset.type;
                valueInput.value = this.dataset.value;
                document.getElementById('min_transaction').value = this.dataset.min;
                document.getElementById('max_discount').value = this.dataset.max;
                
                updateValueValidation();
                updatePreview();
                
                // Animasi kecil biar tahu diklik
                this.classList.add('active');
                setTimeout(() => this.classList.remove('active'), 200);
            });
        });
    });
</script>
@endpush
