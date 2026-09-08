# 🚀 Catatan Progres AI (SajiPOS Backend)

*Dokumen ini dibuat agar AI asisten tidak amnesia di sesi berikutnya. Berisi rangkuman seluruh fitur, perubahan struktur, dan logika bisnis yang sudah diimplementasikan ke dalam SajiPOS.*

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
