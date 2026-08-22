# 📖 Dokumentasi API - SajiPOS Backend

Dokumentasi ini dibuat untuk mempermudah integrasi API antara Backend Laravel dan Frontend Client (Aplikasi Kasir Flutter).

---

## 📌 Base URL

```text
https://sajipos.domcloud.dev/api
```

---

## 🔐 0. API Autentikasi (Auth)

Mengamankan sesi kasir dan memvalidasi akses aplikasi Flutter.

### A. POST Login Kasir

Melakukan login dengan menggunakan email atau username, mengembalikan token Sanctum untuk otentikasi API selanjutnya.

-   **URL:** `/login`
-   **Method:** `POST`
-   **Headers:** `Content-Type: application/json`
-   **Auth Required:** No

#### 📤 Request Body (JSON)

```json
{
    "email": "admin@sajipos.com",
    "password": "password123"
}
```

> _Catatan: Anda juga bisa mengirimkan `"username"` pada field `"email"` (contoh: `"email": "admin"`)._

#### 📥 Response (200 OK)

```json
{
    "success": true,
    "message": "Login berhasil!",
    "token": "1|laravel_sanctum_token_string_here...",
    "user": {
        "id": 1,
        "name": "Admin SajiPOS",
        "email": "admin@sajipos.com",
        "username": "admin",
        "role": "admin"
    }
}
```

### B. POST Logout Kasir

Menghapus token akses API saat ini (mengakhiri sesi).

-   **URL:** `/logout`
-   **Method:** `POST`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📥 Response (200 OK)

```json
{
    "success": true,
    "message": "Logout berhasil!"
}
```

---

## 1. 🍔 API Kategori (Categories)

Mengambil seluruh daftar kategori produk makanan & minuman yang terdaftar di sistem.

-   **URL:** `/categories`
-   **Method:** `GET`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)
-   **Query Parameters:**

| Parameter | Tipe | Wajib | Deskripsi |
| :--- | :--- | :--- | :--- |
| `name` | `string` | ❌ Tidak | Pencarian spesifik berdasarkan nama kategori. |

### 📥 Response (200 OK)

```json
{
    "success": true,
    "message": "List Data Kategori",
    "data": [
        {
            "id": 1,
            "name": "Makanan Utama",
            "description": "Nasi, Mie, Ayam, dll.",
            "created_at": "2026-06-07T12:00:00.000000Z",
            "updated_at": "2026-06-07T12:00:00.000000Z"
        },
        {
            "id": 2,
            "name": "Minuman Dingin",
            "description": "Aneka es segar dan jus.",
            "created_at": "2026-06-07T12:00:00.000000Z",
            "updated_at": "2026-06-07T12:00:00.000000Z"
        }
    ]
}
```

---

## 2. 🍕 API Produk (Products)

Mengambil daftar produk makanan & minuman yang berstatus aktif (`status = 1` atau `true`) serta detail masing-masing produk.

> _Catatan: Kolom `status` otomatis terhitung secara dinamis dari sisa stok produk. Jika `stock > 0`, maka `status` otomatis bernilai `true` (1), sebaliknya jika `stock == 0` maka `status` otomatis bernilai `false` (0)._

### A. GET List & Search Produk Aktif

Mengambil seluruh daftar produk aktif. Mendukung filter pencarian menggunakan query parameter.

-   **URL:** `/products`
-   **Method:** `GET`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)
-   **Query Parameters:**

| Parameter | Tipe | Wajib | Deskripsi |
| :--- | :--- | :--- | :--- |
| `search` | `string` | ❌ Tidak | Pencarian berdasarkan nama atau deskripsi produk. |
| `name` | `string` | ❌ Tidak | Pencarian spesifik berdasarkan nama produk saja. |
| `category_id` | `integer` | ❌ Tidak | Filter produk berdasarkan ID kategori. |

#### 📥 Response (200 OK)

