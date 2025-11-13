const express = require('express');
const cors = require('cors');
const fetch = require('node-fetch');

const app = express();
const PORT = 4000; // kita pakai 4000

app.use(cors());
app.use(express.json());

// alamat service Laravel
const USER_SERVICE = 'http://127.0.0.1:8001/api';
const PAYMENT_SERVICE = 'http://127.0.0.1:8002/api';

console.log('Gateway config:', { USER_SERVICE, PAYMENT_SERVICE });

// ROOT & HEALTH
app.get('/', (req, res) => {
  res.json({
    message: 'E-Wallet API Gateway running (FETCH VERSION)',
    examples: {
      health: 'GET /health',
      listUsers: 'GET /api/user-service/users',
      topup: 'POST /api/payment-service/transactions/topup',
    },
  });
});

app.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    gateway: 'running',
    services: {
      userService: USER_SERVICE,
      paymentService: PAYMENT_SERVICE,
    },
    timestamp: new Date().toISOString(),
  });
});

// ========== USER SERVICE ==========

// GET /api/user-service/users
app.get('/api/user-service/users', async (req, res) => {
  try {
    const resp = await fetch(`${USER_SERVICE}/users`);
    const data = await resp.json();
    res.status(resp.status).json(data);
  } catch (err) {
    console.error('Error forwarding to user-service GET /users:', err.message);
    res.status(500).json({ message: 'Gateway error (users)', error: err.message });
  }
});

// POST /api/user-service/users
app.post('/api/user-service/users', async (req, res) => {
  try {
    const resp = await fetch(`${USER_SERVICE}/users`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(req.body),
    });
    const data = await resp.json();
    res.status(resp.status).json(data);
  } catch (err) {
    console.error('Error forwarding to user-service POST /users:', err.message);
    res.status(500).json({ message: 'Gateway error (create user)', error: err.message });
  }
});

// GET /api/user-service/users/:id/balance
app.get('/api/user-service/users/:id/balance', async (req, res) => {
  const { id } = req.params;
  try {
    const resp = await fetch(`${USER_SERVICE}/users/${id}/balance`);
    const data = await resp.json();
    res.status(resp.status).json(data);
  } catch (err) {
    console.error('Error forwarding to user-service GET /users/:id/balance:', err.message);
    res.status(500).json({ message: 'Gateway error (balance)', error: err.message });
  }
});

// ========== PAYMENT SERVICE ==========

// GET /api/payment-service/transactions
app.get('/api/payment-service/transactions', async (req, res) => {
  try {
    const resp = await fetch(`${PAYMENT_SERVICE}/transactions`);
    const data = await resp.json();
    res.status(resp.status).json(data);
  } catch (err) {
    console.error('Error forwarding to payment-service GET /transactions:', err.message);
    res.status(500).json({ message: 'Gateway error (transactions)', error: err.message });
  }
});

// POST /api/payment-service/transactions/topup
app.post('/api/payment-service/transactions/topup', async (req, res) => {
  try {
    const resp = await fetch(`${PAYMENT_SERVICE}/transactions/topup`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(req.body),
    });
    const data = await resp.json();
    res.status(resp.status).json(data);
  } catch (err) {
    console.error('Error forwarding to payment-service POST /transactions/topup:', err.message);
    res.status(500).json({ message: 'Gateway error (topup)', error: err.message });
  }
});

// POST /api/payment-service/transactions/pay
app.post('/api/payment-service/transactions/pay', async (req, res) => {
  try {
    const resp = await fetch(`${PAYMENT_SERVICE}/transactions/pay`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(req.body),
    });
    const data = await resp.json();
    res.status(resp.status).json(data);
  } catch (err) {
    console.error('Error forwarding to payment-service POST /transactions/pay:', err.message);
    res.status(500).json({ message: 'Gateway error (pay)', error: err.message });
  }
});

app.listen(PORT, () => {
  console.log(`API Gateway listening on http://localhost:${PORT}`);
});
