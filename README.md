# 📘 **E-Wallet Microservices (UTS IAE)**

## 📌 Deskripsi Proyek

Proyek ini adalah implementasi **Service-Oriented Architecture (SOA)** untuk topik:

> **Digital Payment Service (E-Wallet)**
> dengan 2 layanan terpisah:
>
> * **User Service** → Mengelola user & saldo
> * **Payment Service** → Mengelola transaksi top-up & pembayaran

Komunikasi antar service dilakukan melalui **REST API**, dan seluruh request dari client hanya melalui **API Gateway**.

Proyek ini dibuat untuk memenuhi tugas UTS mata kuliah **IAE/EAI**, yang menekankan pembuatan layanan terdistribusi, integrasi API, serta dokumentasi API.

---

# 🏗️ **Arsitektur Sistem**

## **Alur komunikasi**

```
Frontend (Client)
        ↓
   API Gateway (Node.js)
        ↓
 ┌──────────────┬───────────────┐
 │   User Service (Laravel)      │
 │   Payment Service (Laravel)   │
 └──────────────┴───────────────┘
        ↓              ↓
  MySQL DB Users   MySQL DB Payments
```

## **Penjelasan singkat:**

* **Frontend** (HTML/JS) → konsumsi API melalui gateway.
* **Gateway** → mem-proxy request ke service sesuai prefix:

  * `/api/user-service/*` → User Service (port 8001)
  * `/api/payment-service/*` → Payment Service (port 8002)
* **User Service** → CRUD user, cek saldo, update saldo.
* **Payment Service** → top-up, pembayaran, riwayat transaksi.
* **Database** terpisah untuk setiap service (sesuai arsitektur microservices).

---

# 🚀 **Cara Menjalankan Proyek**

## **1. Clone Repository**

```bash
git clone <repo-url>
cd e-wallet-microservices
```

---

# ⚙️ **2. Setup User Service (Laravel – port 8001)**

### Masuk folder:

```bash
cd user-service
```

### Install dependency:

```bash
composer install
```

### Copy & edit environment:

```bash
cp .env.example .env
```

Pastikan di `.env`:

```env
APP_KEY=  (nanti generate)
DB_DATABASE=e_wallet_user
DB_USERNAME=root
DB_PASSWORD=
```

### Generate app key:

```bash
php artisan key:generate
```

### Migrasi database:

```bash
php artisan migrate
```

### Jalankan service:

```bash
php artisan serve --port=8001
```

Akses:

```
http://localhost:8001/api/users
```

---

# ⚙️ **3. Setup Payment Service (Laravel – port 8002)**

### Masuk folder:

```bash
cd payment-service
```

### Install dependency:

```bash
composer install
```

### Copy & edit `.env`:

```bash
cp .env.example .env
```

Isi:

```env
DB_DATABASE=e_wallet_payment
DB_USERNAME=root
DB_PASSWORD=
```

### Generate key:

```bash
php artisan key:generate
```

### Migrasi tabel transaksi:

```bash
php artisan migrate
```

### Jalankan service:

```bash
php artisan serve --port=8002
```

Akses:

```
http://localhost:8002/api/transactions
```

---

# 🌐 **4. Jalankan API Gateway (Node.js – port 4000)**

### Masuk folder:

```bash
cd api-gateway
```

### Install dependency:

```bash
npm install
```

### Jalankan:

```bash
node index.js
```

Gateway akan berjalan di:

```
http://localhost:4000
```

Contoh endpoint via gateway:

* [http://localhost:4000/api/user-service/users](http://localhost:4000/api/user-service/users)
* [http://localhost:4000/api/payment-service/transactions](http://localhost:4000/api/payment-service/transactions)

---

# 🖥️ **5. Jalankan Frontend (HTML/JS)**

### Masuk folder:

```bash
cd frontend
```

### Pilih salah satu cara:

#### **A. Buka langsung**

Klik dua kali `index.html`.

#### **B. Live Server (VS Code)**

Klik kanan → *Open with Live Server*

#### **C. Serve manual**

```bash
npx http-server . -p 8080
```

Akses:

```
http://localhost:8080
```

---

# 👥 **Anggota Kelompok & Peran**

| Nama             | NIM   | Peran                                    |
| ---------------- | ----- | ---------------------------------------- |
| **(Isi Nama 1)** | (NIM) | Implementasi User Service, Database User |
| **(Isi Nama 2)** | (NIM) | Implementasi Payment Service, DB Payment |
| **(Isi Nama 3)** | (NIM) | API Gateway, Proxy & Routing             |
| **(Isi Nama 4)** | (NIM) | Frontend & integrasi client ↔ gateway    |
| **(Opsional)**   |       | Dokumentasi API, Swagger/Postman         |

> *Jika kelompok hanya 2 orang, cukup isi dua baris relevan.*

---

# 📚 **Ringkasan Endpoint**

## **User Service**

| Method | Endpoint              | Deskripsi                   |
| ------ | --------------------- | --------------------------- |
| GET    | `/users`              | List user                   |
| POST   | `/users`              | Membuat user                |
| GET    | `/users/{id}`         | Detail user                 |
| GET    | `/users/{id}/balance` | Cek saldo                   |
| PUT    | `/users/{id}/balance` | Update saldo (credit/debit) |

---

## **Payment Service**

| Method | Endpoint              | Deskripsi        |
| ------ | --------------------- | ---------------- |
| GET    | `/transactions`       | List transaksi   |
| POST   | `/transactions/topup` | Top-up saldo     |
| POST   | `/transactions/pay`   | Pembayaran       |
| GET    | `/transactions/{id}`  | Detail transaksi |

---

## **API Gateway**

| Method | Endpoint                 | Diteruskan ke   |
| ------ | ------------------------ | --------------- |
| GET    | `/api/user-service/*`    | User Service    |
| GET    | `/api/payment-service/*` | Payment Service |

---

# 📄 **Dokumentasi API Lengkap**

Dokumentasi endpoint lengkap ada di folder:

```
docs/api/
```

Isi:

* `user-service.postman_collection.json`
* `payment-service.postman_collection.json`
* `gateway-routes.md`
* `erd.png`
* `architecture-diagram.png`

Jika menggunakan Swagger:

* User service → `http://localhost:8001/api-docs`
* Payment service → `http://localhost:8002/api-docs`

---

# 🏁 **Urutan Menjalankan Proyek (Wajib)**

1. **Start User Service**

   ```
   php artisan serve --port=8001
   ```

2. **Start Payment Service**

   ```
   php artisan serve --port=8002
   ```

3. **Start API Gateway**

   ```
   node index.js
   ```

4. **Buka Frontend**

   ```
   frontend/index.html
   ```

---

# 🎉 **Status**

Proyek ini telah memenuhi ketentuan UTS:
✔ 2 service terpisah
✔ API Gateway
✔ Dokumentasi API
✔ Integrasi antar service (saldo ↔ transaksi)
✔ Frontend mengonsumsi gateway
✔ Database terpisah tiap service

---