```json
{
    "success": true,
    "message": "List Data Produk Aktif",
    "data": [
        {
            "id": 1,
            "category_id": 1,
            "name": "Nasi Goreng Special 43",
            "description": "Nasi goreng pedas gurih dengan topping telur mata sapi.",
            "price": 25000,
            "discount_price": 20000,
            "stock": 80,
            "image": "products/nasgor_special.jpg",
            "status": 1,
            "created_at": "2026-06-07T12:20:00.000000Z",
            "updated_at": "2026-06-07T12:20:00.000000Z",
            "category": {
                "id": 1,
                "name": "Makanan Utama",
                "description": "Nasi, Mie, Ayam, dll.",
                "created_at": "2026-06-07T12:00:00.000000Z",
                "updated_at": "2026-06-07T12:00:00.000000Z"
            }
        }
    ]
}
```

### B. GET Detail Produk

Mengambil informasi detail dari satu produk berdasarkan ID produk.

-   **URL:** `/products/{id}`
-   **Method:** `GET`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📥 Response (200 OK)

```json
{
    "success": true,
    "message": "Detail Data Produk",
    "data": {
        "id": 1,
        "category_id": 1,
        "name": "Nasi Goreng Special 43",
        "description": "Nasi goreng pedas gurih dengan topping telur mata sapi.",
        "price": 25000,
        "discount_price": null,
        "stock": 80,
        "image": "products/nasgor_special.jpg",
        "status": 1,
        "created_at": "2026-06-07T12:20:00.000000Z",
        "updated_at": "2026-06-07T12:20:00.000000Z",
        "category": {
            "id": 1,
            "name": "Makanan Utama",
            "description": "Nasi, Mie, Ayam, dll.",
            "created_at": "2026-06-07T12:00:00.000000Z",
            "updated_at": "2026-06-07T12:00:00.000000Z"
        }
    }
}
```

#### 📥 Response Error (404 Not Found)

```json
{
    "success": false,
    "message": "Produk tidak ditemukan"
}
```

---

## 3. 🏷️ API Diskon (Discounts)

Mengelola data potongan harga/voucher promosi langsung dari Flutter.

### A. GET List Diskon

Mengambil daftar diskon dengan dukungan filter status dan pencarian. Secara default (jika tanpa parameter), akan mengambil semua diskon.

-   **URL:** `/discounts`
-   **Method:** `GET`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)
-   **Query Parameters:**
 
 | Parameter | Tipe | Wajib | Deskripsi |
 | :--- | :--- | :--- | :--- |
 | `status` | `string` | ❌ Tidak | Filter status: `active`, `upcoming`, atau `expired`. |
 | `search` | `string` | ❌ Tidak | Pencarian berdasarkan nama promo atau kode. |

> _Catatan: Field `status` pada response adalah hasil kalkulasi dinamis oleh sistem berdasarkan tanggal hari ini dibandingkan dengan `start_date` dan `expired_date`._

#### 📥 Response (200 OK)

```json
{
    "success": true,
    "message": "List Data Diskon",
    "data": [
        {
            "id": 1,
            "name": "Welcome WCB",
            "code": "WCB20",
            "description": "Member baru WCB",
            "type": "percentage",
            "value": "20.00",
            "max_discount": "20000.00",
            "min_transaction": "50000.00",
            "quota": 100,
            "start_date": "2026-06-01",
            "expired_date": "2026-12-31",
            "status": "active",
            "created_at": "2026-06-07T12:30:00.000000Z",
            "updated_at": "2026-06-07T12:30:00.000000Z"
        }
    ]
}
```

> ⚠️ **Penting untuk Frontend (Flutter):**
> 1. Jika keranjang belanja kurang dari `min_transaction`, diskon tidak boleh digunakan.
> 2. Jika tipe diskon adalah `percentage`, hasil potongan tidak boleh melebihi nilai `max_discount` (jika tidak null).
> 3. Field `data` akan mengembalikan list kosong `[]` (bukan null) jika tidak ada diskon yang aktif.

### B. POST Tambah Diskon Baru

Menambahkan diskon baru ke sistem. Terdapat validasi tambahan di mana `start_date` harus lebih awal atau sama dengan `expired_date`, serta `code` promo harus unik.

