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
                                                <th>Dibuat</th>
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
                                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                                    <td class="text-center">
                                                        @if (auth()->user()->roles == 'admin')
                                                            <div class="d-flex justify-content-center">
                                                                @if($user->status_akun == 'pending_approval')
                                                                    <button id="btn-approve-{{ $user->id }}" class="btn btn-sm btn-success btn-icon mr-1" data-toggle="modal" data-target="#approveModal{{ $user->id }}" title="{{ $user->email_verified_at ? 'Setujui' : 'Email Belum Diverifikasi' }}" {{ $user->email_verified_at ? '' : 'disabled' }}>
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                    <button class="btn btn-sm btn-warning btn-icon mr-1" data-toggle="modal" data-target="#rejectModal{{ $user->id }}" title="Tolak">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                @endif

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

    <!-- Audio Element for Notification -->
    <audio id="notif-sound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <!-- Smart Polling Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Track state locally for pending users so we know when it changes
            let userEmailStatuses = {};
            let knownUserIds = [];
            
            // Inisialisasi state awal (hanya ngambil dari DOM yang unverified/pending)
            document.querySelectorAll('[id^="email-badge-"]').forEach(badge => {
                let userId = parseInt(badge.id.replace('email-badge-', ''));
                userEmailStatuses[userId] = badge.classList.contains('badge-success'); // true = verified
                knownUserIds.push(userId);
            });

            const notifSound = document.getElementById('notif-sound');

            // Polling function
            function pollUserStatus() {
                fetch('{{ route("users.polling") }}')
                    .then(response => response.json())
                    .then(data => {
                        let incomingIds = data.users.map(u => u.id);
                        
                        // Check for brand new users that are not in our known DOM
                        let hasNewUser = incomingIds.some(id => !knownUserIds.includes(id));

                        if (hasNewUser) {
                            // Mainkan suara ting
                            notifSound.play().catch(e => console.log('Audio play di-block browser:', e));
                            
                            // Ambil HTML halaman baru secara diam-diam (PJAX)
                            fetch(window.location.href)
                                .then(res => res.text())
                                .then(html => {
                                    let doc = new DOMParser().parseFromString(html, 'text/html');
                                    let newContainer = doc.getElementById('live-users-container');
                                    if(newContainer) {
                                        document.getElementById('live-users-container').innerHTML = newContainer.innerHTML;
                                        
                                        // Update state lokal
                                        knownUserIds = incomingIds;
                                        data.users.forEach(u => {
                                            userEmailStatuses[u.id] = u.email_verified_at !== null;
                                        });

                                        // Beri highlight sejenak di baris pertama
                                        let tbody = document.querySelector('tbody');
                                        if(tbody && tbody.firstElementChild) {
                                            tbody.firstElementChild.style.transition = "background-color 0.5s ease";
                                            tbody.firstElementChild.style.backgroundColor = "#d4edda";
                                            setTimeout(() => { tbody.firstElementChild.style.backgroundColor = "transparent"; }, 2000);
                                        }
                                    }
                                });
                            return; // Stop di sini, biarkan HTML baru dirender
                        }

                        // Jika tidak ada user baru, cukup update status email (seperti sebelumnya)
                        data.users.forEach(user => {
                            let badge = document.getElementById('email-badge-' + user.id);
                            let btnApprove = document.getElementById('btn-approve-' + user.id);
                            
                            if (badge) {
                                let isVerifiedNow = user.email_verified_at !== null;
                                let wasVerifiedBefore = userEmailStatuses[user.id];

                                // Jika tadinya Unverified dan sekarang Verified
                                if (!wasVerifiedBefore && isVerifiedNow) {
                                    notifSound.play().catch(e => console.log('Audio play di-block browser:', e));
                                    
                                    badge.className = 'badge badge-success';
                                    badge.textContent = 'Verified';
                                    
                                    let cell = document.getElementById('email-cell-' + user.id);
                                    if(cell) {
                                        cell.style.transition = "background-color 0.5s ease";
                                        cell.style.backgroundColor = "#fff3cd"; 
                                        setTimeout(() => { cell.style.backgroundColor = "transparent"; }, 2000);
                                    }

                                    if(btnApprove) {
                                        btnApprove.disabled = false;
                                        btnApprove.title = 'Setujui';
                                    }

                                    userEmailStatuses[user.id] = true;
                                }
                            }
                        });
                    })
                    .catch(err => console.error("Polling error:", err));
            }

            // Jalankan polling setiap 5 detik
            setInterval(pollUserStatus, 5000);
        });
    </script>
@endpush
