@extends('layouts.app')

@section('title', 'Users')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Manajemen Users</h1>
                @if (auth()->user()->roles == 'admin')
                    <div class="section-header-button">
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Tambah User
                        </a>
                    </div>
                @endif
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></div>
                    <div class="breadcrumb-item">Semua Users</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>
                <h2 class="section-title">Users</h2>
                <p class="section-lead">
                    Kelola semua pengguna sistem, termasuk menambah, mengedit, dan menghapus.
                </p>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Semua Users</h4>
                            </div>
                            <div class="card-body">
                                <div class="float-right mb-3">
                                    <form method="GET" action="{{ route('users.index') }}">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Cari user..."
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
                                                <th>Nama</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th class="text-center">Role</th>
                                                <th>Dibuat</th>
                                                <th class="text-center" style="width: 180px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($users as $user)
                                                <tr>
                                                    <td class="text-center">{{ $users->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mr-2"
                                                                style="width: 32px; height: 32px; font-size: 0.75rem; font-weight: 600; color: #3949AB;">
                                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                                            </div>
                                                            <span>{{ $user->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td><code>{{ $user->username }}</code></td>
                                                    <td>{{ $user->email }}</td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge badge-{{ $user->roles == 'admin' ? 'danger' : ($user->roles == 'staff' ? 'warning' : 'primary') }}">
                                                            {{ ucfirst($user->roles) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                                    <td class="text-center">
                                                        @if (auth()->user()->roles == 'admin')
                                                            <div class="d-flex justify-content-center">
                                                                <a href="{{ route('users.edit', $user->id) }}"
                                                                    class="btn btn-sm btn-info btn-icon mr-1"
                                                                    data-toggle="tooltip" title="Edit User">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>

                                                                @if (auth()->id() == $user->id)
                                                                    <button
                                                                        class="btn btn-sm btn-danger btn-icon ml-1"
                                                                        disabled title="Tidak bisa hapus akun sendiri"
                                                                        data-toggle="tooltip">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                @else
                                                                    <form
                                                                        action="{{ route('users.destroy', $user->id) }}"
                                                                        method="POST" class="ml-1">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button
                                                                            class="btn btn-sm btn-danger btn-icon btn-delete"
                                                                            data-toggle="tooltip" title="Hapus User">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-muted"><i class="fas fa-lock"></i> Tidak
                                                                ada akses</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        <i class="fas fa-users fa-2x mb-2 d-block" style="opacity: 0.3;"></i>
                                                        Belum ada data user.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right mt-3">
                                    {{ $users->withQueryString()->links() }}
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
    <!-- JS Libraries -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    <script src="{{ asset('library/sweetalert/dist/sweetalert.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>

    <!-- Konfirmasi Delete -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    swal({
                        title: 'Yakin hapus user ini?',
                        text: 'Data yang dihapus tidak bisa dikembalikan!',
                        icon: 'warning',
                        buttons: {
                            cancel: 'Batal',
                            confirm: {
                                text: 'Ya, Hapus!',
                                value: true,
                                className: 'btn-danger',
                            }
                        },
                        dangerMode: true,
                    }).then(function(willDelete) {
                        if (willDelete) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