-   **URL:** `/discounts`
-   **Method:** `POST`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📤 Request Body (JSON)

```json
{
    "name": "Diskon Gajian",
    "code": "GAJIAN15",
    "description": "Promo gajian diskon flat",
    "type": "fixed",
    "value": 15000,
    "max_discount": null,
    "min_transaction": 50000,
    "quota": 100,
    "status": "active",
    "start_date": "2026-06-25",
    "expired_date": "2026-06-30"
}
```

> _Catatan: Nilai `status` saat ini di request hanya untuk mendefinisikan status dasar (misal `inactive` jika ingin dinonaktifkan manual), sedangkan status aktual di response GET akan terhitung otomatis (upcoming/active/expired). Jika tipe diskon `percentage` dan nilai > 50, maka `max_discount` wajib diisi._

#### 📥 Response (201 Created)

```json
{
    "success": true,
    "message": "Diskon Berhasil Ditambahkan!",
    "data": {
        "id": 2,
        "name": "Diskon Gajian",
        "code": "GAJIAN15",
        "description": "Promo gajian diskon flat",
        "type": "fixed",
        "value": 15000,
        "max_discount": null,
        "min_transaction": 50000,
        "quota": 100,
        "status": "active",
        "start_date": "2026-06-25",
        "expired_date": "2026-06-30",
        "created_at": "2026-06-08T07:12:00.000000Z",
        "updated_at": "2026-06-08T07:12:00.000000Z"
    }
}
```

---

## 4. 📝 API Transaksi & Checkout (Orders)

Menyimpan hasil transaksi belanja kasir dari Flutter ke database Backend Laravel serta melihat riwayatnya.

### A. POST Simpan Transaksi Baru

Menyimpan transaksi order beserta item produk yang dibeli.

-   **URL:** `/orders`
-   **Method:** `POST`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📤 Request Body (JSON)

```json
{
    "cashier_id": 2,
    "transaction_time": "2026-06-07 20:30:00",
    "sub_total": 60000,
    "discount_id": 1,
    "discount_amount": 10000,
    "shipping_cost": 0,
    "service_charge": 3000,
    "tax": 5300,
    "total": 58300,
    "payment_method": "qris",
    "order_items": [
        {
            "product_id": 1,
            "quantity": 2,
            "price": 20000,
            "note": "Pedas, jangan pakai bawang"
        },
        {
            "product_id": 2,
            "quantity": 1,
            "price": 20000,
            "note": "Es batu dipisah"
        }
    ]
}
```

#### 📥 Response (201 Created)

```json
{
    "success": true,
    "message": "Transaksi Berhasil Disimpan!",
    "data": {
        "id": 1,
        "cashier_id": 2,
        "transaction_time": "2026-06-07 20:30:00",
        "sub_total": 60000,
        "discount_id": 1,
        "discount_name": "Welcome WCB",
        "discount_amount": 10000,
        "shipping_cost": 0,
        "service_charge": 3000,
        "tax": 5300,
        "total": 58300,
        "payment_method": "qris",
        "created_at": "2026-06-07T13:47:37.000000Z",
        "updated_at": "2026-06-07T13:47:37.000000Z",
        "items": [
            {
                "id": 1,
                "order_id": 1,
                "product_id": 1,
                "quantity": 2,
                "price": 20000,
                "note": "Pedas, jangan pakai bawang",
                "product": {
                    "id": 1,
                    "name": "Nasi Goreng Special 43",
                    "price": 25000
                }
            }
        ]
    }
}
```

### B. GET List Riwayat Transaksi

Mengambil semua riwayat transaksi kasir yang ada di sistem.

-   **URL:** `/orders`
-   **Method:** `GET`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📥 Response (200 OK)

