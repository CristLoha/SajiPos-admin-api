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
                <h1>{{ request('status') ? 'Approval Users' : 'Manajemen Users' }}</h1>
                @if (auth()->user()->roles == 'admin' && !request('status'))
                    <div class="section-header-button">
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Tambah User
                        </a>
                    </div>
                @endif
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></div>
                    <div class="breadcrumb-item">{{ request('status') == 'pending_approval' ? 'Approval' : 'Semua Users' }}</div>
                </div>
            </div>
            <div id="live-users-container">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>
                <h2 class="section-title">{{ request('status') == 'pending_approval' ? 'Approval Users' : 'Users' }}</h2>
                <p class="section-lead">
                    {{ request('status') == 'pending_approval' ? 'Review dan setujui atau tolak pendaftaran user baru.' : 'Kelola semua pengguna sistem, termasuk menambah, mengedit, dan menghapus.' }}
                </p>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ request('status') == 'pending_approval' ? 'Antrian Approval' : 'Semua Users' }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <ul class="nav nav-pills">
                                            <li class="nav-item">
                                                <a class="nav-link {{ request('status') == '' ? 'active' : '' }}" href="{{ route('users.index', ['name' => request('name')]) }}">Semua</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request('status') == 'pending_approval' ? 'active' : '' }}" href="{{ route('users.index', ['status' => 'pending_approval', 'name' => request('name')]) }}">
                                                    Pending <span class="badge badge-{{ request('status') == 'pending_approval' ? 'white' : 'primary' }}">{{ \App\Models\User::where('status_akun', 'pending_approval')->count() }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}" href="{{ route('users.index', ['status' => 'approved', 'name' => request('name')]) }}">Approved</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}" href="{{ route('users.index', ['status' => 'rejected', 'name' => request('name')]) }}">Rejected</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="float-right">
                                            <form method="GET" action="{{ route('users.index') }}">
                                                @if(request('status'))
                                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                                @endif
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Cari user..."
                                                        name="name" value="{{ request('name') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;">No</th>
                                                <th>Nama</th>
                                                <th>Username / Email</th>
                                                <th class="text-center">Email</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Role</th>
                                                <th>Aktivitas</th>
                                                <th class="text-center" style="width: 200px;">Aksi</th>
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
                                                    <td>
                                                        <code>{{ $user->username }}</code><br>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                    </td>
                                                    <td class="text-center" id="email-cell-{{ $user->id }}">
                                                        @if($user->email_verified_at)
                                                            <span class="badge badge-success" id="email-badge-{{ $user->id }}">Verified</span>
                                                        @else
                                                            <span class="badge badge-light text-muted" id="email-badge-{{ $user->id }}">Unverified</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($user->status_akun == 'pending_approval')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @elseif($user->status_akun == 'approved')
                                                            <span class="badge badge-success">Approved</span>
                                                        @elseif($user->status_akun == 'rejected')
                                                            <span class="badge badge-danger">Rejected</span>
                                                        @else
                                                            <span class="badge badge-secondary">{{ ucfirst($user->status_akun) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($user->roles)
                                                            <span class="badge badge-{{ $user->roles == 'admin' ? 'danger' : ($user->roles == 'staff' ? 'warning' : 'primary') }}">
                                                                {{ ucfirst($user->roles) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small class="text-muted d-block">Terdaftar: {{ $user->created_at->format('d M Y') }}</small>
                                                        @if($user->last_login_at)
                                                            <small class="text-success d-block" title="Last Login"><i class="fas fa-sign-in-alt"></i> {{ $user->last_login_at->format('d M Y, H:i') }}</small>
                                                        @endif
                                                        @if($user->last_logout_at)
                                                            <small class="text-danger d-block" title="Last Logout"><i class="fas fa-sign-out-alt"></i> {{ $user->last_logout_at->format('d M Y, H:i') }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (auth()->user()->roles == 'admin')
                                                            <div class="d-flex justify-content-center">
                                                                @if($user->status_akun == 'pending_approval')
                                                                    @if($user->email_verified_at)
                                                                        <button id="btn-approve-{{ $user->id }}" class="btn btn-sm btn-success btn-icon mr-1" data-toggle="modal" data-target="#approveModal{{ $user->id }}" title="Setujui">
                                                                            <i class="fas fa-check"></i>
                                                                        </button>
                                                                    @else
                                                                        <button id="btn-approve-{{ $user->id }}" class="btn btn-sm btn-secondary btn-icon mr-1" onclick="if(typeof Swal !== 'undefined') { Swal.fire('Belum Verifikasi OTP!', 'Kasir ini belum memverifikasi kode OTP dari emailnya. Anda baru bisa menyetujui akun ini setelah status email menjadi Verified.', 'warning'); } else if(typeof swal !== 'undefined') { swal('Belum Verifikasi OTP!', 'Kasir ini belum memverifikasi kode OTP...', 'warning'); } else { alert('Belum Verifikasi OTP! Kasir ini belum memverifikasi kode OTP dari emailnya.'); }" title="Email Belum Diverifikasi">
                                                                            <i class="fas fa-check"></i>
                                                                        </button>
                                                                    @endif
                                                                    <button class="btn btn-sm btn-warning btn-icon mr-1" data-toggle="modal" data-target="#rejectModal{{ $user->id }}" title="Tolak">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                @endif

                                                                @if($user->status_akun != 'pending_approval')
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
            <!-- Modals for Approve and Reject -->
            @if(auth()->user()->roles == 'admin')
                @foreach($users as $user)
                    @if($user->status_akun == 'pending_approval')
                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('users.approve', $user->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="approveModalLabel{{ $user->id }}">Setujui User: {{ $user->name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Pilih role untuk user ini:</p>
                                            <div class="form-group">
                                                <label>Role</label>
                                                <select name="role" class="form-control selectric" required>
                                                    <option value="" disabled>Pilih Role...</option>
                                                    <option value="admin" {{ $user->roles == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    <option value="staff" {{ $user->roles == 'staff' ? 'selected' : '' }}>Staff</option>
                                                    <option value="user" {{ $user->roles == 'user' ? 'selected' : '' }}>User (Kasir)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">Setujui</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('users.reject', $user->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rejectModalLabel{{ $user->id }}">Tolak User: {{ $user->name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Berikan alasan penolakan:</p>
                                            <div class="form-group">
                                                <label>Alasan</label>
                                                <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
            </div> <!-- End of live-users-container -->
        </section>
    </div>

@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    <script src="{{ asset('library/sweetalert/dist/sweetalert.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>

    <!-- Konfirmasi Delete & Modal Fix -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fix modal z-index issue (Stisla backdrop bug)
            $('.modal').appendTo('body');

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
