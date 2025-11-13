# API Gateway – E-Wallet

Gateway sederhana menggunakan Node.js + Express + http-proxy-middleware.

## Setup

```bash
cd api-gateway
npm install
node index.js
```

Gateway akan berjalan di `http://localhost:3000`.

## Routing

- `/api/user-service/...`    -> `http://localhost:8001/...`
- `/api/payment-service/...` -> `http://localhost:8002/...`

Contoh:

- `GET http://localhost:3000/api/user-service/users`
- `POST http://localhost:3000/api/payment-service/transactions/topup`