```json
{
    "success": true,
    "message": "List Riwayat Transaksi",
    "data": [
        {
            "id": 1,
            "cashier_id": 2,
            "transaction_time": "2026-06-07 20:30:00",
            "sub_total": 60000,
            "discount_id": 1,
            "discount_name": "Welcome WCB",
            "discount_amount": 10000,
            "shipping_cost": 0,
            "service_charge": 3000,
            "tax": 5300,
            "total": 58300,
            "payment_method": "qris",
            "created_at": "2026-06-07T13:47:37.000000Z",
            "updated_at": "2026-06-07T13:47:37.000000Z",
            "cashier": {
                "id": 2,
                "name": "Nama Kasir",
                "email": "kasir@mail.com"
            },
            "items": [
                {
                    "id": 1,
                    "order_id": 1,
                    "product_id": 1,
                    "quantity": 2,
                    "price": 20000,
                    "note": "Pedas, jangan pakai bawang",
                    "product": {
                        "id": 1,
                        "name": "Nasi Goreng Special 43",
                        "price": 25000
                    }
                }
            ]
        }
    ]
}
```

## Pembaruan Produk (Promo/Harga Coret)

Pada API `GET /api/products`, response data setiap produk kini memiliki parameter tambahan yaitu `discount_price` (nullable).
Parameter ini menunjukkan "Harga Setelah Promo/Diskon" untuk produk tersebut. Jika nilainya `null`, berarti produk tersebut sedang tidak promo (gunakan harga normal dari parameter `price`).

Contoh Response JSON untuk produk yang sedang Promo:

```json
{
    "id": 1,
    "category_id": 2,
    "name": "Ayam Geprek Spesial",
    "description": "Pedas mampus",
    "price": 25000,
    "discount_price": 20000,
    "stock": 10,
    "status": 1
}
```

Di frontend (Flutter), Anda dapat mengecek jika `discount_price != null`, maka tampilkan `price` dengan coretan (`TextDecoration.lineThrough`), dan tampilkan `discount_price` sebagai harga jualnya.

---

## 5. 🎉 API Campaign (Promo Khusus)

Mengambil daftar promo/campaign khusus yang sedang aktif beserta daftar produk yang termasuk dalam campaign tersebut.

### A. GET List Campaign Aktif

Mengambil semua campaign yang sedang aktif (`is_active = true`), dan berada dalam rentang tanggal saat ini (`start_date <= now <= end_date`). 

-   **URL:** `/campaigns`
-   **Method:** `GET`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📥 Response (200 OK)

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "name": "Promo Kemerdekaan",
            "start_date": "2026-08-01 00:00:00",
            "end_date": "2026-08-31 23:59:59",
            "discount_type": "percentage",
            "discount_value": 17.00,
            "is_active": 1,
            "created_at": "2026-07-01T10:00:00.000000Z",
            "updated_at": "2026-07-01T10:00:00.000000Z",
            "products": [
                {
                    "id": 1,
                    "name": "Nasi Goreng Special",
                    "price": 25000,
                    "pivot": {
                        "campaign_id": 1,
                        "product_id": 1
                    }
                }
            ]
        }
    ]
}
```

### B. GET Detail Campaign

Mengambil detail dari sebuah campaign berdasarkan ID, beserta produk-produk yang tergabung di dalamnya.

-   **URL:** `/campaigns/{id}`
-   **Method:** `GET`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📥 Response (200 OK)

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "name": "Promo Kemerdekaan",
        "start_date": "2026-08-01 00:00:00",
        "end_date": "2026-08-31 23:59:59",
        "discount_type": "percentage",
        "discount_value": 17.00,
        "is_active": 1,
        "created_at": "2026-07-01T10:00:00.000000Z",
        "updated_at": "2026-07-01T10:00:00.000000Z",
        "products": [
            {
                "id": 1,
                "name": "Nasi Goreng Special",
                "price": 25000,
                "pivot": {
                    "campaign_id": 1,
                    "product_id": 1
                }
            }
        ]
    }
}
```

#### 📥 Response Error (404 Not Found)

```json
{
    "status": "error",
    "message": "Campaign tidak ditemukan"
}
```

---

## 6. 📊 API Laporan Kasir (Reports)

Menyediakan data terkait ringkasan kasir saat sedang berjaga dan riwayat transaksi harian.

### A. GET Transaction History (Riwayat Transaksi Harian)

Mengambil daftar transaksi harian yang baru saja dilakukan oleh kasir. Mendukung filter tanggal dan pagination.

