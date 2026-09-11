# 🚀 Catatan Progres & Aturan AI (SajiPOS Backend)

*Dokumen ini dibuat agar AI asisten tidak amnesia di sesi berikutnya. Berisi aturan ketat, rangkuman seluruh fitur, perubahan struktur, dan logika bisnis yang sudah diimplementasikan ke dalam SajiPOS.*

---

## 🤖 ATURAN WAJIB (GLOBAL RULES) UNTUK AI BACKEND
*Peringatan: Aturan ini bersifat permanen sebagai ingatan utama (Core Memory) dan WAJIB dipatuhi oleh AI pada setiap sesi pengembangan Laravel.*

1. **Efisiensi & Anti-Crash (Shared Hosting Mindset)**
   - Sistem ini di-deploy di **Shared Hosting (Domcloud)**. Selalu pikirkan efisiensi CPU dan RAM.
   - Jangan membuat *query* N+1. Selalu gunakan Eager Loading (`with()`).
   - Wajib gunakan `Cache::remember` untuk query atau *polling* yang sering dipanggil (seperti notifikasi user baru) untuk mencegah Error 508 Resource Limit.
2. **Standardisasi API Response & Error Handling**
   - Setiap API Endpoint WAJIB me-return JSON dengan format seragam: `success` (boolean), `message` (string), dan `data` (jika ada).
   - Jangan pernah me-return HTML Stack Trace. Gunakan format JSON untuk semua response error (401, 403, 404, 422, 500).
   - Status 401 khusus untuk Token Invalid/Expired. Status 403 untuk akses dilarang (contoh: akun pending/ditolak).
3. **Keamanan & Autentikasi**
   - Pertahankan fitur **Single Active Session** (`$user->tokens()->delete()` saat login).
   - Jangan percayai input dari Frontend. Lakukan validasi ketat di Laravel Request/Controller.
4. **Dokumentasi Real-Time**
   - Setiap selesai membuat endpoint baru atau merombak *database*, AI **WAJIB** memperbarui file `resources/docs/api.md` agar tim Frontend/Flutter tidak kebingungan.
5. **Gaya Komunikasi**
   - Gunakan gaya bahasa super santai, *to the point*, dan asik (sebut user "bor", gunakan "wkwk"). Fokus pada eksekusi cepat dan *problem solving*.
6. **Wajib Git Push (Auto-Deploy)**
   - Mengingat website sudah di-hosting, setiap kali selesai implementasi satu fitur atau perbaikan bug, **WAJIB** langsung di-push ke repository biar langsung sinkron dan live di server!

---

## 📅 Log Pembaruan: 10 September 2026 (Sesi Perombakan Keamanan & Fitur Baru)

### 1. 🕒 Tracking Waktu Aktivitas (Login & Logout)
- **Database Update:** Menambahkan kolom `last_login_at` dan `last_logout_at` di tabel `users`.
- **API Update:** Mengubah logika di `AuthController@login` dan `logout` agar selalu mencatat waktu secara *realtime* pakai fungsi `now()`.
- **UI Web Admin:** Mengubah kolom tabel *Dashboard* dari "Dibuat" menjadi **"Aktivitas"**, yang kini menampilkan tanggal daftar, jam terakhir *Login* (hijau), dan jam terakhir *Logout* (merah).

### 2. 🪲 Bug Fix UI Web Admin
- **Modal Action Fix:** Memperbaiki bug di mana tombol *Approve/Reject* tidak bisa di-klik di tab "Semua". Masalahnya karena tag HTML penutup *modal* ketinggalan di luar `div` container AJAX Polling. Strukturnya sudah dibenahi.
- **Dynamic Dashboard Status:** Memperbaiki bug visual di mana status pesanan di halaman utama Admin selalu menampilkan teks "Selesai" (hardcode). Sekarang sudah *dynamic* mengikuti status asli dari database (Sukses, Tertunda, Batal).

### 3. 🛡️ Security Fix: Pembuatan Pesanan (Anti-Spoofing ID Kasir)
- **API Endpoint:** `POST /api/orders` (di `OrderController@store`).
- **Fix:** Menghapus parameter `cashier_id` dari validasi. Backend sekarang BODO AMAT dengan input JSON dari Flutter, dan secara paksa mengambil identitas kasir dari Token Bearer (`$request->user()->id`). Ini mencegah admin/kasir lain sengaja menyamar sebagai kasir lain saat transaksi.

### 4. 📊 Fitur Baru: API Ringkasan Tutup Shift (Laporan Kasir)
- **API Endpoint Baru:** Dibuatkan `GET /api/reports/summary`.
- **Fungsi:** Menyediakan JSON matang tanpa perlu hitung manual di Flutter. Berisi: Total Omzet, Total Transaksi, Total Pajak, Total Diskon, Rincian Pembayaran (Jumlah dan Total Uang Cash, QRIS, Transfer), Menu Terlaris (Top 3), dan Grafik Penjualan (Per Jam).
- **Akses Role:** Memindahkan *middleware* *route* laporan dari yang tadinya eksklusif untuk Admin/Staff, menjadi bisa diakses oleh Kasir (User). Kasir hanya akan melihat omzet/laporannya sendiri.

