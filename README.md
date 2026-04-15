# 💰 FinBuddy — OCR-Powered Expense Tracker

> Laravel 11 · PostgreSQL · Railway · Tesseract OCR · Vite

---

## 📌 Overview

FinBuddy adalah aplikasi manajemen keuangan berbasis web yang memanfaatkan teknologi **OCR (Optical Character Recognition)** untuk membaca struk belanja secara otomatis. Pengguna cukup mengupload foto struk, dan sistem akan mengekstrak nominal transaksi, menyimpannya ke database, serta menampilkannya dalam dashboard pengeluaran yang interaktif.

---

## 🧠 How It Works

```
User Upload Image
        ↓
OCR (Tesseract)
        ↓
Text Processing
        ↓
Extract Nominal
        ↓
Store to Database
        ↓
Display on Dashboard
```

---

## 🚀 Features

- 📤 Upload struk (image) untuk ekstraksi otomatis
- 🔍 OCR text extraction menggunakan Tesseract
- 💾 Penyimpanan otomatis ke database (MySQL / PostgreSQL)
- 📊 Dashboard monitoring pengeluaran
- 💰 Budget tracking system bulanan
- 🔐 Authentication system (Laravel)
- 👤 Profile management (update & delete akun)

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 11 (PHP 8.2) |
| Database | PostgreSQL (Railway) / MySQL (Docker) |
| OCR Engine | Tesseract OCR |
| Frontend Build | Vite |
| Deployment | Railway |
| Containerization | Docker & Docker Compose |

---

## ⚙️ Installation

### Option 1: Docker (Recommended)

```bash
git clone https://github.com/kafkaarko/Capstone-Project---CC26-PS009.git
cd Capstone-Project---CC26-PS009
docker-compose up --build
```

Akses aplikasi di: `http://localhost:8000`

---

### Option 2: Manual Setup (Without Docker)

**Requirements:** PHP 8+, Composer, MySQL, Tesseract OCR

```bash
composer install
cp .env.example .env
php artisan key:generate
```

**Configure `.env`:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=keuangan
DB_USERNAME=root
DB_PASSWORD=
```

**Run Migration & Serve:**

```bash
php artisan migrate
php artisan storage:link
php artisan serve
```

---

## ☁️ Deployment (Railway)

### 1. Environment Variables

```env
DB_CONNECTION=pgsql
DB_HOST=postgres.railway.internal
DB_PORT=5432
DB_DATABASE=railway
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 2. Build Process (via Docker)

Dijalankan otomatis:
- Install PHP dependencies (Composer)
- Install Node dependencies (NPM)
- Build frontend assets (Vite)
- Run migrations otomatis

### 3. Jalankan Aplikasi

```bash
php artisan migrate --force
php artisan serve
```

### Local Development

```bash
composer install
npm install && npm run dev
php artisan migrate
php artisan serve
```

---

## 🧪 API Endpoints

> ⚠️ Semua endpoint memerlukan autentikasi (`middleware: auth`). Pastikan sudah login sebelum mengakses.

---

### `GET /` — Root Redirect

Redirect otomatis ke `/dashboard-expense`.

---

### `GET /upload-page` — Halaman Upload

Menampilkan halaman form upload struk.

| | |
|---|---|
| Controller | `OCRController@index` |
| Route Name | `upload.page` |

---

### `POST /upload` — Upload Receipt (OCR)

Menerima gambar struk, mengekstrak teks menggunakan OCR, dan menyimpan data transaksi.

| | |
|---|---|
| Controller | `OCRController@upload` |
| Route Name | `upload` |
| Content-Type | `multipart/form-data` |

**Request Body:**

| Field | Type | Keterangan |
|---|---|---|
| `file` | image (jpg/png) | Foto struk belanja |

**Response:**

```json
{
  "status": "success",
  "data": "Extracted text from receipt"
}
```

---

### `GET /dashboard-expense` — Dashboard Transaksi

Menampilkan semua data transaksi dan ringkasan pengeluaran pengguna.

| | |
|---|---|
| Controller | `OCRController@dashboard` |
| Route Name | `dashboard.expense` |

---

### `POST /set-budget` — Set Monthly Budget

Menetapkan batas anggaran bulanan pengguna.

| | |
|---|---|
| Controller | `OCRController@setBudget` |
| Route Name | `set.budget` |

**Request Body:**

```json
{
  "budget": 1000000
}
```

---

### `GET /profile` — Halaman Edit Profil

| | |
|---|---|
| Controller | `ProfileController@edit` |
| Route Name | `profile.edit` |

---

### `PATCH /profile` — Update Profil

| | |
|---|---|
| Controller | `ProfileController@update` |
| Route Name | `profile.update` |

---

### `DELETE /profile` — Hapus Akun

| | |
|---|---|
| Controller | `ProfileController@destroy` |
| Route Name | `profile.destroy` |

---

### Ringkasan Semua Route

| Method | Endpoint | Controller | Route Name |
|---|---|---|---|
| `GET` | `/` | — (redirect) | — |
| `GET` | `/upload-page` | `OCRController@index` | `upload.page` |
| `POST` | `/upload` | `OCRController@upload` | `upload` |
| `GET` | `/dashboard-expense` | `OCRController@dashboard` | `dashboard.expense` |
| `POST` | `/set-budget` | `OCRController@setBudget` | `set.budget` |
| `GET` | `/profile` | `ProfileController@edit` | `profile.edit` |
| `PATCH` | `/profile` | `ProfileController@update` | `profile.update` |
| `DELETE` | `/profile` | `ProfileController@destroy` | `profile.destroy` |

---

## 📂 Project Structure

```
app/
├── Http/Controllers
│   ├── OCRController.php       # Upload, OCR, dashboard, budget
│   └── ProfileController.php  # Profile edit, update, destroy
├── Models
resources/
├── views/                      # Blade templates (frontend)
routes/
└── web.php                     # Route definitions
```

---

## ⚠️ Known Limitations

- OCR accuracy bergantung pada kualitas gambar yang diupload
- Tidak semua format struk dapat dibaca dengan sempurna
- Parsing nominal masih berbasis pola sederhana (regex)
- Gambar berukuran besar dapat memperlambat proses OCR
- Queue system belum diimplementasi (OCR berjalan secara sinkron)

---

## 📈 Future Improvements

- Async OCR processing menggunakan Queue system
- Auto-kategorisasi transaksi berbasis AI/NLP
- Peningkatan akurasi parsing OCR
- Support multiple currencies
- Advanced analytics dashboard
- Export laporan ke PDF/Excel
- Integrasi mobile app

---

## 💡 Notes

Project ini dirancang dengan pendekatan **reproducibility**, sehingga dapat dijalankan di environment berbeda menggunakan Docker tanpa perlu setup manual yang kompleks.

---

## 👨‍💻 Author

Developed as Capstone Project — CC26-PS009

---

> *This is not just a CRUD app. This is a system.* 🚀
