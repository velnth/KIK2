<?php
include 'auth.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountster - Checkout</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        /* CSS Tambahan Khusus Checkout */
        .payment-option-note {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
            line-height: 1.5;
        }

        #mapContainer {
            height: 200px;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
            z-index: 1;
        }

        .checkout-wrapper {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 20px;
        }

        @media (min-width: 768px) {
            .checkout-wrapper {
                grid-template-columns: 2fr 1fr;
                align-items: start;
            }
        }

        .checkout-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }

        .checkout-card-title {
            font-size: 14px;
            font-weight: bold;
            color: var(--text-muted);
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .custom-radio {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            cursor: pointer;
        }

        .custom-radio input[type="radio"] {
            accent-color: var(--primary);
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .qris-box {
            text-align: center;
            padding: 10px 0;
        }

        .qris-code-wrap {
            width: 240px;
            height: 240px;
            margin: 18px auto;
            background: #fff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qris-code-wrap img,
        .qris-code-wrap canvas {
            max-width: 100%;
            max-height: 100%;
        }

        .qris-amount {
            font-size: 30px;
            font-weight: 800;
            color: var(--primary);
            margin-top: 8px;
        }

        .qris-subtext {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
            max-width: 280px;
            margin: 12px auto 0;
        }

        .qris-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: #f4fbf4;
            border-radius: 999px;
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
        }

        .qris-chip-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2eb872;
        }

        .qris-meta {
            background: #f7faf8;
            border: 1px solid #e5efea;
            border-radius: 16px;
            padding: 14px;
            margin-top: 16px;
            text-align: left;
        }

        .qris-meta-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .qris-meta-row:last-child {
            margin-bottom: 0;
        }

        .qris-meta-row span:first-child {
            color: var(--text-muted);
        }

        .qris-meta-row span:last-child {
            color: var(--text-main);
            font-weight: 700;
            text-align: right;
        }

        .qris-expired {
            color: #d9534f;
            font-weight: 700;
        }

        .qris-loading {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 5px solid #dbe7df;
            border-top-color: var(--primary);
            margin: 18px auto 0;
            animation: qrisSpin 0.8s linear infinite;
        }

        .success-shell {
            text-align: center;
            padding: 10px 0 5px;
        }

        .success-ring {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            margin: 5px auto 18px;
            background: linear-gradient(135deg, #dff7ea, #effcf4);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-check {
            width: 48px;
            height: 24px;
            border-left: 6px solid #1fa65a;
            border-bottom: 6px solid #1fa65a;
            transform: rotate(-45deg) scale(0.3);
            opacity: 0;
            animation: checkPop 0.45s ease forwards;
            animation-delay: 0.1s;
        }

        .success-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .success-caption {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .success-card {
            background: #f7faf8;
            border: 1px solid #e5efea;
            border-radius: 16px;
            padding: 16px;
            text-align: left;
            margin-bottom: 18px;
        }

        .success-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .success-row:last-child {
            margin-bottom: 0;
        }

        .success-row span:first-child {
            color: var(--text-muted);
        }

        .success-row span:last-child {
            font-weight: 700;
            color: var(--text-main);
            text-align: right;
        }

        .success-countdown {
            color: var(--primary);
            font-weight: 700;
            font-size: 13px;
        }

        @keyframes qrisSpin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes checkPop {
            from {
                transform: rotate(-45deg) scale(0.3);
                opacity: 0;
            }

            to {
                transform: rotate(-45deg) scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="app-container" style="max-width: 1000px;">
        <div class="p-20 flex-between" style="background: white; border-bottom: 1px solid #eee;">
            <a href="javascript:history.back()" style="text-decoration: none; color: black; font-size: 20px;">←</a>
            <h2 style="font-size: 20px;">Checkout</h2>
            <div style="width: 20px;"></div>
        </div>

        <div class="checkout-wrapper">
            <div>
                <div class="checkout-card">
                    <div class="checkout-card-title">Alamat Pengiriman</div>
                    <div id="addressDisplay"></div>
                </div>

                <div class="checkout-card">
                    <p style="font-weight: bold; margin-bottom: 15px;">⛺ Mountster Rental Store</p>
                    <div id="checkoutItemsContainer"></div>

                    <div style="margin-top: 20px; border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                        <p style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">Pilih Pengiriman</p>
                        <select class="input-form" style="padding: 10px; font-weight: bold; width: 100%;" id="shippingSelect" onchange="updateSummary()">
                            <option value="78000" data-desc="Estimasi tiba hari ini - besok, maks. 14:00 WIB">Instant (Rp78.000)</option>
                            <option value="40000" data-desc="Estimasi tiba hari ini - besok, maks. 23:00 WIB">Sameday (Rp40.000)</option>
                            <option value="20000" data-desc="Estimasi tiba besok, maks. 23:00 WIB">Next Day (Rp20.000)</option>
                            <option value="0" data-desc="Barang diambil langsung ke toko kami.">Ambil di Toko (Gratis)</option>
                        </select>
                        <p id="shippingDesc" style="font-size: 12px; color: var(--text-muted); margin-top: 8px;">Estimasi tiba hari ini - besok, maks. 14:00 WIB</p>
                    </div>
                </div>
            </div>

            <div>
                <div class="checkout-card">
                    <div class="flex-between" style="margin-bottom: 10px;">
                        <div class="checkout-card-title" style="margin: 0;">Metode Pembayaran</div>
                        <span style="color: var(--primary); font-size: 14px; font-weight: bold; cursor: pointer;" onclick="openPaymentModal()">Lihat Semua</span>
                    </div>

                    <div id="selectedPaymentContainer" onclick="openPaymentModal()" style="cursor: pointer; padding-top: 5px;">
                    </div>
                </div>

                <div class="checkout-card">
                    <div class="checkout-card-title">Ringkasan Transaksi</div>
                    <div class="flex-between" style="margin-bottom: 10px; font-size: 14px; color: var(--text-muted);">
                        <span id="summaryTotalItems">Total Harga (0 Barang)</span>
                        <span id="summaryItemPrice">Rp0</span>
                    </div>
                    <div class="flex-between" style="margin-bottom: 15px; font-size: 14px; color: var(--text-muted);">
                        <span>Total Ongkos Kirim</span>
                        <span id="summaryShippingPrice">Rp78.000</span>
                    </div>
                    <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 15px;">
                    <div class="flex-between" style="margin-bottom: 20px;">
                        <span style="font-weight: bold; font-size: 16px;">Total Tagihan</span>
                        <span id="summaryGrandTotal" style="font-weight: bold; font-size: 18px; color: var(--primary);">Rp0</span>
                    </div>
                    <button class="btn btn-primary" onclick="processPayment()">Buat Pesanan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="addressModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle" style="font-size: 18px; flex: 1; text-align: center;">Daftar Alamat</h3>
                <span class="modal-close" onclick="closeAddressModal()">×</span>
            </div>
            <div class="modal-body" id="addressListView">
                <button style="width: 100%; padding: 12px; background: white; border: 1px solid var(--primary); color: var(--primary); border-radius: 8px; font-weight: bold; cursor: pointer; margin-bottom: 20px;" onclick="showAddForm()">+ Tambah Alamat Baru</button>
                <div id="addressListContainer"></div>
            </div>
            <div class="modal-body" id="addressFormView" style="display: none;">
                <input type="text" id="mapSearchInput" class="input-form" placeholder="Cari lokasi di peta (Tekan Enter)..." style="margin-bottom: 10px;">
                <div id="mapContainer"></div>
                <div class="input-group"><label>Kontak (Nama Penerima)</label><input type="text" id="formName" class="input-form" placeholder="Contoh: Halena"></div>
                <div class="input-group"><label>Nomor Telepon</label><input type="number" id="formPhone" class="input-form" placeholder="Contoh: 6281234567"></div>
                <div class="input-group"><label>Alamat Lengkap</label><textarea id="formDetail" class="input-form" rows="3" placeholder="Masukkan alamat lengkap..."></textarea></div>
                <button class="btn btn-primary" onclick="saveNewAddress()">Simpan Alamat Baru</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="confirmDeleteModal">
        <div class="modal-content" style="max-width: 350px; padding: 25px; text-align: center; border-radius: 15px; margin: 0 auto;">
            <div style="font-size: 40px; margin-bottom: 10px;">🗑️</div>
            <h3 style="margin-bottom: 10px; font-size: 18px;">Hapus Alamat?</h3>
            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 25px;">Apakah Anda yakin ingin menghapus alamat ini? Data yang dihapus tidak bisa dikembalikan.</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button class="btn" style="margin: 0; flex: 1; background: transparent; border: 1px solid #ccc; color: #555;" onclick="closeConfirmModal()">Batal</button>
                <button class="btn" style="margin: 0; flex: 1; background-color: #ff4d4f; color: white; border: none;" onclick="executeDeleteAddress()">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="alertModal">
        <div class="modal-content" style="max-width: 350px; padding: 25px; text-align: center; border-radius: 15px; margin: 0 auto;">
            <div style="font-size: 40px; margin-bottom: 10px;">⚠️</div>
            <h3 style="margin-bottom: 10px; font-size: 18px;">Perhatian</h3>
            <p id="alertModalMessage" style="color: var(--text-muted); font-size: 14px; margin-bottom: 25px;">Pesan peringatan akan muncul di sini.</p>
            <button class="btn btn-primary" style="margin: 0; width: 100%;" onclick="closeAlertModal()">OK</button>
        </div>
    </div>

    <div class="modal-overlay" id="paymentModal">
        <div class="modal-content" style="max-height: 85vh; display: flex; flex-direction: column;">
            <div class="modal-header">
                <h3 style="font-size: 18px; flex: 1; text-align: center;">Metode Pembayaran</h3>
                <span class="modal-close" onclick="closePaymentModal()">×</span>
            </div>
            <div class="modal-body" style="overflow-y: auto; padding: 20px;">

                <label class="custom-radio" style="border-bottom: 1px solid #eee; padding-bottom: 15px;">
                    <span><b>QRIS</b></span>
                    <input type="radio" name="modal_payment" value="QRIS" onclick="selectPayment('<b>QRIS</b>', 'QRIS', false)">
                </label>

                <label class="custom-radio" style="border-top: 1px solid #eee; padding-top: 15px;">
                    <span>
                        <b>COD (Bayar di Tempat)</b>
                        <div class="payment-option-note">Bayar tunai saat pesanan diterima kurir atau saat ambil di toko.</div>
                    </span>
                    <input type="radio" name="modal_payment" value="COD" onclick="selectPayment('COD (Bayar di Tempat)', 'COD', true)">
                </label>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="qrisModal">
        <div class="modal-content" style="max-width: 360px; padding: 22px;">
            <div class="modal-header" style="padding: 0 0 18px; border-bottom: none;">
                <h3 style="font-size: 18px; flex: 1; text-align: center;">QRIS Demo Payment</h3>
                <span class="modal-close" onclick="closeQrisModal()">x</span>
            </div>
            <div id="qrisModalBody" class="qris-box">
                <div class="qris-chip"><span class="qris-chip-dot"></span>Menunggu Pembayaran</div>
                <p class="qris-amount" id="qrisAmountText">Rp0</p>
                <div class="qris-code-wrap">
                    <div id="qrisCode"></div>
                </div>
                <p class="qris-subtext">Scan QR ini menggunakan kamera HP untuk membuka halaman pembayaran demo. Setelah tombol bayar di HP ditekan, status di web ini akan otomatis berubah.</p>
                <div class="qris-meta">
                    <div class="qris-meta-row"><span>Order ID</span><span id="qrisOrderIdText">-</span></div>
                    <div class="qris-meta-row"><span>Status</span><span id="qrisStatusText">Menunggu pembayaran</span></div>
                    <div class="qris-meta-row"><span>Berlaku sampai</span><span id="qrisExpiryText">10:00</span></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- 1. INISIALISASI DATA ---
        let addresses = JSON.parse(localStorage.getItem('mountsterAddresses')) || [];
        let selectedAddressId = localStorage.getItem('mountsterSelectedAddress') || null;
        let pendingDeleteId = null;

        let cartToCheckout = [];
        let totalItemPrice = 0;
        let qrisTransactionId = null;
        let qrisCountdownTimer = null;
        let qrisStatusPollingTimer = null;
        let qrisExpiryTimer = null;
        let currentQrisOrderId = null;
        let currentQrisExpiryAt = null;
        let currentQrisPaymentToken = null;
        let currentQrisAmount = 0;
        let currentQrisPaymentUrl = '';
        let currentServerBaseUrl = '';

        function getApiBaseUrl() {
            if (window.location.port === '5500') {
                return `${window.location.protocol}//${window.location.hostname}:3000`;
            }
            return window.location.origin;
        }

        async function initializeServerBaseUrl() {
            try {
                const response = await fetch(`${getApiBaseUrl()}/api/server-info`, {
                    cache: 'no-store'
                });
                if (!response.ok) throw new Error('server-info gagal dibaca');
                const data = await response.json();
                currentServerBaseUrl = data.baseUrl || getApiBaseUrl();
            } catch (error) {
                currentServerBaseUrl = getApiBaseUrl();
            }
        }

        function createDemoPaymentToken() {
            return Math.random().toString(36).slice(2, 10).toUpperCase();
        }

        function getProductImage(productName) {
            if (productName === 'Eiger Wanderlust 60') return 'images/eiger-wanderlust-60.jpeg';
            if (productName === 'Consina Magnum 4') return 'images/consina-magnum-4.jpeg';
            if (productName === 'Great Outdoor Java 4') return 'images/great-outdoor-java-4.jpeg';
            if (productName === 'Naturehike Cloud Up 2') return 'images/naturehike-cloud-up-2.jpeg';
            if (productName === 'Antarestar') return 'images/antarestar.png';
            if (productName === 'Merapi Mountain Half Moon') return 'images/merapi-mountain-half-moon.jpeg';
            if (productName === 'Tenda Pramuka Regu') return 'images/tenda-pramuka-regu.jpg';
            if (productName === 'Tenda Dome 2 Orang') return 'images/tenda-dome-2-orang.jpg';
            if (productName === 'Naturehike Village 5') return 'images/naturehike-village-5.jpg';
            if (productName === 'Eiger Shira 1P') return 'images/eiger-shira-1p.jpg';
            if (productName === 'Osprey Aether 65L') return 'images/osprey-aether-65l.jpg';
            if (productName === 'Deuter Futura Pro 40') return 'images/deuter-futura-pro-40.jpg';
            if (productName === 'Eiger Eliptic Solaris 65L') return 'images/eiger-eliptic-solaris-65l.jpg';
            if (productName === 'Consina Tarebbi 60L') return 'images/consina-tarebbi-60l.jpg';
            if (productName === 'Arei Ramandika 60L') return 'images/arei-ramandika-60l.jpg';
            if (productName === 'Eiger Rhinos 60L') return 'images/eiger-rhinos-60l.jpg';
            if (productName === 'Osprey Ariel 55L (Women)') return 'images/osprey-ariel-55l-women.jpg';
            if (productName === 'Consina Extraterrestrial 60L') return 'images/consina-extraterrestrial-60l.jpg';
            if (productName === 'Deuter Aircontact 50+10') return 'images/deuter-aircontact-50plus10.jpg';
            if (productName === 'Naturehike Rock 60L') return 'images/naturehike-rock-60l.jpg';
            if (productName === 'Salomon Quest 4 GTX') return 'images/salomon-quest-4-gtx.jpg';
            if (productName === 'Eiger Pollock') return 'images/eiger-pollock.jpg';
            if (productName === 'Consina Alpine') return 'images/consina-alpine.jpg';
            if (productName === 'SNTA 471') return 'images/snta-471.jpg';
            if (productName === 'La Sportiva TX4') return 'images/la-sportiva-tx4.jpg';
            if (productName === 'Merrell Moab 3') return 'images/merrell-moab-3.jpg';
            if (productName === 'Eiger Anaconda') return 'images/eiger-anaconda.jpg';
            if (productName === 'Columbia Newton Ridge') return 'images/columbia-newton-ridge.png';
            if (productName === 'Arei Outdoorgear Shoes') return 'images/arei-outdoorgear-shoes.jpg';
            if (productName === 'Karrimor Bodmin') return 'images/karrimor-bodmin.jpg';
            if (productName === 'Kompor Portable Kotak') return 'images/kompor-portable-kotak.jpg';
            if (productName === 'Trangia 27-1 UL') return 'images/trangia-27-1-ul.jpg';
            if (productName === 'Nesting Bulat 4 in 1') return 'images/nesting-bulat-4-in-1.png';
            if (productName === 'Nesting Kotak TNI') return 'images/nesting-kotak-tni.jpg';
            if (productName === 'Kompor Mawar (Windproof)') return 'images/kompor-mawar-windproof.jpg';
            if (productName === 'Panci Lipat Naturehike') return 'images/panci-lipat-naturehike.png';
            if (productName === 'Gas Kaleng Hi-Cook') return 'images/gas-kaleng-hi-cook.jpg';
            if (productName === 'Windshield (Pelindung Angin)') return 'images/windshield-pelindung-angin.jpg';
            if (productName === 'Jerigen Air Lipat 5L') return 'images/jerigen-air-lipat-5l.jpg';
            if (productName === 'Set Alat Makan (Sendok Garpu Pisau)') return 'images/set-alat-makan-sendok-garpu-pisau.jpg';
            if (productName === 'Jaket Eiger Tropic') return 'images/jaket-eiger-tropic.jpg';
            if (productName === 'Celana Sambung Consina') return 'images/celana-sambung-consina.jpg';
            if (productName === 'Jas Hujan Arei Ponco') return 'images/jas-hujan-arei-ponco.jpg';
            if (productName === 'Base Layer Thermal') return 'images/base-layer-thermal.jpg';
            if (productName === 'Kupluk Rajut (Beanie)') return 'images/kupluk-rajut-beanie.jpg';
            if (productName === 'Sarung Tangan Polar') return 'images/sarung-tangan-polar.jpg';
            if (productName === 'Jaket Bulang (Down Jacket)') return 'images/jaket-bulang-down-jacket.jpg';
            if (productName === 'Kaos Kaki Trekking Tebal') return 'images/kaos-kaki-trekking-tebal.jpg';
            if (productName === 'Gaiter Anti Pacet') return 'images/gaiter-anti-pacet.jpg';
            if (productName === 'Topi Rimba Eiger') return 'images/topi-rimba-eiger.jpg';
            return '';
        }

        // Cek "Beli Langsung" atau "Keranjang"
        let directBuy = JSON.parse(localStorage.getItem('mountsterDirectBuy'));
        if (directBuy) {
            cartToCheckout = directBuy;
            localStorage.removeItem('mountsterDirectBuy');
        } else {
            cartToCheckout = JSON.parse(localStorage.getItem('mountsterCart')) || [];
        }

        // --- Variabel Pembayaran (Kosong secara default) ---
        let finalPaymentMethod = null;
        let finalPaymentDisplayName = null;
        let isFinalPaymentCOD = false;

        // --- 2. RENDER PRODUK & RINGKASAN ---
        function loadCheckout() {
            if (cartToCheckout.length === 0) {
                window.location.href = "home.php";
                return;
            }

            const container = document.getElementById('checkoutItemsContainer');
            container.innerHTML = "";
            let totalItems = 0;
            totalItemPrice = 0;

            cartToCheckout.forEach(item => {
                let priceNumber = parseInt(item.price.replace(/[^0-9]/g, '')) || 0;
                totalItems += item.qty;
                totalItemPrice += (priceNumber * item.qty);

                container.innerHTML += `
                    <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 60px; height: 60px; background: #eee; border-radius: 8px; overflow: hidden; display:flex; justify-content:center; align-items:center;">
                            <img src="${getProductImage(item.name)}" alt="${item.name}" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div style="flex: 1;">
                            <h4 style="font-size: 14px; margin-bottom: 5px;">${item.name}</h4>
                            <p style="font-weight: bold; font-size: 14px;">${item.price} <span style="font-weight:normal; color:#888; font-size:12px;">x ${item.qty}</span></p>
                        </div>
                    </div>`;
            });

            document.getElementById('summaryTotalItems').innerText = `Total Harga (${totalItems} Barang)`;
            document.getElementById('summaryItemPrice').innerText = `Rp ` + totalItemPrice.toLocaleString('id-ID');

            updateSummary();
            renderActiveAddress();
            renderPaymentUI(); // Render tampilan pembayaran awal (Pilih Metode)
        }

        function updateSummary() {
            const selectEl = document.getElementById('shippingSelect');
            const shippingCost = parseInt(selectEl.value);
            document.getElementById('shippingDesc').innerText = selectEl.options[selectEl.selectedIndex].getAttribute('data-desc');
            document.getElementById('summaryShippingPrice').innerText = `Rp ` + shippingCost.toLocaleString('id-ID');
            document.getElementById('summaryGrandTotal').innerText = `Rp ` + (totalItemPrice + shippingCost).toLocaleString('id-ID');
        }

        function getGrandTotalValue() {
            return totalItemPrice + parseInt(document.getElementById('shippingSelect').value);
        }

        function formatRupiah(amount) {
            return `Rp ${amount.toLocaleString('id-ID')}`;
        }

        function createDummyTransactionId() {
            return `TRX-${Date.now().toString().slice(-8)}`;
        }

        // --- 3. LOGIKA ALAMAT ---
        function renderActiveAddress() {
            const display = document.getElementById('addressDisplay');
            if (addresses.length === 0 || !selectedAddressId) {
                display.innerHTML = `<div style="text-align: center; padding: 20px; border: 1px dashed #ccc; border-radius: 8px; cursor: pointer;" onclick="openAddressModal()"><span style="font-size: 24px; color: var(--primary);">+</span><p style="margin-top: 10px; color: var(--text-main); font-weight: bold;">Isi Alamat Pengiriman</p></div>`;
                return;
            }
            const activeAddr = addresses.find(a => a.id == selectedAddressId);
            if (activeAddr) {
                display.innerHTML = `<div style="display: flex; justify-content: space-between; align-items: flex-start;"><div><p style="font-size: 14px; margin-bottom: 5px;"><span style="font-weight: bold;">${activeAddr.name}</span></p><p style="font-size: 13px; color: var(--text-main); line-height: 1.5; margin-bottom: 5px;">${activeAddr.address}</p><p style="font-size: 13px; color: var(--text-muted);">+${activeAddr.phone}</p></div><button style="background: none; border: 1px solid var(--primary); color: var(--primary); padding: 5px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 12px;" onclick="openAddressModal()">Ganti</button></div>`;
            }
        }

        function openAddressModal() {
            document.getElementById('addressModal').classList.add('active');
            document.getElementById('addressListView').style.display = 'block';
            document.getElementById('addressFormView').style.display = 'none';
            document.getElementById('modalTitle').innerText = 'Daftar Alamat';
            renderAddressList();
        }

        function closeAddressModal() {
            document.getElementById('addressModal').classList.remove('active');
        }

        function renderAddressList() {
            const container = document.getElementById('addressListContainer');
            container.innerHTML = "";
            if (addresses.length === 0) {
                container.innerHTML = `<p style="text-align:center; color:#888; font-size:14px; padding: 20px;">Belum ada alamat tersimpan.</p>`;
                return;
            }
            addresses.forEach(addr => {
                const isActive = (addr.id == selectedAddressId);
                const borderStyle = isActive ? 'border: 1px solid var(--primary); background-color: #f4fbf4;' : 'border: 1px solid #ddd;';
                const actionBtn = isActive ? `<span style="color: var(--primary); font-size: 20px; font-weight: bold;">✓</span>` : `<button class="btn btn-primary" style="padding: 8px 20px; width: auto; font-size: 12px; margin:0;" onclick="selectAddress(${addr.id})">Pilih</button>`;
                container.innerHTML += `<div style="${borderStyle} padding: 15px; border-radius: 8px; margin-bottom: 15px;"><p style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">${addr.name}</p><p style="font-size: 13px; color: var(--text-main); margin-bottom: 5px;">+${addr.phone}</p><p style="font-size: 13px; color: var(--text-main); line-height: 1.4; margin-bottom: 15px;">${addr.address}</p><div class="flex-between" style="border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px;"><div style="display: flex; gap: 15px;"><span style="color: var(--primary); font-size: 12px; font-weight: bold; cursor: pointer;">Ubah</span><span style="color: #ff4d4f; font-size: 12px; font-weight: bold; cursor: pointer;" onclick="deleteAddress(${addr.id})">Hapus</span></div>${actionBtn}</div></div>`;
            });
        }

        function selectAddress(id) {
            selectedAddressId = id;
            localStorage.setItem('mountsterSelectedAddress', id);
            renderActiveAddress();
            closeAddressModal();
        }

        function deleteAddress(id) {
            pendingDeleteId = id;
            document.getElementById('confirmDeleteModal').classList.add('active');
        }

        function closeConfirmModal() {
            pendingDeleteId = null;
            document.getElementById('confirmDeleteModal').classList.remove('active');
        }

        function executeDeleteAddress() {
            if (pendingDeleteId !== null) {
                addresses = addresses.filter(a => a.id !== pendingDeleteId);
                localStorage.setItem('mountsterAddresses', JSON.stringify(addresses));
                if (selectedAddressId == pendingDeleteId) {
                    selectedAddressId = null;
                    localStorage.removeItem('mountsterSelectedAddress');
                }
                renderAddressList();
                renderActiveAddress();
                closeConfirmModal();
            }
        }

        // --- 4. LOGIKA ALERT MODAL ---
        function showAlert(message) {
            document.getElementById('alertModalMessage').innerText = message;
            document.getElementById('alertModal').classList.add('active');
        }

        function closeAlertModal() {
            document.getElementById('alertModal').classList.remove('active');
        }

        // --- 5. LOGIKA PETA (LEAFLET) ---
        let map, marker;

        function showAddForm() {
            document.getElementById('addressListView').style.display = 'none';
            document.getElementById('addressFormView').style.display = 'block';
            document.getElementById('modalTitle').innerText = 'Tambah Alamat Baru';
            if (!map) {
                map = L.map('mapContainer').setView([-6.238270, 106.975573], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                marker = L.marker([-6.238270, 106.975573], {
                    draggable: true
                }).addTo(map);
                marker.on('dragend', function(e) {
                    let pos = marker.getLatLng();
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.lat}&lon=${pos.lng}`).then(res => res.json()).then(data => {
                        document.getElementById('formDetail').value = data.display_name;
                    });
                });
            }
            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        }
        document.getElementById('mapSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${this.value}&limit=1`).then(res => res.json()).then(data => {
                    if (data.length > 0) {
                        let lat = data[0].lat;
                        let lon = data[0].lon;
                        map.setView([lat, lon], 16);
                        marker.setLatLng([lat, lon]);
                        document.getElementById('formDetail').value = data[0].display_name;
                    } else {
                        showAlert("Lokasi tidak ditemukan!");
                    }
                });
            }
        });

        function saveNewAddress() {
            const name = document.getElementById('formName').value,
                phone = document.getElementById('formPhone').value,
                detail = document.getElementById('formDetail').value;
            if (!name || !phone || !detail) {
                showAlert("Harap lengkapi semua data alamat!");
                return;
            }
            const newId = Date.now();
            addresses.push({
                id: newId,
                name: name,
                phone: phone,
                address: detail
            });
            localStorage.setItem('mountsterAddresses', JSON.stringify(addresses));
            document.getElementById('formName').value = "";
            document.getElementById('formPhone').value = "";
            document.getElementById('formDetail').value = "";
            selectAddress(newId);
        }

        // --- 6. LOGIKA METODE PEMBAYARAN (MODAL & UI) ---
        function renderPaymentUI() {
            const container = document.getElementById('selectedPaymentContainer');

            if (!finalPaymentMethod) {
                // Tampilan jika belum ada yang dipilih
                container.innerHTML = `
                    <div style="padding: 12px; border: 1px dashed var(--primary); border-radius: 8px; text-align: center; background: #f4fbf4;">
                        <span style="font-size: 14px; font-weight: bold; color: var(--primary);">+ Pilih Metode Pembayaran</span>
                    </div>
                `;
            } else if (isFinalPaymentCOD) {
                container.innerHTML = `
                    <div style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">Pembayaran COD</div>
                    <label class="custom-radio" style="padding: 0; border: none; pointer-events: none;">
                        <span style="color: #333; font-size: 14px;">${finalPaymentDisplayName}</span>
                        <input type="radio" checked>
                    </label>
                `;
            } else {
                // Tampilan jika milih QRIS
                container.innerHTML = `
                    <label class="custom-radio" style="padding: 0; border: none; pointer-events: none;">
                        <span style="font-size: 14px;">${finalPaymentDisplayName}</span>
                        <input type="radio" checked>
                    </label>
                `;
            }
        }

        function openPaymentModal() {
            document.getElementById('paymentModal').classList.add('active');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('active');
        }

        function selectPayment(displayName, systemName, isCOD) {
            finalPaymentMethod = systemName;
            finalPaymentDisplayName = displayName;
            isFinalPaymentCOD = isCOD;

            renderPaymentUI();
            closePaymentModal();
        }

        function renderQrisWaitingState() {
            document.getElementById('qrisModalBody').innerHTML = `
                <div class="qris-chip"><span class="qris-chip-dot"></span>Menunggu Pembayaran dari HP</div>
                <p class="qris-amount">${formatRupiah(currentQrisAmount)}</p>
                <div class="qris-code-wrap">
                    <div id="qrisCode"></div>
                </div>
                <p class="qris-subtext">Scan QR ini dengan kamera HP Anda. Begitu halaman hasil scan terbuka di HP, pembayaran akan langsung ter-trigger otomatis.</p>
                <div class="qris-meta">
                    <div class="qris-meta-row"><span>Order ID</span><span id="qrisOrderIdText">${currentQrisOrderId}</span></div>
                    <div class="qris-meta-row"><span>Status</span><span id="qrisStatusText">Menunggu pembayaran</span></div>
                    <div class="qris-meta-row"><span>Berlaku sampai</span><span id="qrisExpiryText">10:00</span></div>
                </div>
                <p class="qris-subtext" style="margin-top:12px;">Akses dari HP: ${currentServerBaseUrl || getApiBaseUrl()}</p>
                <a href="${currentQrisPaymentUrl}" target="_blank" style="display:block; margin-top:14px; color:var(--primary); font-size:13px; font-weight:700; word-break:break-word;">Buka link hasil scan</a>
            `;

            const qrisCodeEl = document.getElementById('qrisCode');
            if (qrisCodeEl) {
                qrisCodeEl.innerHTML = '';
                new QRCode(qrisCodeEl, {
                    text: currentQrisPaymentUrl,
                    width: 200,
                    height: 200
                });
            }
        }

        function showQrisLoadingState() {
            document.getElementById('qrisModalBody').innerHTML = `
                <div class="qris-chip"><span class="qris-chip-dot"></span>Mempersiapkan QR Demo</div>
                <p class="qris-amount">${formatRupiah(currentQrisAmount)}</p>
                <div class="qris-loading"></div>
                <p class="qris-subtext">Sedang membuat link pembayaran demo untuk HP. Tunggu sebentar, QR akan muncul otomatis.</p>
            `;
        }

        function showQrisErrorState(message) {
            document.getElementById('qrisModalBody').innerHTML = `
                <div class="qris-chip"><span class="qris-chip-dot" style="background:#d9534f;"></span>QRIS Belum Siap</div>
                <p class="qris-amount">${formatRupiah(currentQrisAmount)}</p>
                <p class="qris-subtext">${message}</p>
                <div class="qris-meta">
                    <div class="qris-meta-row"><span>Status</span><span>Gagal membuat QR</span></div>
                    <div class="qris-meta-row"><span>Saran</span><span>Pastikan server Node aktif dan HP terhubung ke WiFi yang sama</span></div>
                </div>
            `;
        }

        function startQrisExpiryCountdown() {
            updateQrisExpiryText();
            qrisExpiryTimer = setInterval(() => {
                updateQrisExpiryText();
            }, 1000);
        }

        function updateQrisExpiryText() {
            const expiryEl = document.getElementById('qrisExpiryText');
            const statusEl = document.getElementById('qrisStatusText');
            if (!expiryEl || !currentQrisExpiryAt) return;

            const remainingMs = currentQrisExpiryAt - Date.now();
            if (remainingMs <= 0) {
                expiryEl.innerText = 'Expired';
                expiryEl.classList.add('qris-expired');
                if (statusEl) statusEl.innerText = 'QR expired';
                stopQrisRealtimeChecks();
                return;
            }

            const totalSeconds = Math.floor(remainingMs / 1000);
            const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
            const seconds = String(totalSeconds % 60).padStart(2, '0');
            expiryEl.innerText = `${minutes}:${seconds}`;
        }

        async function startQrisStatusPolling() {
            await checkPaymentStatus();
            qrisStatusPollingTimer = setInterval(() => {
                checkPaymentStatus();
            }, 3000);
        }

        async function checkPaymentStatus() {
            if (!currentQrisOrderId) return;
            try {
                const response = await fetch(`${getApiBaseUrl()}/api/payment/status?orderId=${encodeURIComponent(currentQrisOrderId)}`, {
                    cache: 'no-store'
                });
                if (!response.ok) return;
                const data = await response.json();
                const statusEl = document.getElementById('qrisStatusText');
                if (statusEl) {
                    statusEl.innerText = data.status === 'paid' ?
                        'Pembayaran terdeteksi' :
                        data.status === 'expired' ?
                        'QR expired' :
                        'Menunggu pembayaran';
                }
                if (data.status === 'paid') {
                    stopQrisRealtimeChecks();
                    showQrisSuccess();
                } else if (data.status === 'expired') {
                    stopQrisRealtimeChecks();
                }
            } catch (error) {
                const statusEl = document.getElementById('qrisStatusText');
                if (statusEl) statusEl.innerText = 'Menunggu koneksi server';
            }
        }

        function stopQrisRealtimeChecks() {
            if (qrisStatusPollingTimer) {
                clearInterval(qrisStatusPollingTimer);
                qrisStatusPollingTimer = null;
            }
            if (qrisExpiryTimer) {
                clearInterval(qrisExpiryTimer);
                qrisExpiryTimer = null;
            }
        }

        async function openQrisModal() {
            await initializeServerBaseUrl();
            currentQrisOrderId = qrisTransactionId;
            currentQrisAmount = getGrandTotalValue();
            currentQrisPaymentToken = createDemoPaymentToken();
            currentQrisExpiryAt = Date.now() + (10 * 60 * 1000);
            currentQrisPaymentUrl = `${currentServerBaseUrl || getApiBaseUrl()}/api/payment/confirm-direct?orderId=${encodeURIComponent(currentQrisOrderId)}&token=${encodeURIComponent(currentQrisPaymentToken)}&amount=${encodeURIComponent(currentQrisAmount)}`;
            document.getElementById('qrisModal').classList.add('active');
            renderQrisWaitingState();
            startQrisExpiryCountdown();
            startQrisStatusPolling();
        }

        function closeQrisModal() {
            if (qrisCountdownTimer) {
                clearInterval(qrisCountdownTimer);
                qrisCountdownTimer = null;
            }
            stopQrisRealtimeChecks();
            currentQrisOrderId = null;
            currentQrisExpiryAt = null;
            currentQrisPaymentToken = null;
            currentQrisAmount = 0;
            currentQrisPaymentUrl = '';
            document.getElementById('qrisModal').classList.remove('active');
        }

        function normalizeOrderItems(items) {
            if (!Array.isArray(items)) return [];
            return items.map(item => ({
                id: item?.id ?? Date.now(),
                name: item?.name || 'Produk Mountster',
                price: item?.price || 'Rp0',
                image: getProductImage(item?.name || ''),
                qty: Number.isFinite(Number(item?.qty)) && Number(item.qty) > 0 ? Number(item.qty) : 1
            }));
        }

        function saveSuccessfulOrder() {
            const activeAddr = addresses.find(a => a.id == selectedAddressId) || null;
            const normalizedItems = normalizeOrderItems(cartToCheckout);
            const createdAt = new Date().toISOString();
            const estimatedArrival = formatOrderDateRange(createdAt);
            const orderPayload = {
                id: qrisTransactionId,
                paymentMethod: finalPaymentMethod === 'QRIS' ? 'QRIS Demo Payment' : finalPaymentDisplayName.replace(/<[^>]*>?/gm, ''),
                status: 'Dikemas',
                createdAt,
                estimatedArrival,
                items: normalizedItems,
                productName: normalizedItems[0]?.name || 'Produk Mountster',
                productImage: normalizedItems[0]?.image || '',
                productPrice: normalizedItems[0]?.price || 'Rp0',
                address: activeAddr,
                totalItemPrice,
                shippingCost: parseInt(document.getElementById('shippingSelect').value),
                grandTotal: getGrandTotalValue()
            };

            localStorage.setItem('mountsterLastOrder', JSON.stringify(orderPayload));
            const existingOrders = JSON.parse(localStorage.getItem('orders') || '[]');
            existingOrders.unshift(orderPayload);
            localStorage.setItem('orders', JSON.stringify(existingOrders));
            localStorage.setItem('mountsterOrders', JSON.stringify(existingOrders));
            if (!directBuy) {
                localStorage.removeItem('mountsterCart');
            }
        }

        function redirectToOrderPage() {
            window.location.href = 'order.html?tab=riwayat';
        }

        function showQrisSuccess() {
            stopQrisRealtimeChecks();
            saveSuccessfulOrder();
            document.getElementById('qrisModalBody').innerHTML = `
                <div class="success-shell">
                    <div class="success-ring"><div class="success-check"></div></div>
                    <h3 class="success-title">Pembayaran Berhasil!</h3>
                    <p class="success-caption">Pembayaran QRIS demo berhasil diproses dari halaman HP. Pesanan sedang kami siapkan dan Anda akan diarahkan ke halaman order.</p>
                    <div class="success-card">
                        <div class="success-row"><span>Nominal</span><span>${formatRupiah(currentQrisAmount)}</span></div>
                        <div class="success-row"><span>Order ID</span><span>${currentQrisOrderId || qrisTransactionId}</span></div>
                        <div class="success-row"><span>Token</span><span>${currentQrisPaymentToken || '-'}</span></div>
                        <div class="success-row"><span>Metode</span><span>QRIS Demo Payment</span></div>
                    </div>
                    <p class="success-countdown" id="successCountdownText">Mengalihkan ke halaman order dalam 3 detik...</p>
                </div>
            `;

            let countdown = 3;
            qrisCountdownTimer = setInterval(() => {
                countdown -= 1;
                if (countdown <= 0) {
                    clearInterval(qrisCountdownTimer);
                    qrisCountdownTimer = null;
                    redirectToOrderPage();
                    return;
                }
                document.getElementById('successCountdownText').innerText = `Mengalihkan ke halaman order dalam ${countdown} detik...`;
            }, 1000);
        }

        function showCodSuccess() {
            currentQrisAmount = getGrandTotalValue();
            document.getElementById('qrisModal').classList.add('active');
            document.getElementById('qrisModalBody').innerHTML = `
                <div class="success-shell">
                    <div class="success-ring"><div class="success-check"></div></div>
                    <h3 class="success-title">Pesanan COD Berhasil!</h3>
                    <p class="success-caption">Pesanan Anda berhasil dibuat dengan metode COD. Pembayaran dilakukan saat pesanan diterima atau saat ambil di toko.</p>
                    <div class="success-card">
                        <div class="success-row"><span>Nominal</span><span>${formatRupiah(currentQrisAmount)}</span></div>
                        <div class="success-row"><span>Order ID</span><span>${qrisTransactionId}</span></div>
                        <div class="success-row"><span>Metode</span><span>${finalPaymentDisplayName.replace(/<[^>]*>?/gm, '')}</span></div>
                        <div class="success-row"><span>Status</span><span>Menunggu Pengiriman</span></div>
                    </div>
                    <p class="success-countdown" id="successCountdownText">Mengalihkan ke halaman order dalam 3 detik...</p>
                </div>
            `;

            let countdown = 3;
            qrisCountdownTimer = setInterval(() => {
                countdown -= 1;
                if (countdown <= 0) {
                    clearInterval(qrisCountdownTimer);
                    qrisCountdownTimer = null;
                    redirectToOrderPage();
                    return;
                }
                document.getElementById('successCountdownText').innerText = `Mengalihkan ke halaman order dalam ${countdown} detik...`;
            }, 1000);
        }

        // --- 7. PROSES BAYAR ---
        function processPayment() {
            // Validasi Alamat menggunakan Modal Alert yang baru
            if (!selectedAddressId) {
                showAlert("Harap isi alamat pengiriman terlebih dahulu sebelum membayar!");
                return;
            }

            // Validasi Pembayaran menggunakan Modal Alert yang baru
            if (!finalPaymentMethod) {
                showAlert("Harap pilih metode pembayaran terlebih dahulu!");
                return;
            }

            qrisTransactionId = createDummyTransactionId();

            if (finalPaymentMethod === 'QRIS') {
                openQrisModal();
                return;
            }

            saveSuccessfulOrder();
            showCodSuccess();
        }

        // Jalankan saat pertama load
        loadCheckout();
    </script>
</body>

</html>