# 🚀 Expense Tracker + OCR (Laravel + Docker)

Aplikasi ini adalah sistem pencatatan pengeluaran berbasis Laravel yang dilengkapi dengan fitur **OCR (Optical Character Recognition)** menggunakan Tesseract untuk membaca struk otomatis.

---

## 🔥 Features

* 📸 Upload struk → auto extract nominal
* 💰 Tracking pengeluaran user
* 📊 Dashboard dengan total spending
* ⚠️ Budget limit + warning jika over
* 🔐 Authentication (Login/Register)
* 🐳 Full Dockerized (no ribet setup)

---

## 🧠 Tech Stack

* Laravel (Backend)
* MySQL (Database)
* Tesseract OCR
* Docker + Docker Compose
* Tailwind CSS

---

## ⚡ Quick Start (Super Easy Mode)

```bash
git clone https://github.com/your-repo/project.git
cd be
docker-compose up --build
```

Open di browser:

```
http://localhost:8000
```

DONE. No setup tambahan. No drama. 🧘

---

## 🐳 Docker Breakdown

### Services:

* `app` → Laravel + PHP + Tesseract
* `db` → MySQL

---

## 🗂️ Project Structure

```
be/
├── app/
├── public/
├── resources/
├── routes/
├── storage/
├── Dockerfile
├── docker-compose.yml
├── nginx.conf (optional)
└── .env
```

---

## ⚙️ Auto Setup (Handled by Docker)

Saat pertama kali run:

* Auto copy `.env`
* Auto generate `APP_KEY`
* Auto migrate database
* Auto install dependencies
* Auto run server

👉 Jadi user gak perlu setup manual sama sekali.

---

## 💡 How It Works

1. User upload gambar struk
2. Tesseract membaca teks dari gambar
3. Sistem extract angka (total belanja)
4. Data disimpan ke database
5. Dashboard menampilkan:

   * Total pengeluaran
   * Chart
   * Budget warning

---

## ⚠️ Budget System

User bisa set maksimal budget.

Contoh:

* Budget: Rp1.000.000
* Spending: Rp1.200.000

👉 Sistem akan kasih warning (over budget)

---

## 🧪 Testing OCR (Optional)

Masuk container:

```bash
docker exec -it laravel_app bash
tesseract --version
```

Kalau muncul versi → berarti OCR ready 🔥

---

## 🚨 Known Limitations

* OCR tidak selalu 100% akurat (tergantung kualitas gambar)
* Setup ini optimized untuk development/demo (bukan production)
* Belum ada optimasi scaling

---

## 🧠 Future Improvements

* AI-based parsing (biar lebih akurat dari regex)
* Multi-currency support
* Analytics lebih advanced
* Mobile responsive dashboard
* Export laporan (PDF/Excel)

---

## 👨‍💻 Author

Built under pressure, powered by deadline 😤🔥

---

## 🏁 Final Notes

Project ini dibuat dengan mindset:

> "Ship first. Perfect later."

Kalau jalan → itu sudah win.
Kalau clean → itu bonus.

---

**Now go demo and dominate. 🚀**
