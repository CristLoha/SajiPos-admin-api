@extends('layouts.app')

@section('title', 'Edit User')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit User</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></div>
                    <div class="breadcrumb-item">Edit User</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Edit User</h2>
                <p class="section-lead">
                    Perbarui informasi pengguna <strong>{{ $user->name }}</strong>.
                </p>

                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Form Edit User</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input id="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                name="name" value="{{ old('name', $user->name) }}" required
                                                placeholder="Masukkan nama lengkap">
                                        </div>
                                        @error('name')
                                            <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="username">Username <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-at"></i></span>
                                            </div>
                                            <input id="username" type="text"
                                                class="form-control @error('username') is-invalid @enderror"
                                                name="username" value="{{ old('username', $user->username) }}" required
                                                placeholder="Masukkan username">
                                        </div>
                                        @error('username')
                                            <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email', $user->email) }}" required
                                                placeholder="Masukkan alamat email">
                                        </div>
                                        @error('email')
                                            <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="password">Password <small class="text-muted">(opsional)</small></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                name="password"
                                                placeholder="Kosongkan jika tidak ingin mengubah">
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
                                        <label for="password_confirmation">Konfirmasi Password</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input id="password_confirmation" type="password"
                                                class="form-control"
                                                name="password_confirmation"
                                                placeholder="Ketik ulang password baru">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                                    data-target="password_confirmation"
                                                    style="border: 1px solid #ced4da; border-left: none; background-color: #fff;">
                                                    <i class="fas fa-eye toggle-icon" style="color: #6c757d;"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @error('password_confirmation')
                                            <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if (auth()->user()->roles == 'admin')
                                        <div class="form-group">
                                            <label for="roles">Role <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                                </div>
                                                <select id="roles" name="roles"
                                                    class="form-control @error('roles') is-invalid @enderror" required>
                                                    <option value="" disabled>Pilih role</option>
                                                    <option value="admin" {{ old('roles', $user->roles) == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    <option value="staff" {{ old('roles', $user->roles) == 'staff' ? 'selected' : '' }}>Staff</option>
                                                    <option value="user" {{ old('roles', $user->roles) == 'user' ? 'selected' : '' }}>User (Kasir)</option>
                                                </select>
                                            </div>
                                            @error('roles')
                                                <div class="text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save mr-2"></i>Update User
                                        </button>
                                        @if (auth()->user()->roles == 'admin')
                                            <a href="{{ route('users.index') }}" class="btn btn-outline-danger btn-lg ml-2">
                                                <i class="fas fa-arrow-left mr-2"></i>Batal
                                            </a>
                                        @else
                                            <a href="{{ route('home') }}" class="btn btn-outline-danger btn-lg ml-2">
                                                <i class="fas fa-arrow-left mr-2"></i>Batal
                                            </a>
                                        @endif
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

@push('scripts')
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
