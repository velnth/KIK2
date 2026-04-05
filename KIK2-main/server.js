const express = require('express');
const path = require('path');
const os = require('os');
const crypto = require('crypto');
const dotenv = require('dotenv');

dotenv.config();

const app = express();
const api = express.Router();
const PORT = process.env.PORT || 3000;
const PUBLIC_DIR = path.join(__dirname, 'KIK');
const paymentStatus = {};

function getLocalIP() {
    const nets = os.networkInterfaces();
    for (const name of Object.keys(nets)) {
        for (const net of nets[name] || []) {
            if (net.family === 'IPv4' && !net.internal) {
                return net.address;
            }
        }
    }
    return '127.0.0.1';
}

function getBaseUrl(req) {
    const host = req?.headers?.host || `${getLocalIP()}:${PORT}`;
    if (host.includes('127.0.0.1') || host.includes('localhost')) {
        return `http://${getLocalIP()}:${PORT}`;
    }
    return `http://${host}`;
}

function createPaymentToken() {
    return crypto.randomBytes(4).toString('hex').toUpperCase();
}

function createPaymentPayload(req) {
    const { orderId, amount, customerName } = req.body;
    const grossAmount = Number.parseInt(amount, 10);

    if (!orderId || !amount) {
        return { error: 'orderId dan amount wajib diisi', status: 400 };
    }

    if (!Number.isInteger(grossAmount) || grossAmount <= 0) {
        return { error: 'amount harus berupa angka bulat lebih dari 0', status: 400 };
    }

    const token = createPaymentToken();
    const expiresAt = Date.now() + (10 * 60 * 1000);
    const successUrl = `${getBaseUrl(req)}/KIK2-main/KIK/payment-success.html?orderId=${encodeURIComponent(orderId)}&token=${encodeURIComponent(token)}&amount=${encodeURIComponent(grossAmount)}`;
    const paymentUrl = `${getBaseUrl(req)}/api/payment/confirm-direct?orderId=${encodeURIComponent(orderId)}&token=${encodeURIComponent(token)}&amount=${encodeURIComponent(grossAmount)}`;

    paymentStatus[orderId] = {
        status: 'pending',
        token,
        grossAmount,
        customerName: customerName || 'Mountster Customer',
        createdAt: Date.now(),
        expiresAt,
        paymentUrl,
        successUrl,
        paidAt: null
    };

    return {
        orderId,
        token,
        grossAmount,
        expiresAt,
        paymentUrl
    };
}

function ensurePaymentRecord({ orderId, token, grossAmount = 0 }) {
    if (!paymentStatus[orderId]) {
        paymentStatus[orderId] = {
            status: 'pending',
            token,
            grossAmount,
            createdAt: Date.now(),
            expiresAt: Date.now() + (10 * 60 * 1000),
            paymentUrl: null,
            successUrl: null,
            paidAt: null
        };
    }
    return paymentStatus[orderId];
}

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET,POST,OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type');
    if (req.method === 'OPTIONS') {
        return res.sendStatus(204);
    }
    next();
});

api.get('/server-info', (req, res) => {
    res.json({
        localIp: getLocalIP(),
        port: PORT,
        baseUrl: getBaseUrl(req)
    });
});

api.post('/payment/create', (req, res) => {
    const result = createPaymentPayload(req);
    if (result.error) {
        return res.status(result.status).json({ error: result.error });
    }
    return res.json(result);
});

api.post('/create-payment', (req, res) => {
    const result = createPaymentPayload(req);
    if (result.error) {
        return res.status(result.status).json({ error: result.error });
    }
    return res.json(result);
});

api.post('/payment/confirm', (req, res) => {
    const { orderId, token, amount } = req.body;
    const grossAmount = Number.parseInt(amount, 10);

    if (!orderId || !token) {
        return res.status(400).json({ error: 'orderId dan token wajib diisi' });
    }

    const payment = ensurePaymentRecord({
        orderId,
        token,
        grossAmount: Number.isInteger(grossAmount) && grossAmount > 0 ? grossAmount : 0
    });

    if (payment.token !== token) {
        return res.status(403).json({ error: 'Token pembayaran tidak valid' });
    }

    payment.status = 'paid';
    payment.paidAt = Date.now();

    return res.json({
        success: true,
        orderId,
        grossAmount: payment.grossAmount,
        paidAt: payment.paidAt
    });
});

app.get('/api/payment/confirm-direct', (req, res) => {
    const { orderId, token, amount } = req.query;

    if (!orderId || !token) {
        return res.status(400).send('orderId dan token wajib diisi');
    }

    paymentStatus[orderId] = {
        ...(paymentStatus[orderId] || {}),
        status: 'paid',
        token,
        grossAmount: Number.parseInt(amount, 10) || paymentStatus[orderId]?.grossAmount || 0,
        paidAt: Date.now()
    };

    return res.redirect(`/KIK2-main/KIK/payment-success.html?orderId=${encodeURIComponent(orderId)}&token=${encodeURIComponent(token)}&amount=${encodeURIComponent(amount || paymentStatus[orderId].grossAmount || '')}`);
});

api.get('/payment/status', (req, res) => {
    const { orderId } = req.query;

    if (!orderId) {
        return res.status(400).json({ status: 'pending', error: 'orderId wajib diisi' });
    }

    const payment = paymentStatus[orderId];
    if (!payment) {
        return res.json({ status: 'pending' });
    }

    const isExpired = Date.now() > payment.expiresAt && payment.status !== 'paid';
    const status = isExpired ? 'expired' : payment.status;

    return res.json({
        status,
        orderId,
        grossAmount: payment.grossAmount,
        paidAt: payment.paidAt,
        expiresAt: payment.expiresAt,
        paymentUrl: payment.paymentUrl,
        successUrl: payment.successUrl || null
    });
});

app.use('/api', api);
app.use('/KIK2-main/KIK', express.static(PUBLIC_DIR));
app.use(express.static(PUBLIC_DIR));

app.get('/', (req, res) => {
    res.sendFile(path.join(PUBLIC_DIR, 'home.html'));
});

if (require.main === module) {
    app.listen(PORT, '0.0.0.0', () => {
        console.log(`Mountster server running on http://0.0.0.0:${PORT}`);
        console.log(`Local network access: http://${getLocalIP()}:${PORT}`);
    });
}

module.exports = app;
