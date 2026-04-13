# 💰 Capstone Project - Expense Tracker with OCR

## 📌 Overview

Project ini adalah aplikasi manajemen keuangan berbasis web yang memanfaatkan teknologi **OCR (Optical Character Recognition)** untuk membaca struk belanja secara otomatis.

User dapat mengupload gambar struk, kemudian sistem akan:

1. Mengekstrak teks menggunakan OCR (Tesseract)
2. Mengidentifikasi nilai transaksi
3. Menyimpan data ke database
4. Menampilkan data dalam dashboard

---

## 🚀 Features

* 📤 Upload struk (image)
* 🔍 OCR text extraction (Tesseract)
* 💾 Penyimpanan otomatis ke database
* 📊 Dashboard monitoring pengeluaran
* 💰 Budget tracking system
* 🔐 Authentication system (Laravel)

---

## 🧠 How It Works

```text
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

## 🛠️ Tech Stack

* **Backend**: Laravel (PHP 8.2)
* **Database**: MySQL (Dockerized)
* **OCR Engine**: Tesseract OCR
* **Containerization**: Docker & Docker Compose

---

## ⚙️ Installation (Docker - Recommended)

### 1. Clone Repository

```bash
git clone https://github.com/kafkaarko/Capstone-Project---CC26-PS009.git
cd Capstone-Project---CC26-PS009
```

### 2. Run Docker

```bash
docker-compose up --build
```

### 3. Access App

```
http://localhost:8000
```

---

## ⚙️ Manual Setup (Without Docker)

### Requirements

* PHP 8+
* Composer
* MySQL
* Tesseract OCR installed

### Steps

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### Configure Database (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=keuangan
DB_USERNAME=root
DB_PASSWORD=
```

### Run Migration & Serve

```bash
php artisan migrate
php artisan storage:link
php artisan serve
```

---

## 📂 Project Structure

```text
app/
├── Http/Controllers
├── Models
resources/
├── views/
routes/
├── web.php
```

---

## ⚠️ Known Limitations

* OCR accuracy bergantung pada kualitas gambar
* Tidak semua format struk dapat dibaca dengan sempurna
* Parsing nominal masih berbasis pola sederhana

---

## 📈 Future Improvements

* Improve OCR parsing accuracy (AI/NLP)
* Support multiple currencies
* Advanced analytics dashboard
* Export report (PDF/Excel)

---

## 👨‍🍳 Author

Developed as Capstone Project

---

## 💡 Notes

Project ini dirancang dengan pendekatan **reproducibility**, sehingga dapat dijalankan di environment berbeda menggunakan Docker tanpa perlu setup manual yang kompleks.

---

## 🔥 Final Statement

> This project is not just about features,
> but about building a system that is portable, scalable, and reproducible.