### 5. ⚡ Performa & Optimasi (Sesi Pagi/Siang)
- **Optimasi Server Shared Hosting (Cache):** Menerapkan `Cache::remember('polling_pending_users', 5)` di `UserController@polling` untuk menghindari DB Crash (Error 508) saat banyak tab admin terbuka dan melakukan AJAX Polling secara bersamaan.
- **Cache Invalidation:** Menambahkan `Cache::forget('polling_pending_users')` di `UserController@approve`, `UserController@reject`, dan `AuthController@verifyEmail` agar UI Admin Web tetap *realtime* tanpa perlu menunggu cache kedaluwarsa jika ada aksi.
- **Troubleshooting Frontend:** Menemukan bahwa jika suara atau *request* terjadi secara bar-bar/realtime terus-menerus, itu disebabkan oleh implementasi *looping polling* yang terlalu cepat di sisi Frontend (Flutter atau Admin Web tab yang terbuka banyak), bukan karena ada fitur *Push Notification realtime* di backend saat pesanan masuk.

---

## 📅 Log Pembaruan: 8 September 2026 (Sesi Malam - The Final Polish)

### 1. 🔐 Penyempurnaan Alur Register (Jalan Ninja) & Anti-Spam
- **Hide Unverified Accounts:** Admin Dashboard tidak lagi diganggu (tidak ada notif/bunyi) oleh email bodong. User hanya akan memicu notif "Ting!" **setelah** mereka berhasil memverifikasi OTP (`whereNotNull('email_verified_at')`).
- **Smart Register (Jalan Ninja):** Mengatasi isu "Email/Username sudah dipakai" pada kasir yang gagal OTP. Jika user mendaftar dengan email yang *belum terverifikasi*, sistem akan menimpa (overwrite) data lamanya, mereset statusnya ke `pending_approval`, mengirim OTP baru, dan mengembalikan status sukses (201).
- **Check-Email Realtime UI:** Endpoint `POST /api/check-email` diperbarui agar mengembalikan `is_valid: true` untuk email yang belum diverifikasi, sehingga UI Flutter tidak terkunci.
- **Bug Fix Model:** Menambahkan `email_verified_at` ke dalam array `#[Fillable]` pada `User.php` (sebelumnya gagal update ke database).
- **Format Error Standard:** Menyamakan format response 422 (jika email bounce/gagal kirim) dengan standar validasi Laravel (menyertakan array `errors`) agar mudah di-parse oleh Flutter.

### 2. 🛡️ Keamanan: Anti Multi-Login (Single Active Session)
- **Token Revocation (Sistem Tendang):** Diimplementasikan di `POST /api/login`. Jika kasir login di HP baru, semua token lama akan dihapus otomatis (HP lama mendapat 401 Unauthorized). Jauh lebih user-friendly dibanding device locking yang kaku.
- **Device Tracking:** Menambahkan kolom `device_id` di database `users` untuk pencatatan keamanan operasional.
- **Reset Device Button:** Menambahkan tombol "Reset Device (Ikon HP)" warna kuning di halaman Web Admin `users.index` jika sewaktu-waktu admin butuh me-reset pencatatan device kasir.

### 3. 👥 Multi-Tenant / Role-Based Isolation untuk Riwayat Transaksi
- **Order History Isolation:** Mengubah logika `GET /api/orders` di `OrderController`. 
  - Jika yang login adalah `user` (Kasir), API akan mengunci data agar kasir hanya bisa menarik/melihat riwayat transaksinya sendiri (mencegah saling intip omzet).
  - Jika yang login adalah `admin` atau `staff`, akses terbuka untuk melihat semua riwayat.
- Setiap objek order kini mengirimkan data `cashier` sehingga aplikasi/web bisa menampilkan siapa yang memproses transaksi tersebut.

### 4. 🎛️ Penyesuaian Server Shared Hosting (Domcloud)
- **Interval Polling Aman:** Menurunkan dan menaikkan interval AJAX polling di `app.blade.php`. Saat ini dipatok di **10 detik** (`10000ms`) sebagai jalan tengah antara UX yang real-time dan mencegah terjadinya DDoS / `ERR_TIMED_OUT` / Server 508 Resource Limit di shared hosting.

### 5. 📚 Dokumentasi API
- Mengupdate `resources/docs/api.md` untuk merangkum semua endpoint yang baru (Check Email), merapikan format 422 Register, menambahkan info *Single Active Session* pada Login, dan informasi *Role-Based Access* pada endpoint Riwayat Transaksi.

---

## 📅 Log Pembaruan Sebelumnya (Sesi Sore)
*(Ringkasan Cepat)*
- **OTP System:** Pengiriman 6-digit OTP berwarna-warni.
- **Suggest Credentials:** API `GET /api/suggestions/credentials` tanpa simbol.
- **Global Smart Polling:** Tab badge & sound "Ting!" yang jalan di seluruh halaman layout admin.
- **README & Portofolio:** Markdown dirombak rapi dengan badges Shields.io. File dokumen API di-move ke `resources/docs/api.md`.

