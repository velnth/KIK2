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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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

        /* Modal & QRIS CSS (Sama dengan Checkout) */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: none; align-items: center; justify-content: center; z-index: 9000; backdrop-filter: blur(4px); }
        .modal-overlay.active { display: flex; }
        .modal-content { background: white; border-radius: 12px; width: 90%; max-width: 400px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); animation: modalFadeIn 0.3s ease; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px; }
        .modal-close { font-size: 24px; color: #888; cursor: pointer; font-weight: bold; line-height: 1; }
        @keyframes modalFadeIn { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .qris-box { text-align: center; padding: 10px 0; }
        .qris-code-wrap { width: 240px; height: 240px; margin: 18px auto; background: #fff; border-radius: 20px; padding: 20px; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08); display: flex; align-items: center; justify-content: center; }
        .qris-code-wrap img, .qris-code-wrap canvas { max-width: 100%; max-height: 100%; }
        .qris-amount { font-size: 30px; font-weight: 800; color: var(--primary); margin-top: 8px; }
        .qris-subtext { font-size: 13px; color: var(--text-muted); line-height: 1.5; max-width: 280px; margin: 12px auto 0; }
        .qris-chip { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; background: #f4fbf4; border-radius: 999px; color: var(--primary); font-size: 12px; font-weight: 700; }
        .qris-chip-dot { width: 8px; height: 8px; border-radius: 50%; background: #2eb872; }
        .qris-meta { background: #f7faf8; border: 1px solid #e5efea; border-radius: 16px; padding: 14px; margin-top: 16px; text-align: left; }
        .qris-meta-row { display: flex; justify-content: space-between; gap: 12px; font-size: 13px; margin-bottom: 8px; }
        .qris-meta-row:last-child { margin-bottom: 0; }
        .qris-meta-row span:first-child { color: var(--text-muted); }
        .qris-meta-row span:last-child { color: var(--text-main); font-weight: 700; text-align: right; }
        .qris-expired { color: #d9534f; font-weight: 700; }
        .qris-loading { width: 56px; height: 56px; border-radius: 50%; border: 5px solid #dbe7df; border-top-color: var(--primary); margin: 18px auto 0; animation: qrisSpin 0.8s linear infinite; }
        .success-shell { text-align: center; padding: 10px 0 5px; }
        .success-ring { width: 92px; height: 92px; border-radius: 50%; margin: 5px auto 18px; background: linear-gradient(135deg, #dff7ea, #effcf4); display: flex; align-items: center; justify-content: center; }
        .success-check { width: 48px; height: 24px; border-left: 6px solid #1fa65a; border-bottom: 6px solid #1fa65a; transform: rotate(-45deg) scale(0.3); opacity: 0; animation: checkPop 0.45s ease forwards; animation-delay: 0.1s; }
        .success-title { font-size: 24px; font-weight: 800; color: var(--text-main); margin-bottom: 8px; }
        .success-caption { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }

        @keyframes qrisSpin { to { transform: rotate(360deg); } }
        @keyframes checkPop { from { transform: rotate(-45deg) scale(0.3); opacity: 0; } to { transform: rotate(-45deg) scale(1); opacity: 1; } }
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
            <a href="home.php" class="nav-item"><span>🏠</span>Beranda</a>
            <a href="order.php" class="nav-item active"><span>📋</span>Order</a>
            <a href="profile.php" class="nav-item"><span>👤</span>Saya</a>
        </div>
    </div>

    <div class="modal-overlay" id="qrisModal">
        <div class="modal-content" style="max-width: 360px; padding: 22px;">
            <div class="modal-header" style="padding: 0 0 18px; border-bottom: none;">
                <h3 style="font-size: 18px; flex: 1; text-align: center;">QRIS Demo Payment</h3>
                <span class="modal-close" id="qrisModalClose" onclick="closeQrisModal()">×</span>
            </div>
            <div id="qrisModalBody" class="qris-box">
                </div>
        </div>
    </div>

    <script>
        function switchOrderTab(element, tabId) {
            document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        // --- MANAJEMEN QRIS DI ORDER.PHP ---
        let qrisStatusPollingTimer = null;
        let qrisExpiryTimer = null;
        let qrisCountdownTimer = null;
        let currentQrisOrderId = null;
        let currentQrisExpiryAt = null;
        let currentQrisPaymentToken = null;
        let currentQrisAmount = 0;
        let currentQrisPaymentUrl = '';
        let currentServerBaseUrl = '';

        function getApiBaseUrl() { return `${window.location.protocol}//${window.location.hostname}:3000`; }
        async function initializeServerBaseUrl() { currentServerBaseUrl = window.location.origin; }
        function createDemoPaymentToken() { return Math.random().toString(36).slice(2, 10).toUpperCase(); }
        function formatRupiah(amount) { return `Rp ${amount.toLocaleString('id-ID')}`; }

        async function openQrisModalForOrder(orderId) {
            let orders = JSON.parse(localStorage.getItem('mountsterOrders')) || [];
            let order = orders.find(o => o.id === orderId);
            if(!order) return;

            currentQrisOrderId = orderId;
            currentQrisAmount = order.grandTotal;
            currentQrisPaymentToken = createDemoPaymentToken();
            currentQrisExpiryAt = Date.now() + (10 * 60 * 1000); // 10 Menit masa aktif barcode 1 sesi

            document.getElementById('qrisModal').classList.add('active');
            showQrisLoadingState();

            try {
                await initializeServerBaseUrl();
                currentQrisPaymentUrl = `${currentServerBaseUrl || getApiBaseUrl()}/api/payment/confirm-direct?orderId=${encodeURIComponent(currentQrisOrderId)}&token=${encodeURIComponent(currentQrisPaymentToken)}&amount=${encodeURIComponent(currentQrisAmount)}`;
                renderQrisWaitingState();
                startQrisExpiryCountdown();
                startQrisStatusPolling();
            } catch (error) { 
                showQrisErrorState('QRIS gagal dimuat.'); 
            }
        }

        function closeQrisModal() {
            if (qrisCountdownTimer) clearInterval(qrisCountdownTimer);
            stopQrisRealtimeChecks();
            currentQrisOrderId = null; currentQrisExpiryAt = null; currentQrisPaymentToken = null; currentQrisAmount = 0; currentQrisPaymentUrl = '';
            document.getElementById('qrisModal').classList.remove('active');
        }

        function renderQrisWaitingState() {
            const isLocalhostHost = ['localhost', '127.0.0.1'].includes(window.location.hostname);
            const networkHint = isLocalhostHost ? `<p class="qris-subtext" style="margin-top:12px; color:#d9534f;">Buka website ini memakai IP LAN yang sama supaya QR bisa discan dari HP lain.</p>` : `<p class="qris-subtext" style="margin-top:12px;">Akses dari HP: ${currentServerBaseUrl || getApiBaseUrl()}</p>`;

            document.getElementById('qrisModalBody').innerHTML = `
                <div class="qris-chip"><span class="qris-chip-dot"></span>Menunggu Pembayaran</div>
                <p class="qris-amount">${formatRupiah(currentQrisAmount)}</p>
                <div class="qris-code-wrap"><div id="qrisCode"></div></div>
                <p class="qris-subtext">Scan QR ini dengan kamera HP Anda. Pembayaran otomatis ter-trigger saat halaman scan terbuka di HP.</p>
                <div class="qris-meta">
                    <div class="qris-meta-row"><span>Order ID</span><span id="qrisOrderIdText">${currentQrisOrderId}</span></div>
                    <div class="qris-meta-row"><span>Status</span><span id="qrisStatusText">Menunggu pembayaran</span></div>
                    <div class="qris-meta-row"><span>Berlaku sampai</span><span id="qrisExpiryText">10:00</span></div>
                </div>
                ${networkHint}
                <a href="${currentQrisPaymentUrl}" target="_blank" style="display:block; margin-top:14px; color:var(--primary); font-size:13px; font-weight:700; word-break:break-word;">Buka link hasil scan</a>
            `;

            const qrisCodeEl = document.getElementById('qrisCode');
            if (qrisCodeEl) {
                qrisCodeEl.innerHTML = '';
                if (typeof QRCode === 'function') new QRCode(qrisCodeEl, { text: currentQrisPaymentUrl, width: 200, height: 200 });
            }
        }
        function showQrisLoadingState() { document.getElementById('qrisModalBody').innerHTML = `<div class="qris-chip"><span class="qris-chip-dot"></span>Mempersiapkan QR Demo</div><p class="qris-amount">${formatRupiah(currentQrisAmount)}</p><div class="qris-loading"></div>`; }
        function showQrisErrorState(msg) { document.getElementById('qrisModalBody').innerHTML = `<div class="qris-chip"><span class="qris-chip-dot" style="background:#d9534f;"></span>QRIS Belum Siap</div><p class="qris-amount">${formatRupiah(currentQrisAmount)}</p><p class="qris-subtext">${msg}</p>`; }
        function startQrisExpiryCountdown() { updateQrisExpiryText(); qrisExpiryTimer = setInterval(updateQrisExpiryText, 1000); }
        function updateQrisExpiryText() {
            const expiryEl = document.getElementById('qrisExpiryText'); const statusEl = document.getElementById('qrisStatusText');
            if (!expiryEl || !currentQrisExpiryAt) return;
            const remainingMs = currentQrisExpiryAt - Date.now();
            if (remainingMs <= 0) {
                expiryEl.innerText = 'Expired'; expiryEl.classList.add('qris-expired');
                if (statusEl) statusEl.innerText = 'QR expired';
                stopQrisRealtimeChecks(); return;
            }
            const totalSeconds = Math.floor(remainingMs / 1000);
            expiryEl.innerText = `${String(Math.floor(totalSeconds / 60)).padStart(2, '0')}:${String(totalSeconds % 60).padStart(2, '0')}`;
        }
        async function startQrisStatusPolling() { await checkPaymentStatus(); qrisStatusPollingTimer = setInterval(checkPaymentStatus, 3000); }
        async function checkPaymentStatus() {
            if (!currentQrisOrderId) return;
            try {
                const response = await fetch(`${getApiBaseUrl()}/api/payment/status?orderId=${encodeURIComponent(currentQrisOrderId)}`, { cache: 'no-store' });
                if (!response.ok) return;
                const data = await response.json();
                const statusEl = document.getElementById('qrisStatusText');
                if (statusEl) statusEl.innerText = data.status === 'paid' ? 'Pembayaran terdeteksi' : (data.status === 'expired' ? 'QR expired' : 'Menunggu pembayaran');
                if (data.status === 'paid') { stopQrisRealtimeChecks(); showQrisSuccess(); }
                else if (data.status === 'expired') { stopQrisRealtimeChecks(); }
            } catch (error) { }
        }
        function stopQrisRealtimeChecks() {
            if (qrisStatusPollingTimer) clearInterval(qrisStatusPollingTimer);
            if (qrisExpiryTimer) clearInterval(qrisExpiryTimer);
            qrisStatusPollingTimer = null; qrisExpiryTimer = null;
        }

        function showQrisSuccess() {
            stopQrisRealtimeChecks();
            updateOrderStatusToPaid(currentQrisOrderId);

            document.getElementById('qrisModalBody').innerHTML = `
                <div class="success-shell">
                    <div class="success-ring"><div class="success-check"></div></div>
                    <h3 class="success-title">Pembayaran Berhasil!</h3>
                    <p class="success-caption">Pesananmu sedang diproses. Terima kasih!</p>
                    <button class="btn btn-primary" type="button" style="margin-top: 16px;" onclick="closeQrisModalAndRefresh()">Tutup & Refresh</button>
                </div>
            `;
        }

        function closeQrisModalAndRefresh() {
            closeQrisModal();
            renderOrderHistory();
        }

        function updateOrderStatusToPaid(orderId) {
            let existingOrders = JSON.parse(localStorage.getItem('mountsterOrders')) || [];
            const idx = existingOrders.findIndex(o => String(o.id) === String(orderId));
            if (idx !== -1) {
                existingOrders[idx].status = 'Dikemas'; 
                existingOrders[idx].paidAt = new Date().toISOString();
                localStorage.setItem('mountsterOrders', JSON.stringify(existingOrders));
            }
        }

        // Fungsi buka Google Maps & ubah status jadi "Selesai"
        function takeOrder(orderId) {
            let orders = JSON.parse(localStorage.getItem('mountsterOrders')) || [];
            let index = orders.findIndex(o => o.id === orderId);

            if (index !== -1) {
                orders[index].status = "Pesanan Selesai";
                localStorage.setItem('mountsterOrders', JSON.stringify(orders));
                window.open("https://maps.google.com/?q=Summarecon+Mall+Bekasi", "_blank");
                renderOrderHistory();
            }
        }

        function renderOrderHistory() {
            const container = document.getElementById('orderListContainer');
            let orders = JSON.parse(localStorage.getItem('mountsterOrders')) || [];
            let now = Date.now();
            let needsUpdate = false;

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
                // Update ke Dibatalkan jika waktu lewat
                if (order.status === "Belum Dibayar" && order.deadline && now > order.deadline) {
                    order.status = "Dibatalkan";
                    needsUpdate = true;
                }

                let statusColor = "#009933";
                let infoBoxContent = `<p style="font-weight: bold;">Estimasi Tiba: ${order.estimatedArrival || 'Hari Ini'}</p><p style="font-size: 12px; margin-top: 3px; color: #666;">Pesanan sedang dikemas</p>`;
                let actionButtons = `<button class="btn-order" disabled style="padding: 8px 15px; border-radius: 6px; font-size: 12px; border: 1px solid #ddd; background: white; color: #ccc; font-weight: bold;">Pesanan Selesai</button>`;

                if (order.status === "Ambil di Toko") {
                    statusColor = "#11998e"; 
                    infoBoxContent = `<p style="font-weight: bold; color: #11998e;">Menunggu Pengambilan</p><p style="font-size: 12px; margin-top: 3px; color: #666;">Ambil pesanan di toko (Summarecon Bekasi).</p>`;
                    actionButtons = `<button class="btn-order" onclick="takeOrder('${order.id}')" style="padding: 10px 15px; border-radius: 6px; font-size: 13px; border: none; background: #11998e; color: white; font-weight: bold; cursor: pointer; width: 100%;">📍 Ambil Pesanan (Buka Maps)</button>`;
                }
                else if (order.status === "Pesanan Selesai") {
                    statusColor = "#888";
                    infoBoxContent = `<p style="font-weight: bold; color: #888;">Barang Diterima</p><p style="font-size: 12px; margin-top: 3px; color: #666;">Silakan kembalikan barang tepat waktu.</p>`;
                    actionButtons = `<button class="btn-order" disabled style="padding: 8px 15px; border-radius: 6px; font-size: 12px; border: 1px solid #ddd; background: white; color: #ccc; font-weight: bold; width: 100%;">Pesanan Selesai</button>`;
                }
                else if (order.status === "Belum Dibayar") {
                    statusColor = "#ff4d4f";
                    infoBoxContent = `<p style="font-weight: bold; color: #ff4d4f;">Menunggu Pembayaran</p><p style="font-size: 12px; margin-top: 3px; color: #666;">Batas bayar: <span id="countdown-${order.id}" style="color:#ff4d4f; font-weight:bold;">Memuat...</span></p>`;
                    actionButtons = `<button class="btn-order" onclick="openQrisModalForOrder('${order.id}')" style="padding: 8px 15px; border-radius: 6px; font-size: 12px; border: none; background: #ff4d4f; color: white; font-weight: bold; cursor: pointer; width: 100%;">Bayar Sekarang</button>`;
                }
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
                                    <img src="${item.image || 'logo_mountster.png'}" alt="Item" style="width: 100%; height: 100%; object-fit: contain;">
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="font-size: 14px; font-weight: bold; margin-bottom: 3px;">${item.name}</h4>
                                    <p style="text-align: right; font-weight: bold; margin-top: 5px; font-size: 13px;">${item.price} <span style="font-weight: normal; color: #888;">x${item.qty}</span></p>
                                </div>
                            </div>
                        `;
                    });
                }

                let totalSewaRp = order.grandTotal ? `Rp ${order.grandTotal.toLocaleString('id-ID')}` : (order.price || "Rp 0");
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

            if (needsUpdate) {
                localStorage.setItem('mountsterOrders', JSON.stringify(orders));
            }
        }

        // Loop Global untuk mengupdate teks hitung mundur (hanya UI tanpa harus re-render ulang HTML)
        setInterval(() => {
            let orders = JSON.parse(localStorage.getItem('mountsterOrders')) || [];
            let now = Date.now();
            let needsRender = false;

            orders.forEach(order => {
                if (order.status === "Belum Dibayar" && order.deadline) {
                    let remaining = order.deadline - now;
                    if (remaining <= 0) {
                        order.status = "Dibatalkan";
                        needsRender = true; // Refresh UI karena status berubah total
                    } else {
                        let el = document.getElementById(`countdown-${order.id}`);
                        if (el) {
                            let h = Math.floor(remaining / (1000 * 60 * 60));
                            let m = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
                            let s = Math.floor((remaining % (1000 * 60)) / 1000);
                            el.innerText = `${h}j ${m}m ${s}d`;
                        }
                    }
                }
            });

            if (needsRender) {
                localStorage.setItem('mountsterOrders', JSON.stringify(orders));
                renderOrderHistory();
            }
        }, 1000);

        // Render saat pertama kali dimuat
        renderOrderHistory();
    </script>

</body>

</html>