<?php
include 'auth.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountster - Pesanan Saya</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Tambahan agar Tab berfungsi sempurna */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tab-item {
            border-bottom: 2px solid transparent;
            color: #888;
            transition: 0.3s;
        }

        .tab-item.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="app-container" style="padding-bottom: 100px;">
        <div class="p-20 flex-between" style="background: white; border-bottom: 1px solid #eee;">
            <a href="home.php" style="text-decoration: none; color: black; font-size: 20px;">←</a>
            <h2 style="font-size: 18px;">Pesanan Saya</h2>
            <div style="width: 20px;"></div>
        </div>

        <div class="tab-container" style="background: white; padding: 0 20px; margin-top: 0; display: flex;">
            <div class="tab-item active" style="flex:1; text-align:center; padding: 15px 0; cursor: pointer;" onclick="switchOrderTab(this, 'riwayat')">Riwayat Order</div>
            <div class="tab-item" style="flex:1; text-align:center; padding: 15px 0; cursor: pointer;" onclick="switchOrderTab(this, 'pengembalian')">Pengembalian</div>
        </div>

        <div id="riwayat" class="tab-content active" style="padding: 15px;">
            <div id="orderListContainer">
            </div>
        </div>

        <div id="pengembalian" class="tab-content" style="padding: 50px 20px; text-align: center;">
            <p style="color: #888; font-size: 14px;">Belum ada pengajuan pengembalian alat aktif.</p>
        </div>

        <div class="bottom-nav">
            <a href="home.php" class="nav-item active"><span>🏠</span>Beranda</a>
            <a href="order.php" class="nav-item"><span>📋</span>Order</a>
            <a href="profile.php" class="nav-item"><span>👤</span>Saya</a>
        </div>
    </div>

    <script>
        function switchOrderTab(element, tabId) {
            document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        // Fungsi buka Google Maps & ubah status jadi "Selesai"
        function takeOrder(orderId) {
            let orders = JSON.parse(localStorage.getItem('mountsterOrders')) || [];
            let index = orders.findIndex(o => o.id === orderId);

            if (index !== -1) {
                // Ubah status ke Selesai
                orders[index].status = "Pesanan Selesai";
                localStorage.setItem('mountsterOrders', JSON.stringify(orders));

                // Buka tab baru ke Google Maps Summarecon Bekasi
                window.open("https://maps.google.com/?q=Summarecon+Mall+Bekasi", "_blank");

                // Render ulang halamannya biar tombolnya langsung berubah
                renderOrderHistory();
            }
        }

        function renderOrderHistory() {
            const container = document.getElementById('orderListContainer');
            let orders = JSON.parse(localStorage.getItem('mountsterOrders')) || [];
            let now = new Date().getTime();

            if (orders.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; padding: 50px 20px;">
                        <p style="color: #888; font-size: 14px;">Belum ada riwayat pesanan.</p>
                        <a href="home.php" class="btn btn-primary" style="margin-top:20px; display:inline-block; width:auto; padding:10px 25px;">Mulai Sewa Alat</a>
                    </div>`;
                return;
            }

            container.innerHTML = "";

            orders.forEach(order => {
                if (order.status === "Belum Dibayar" && order.deadline && now > order.deadline) {
                    order.status = "Dibatalkan";
                }

                // Default untuk "Dikemas" (Kirim Kurir)
                let statusColor = "#009933";
                let infoBoxContent = `<p style="font-weight: bold;">Estimasi Tiba: 4 Apr - 5 Apr</p><p style="font-size: 12px; margin-top: 3px; color: #666;">Pesanan sedang dikemas</p>`;
                let actionButtons = `<button class="btn-order" disabled style="padding: 8px 15px; border-radius: 6px; font-size: 12px; border: 1px solid #ddd; background: white; color: #ccc; font-weight: bold;">Pesanan Selesai</button>`;

                // LOGIKA KHUSUS "AMBIL DI TOKO"
                if (order.status === "Ambil di Toko") {
                    statusColor = "#11998e"; // Warna tosca biar beda
                    infoBoxContent = `<p style="font-weight: bold; color: #11998e;">Menunggu Pengambilan</p><p style="font-size: 12px; margin-top: 3px; color: #666;">Ambil pesanan di toko (Summarecon Bekasi).</p>`;
                    actionButtons = `<button class="btn-order" onclick="takeOrder('${order.id}')" style="padding: 10px 15px; border-radius: 6px; font-size: 13px; border: none; background: #11998e; color: white; font-weight: bold; cursor: pointer; width: 100%;">📍 Ambil Pesanan (Buka Maps)</button>`;
                }
                // LOGIKA KHUSUS "PESANAN SELESAI"
                else if (order.status === "Pesanan Selesai") {
                    statusColor = "#888";
                    infoBoxContent = `<p style="font-weight: bold; color: #888;">Barang Diterima</p><p style="font-size: 12px; margin-top: 3px; color: #666;">Silakan kembalikan barang tepat waktu.</p>`;
                    actionButtons = `<button class="btn-order" disabled style="padding: 8px 15px; border-radius: 6px; font-size: 12px; border: 1px solid #ddd; background: white; color: #ccc; font-weight: bold; width: 100%;">Pesanan Selesai</button>`;
                }
                // LOGIKA "BELUM DIBAYAR"
                else if (order.status === "Belum Dibayar") {
                    statusColor = "#ff4d4f";
                    infoBoxContent = `<p style="font-weight: bold; color: #ff4d4f;">Menunggu Pembayaran</p><p style="font-size: 12px; margin-top: 3px; color: #666;">Selesaikan pembayaran sebelum batas waktu habis.</p>`;
                    actionButtons = `<button class="btn-order" onclick="window.location.href='payment.php?id=${order.id}'" style="padding: 8px 15px; border-radius: 6px; font-size: 12px; border: none; background: #ff4d4f; color: white; font-weight: bold; cursor: pointer; width: 100%;">Bayar Sekarang</button>`;
                }
                // LOGIKA "DIBATALKAN"
                else if (order.status === "Dibatalkan") {
                    statusColor = "#888";
                    infoBoxContent = `<p style="font-weight: bold; color: #888;">Pesanan Dibatalkan</p><p style="font-size: 12px; margin-top: 3px; color: #666;">Batas waktu pembayaran telah habis.</p>`;
                    actionButtons = `<button class="btn-order" disabled style="padding: 8px 15px; border-radius: 6px; font-size: 12px; border: 1px solid #ddd; background: white; color: #ccc; font-weight: bold; width: 100%;">Dibatalkan</button>`;
                }

                let itemsHTML = "";
                if (order.items && Array.isArray(order.items)) {
                    order.items.forEach(item => {
                        itemsHTML += `
                            <div style="display: flex; gap: 15px; margin-top: 10px;">
                                <div style="width: 60px; height: 60px; border-radius: 8px; display:flex; justify-content:center; align-items:center; overflow:hidden; border: 1px solid #eee;">
                                    <img src="logo_mountster.png" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="font-size: 14px; font-weight: bold; margin-bottom: 3px;">${item.name}</h4>
                                    <p style="text-align: right; font-weight: bold; margin-top: 5px; font-size: 13px;">${item.price} <span style="font-weight: normal; color: #888;">x${item.qty}</span></p>
                                </div>
                            </div>
                        `;
                    });
                }

                let totalSewaRp = order.totalPay ? `Rp ${order.totalPay.toLocaleString('id-ID')}` : (order.price || "Rp 0");
                let invoiceId = order.id ? order.id : "INV" + Date.now();

                container.innerHTML += `
                    <div class="order-card" style="background: white; border-radius: 12px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <div style="border-bottom: 1px dashed #eee; padding-bottom: 10px; margin-bottom: 10px;">
                            <span style="font-size: 11px; color: #888;">${invoiceId}</span>
                            <span class="status-badge" style="color: ${statusColor}; font-size: 12px; font-weight: bold; float: right;">${order.status}</span>
                        </div>
                        
                        ${itemsHTML}
                        
                        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 13px; color: #888;">Total Pesanan:</span>
                            <span style="font-size: 14px; font-weight: bold; color: var(--primary);">${totalSewaRp}</span>
                        </div>
                        
                        <div class="order-info-box" style="background: ${order.status === 'Belum Dibayar' ? '#fff1f0' : (['Dibatalkan', 'Pesanan Selesai'].includes(order.status) ? '#f5f5f5' : '#f4fbf4')}; border-radius: 8px; padding: 12px; margin-top: 10px; font-size: 13px; border: 1px solid ${order.status === 'Belum Dibayar' ? '#ffa39e' : (['Dibatalkan', 'Pesanan Selesai'].includes(order.status) ? '#ddd' : (order.status === 'Ambil di Toko' ? '#a7e8e1' : '#e0f2e0'))};">
                            ${infoBoxContent}
                        </div>

                        <div class="order-btn-group" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 15px;">
                            ${actionButtons}
                        </div>
                    </div>
                `;
            });

            localStorage.setItem('mountsterOrders', JSON.stringify(orders));
        }

        renderOrderHistory();
    </script>

</body>

</html>