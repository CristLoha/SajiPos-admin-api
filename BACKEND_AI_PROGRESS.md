# 🚀 Catatan Progres AI (SajiPOS Backend)

*Dokumen ini dibuat agar AI asisten tidak amnesia di sesi berikutnya. Berisi rangkuman seluruh fitur, perubahan struktur, dan logika bisnis yang sudah diimplementasikan ke dalam SajiPOS.*

---

## 📅 Log Pembaruan: 8 September 2026

### 1. 🔐 Sistem Autentikasi & Keamanan (OTP & Credentials)
- **OTP Verifikasi Email:** Menambahkan sistem pengiriman kode OTP 6-digit (warna-warni) ke email pendaftar.
- **Modifikasi Database:** Menambahkan kolom `otp_code` dan `otp_expires_at` pada tabel `users`.
- **Saran Kredensial:** Membuat endpoint API untuk generate saran `username` unik dan `password` aman (minimal 8 karakter, kombinasi huruf besar, kecil, angka, **tanpa simbol**).
- **Two-Tier Approval:** Akun baru default role-nya `user` (kasir) dan statusnya `pending_approval`. Kasir harus verifikasi email dulu (OTP), baru Admin bisa meng-Approve.

### 2. 💻 Real-Time Admin Dashboard (Smart Polling & PJAX)
- **Global Polling (Setiap 5 detik):** Mengimplementasikan AJAX Smart Polling yang jalan di **seluruh halaman admin** (di-inject via `layouts/app.blade.php`).
- **Zero-Flicker Table Update:** Di halaman Users, jika ada pendaftar baru, tabel otomatis memperbarui dirinya menggunakan teknik PJAX (fetch HTML baru dan me-replace DOM) tanpa refresh halaman utuh.
- **Notifikasi Audio & Visual:** 
  - Suara "Ting!" (volume 30%) akan berbunyi tiap ada kasir baru atau kasir yang berhasil verifikasi email.
  - Tab browser akan menampilkan notifikasi ala Facebook, misal: `(2) Users — SajiPOS`.
  - Badge kuning di menu Sidebar (kiri) otomatis ter-update jumlahnya.
- **Proteksi Tombol Approve:** Tombol "Approve" (hijau) akan berubah jadi abu-abu jika user belum verifikasi email. Jika diklik, akan muncul **SweetAlert2** ("Belum Verifikasi OTP!").

### 3. 🎨 Perbaikan UI & Dokumentasi Portofolio
- **Logo Baru:** Mengganti logo default menjadi logo khusus buatan AI (SajiPOS, kotak biru rounded dengan sendok garpu) di `public/img/sajipos-logo.jpg`.
- **README.md (Portofolio Mode):** Merombak total `README.md` dengan Badges (Laravel, Flutter, Firebase, dll), penjelasan fitur unggulan (Xendit, OTP, Polling, FCM), dan tata cara setup `.env` yang sangat rapi.
- **API Documentation:** 
  - Mengupdate endpoints Auth di dokumen API.
  - Memindahkan file dari root (`API_DOCUMENTATION.md`) ke dalam `resources/docs/api.md` agar halaman GitHub utama bersih.
  - Mengubah rute view web `/docs` (`api.blade.php`) agar tetap bisa membaca markdown tersebut menggunakan Zero-MD.

---

## 📡 Rangkuman API Endpoints Baru
- `POST /api/register` : Registrasi + Kirim Email OTP.
- `POST /api/verify-email` : Validasi 6 digit OTP.
- `POST /api/resend-otp` : Kirim ulang OTP.
- `GET /api/suggestions/credentials` : Generate username dan password otomatis.
- `GET /users/polling` : (Web Route) Endpoint ringan mengembalikan JSON user pending_approval untuk kebutuhan dashboard.

---

## 🛠️ Catatan Penting untuk Sesi Berikutnya
- **Payment Gateway:** Integrasi Xendit Webhook sudah ada (`/api/xendit/webhook`), pastikan mengetesnya dengan ngrok jika mencoba dari lokal.
- **Push Notification:** Fitur broadcast promo menggunakan Firebase Cloud Messaging (FCM) via *Topics* (`promo_broadcast`).
- **Pesan dari Sesi Ini:** User menyukai komunikasi casual ("bor", "jir") dan eksekusi yang super cepat. Tidak suka simbol pada password. Hindari penggunaan `.gitignore` untuk file yang masih di-render di production (kayak API docs).
