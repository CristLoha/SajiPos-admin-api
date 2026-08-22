@extends('layouts.auth')

@section('title', 'Login')

@section('main')
    <div class="card card-primary">
        <div class="card-header">
            <h4>Masuk ke SajiPOS</h4>
        </div>

        <div class="card-body">
            <p class="text-muted mb-4">Silakan masuk untuk mengakses dashboard restoran Anda</p>

            <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
                @csrf
                <div class="form-group">
                    <label for="email">Email atau Username</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input id="email" type="text"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" tabindex="1" required autofocus
                            placeholder="Masukkan email atau username">
                    </div>
                    @error('email')
                        <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password" tabindex="2" required
                            placeholder="Masukkan password">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                style="border: 1px solid #ced4da; border-left: none; background-color: #fff;">
                                <i class="fas fa-eye" id="toggleIcon" style="color: #6c757d;"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="remember" class="custom-control-input" tabindex="3"
                            id="remember-me">
                        <label class="custom-control-label" for="remember-me">Ingat Saya</label>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center text-muted">
                Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if ($errors->has('email'))
            Swal.fire({
                icon: 'error',
                title: 'Waduh...',
                text: '{{ $errors->first('email') }}',
                confirmButtonColor: '#6777ef',
                confirmButtonText: 'Siap Bung!'
            });
        @endif

        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
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
    </script>
@endpush