-   **URL:** `/cashier/reports/transactions`
-   **Method:** `GET`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)
-   **Query Parameters:**
 
 | Parameter | Tipe | Wajib | Deskripsi |
 | :--- | :--- | :--- | :--- |
 | `date` | `string` | ❌ Tidak | Filter transaksi berdasarkan tanggal (format `YYYY-MM-DD`). Default: hari ini. |
 | `page` | `integer` | ❌ Tidak | Nomor halaman untuk *pagination*. |
 | `limit` | `integer` | ❌ Tidak | Jumlah item per halaman. Default: 15. |

#### 📥 Response (200 OK)

```json
{
  "status": "success",
  "message": "Berhasil mengambil riwayat transaksi",
  "data": {
    "transactions": [
      {
        "id": "TRX-20260722-0001",
        "time": "11:45:00",
        "customer_name": "Customer",
        "payment_method": "QRIS",
        "total_items": 3,
        "grand_total": 75000,
        "status": "completed"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_items": 45
    }
  }
}
```

---

## 7. 🧾 API Pengaturan (Profil Toko & Biaya)

Mengelola pengaturan profil toko (untuk cetak struk) dan pengaturan perhitungan biaya (ongkir, layanan, pajak).

### A. GET Profil Toko (Struk)
Mengambil informasi nama toko, nomor telepon, alamat, dan preferensi tampilan struk.

-   **URL:** `/settings/store`
-   **Method:** `GET`
-   **Headers:**
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📥 Response (200 OK)
```json
{
    "success": true,
    "message": "Profil Toko",
    "data": {
        "name": "SajiPos",
        "address": "Desa Balisoan di Kecamatan Sahu, Kabupaten Halmahera Barat, Maluku Utara",
        "phone": "081247582918",
        "logo_url": null,
        "show_phone_on_receipt": true,
        "show_address_on_receipt": true,
        "show_logo_on_receipt": false
    }
}
```

### B. PUT Update Profil Toko (Struk)
Menyimpan perubahan informasi profil toko dan preferensi struk.

-   **URL:** `/settings/store`
-   **Method:** `PUT`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📤 Request Body (JSON)
```json
{
    "name": "SajiPos",
    "phone": "081247582918",
    "address": "Desa Balisoan di Kecamatan Sahu, Kabupaten Halmahera Barat, Maluku Utara",
    "show_phone_on_receipt": true,
    "show_address_on_receipt": false,
    "show_logo_on_receipt": false
}
```

#### 📥 Response (200 OK)
Akan mengembalikan response JSON yang sama dengan endpoint `GET /settings/store`.

### C. GET Pengaturan Perhitungan Biaya
Mengambil pengaturan biaya yang sedang aktif.

-   **URL:** `/settings/cost-calculation`
-   **Method:** `GET`
-   **Headers:**
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📥 Response (200 OK)
```json
{
    "success": true,
    "message": "Pengaturan Perhitungan Biaya",
    "data": {
        "shipping_fee": 10000,
        "include_shipping_in_tax": true,
        "service_fee": 5000,
        "include_service_fee_in_tax": false,
        "tax_percentage": 10
    }
}
```

### D. PUT Update Pengaturan Perhitungan Biaya
Menyimpan perubahan pengaturan biaya.

-   **URL:** `/settings/cost-calculation`
-   **Method:** `PUT`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📤 Request Body (JSON)
```json
{
    "shipping_fee": 10000,
    "include_shipping_in_tax": true,
    "service_fee": 5000,
    "include_service_fee_in_tax": false,
    "tax_percentage": 10
}
```

#### 📥 Response (200 OK)
```json
{
    "success": true,
    "message": "Settings updated successfully",
    "data": {
        "shipping_fee": 10000,
        "include_shipping_in_tax": true,
        "service_fee": 5000,
        "include_service_fee_in_tax": false,
        "tax_percentage": 10
    }
}
```

---

## 8. 📈 API Analitik (Analytics)

Mengambil data statistik penjualan untuk ditampilkan di aplikasi kasir atau dashboard frontend.

