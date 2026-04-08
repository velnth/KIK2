<?php 
include 'auth.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountster - Pembayaran</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .pay-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 20px; text-align: center; }
        .countdown { font-size: 24px; font-weight: bold; color: #ff4d4f; margin: 10px 0; }
        .va-number { font-size: 22px; font-weight: bold; color: var(--primary); letter-spacing: 2px; padding: 15px; background: #f4fbf4; border-radius: 8px; margin: 15px 0; border: 1px dashed var(--primary); }
        .qr-code { width: 200px; height: 200px; margin: 20px auto; padding: 10px; border: 2px solid #eee; border-radius: 12px; }
        .copy-btn { color: var(--primary); font-size: 14px; font-weight: bold; cursor: pointer; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="app-container" style="max-width: 600px; padding-bottom: 100px;">
        <div class="p-20 flex-between" style="background: white; border-bottom: 1px solid #eee;">
            <a href="order.php" style="text-decoration: none; color: black; font-size: 20px;">←</a>
            <h2 style="font-size: 18px;">Selesaikan Pembayaran</h2>
            <div style="width: 20px;"></div>
        </div>

        <div style="padding: 20px;">
            <div class="pay-card">
                <p style="color: var(--text-muted); font-size: 14px;">Batas Waktu Pembayaran</p>
                <div class="countdown" id="timerDisplay">23:00:00</div>
                <p style="font-size: 12px; color: #888;">Bayar sebelum waktu habis agar pesanan tidak dibatalkan.</p>
            </div>

            <div class="pay-card">
                <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 5px;">Metode Pembayaran</p>
                <h3 id="payMethodName" style="font-size: 18px; margin-bottom: 20px;">Memuat...</h3>

                <div id="vaArea" style="display: none;">
                    <p style="font-size: 14px;">Nomor Virtual Account:</p>
                    <div class="va-number" id="vaNumberDisplay">-</div>
                    <span class="copy-btn" onclick="alert('Nomor VA berhasil disalin!')">Salin Nomor</span>
                </div>

                <div id="qrisArea" style="display: none;">
                    <p style="font-size: 14px;">Scan QR Code di bawah ini:</p>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=MountsterPayment" class="qr-code" alt="QRIS">
                    <p style="font-size: 12px; color: #888;">Buka aplikasi e-wallet / m-banking Anda dan scan kode ini.</p>
                </div>

                <div style="border-top: 1px solid #eee; margin-top: 25px; padding-top: 20px;">
                    <p style="font-size: 14px; color: var(--text-muted);">Total Tagihan</p>
                    <h2 id="totalPayDisplay" style="color: var(--primary); font-size: 24px; margin-top: 5px;">Rp 0</h2>
                </div>
            </div>

            <button class="btn btn-primary" onclick="confirmPayment()" style="width: 100%; margin-top: 10px; padding: 15px; font-size: 16px;">Saya Sudah Membayar</button>
            <button class="btn" onclick="window.location.href='order.php'" style="width: 100%; margin-top: 10px; background: transparent; color: var(--text-muted); font-weight: bold;">Bayar Nanti</button>
        </div>
    </div>

    <script>
        // Ambil ID transaksi dari URL (?id=...)
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('id');

        let orders = JSON.parse(localStorage.getItem('mountsterOrders')) || [];
        let currentOrderIndex = orders.findIndex(o => o.id === orderId);
        
        if (currentOrderIndex === -1) {
            alert("Pesanan tidak ditemukan!"); window.location.href = "order.php";
        }
        
        let order = orders[currentOrderIndex];

        // Tampilkan Data Pembayaran
        document.getElementById('payMethodName').innerHTML = order.paymentName;
        document.getElementById('totalPayDisplay').innerText = `Rp ${order.totalPay.toLocaleString('id-ID')}`;

        if (order.isVA) {
            document.getElementById('vaArea').style.display = 'block';
            document.getElementById('vaNumberDisplay').innerText = order.vaNumber;
        } else {
            document.getElementById('qrisArea').style.display = 'block';
        }

        // Fungsi Hitung Mundur 23 Jam
        let timer = setInterval(function() {
            let now = new Date().getTime();
            let distance = order.deadline - now;

            if (distance < 0) {
                clearInterval(timer);
                document.getElementById("timerDisplay").innerHTML = "KEDALUWARSA";
                order.status = "Dibatalkan";
                localStorage.setItem('mountsterOrders', JSON.stringify(orders));
                alert("Waktu pembayaran habis. Pesanan dibatalkan.");
                window.location.href = "order.php";
            } else {
                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById("timerDisplay").innerHTML = hours + "j " + minutes + "m " + seconds + "d";
            }
        }, 1000);

        function confirmPayment() {
            // Cek tipe pengirimannya
            if (order.shippingType === 'pickup') {
                order.status = "Ambil di Toko"; 
            } else {
                order.status = "Dikemas"; 
            }
            
            localStorage.setItem('mountsterOrders', JSON.stringify(orders));
            window.location.href = "order.php";
        }

    </script>
</body>
</html>