---

## 🛠️ Catatan Penting untuk Sesi Berikutnya
- **Server State:** Server SGP Domcloud sempat sering down (Time Out) karena limit resource shared hosting atau IP terkena Fail2Ban akibat spam request/polling terlalu kencang. Jangan atur polling di bawah 10 detik.
- **Flutter Requirements:** 
  1. Wajib ada **Global Interceptor** untuk nge-handle HTTP 401 dan menendang user ke Halaman Login (efek Single Active Session).
  2. Saat tekan verifikasi OTP, wajib kirim `email` dan `otp_code` berbarengan.
- **Tone:** Komunikasi user super santai (banyak ngakak "wkwkwk", panggil "bor", "jir"). Gas aja eksekusi cepat!

### 6. 🚨 Peringatan Stok & Manajemen Inventaris
- **API Update:** Modifikasi `OrderController@store` untuk memantau sisa stok otomatis setiap kali pesanan masuk.
- **FCM Push Notif:** Menambahkan Job baru `StockLowAlertJob` yang mengirim Push Notification ke topik `stock_alerts` ketika stok drop menjadi <= 5 (Peringatan Menipis) dan ketika stok mencapai 0 (Habis/Sold Out) agar kasir tidak panik kecolongan barang kosong.

### 7. 🎨 Revamp Template Laporan (Export PDF & Excel)
- **Bug Fix:** Memperbaiki nomor struk yang sebelumnya kosong, sekarang tersinkronisasi menggunakan format `ORD-0000` dari ID.
- **Styling UI:** Menambahkan branding SajiPOS, styling header tabel, striping zebra selang-seling, dan highlight tegas untuk baris Total Pendapatan. Nilai `Rp 0` kini dibuat redup (abu-abu).
- **Footer:** Menambahkan *timestamp* kapan dokumen di-*generate* beserta nama admin pencetak.

### 8. 📊 Fix Lebar Kolom Excel
- **Styling Update:** Mengimplementasikan `WithEvents` di `OrderExport.php` untuk me-override auto-size dengan lebar kolom manual yang lebih lebar, sehingga data tanggal, no struk, dan nilai nominal tidak bertumpuk/terpotong di file XLSX.

- **UI Tweaks:** Memperbaiki layout pencarian di menu Diskon yang sebelumnya berjejalan, sekarang lebih rapi, lega, dan intuitif menggunakan form-inline.

- **UI Tweaks:** Menghapus label teks pada tombol aksi (Edit/Hapus) di halaman Produk agar UI lebih ringkas (icon only) dan seragam dengan halaman lain.

- **UI Tweaks:** Mengganti label menu 'Toko' di sidebar menjadi 'Pengaturan'.

### 9. 🔐 UX Keamanan Akses
- **Refactor:** Memindahkan fitur 'Reset Device' dari halaman Users (karena terlalu teknis) ke dalam halaman Pengaturan (Sistem). Menambahkan dropdown *select user* untuk memilih kasir yang akan di-reset.

- **UI Tweaks:** Menghapus info ID Perangkat teknis dari tabel Users, dan memindahkannya ke dropdown opsi 'Reset Device' di halaman Pengaturan.

- **UI Keamanan:** Memperbaiki logika UI (`@if`) di halaman Kategori, Diskon, dan Campaign. Kasir (User/Staff) sekarang benar-benar tidak bisa melihat tombol Tambah, Edit, atau Hapus (Sebelumnya tombol tetap muncul walaupun aksi di-*block* oleh backend).


- **Fitur Baru (Keamanan):** Mengaktifkan Single Active Session untuk Web Dashboard. Jika admin login di perangkat baru, sesi di perangkat lama akan otomatis ter-logout menggunakan `AuthenticateSession` dan `Auth::logoutOtherDevices`.

- **Dokumentasi API:** Memperbarui `resources/docs/api.md` dengan menambahkan dokumentasi fitur *Peringatan Stok & Manajemen Inventaris* (termasuk instruksi subscribe FCM Topic `stock_alerts` dan payload JSON-nya) untuk tim Frontend Flutter.

- **Fitur Baru (Katalog Sinkronisasi Otomatis):** Menambahkan `CatalogUpdateAlertJob` yang mengirim FCM ke topik `catalog_updates` setiap kali Admin menambah, mengubah, atau menghapus Produk/Kategori. Dokumentasi FCM juga sudah diupdate.

- **Perbaikan Bug (Login):** Menyesuaikan pesan error di Web Dashboard (Fortify) agar menampilkan status 'Akun menunggu persetujuan' jika akun belum di-approve, bukan 'Username/Password salah'.

- **Perbaikan UI (Modal):** Mengatasi *bug* layar hitam (z-index backdrop) saat membuka Modal Setujui/Tolak User di halaman Kasir dengan memindahkan elemen modal ke `body` via JavaScript.