### A. GET Menu Terlaris (Top Menus)
Mengambil daftar menu paling laris secara keseluruhan (berdasarkan total kuantitas yang terjual).

-   **URL:** `/menu-terlaris`
-   **Method:** `GET`
-   **Headers:**
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📥 Response (200 OK)
```json
{
    "success": true,
    "message": "Data menu terlaris",
    "data": [
        {
            "product_id": 1,
            "name": "Nasi Goreng Special 43",
            "total_sold": 150
        }
    ]
}
```

### B. GET Menu Terlaris Harian (Daily Top Menus)
Mengambil daftar menu paling laris khusus untuk hari ini saja.

-   **URL:** `/menu-terlaris/harian`
-   **Method:** `GET`
-   **Headers:**
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📥 Response (200 OK)
Format response sama seperti endpoint Menu Terlaris di atas, namun difilter untuk transaksi hari ini.

---

## 9. 🛠️ API Utilitas Tambahan

### A. GET Validasi Kode Diskon (Check Code)
Mengecek apakah sebuah kode diskon/promo valid dan bisa digunakan oleh kasir.

-   **URL:** `/discounts/check-code?code=WCB20`
-   **Method:** `GET`
-   **Headers:**
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📥 Response (200 OK - Jika Valid)
Akan mengembalikan detail objek diskon yang bersangkutan.

#### 📥 Response (404 Not Found - Jika Invalid)
```json
{
    "success": false,
    "message": "Kode diskon tidak ditemukan, belum aktif, atau sudah kedaluwarsa"
}
```

### B. POST Kalkulasi Total Belanja (Hitung Total)
Mengirimkan rincian keranjang belanja ke server untuk dikalkulasikan total akhirnya (termasuk pajak, layanan, ongkir, dan diskon) sebelum kasir melakukan proses Checkout/Bayar sesungguhnya.

-   **URL:** `/orders/hitung-total`
-   **Method:** `POST`
-   **Headers:**
    -   `Content-Type: application/json`
    -   `Authorization: Bearer <your-token>`
-   **Auth Required:** Yes (Sanctum)

#### 📤 Request Body (JSON)
Format data sama persis dengan **POST Simpan Transaksi Baru** (hanya data order/items-nya saja).

## 7. 📢 Integrasi Push Notification (FCM Broadcast)

Sistem backend SajiPOS secara otomatis akan melakukan _broadcast_ notifikasi promo (Campaign) setiap kali Admin membuat Campaign baru melalui panel web. Frontend (Mobile App) tidak memerlukan API khusus untuk menerima notifikasi ini, melainkan harus terhubung langsung dengan **Firebase Cloud Messaging (FCM)** menggunakan metode **Topic**.

### 📱 Panduan Untuk Aplikasi Mobile (Frontend)

Agar pelanggan dapat menerima notifikasi promo, pastikan aplikasi mobile melakukan langkah berikut:

**1. Subscribe ke Topic FCM**
Saat aplikasi dibuka atau pelanggan berhasil _login_, aplikasi wajib melakukan subscribe ke topik berikut:
- **Nama Topic:** `promo_broadcast`

*(Contoh kode pada Flutter: `await FirebaseMessaging.instance.subscribeToTopic("promo_broadcast");`)*

**2. Struktur Data Payload (Data Tersembunyi)**
Ketika pelanggan meng-klik notifikasi promo yang masuk, backend SajiPOS sudah menyisipkan data ekstra (payload) di dalam push notification tersebut dengan format JSON berikut:

```json
{
    "campaign_id": 5,
    "action": "open_promo"
}
```

**3. Aksi Navigasi (Routing & Fetch Data)**
Frontend dapat mem-parsing data payload tersebut. Jika `action` bernilai `"open_promo"`, maka Frontend bisa otomatis mengarahkan pelanggan ke layar/halaman **Detail Promo**, lalu mengambil data lengkap promo tersebut dengan melakukan request HTTP ke:
👉 `GET /api/campaigns/{id}` *(gunakan `campaign_id` dari payload)*.
