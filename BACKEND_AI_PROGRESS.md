# Backend AI Progress Log

## 📅 07 September 2026

### 🐛 Bug Fixes
- **[Penting untuk Next Step]**: Rencana penambahan kolom `bank_code` (dan `va_number` jika belum ada) di tabel `orders` MySQL di server live supaya API Create Order tidak lagi merespons Error 500 (SQL Column not found) saat memproses transaksi Transfer/VA.
- Memperbaiki parsing data JSON di response `payment_details` dari API pesanan. Nilai atribut yang sebelumnya berisi `""` (string kosong) untuk `expires_at` dan `qr_image_url` sudah dikembalikan menjadi `null` agar tidak memicu `FormatException` saat proses `DateTime.parse()` di aplikasi Flutter.

### ✨ Fitur Baru
- Penambahan sistem Pagination menggunakan method Laravel `$query->paginate` di endpoint `OrderController@index`. Response API kini mengembalikan array `data` yang di-limit, beserta object `meta` yang berisi flag `has_more` untuk mempermudah implementasi Infinite Scroll di aplikasi Flutter.
- Penambahan filter parameter `start_date` dan `end_date` pada query pencarian riwayat pesanan (API list pesanan).

