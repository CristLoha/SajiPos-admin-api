@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Riwayat Pesanan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pesanan</a></div>
                    <div class="breadcrumb-item">Riwayat</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Riwayat Transaksi</h2>
                <p class="section-lead">
                    Pantau semua transaksi pesanan kasir, total pendapatan, pajak, dan detail item di sini.
                </p>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Semua Pesanan</h4>
                            </div>
                            <div class="card-body">
                                <style>
                                    /* Sticky column untuk Aksi */
                                    .sticky-action {
                                        position: sticky;
                                        right: 0;
                                        background-color: #fff;
                                        z-index: 1;
                                        box-shadow: -2px 0 5px rgba(0,0,0,0.05);
                                    }
                                </style>
                                <div class="d-flex align-items-center justify-content-end mb-3" style="gap: 24px;">
                                    <form method="GET" action="{{ route('orders.index') }}" class="m-0">
                                        <div class="input-group">
                                            <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary"><i class="fas fa-filter mr-1"></i>Filter Tanggal</button>
                                                @if(request('date'))
                                                    <a href="{{ route('orders.index') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>

                                    @if (in_array(auth()->user()->roles, ['admin', 'staff', 'user']) && $orders->total() > 0)
                                        <button type="button" class="btn btn-outline-danger h-100" id="btnToggleMode">
                                            <i class="fas fa-trash-alt mr-1"></i> <span>Kelola Hapus</span>
                                        </button>

                                        <form action="{{ route('orders.bulkDelete') }}" method="POST" id="btnBulkDelete" class="d-none m-0">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="order_ids" id="order-ids-input">
                                            <button type="button" class="btn btn-danger h-100" onclick="confirmBulkDelete()">
                                                <i class="fas fa-trash-alt mr-1"></i> <span id="bulk-delete-text">Hapus (0) Pesanan</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="clearfix mb-3"></div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="col-checkbox d-none text-center px-3" style="width: 5%;">
                                                    <input type="checkbox" id="selectAllCheckbox">
                                                </th>
                                                <th class="text-center" style="width: 5%;">No</th>
                                                <th style="min-width: 130px;">No Order</th>
                                                <th style="min-width: 160px;">Waktu Transaksi</th>
                                                <th style="min-width: 150px;">Kasir</th>
                                                <th style="min-width: 120px;">Subtotal</th>
                                                <th style="min-width: 120px;">Diskon</th>
                                                <th style="min-width: 120px;">Ongkir</th>
                                                <th style="min-width: 120px;">Svc Charge</th>
                                                <th style="min-width: 120px;">Pajak</th>
                                                <th style="min-width: 140px;">Total Akhir</th>
                                                <th class="text-center" style="min-width: 130px;">Pembayaran</th>
                                                <th class="text-center" style="min-width: 110px;">Status</th>
                                                <th class="text-center sticky-action" style="min-width: 120px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($orders as $order)
                                                <tr>
                                                    <td class="col-checkbox d-none text-center px-3">
                                                        <input type="checkbox" class="order-checkbox" value="{{ $order->id }}">
                                                    </td>
                                                    <td class="text-center">{{ $orders->firstItem() + $loop->index }}</td>
                                                    <td><strong>#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($order->transaction_time)->format('d M Y H:i') }}</td>
                                                    <td>{{ $order->cashier->name ?? '-' }}</td>
                                                    <td>Rp {{ number_format($order->sub_total, 0, ',', '.') }}</td>
                                                    <td class="{{ $order->discount_amount > 0 ? 'text-danger' : 'text-muted' }}">
                                                        {{ $order->discount_amount > 0 ? '-Rp ' : 'Rp ' }}{{ number_format($order->discount_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($order->service_charge, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($order->tax, 0, ',', '.') }}</td>
                                                    <td><strong class="text-success">Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
                                                    <td class="text-center">
                                                        <span class="badge badge-light" style="border: 1px solid var(--sp-border); text-transform: uppercase;">
                                                            {{ $order->payment_method }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if (strtolower($order->status) == 'success')
                                                            <span class="badge badge-success">Sukses</span>
                                                        @elseif (strtolower($order->status) == 'pending')
                                                            <span class="badge badge-warning">Tertunda</span>
                                                        @else
                                                            <span class="badge badge-danger">Batal</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center sticky-action">
                                                        <a href="{{ route('orders.show', $order->id) }}"
                                                            class="btn btn-sm btn-primary"
                                                            data-toggle="tooltip" title="Lihat Detail Pesanan">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="14" class="text-center text-muted py-4">
                                                        <i class="fas fa-history fa-2x mb-2 d-block" style="opacity: 0.3;"></i>
                                                        Tidak ditemukan data transaksi.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right mt-4">
                                    {{ $orders->withQueryString()->links() }}
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkAll = document.getElementById('selectAllCheckbox');
        const checkboxes = document.querySelectorAll('.order-checkbox');
        const checkboxColumns = document.querySelectorAll('.col-checkbox');
        const bulkDeleteForm = document.getElementById('btnBulkDelete');
        const bulkDeleteText = document.getElementById('bulk-delete-text');
        const orderIdsInput = document.getElementById('order-ids-input');
        const toggleSelectModeBtn = document.getElementById('btnToggleMode');

        // State global untuk menampung ID pesanan
        let selectedIds = JSON.parse(localStorage.getItem('selected_orders')) || [];
        let isSelectMode = false;

        // Auto-aktifkan Mode Pilih jika ada data di localStorage saat halaman dimuat
        if (selectedIds.length > 0) {
            isSelectMode = true;
            enterSelectModeUI();
        }

        function saveToLocalStorage() {
            if (selectedIds.length > 0) {
                localStorage.setItem('selected_orders', JSON.stringify(selectedIds));
            } else {
                localStorage.removeItem('selected_orders');
            }
        }

        function enterSelectModeUI() {
            if (toggleSelectModeBtn) {
                toggleSelectModeBtn.innerHTML = '<i class="fas fa-times mr-1"></i> Batal';
                toggleSelectModeBtn.classList.remove('btn-outline-danger');
                toggleSelectModeBtn.classList.add('btn-secondary');
            }
            
            checkboxColumns.forEach(col => col.classList.remove('d-none'));

            // Centang ulang berdasarkan state dari localStorage
            checkboxes.forEach(cb => {
                if (selectedIds.includes(cb.value)) {
                    cb.checked = true;
                }
            });

            checkCheckAllState();
            updateBulkDeleteUI();
        }

        function exitSelectModeUI() {
            if (toggleSelectModeBtn) {
                toggleSelectModeBtn.innerHTML = '<i class="fas fa-trash-alt mr-1"></i> <span>Kelola Hapus</span>';
                toggleSelectModeBtn.classList.remove('btn-secondary');
                toggleSelectModeBtn.classList.add('btn-outline-danger');
            }
            
            checkboxColumns.forEach(col => col.classList.add('d-none'));
            
            if (checkAll) checkAll.checked = false;
            checkboxes.forEach(cb => cb.checked = false);
            
            if (bulkDeleteForm) bulkDeleteForm.classList.add('d-none');
            
            // Bersihkan state & localStorage
            selectedIds = [];
            localStorage.removeItem('selected_orders');
        }

        if (toggleSelectModeBtn) {
            toggleSelectModeBtn.addEventListener('click', function() {
                isSelectMode = !isSelectMode;
                if (isSelectMode) {
                    enterSelectModeUI();
                } else {
                    exitSelectModeUI();
                }
            });
        }

        function updateBulkDeleteUI() {
            if (!bulkDeleteForm) return;

            if (isSelectMode && selectedIds.length > 0) {
                bulkDeleteForm.classList.remove('d-none');
                bulkDeleteText.innerText = `Hapus (${selectedIds.length}) Pesanan`;
                orderIdsInput.value = JSON.stringify(selectedIds);
            } else {
                bulkDeleteForm.classList.add('d-none');
                orderIdsInput.value = '';
            }
        }

        function checkCheckAllState() {
            const allChecked = document.querySelectorAll('.order-checkbox:checked').length === checkboxes.length && checkboxes.length > 0;
            if (checkAll) checkAll.checked = allChecked;
        }

        function toggleIdInSelection(id, isChecked) {
            if (isChecked) {
                if (!selectedIds.includes(id)) {
                    selectedIds.push(id);
                }
            } else {
                selectedIds = selectedIds.filter(itemId => itemId !== id);
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const isChecked = this.checked;
                checkboxes.forEach(cb => {
                    cb.checked = isChecked;
                    toggleIdInSelection(cb.value, isChecked);
                });
                saveToLocalStorage();
                updateBulkDeleteUI();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                toggleIdInSelection(this.value, this.checked);
                saveToLocalStorage();
                checkCheckAllState();
                updateBulkDeleteUI();
            });
        });

        // Diexpose ke window agar form onSubmit bisa mengaksesnya
        window.confirmBulkDelete = function() {
            if (confirm('Apakah Anda yakin ingin menghapus data pesanan yang dipilih? Stok produk akan dikembalikan.')) {
                // Hapus state agar tidak membekas setelah sukses hapus
                localStorage.removeItem('selected_orders');
                bulkDeleteForm.submit();
            }
        }
    });
</script>
@endpush
