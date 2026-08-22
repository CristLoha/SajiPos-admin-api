@extends('layouts.app')

@section('title', 'Campaign')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Manajemen Campaign</h1>
                @if (in_array(auth()->user()->roles, ['admin', 'staff', 'user']))
                    <div class="section-header-button">
                        <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Tambah Campaign
                        </a>
                    </div>
                @endif
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaign</a></div>
                    <div class="breadcrumb-item">Semua Campaign</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>
                <h2 class="section-title">Campaign</h2>
                <p class="section-lead">
                    Kelola semua campaign promo produk di sini.
                </p>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Semua Campaign</h4>
                            </div>
                            <div class="card-body">
                                <div class="float-right mb-3">
                                    <form method="GET" action="{{ route('campaigns.index') }}" class="w-100">
                                        <div class="row m-0 justify-content-end">
                                            <div class="col-md-5 pr-0 mb-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Cari nama campaign..." name="name" value="{{ request('name') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                                        @if(request('name'))
                                                            <a href="{{ route('campaigns.index') }}" class="btn btn-secondary"><i class="fas fa-sync"></i></a>
                                                        @endif
                                                    </div>
                                                </div>
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
                                                <th>Nama Campaign</th>
                                                <th>Periode</th>
                                                <th>Tipe Diskon</th>
                                                <th>Nilai Diskon</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center" style="width: 180px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($campaigns as $campaign)
                                                <tr>
                                                    <td class="text-center">{{ $campaigns->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        <strong>{{ $campaign->name }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($campaign->start_date)->format('d M Y') }} - 
                                                        {{ \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') }}
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light" style="border: 1px solid var(--sp-border); font-weight: 500;">
                                                            {{ $campaign->discount_type == 'percent' ? 'Persentase (%)' : 'Potongan Tetap (Nominal)' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($campaign->discount_type == 'percent')
                                                            {{ number_format($campaign->discount_value, 0) }}%
                                                        @else
                                                            Rp {{ number_format($campaign->discount_value, 0, ',', '.') }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-{{ $campaign->is_active ? 'success' : 'danger' }}">
                                                            {{ $campaign->is_active ? 'Aktif' : 'Non-aktif' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if (in_array(auth()->user()->roles, ['admin', 'staff', 'user']))
                                                            <div class="d-flex justify-content-center">
                                                                <a href="{{ route('campaigns.edit', $campaign->id) }}"
                                                                    class="btn btn-sm btn-info btn-icon mr-1"
                                                                    data-toggle="tooltip" title="Edit Campaign">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>

                                                                <form action="{{ route('campaigns.destroy', $campaign->id) }}" method="POST"
                                                                    class="ml-1 delete-form" id="delete-form-{{ $campaign->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn btn-sm btn-danger btn-icon confirm-delete"
                                                                        data-id="{{ $campaign->id }}"
                                                                        data-name="{{ $campaign->name }}"
                                                                        data-toggle="tooltip" title="Hapus Campaign">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @else
                                                            <span class="text-muted"><i class="fas fa-lock"></i> Tidak ada akses</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        <i class="fas fa-tag fa-2x mb-2 d-block" style="opacity: 0.3;"></i>
                                                        Belum ada data campaign.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $campaigns->withQueryString()->links() }}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.confirm-delete').click(function(e) {
                e.preventDefault();
                let formId = $(this).closest('form').attr('id');
                let name = $(this).data('name');

                Swal.fire({
                    title: 'Yakin mau dihapus?',
                    text: "Campaign '" + name + "' akan dihapus permanen dan tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    buttonsStyling: false,
                    width: '400px',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-light mr-3'
                    },
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#' + formId).submit();
                    }
                });
            });
        });
    </script>
@endpush
