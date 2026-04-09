<?php
include 'auth.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Demo Mountster</title>
    <style>
        :root {
            --primary: #1f5130;
            --bg: #f3f7f4;
            --text: #223127;
            --muted: #66756b;
            --card: #ffffff;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(180deg, #edf8f0 0%, #f8fbf9 100%);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-page {
            width: 100%;
            max-width: 420px;
            background: var(--card);
            border-radius: 24px;
            box-shadow: 0 18px 40px rgba(0,0,0,0.08);
            padding: 28px 24px;
            text-align: center;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #edf8f0;
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #28b463;
        }

        .ring {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dff7ea, #f3fcf6);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
        }

        .check {
            width: 54px;
            height: 28px;
            border-left: 7px solid #1fa65a;
            border-bottom: 7px solid #1fa65a;
            transform: rotate(-45deg) scale(0.3);
            opacity: 0;
            animation: checkIn 0.45s ease forwards;
        }

        h1 {
            font-size: 28px;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .subtitle {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .summary {
            background: #f7faf8;
            border: 1px solid #e4eee8;
            border-radius: 18px;
            padding: 16px;
            text-align: left;
            margin-bottom: 18px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .row:last-child { margin-bottom: 0; }
        .row span:first-child { color: var(--muted); }
        .row span:last-child { font-weight: 700; text-align: right; }

        .small {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #153b22;
        }

        @keyframes checkIn {
            from { transform: rotate(-45deg) scale(0.3); opacity: 0; }
            to { transform: rotate(-45deg) scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="success-page">
        <div class="badge"><span class="badge-dot"></span>Demo Payment</div>
        <div class="ring"><div class="check"></div></div>
        <h1>Pembayaran Berhasil!</h1>
        <p class="subtitle">Halaman ini adalah tampilan sukses cadangan untuk flow pembayaran demo Mountster.</p>

        <div class="summary">
            <div class="row"><span>Toko</span><span>Mountster Rental Store</span></div>
            <div class="row"><span>Nominal</span><span id="amountText">Rp 0</span></div>
            <div class="row"><span>Order ID</span><span id="orderIdText">-</span></div>
            <div class="row"><span>Token</span><span id="tokenText">-</span></div>
        </div>

        <p class="small">Untuk demo utama, gunakan halaman pembayaran HP yang muncul dari QR checkout lalu tekan tombol bayar di sana.</p>
        <a href="home.php" class="btn">Kembali ke Beranda</a>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        const orderId = params.get('orderId') || 'ORD-DEMO';
        const token = params.get('token') || 'TOKEN';
        const amount = parseInt(params.get('amount') || '0', 10);

        document.getElementById('orderIdText').innerText = orderId;
        document.getElementById('tokenText').innerText = token;
        document.getElementById('amountText').innerText = `Rp ${amount.toLocaleString('id-ID')}`;

    </script>
</body>
</html>