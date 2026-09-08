<p align="center">
  <img src="public/img/sajipos-logo.jpg" width="150" alt="SajiPOS Logo" style="border-radius: 20px;">
</p>

<h1 align="center">SajiPOS Backend API & Admin Dashboard</h1>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Firebase_FCM-FFCA28?style=for-the-badge&logo=firebase&logoColor=black" alt="Firebase FCM">
  <img src="https://img.shields.io/badge/Flutter_API-02569B?style=for-the-badge&logo=flutter&logoColor=white" alt="Flutter API">
  <img src="https://img.shields.io/badge/Status-Active_Development-brightgreen?style=for-the-badge" alt="Status">
</p>

<p align="center">
  Aplikasi backend komprehensif untuk sistem <strong>Point of Sale (POS) Restoran</strong>. Dibangun menggunakan <strong>Laravel</strong>, menyediakan RESTful API untuk aplikasi kasir (Flutter/Mobile) dan Dashboard Admin yang dilengkapi dengan fitur Real-Time Smart Polling, OTP Email Verification, serta integrasi Payment Gateway Xendit.
</p>

---

## ✨ Fitur Unggulan

### 🔒 1. Sistem Autentikasi & Keamanan Canggih
- **Laravel Sanctum Token:** Sistem login API yang aman untuk frontend kasir.
- **OTP Email Verification:** Mengirim kode 6 digit warna-warni secara otomatis ke email pengguna (menggunakan konfigurasi SMTP kustom).
- **Two-Tier Approval System:** Akun yang mendaftar harus melakukan verifikasi email terlebih dahulu, baru kemudian disetujui (Approve) secara manual oleh Admin.
- **Password & Username Generator:** Endpoint API khusus (`GET /api/suggestions/credentials`) untuk men-generate username unik dan passphrase yang aman.
- **Strict Password Validation:** Wajib menggunakan kombinasi huruf besar, kecil, dan angka tanpa menyulitkan dengan simbol.

### 💻 2. Real-Time Admin Dashboard (Stisla UI)
- **Smart Polling Architecture:** Admin dashboard akan memonitor pendaftar baru secara real-time setiap 5 detik melalui request AJAX/PJAX yang sangat ringan.
- **Audio Notification:** Suara notifikasi "Ting" kasir akan otomatis berbunyi ketika ada pendaftar baru atau saat kasir berhasil memverifikasi email.
- **Browser Tab Badge:** Notifikasi ala Facebook di tab browser (misal: `(2) Approval Users — SajiPOS`) jika ada kasir yang menunggu persetujuan.
- **Seamless DOM Replacement:** Tabel akan ter-update seketika (menambahkan baris baru, animasi *glow*, dan membuka kunci tombol aksi) tanpa perlu *refresh* halaman (Zero Flicker).

### 💳 3. Integrasi Payment Gateway (Xendit)
- Pembuatan *Invoice* otomatis dari pesanan pelanggan.
- **Xendit Webhook (`/api/xendit/webhook`):** Sinkronisasi status pembayaran secara real-time dari server Xendit ke sistem SajiPOS tanpa perlu dicek manual.

### 📣 4. Integrasi Push Notification (Firebase FCM)
- **Promo Broadcast:** Sistem akan mengirimkan *Push Notification* secara massal ke semua device kasir/pelanggan (melalui langganan topik `promo_broadcast`) setiap kali Admin membuat Campaign promo baru.
- **Payload Dinamis:** Notifikasi membawa data *payload* khusus yang memungkinkan aplikasi mobile untuk langsung mengarahkan user ke halaman detail promo (*Deep Linking*).

### 📦 5. Manajemen Master Data & Order
- **Produk & Kategori:** CRUD lengkap melalui panel admin, serta API terpisah untuk ditampilkan di kasir.
- **Diskon & Campaign:** Sistem voucher, potongan harga, dan campaign promosi aktif.
- **Kalkulasi Pesanan:** Endpoint pintar untuk menghitung Subtotal, Pajak, Diskon, dan Total Harga sebelum checkout.

### 📊 6. Analitik & Laporan
- **Menu Terlaris:** Menampilkan produk paling laku (Harian / Sepanjang Waktu) melalui API.
- **Laporan Transaksi:** Laporan penjualan lengkap berdasarkan filter waktu.

---

## 🛠️ Tech Stack
- **Framework:** Laravel 11.x / 10.x
- **Authentication:** Laravel Sanctum
- **Database:** MySQL
- **Template Admin:** [Stisla](https://getstisla.com/)
- **Mailing:** Custom SMTP (Gmail / Mailtrap)
- **Push Notification:** Firebase Cloud Messaging (FCM)
- **Payment Gateway:** Xendit PHP SDK

---

## 🚀 Cara Instalasi (Development)

1. **Clone Repository**
   ```bash
   git clone https://github.com/CristLoha/SajiPos-admin-api.git
   cd SajiPos-admin-api
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Setup Environment**
   Salin file konfigurasi *environment*:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan atur konfigurasi database, SMTP Email, dan Xendit:
   ```env
   APP_NAME="Tim SajiPOS"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sajipos_db
   DB_USERNAME=root
   DB_PASSWORD=

   # SMTP Setup (Untuk Kirim OTP)
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=465
   MAIL_USERNAME=email.anda@gmail.com
   MAIL_PASSWORD="sandi_aplikasi_google_16_huruf"
   MAIL_ENCRYPTION=smtps
   MAIL_FROM_ADDRESS="email.anda@gmail.com"
   MAIL_FROM_NAME="${APP_NAME}"

   # Xendit Keys
   XENDIT_SECRET_KEY=xnd_development_...
   ```

4. **Generate App Key**
   ```bash
   php artisan key:generate
   ```

5. **Migrate Database & Seeder**
   ```bash
   php artisan migrate --seed
   ```

6. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   > Dashboard Admin dapat diakses di: `http://localhost:8000/login`

---

## 📡 Dokumentasi Endpoint API Penting

Silakan lihat dokumentasi lengkap di file `API_DOCUMENTATION.md` untuk detail lengkap *request body* dan *response*.
Beberapa endpoint utama meliputi:

### Authentication & Akun
- `POST /api/register` : Mendaftar akun baru dan otomatis mengirim OTP ke email.
- `POST /api/verify-email` : Memasukkan kode OTP 6 digit.
- `POST /api/resend-otp` : Mengirim ulang kode verifikasi.
- `GET /api/suggestions/credentials` : Meminta saran kombinasi Username dan Password aman.
- `POST /api/login` : Mendapatkan Sanctum Bearer Token.

### Transaksi & Pesanan
- `POST /api/orders/hitung-total` : Menghitung harga keranjang belanja.
- `POST /api/orders` : Menyimpan pesanan dan membuat koneksi ke Xendit.
- `GET /api/orders/{id}/check-status` : Mengecek status pembayaran pesanan.
- `POST /api/xendit/webhook` : Endpoint yang menerima ping (update) dari Xendit.

### Master Data
- `GET /api/categories` : List Kategori.
- `GET /api/products` : List Produk.
- `GET /api/discounts/available` : List Diskon aktif.
- `GET /api/menu-terlaris` : Top Menu Analytics.

---
> Dikembangkan sebagai Portofolio Sistem Informasi Restoran Profesional.  
> **© 2026 Tim SajiPOS.**
