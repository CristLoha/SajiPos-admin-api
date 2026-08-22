@extends('layouts.auth')

@section('title', 'Register')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="card card-primary">
        <div class="card-header">
            <h4>Daftar Akun Baru</h4>
        </div>

        <div class="card-body">
            <p class="text-muted mb-4">Buat akun untuk mengakses SajiPOS</p>

            <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate="">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input id="name" type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" tabindex="1" required autofocus
                            placeholder="Masukkan nama lengkap">
                    </div>
                    @error('name')
                        <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-at"></i></span>
                        </div>
                        <input id="username" type="text"
                            class="form-control @error('username') is-invalid @enderror"
                            name="username" value="{{ old('username') }}" tabindex="2" required
                            placeholder="Masukkan username (tanpa spasi)">
                    </div>
                    @error('username')
                        <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input id="email" type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" tabindex="3" required
                            placeholder="Masukkan alamat email">
                    </div>
                    @error('email')
                        <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="d-block">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password" tabindex="4" required
                            placeholder="Min. 8 karakter">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                data-target="password"
                                style="border: 1px solid #ced4da; border-left: none; background-color: #fff;">
                                <i class="fas fa-eye toggle-icon" style="color: #6c757d;"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="d-block">Konfirmasi Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input id="password_confirmation" type="password" class="form-control"
                            name="password_confirmation" tabindex="5" required
                            placeholder="Ulangi password">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                data-target="password_confirmation"
                                style="border: 1px solid #ced4da; border-left: none; background-color: #fff;">
                                <i class="fas fa-eye toggle-icon" style="color: #6c757d;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="6">
                        <i class="fas fa-user-plus mr-2"></i>Daftar
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center text-muted">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('.toggle-icon');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
@endpush
