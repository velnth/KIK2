<?php
include 'auth.php';
$userEmail = $_SESSION['user_email'] ?? '';
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
        .payment-option-note { font-size: 12px; color: var(--text-muted); margin-top: 6px; line-height: 1.5; }
        #mapContainer { height: 200px; width: 100%; border-radius: 8px; margin-bottom: 15px; z-index: 1; }
        .checkout-wrapper { display: grid; grid-template-columns: 1fr; gap: 20px; padding: 20px; }
        @media (min-width: 768px) { .checkout-wrapper { grid-template-columns: 2fr 1fr; align-items: start; } }
        .checkout-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); margin-bottom: 15px; }
        .checkout-card-title { font-size: 14px; font-weight: bold; color: var(--text-muted); margin-bottom: 15px; text-transform: uppercase; }
        
        .custom-radio { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; cursor: pointer; }
        .custom-radio input[type="radio"] { accent-color: var(--primary); width: 18px; height: 18px; cursor: pointer; }

        /* Modal & QRIS CSS */
        .qris-box { text-align: center; padding: 10px 0; }
        .qris-code-wrap { width: 240px; height: 240px; margin: 18px auto; background: #fff; border-radius: 20px; padding: 20px; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 0.2s; }
        .qris-code-wrap:hover { transform: scale(1.02); }
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
        .success-countdown { color: var(--primary); font-weight: 700; font-size: 13px; }

        @keyframes qrisSpin { to { transform: rotate(360deg); } }
        @keyframes checkPop { from { transform: rotate(-45deg) scale(0.3); opacity: 0; } to { transform: rotate(-45deg) scale(1); opacity: 1; } }

        /* Durasi Controls */
        .duration-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f4fbf4;
            padding: 5px 10px;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
        }
        .duration-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            padding: 0 5px;
        }
        .duration-text {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            min-width: 45px;
            text-align: center;
        }

        /* --- CSS Search Suggestions Maps --- */
        .search-suggestions {
            position: absolute;
            background: white;
            width: 100%;
            max-height: 160px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            top: 100%;
            margin-top: 5px;
        }
        .suggestion-item {
            padding: 12px;
            font-size: 13px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            color: #333;
            line-height: 1.4;
        }
        .suggestion-item:last-child {
            border-bottom: none;
        }
        .suggestion-item:hover {
            background-color: #f4fbf4;
            color: var(--primary);
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
                    <div class="flex-between" style="margin-bottom: 15px;">
                        <p style="font-weight: bold; margin: 0;">⛺ Mountster Rental Store</p>
                        
                        <div class="duration-controls">
                            <button class="duration-btn" onclick="updateDuration(-1)">-</button>
                            <span class="duration-text" id="durationText">1 Hari</span>
                            <button class="duration-btn" onclick="updateDuration(1)">+</button>
                        </div>
                    </div>

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

                <div class="checkout-card" id="voucherCard">
                    </div>
            </div>

            <div>
                <div class="checkout-card">
                    <div class="flex-between" style="margin-bottom: 10px;">
                        <div class="checkout-card-title" style="margin: 0;">Metode Pembayaran</div>
                        <span style="color: var(--primary); font-size: 14px; font-weight: bold; cursor: pointer;" onclick="openPaymentModal()">Lihat Semua</span>
                    </div>

                    <div id="selectedPaymentContainer" onclick="openPaymentModal()" style="cursor: pointer; padding-top: 5px;"></div>
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
                    <button id="checkoutSubmitBtn" class="btn btn-primary" type="button" style="position: relative; z-index: 5; pointer-events: auto;">Buat Pesanan</button>
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
                
                <div style="position: relative; margin-bottom: 10px; text-align: left;">
                    <input type="text" id="mapSearchInput" class="input-form" placeholder="Ketik area/jalan (cth: summarecon)...">
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </div>

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

    <div class="modal-overlay" id="verifAlertModal">
        <div class="modal-content" style="max-width: 350px; padding: 25px; text-align: center; border-radius: 15px; margin: 0 auto;">
            <div style="font-size: 40px; margin-bottom: 10px;">🛡️</div>
            <h3 style="margin-bottom: 10px; font-size: 18px;">Verifikasi Diperlukan</h3>
            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 25px;">Untuk keamanan bersama, Anda harus menyelesaikan Verifikasi 2 Langkah (KTP & Selfie) sebelum dapat menyewa alat.</p>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <button class="btn btn-primary" style="margin: 0; width: 100%; padding: 12px;" onclick="window.location.href='profile.php?require=verif'">Verifikasi Sekarang</button>
                <button class="btn" style="margin: 0; width: 100%; padding: 12px; background: #f0f0f0; color: #666;" onclick="document.getElementById('verifAlertModal').classList.remove('active')">Nanti Saja</button>
            </div>
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
                <div class="qris-code-wrap"><div id="qrisCode"></div></div>
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
        const USER_ID = "<?php echo $userEmail; ?>";
        const KEY_VOUCHER = 'mountsterVouchers_' + USER_ID;
        const KEY_ECO_POINTS = 'mountsterEcoPoints_' + USER_ID;
        const KEY_VERIF = 'is_verified_' + USER_ID;

        let appliedVoucher = null;
        let discountAmount = 0;
        let rentalDays = 1; // Variabel Durasi Sewa (Hari)

        let addresses = JSON.parse(localStorage.getItem('mountsterAddresses')) || [];
        let selectedAddressId = localStorage.getItem('mountsterSelectedAddress') || null;
        let pendingDeleteId = null;

        let cartToCheckout = [];
        let baseItemPrice = 0; // Total Harga Barang (Hanya 1 Hari)
        let totalItemPrice = 0; // Total Harga Barang x Jumlah Hari

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

        // Tambahkan fungsi ini agar tombol tidak error
function createDummyTransactionId() {
    return 'MNT-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
}

        function getApiBaseUrl() { return `${window.location.protocol}//${window.location.hostname}:3000`; }
        function createDemoPaymentToken() { return Math.random().toString(36).slice(2, 10).toUpperCase(); }

        function getProductImage(productName) {
            if (productName === 'Eiger Wanderlust 60') return 'images/eiger-wanderlust-60.jpeg';
            if (productName === 'Consina Magnum 4') return 'images/consina-magnum-4.jpeg';
            if (productName === 'Great Outdoor Java 4') return 'images/great-outdoor-java-4.jpeg';
            if (productName === 'Naturehike Cloud Up 2') return 'images/naturehike-cloud-up-2.jpeg';
            return 'logo_mountster.png';
        }

        let directBuy = JSON.parse(localStorage.getItem('mountsterDirectBuy'));
        if (directBuy) {
            cartToCheckout = directBuy;
            localStorage.removeItem('mountsterDirectBuy');
        } else {
            cartToCheckout = JSON.parse(localStorage.getItem('mountsterCart')) || [];
        }

        let finalPaymentMethod = null;
        let finalPaymentDisplayName = null;
        let isFinalPaymentCOD = false;

        function updateSubmitButtonState() {
            const submitBtn = document.getElementById('checkoutSubmitBtn');
            if (!submitBtn) return;
            const isReady = Boolean(selectedAddressId && finalPaymentMethod);
            submitBtn.disabled = false;
            submitBtn.style.opacity = isReady ? '1' : '0.65';
            submitBtn.style.cursor = isReady ? 'pointer' : 'not-allowed';
            submitBtn.setAttribute('aria-disabled', isReady ? 'false' : 'true');
        }

        function syncPaymentSelectionUI() {
            updateSubmitButtonState();
        }

        // FUNGSI UPDATE DURASI
        function updateDuration(change) {
            let newDuration = rentalDays + change;
            if (newDuration < 1) newDuration = 1;
            
            rentalDays = newDuration;
            document.getElementById('durationText').innerText = `${rentalDays} Hari`;
            
            // Hitung ulang harga
            totalItemPrice = baseItemPrice * rentalDays;
            
            updateSummary();
        }

        // --- 2. RENDER PRODUK & RINGKASAN ---
        function loadCheckout() {
            if (cartToCheckout.length === 0) {
                window.location.href = "home.php";
                return;
            }

            const container = document.getElementById('checkoutItemsContainer');
            container.innerHTML = "";
            let totalItems = 0;
            baseItemPrice = 0;

            cartToCheckout.forEach(item => {
                let priceNumber = parseInt(item.price.replace(/[^0-9]/g, '')) || 0;
                totalItems += item.qty;
                baseItemPrice += (priceNumber * item.qty);

                let imageSrc = item.image ? item.image : getProductImage(item.name);

                container.innerHTML += `
                    <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 60px; height: 60px; background: #eee; border-radius: 8px; overflow: hidden; display:flex; justify-content:center; align-items:center;">
                            <img src="${imageSrc}" alt="${item.name}" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div style="flex: 1;">
                            <h4 style="font-size: 14px; margin-bottom: 5px;">${item.name}</h4>
                            <p style="font-weight: bold; font-size: 14px;">${item.price} <span style="font-weight:normal; color:#888; font-size:12px;">x ${item.qty}</span></p>
                        </div>
                    </div>`;
            });

            document.getElementById('summaryTotalItems').innerText = `Total Harga (${totalItems} Barang)`;
            
            // Set harga awal
            totalItemPrice = baseItemPrice * rentalDays;

            renderVoucherPickerCard();
            updateSummary();
            renderActiveAddress();
            renderPaymentUI();
            syncPaymentSelectionUI();
        }

        function updateSummary() {
            document.getElementById('summaryItemPrice').innerText = `Rp ` + totalItemPrice.toLocaleString('id-ID');

            const selectEl = document.getElementById('shippingSelect');
            let shippingCost = parseInt(selectEl.value);
            document.getElementById('shippingDesc').innerText = selectEl.options[selectEl.selectedIndex].getAttribute('data-desc');

            discountAmount = 0;
            if (appliedVoucher) {
                if (appliedVoucher.type === 'percent') discountAmount = Math.round(totalItemPrice * appliedVoucher.value / 100);
                else if (appliedVoucher.type === 'flat') discountAmount = appliedVoucher.value;
            }

            const grandTotal = Math.max(0, totalItemPrice - discountAmount + shippingCost);

            document.getElementById('summaryShippingPrice').innerText = 'Rp ' + shippingCost.toLocaleString('id-ID');

            let discountRow = document.getElementById('summaryDiscountRow');
            if (discountAmount > 0) {
                if (!discountRow) {
                    const hr = document.querySelector('.checkout-card hr');
                    const row = document.createElement('div');
                    row.id = 'summaryDiscountRow';
                    row.className = 'flex-between';
                    row.style = 'margin-bottom: 10px; font-size: 14px; color: #009933;';
                    row.innerHTML = `<span id="summaryDiscountLabel">Diskon Voucher</span><span id="summaryDiscountValue">-Rp0</span>`;
                    hr.parentNode.insertBefore(row, hr);
                    discountRow = row;
                }
                document.getElementById('summaryDiscountLabel').innerText = `Voucher (${appliedVoucher.code})`;
                document.getElementById('summaryDiscountValue').innerText = `-Rp ${discountAmount.toLocaleString('id-ID')}`;
                discountRow.style.display = 'flex';
            } else if (discountRow) {
                discountRow.style.display = 'none';
            }

            document.getElementById('summaryGrandTotal').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        }

        function getGrandTotalValue() {
            const selectEl = document.getElementById('shippingSelect');
            let shippingCost = parseInt(selectEl.value);
            return Math.max(0, totalItemPrice - discountAmount + shippingCost);
        }

        // --- Fitur Input Kode Voucher ---
        function renderVoucherPickerCard() {
            const card = document.getElementById('voucherCard');
            if (!card) return;
            card.innerHTML = `
                <div class="checkout-card-title">Voucher & Promo</div>
                <div style="display: flex; gap: 10px;" id="voucherInputArea">
                    <input type="text" id="manualVoucherCode" class="input-form" placeholder="Contoh: ECO20" style="flex: 1; text-transform: uppercase;">
                    <button class="btn btn-primary" style="margin: 0; padding: 10px 20px; width: auto;" onclick="applyManualVoucher()">Pakai</button>
                </div>
                <div id="voucherFeedback" style="margin-top: 10px; font-size: 13px;"></div>
                <div id="voucherAppliedBanner" style="display: none; margin-top: 15px;"></div>
            `;
        }

        function applyManualVoucher() {
            const inputEl = document.getElementById('manualVoucherCode');
            const code = inputEl.value.trim().toUpperCase();
            const feedback = document.getElementById('voucherFeedback');

            if (!code) { feedback.innerHTML = '<span style="color: #ff4d4f;">Masukkan kode voucher.</span>'; return; }

            const VALID_VOUCHERS = { "ECO20": { code: "ECO20", label: "Diskon 20% Eco-Warrior", type: "percent", value: 20, reqPoints: 25 } };
            const voucher = VALID_VOUCHERS[code];

            if (!voucher) { feedback.innerHTML = '<span style="color: #ff4d4f;">Kode tidak valid.</span>'; return; }

            const allVouchers = JSON.parse(localStorage.getItem(KEY_VOUCHER) || '[]');
            const existingVoucher = allVouchers.find(v => v.code === code);
            if (existingVoucher && existingVoucher.used) {
                feedback.innerHTML = '<span style="color: #ff4d4f;">Voucher sudah digunakan.</span>'; return;
            }

            if (voucher.code === 'ECO20') {
                const currentEcoPoints = parseInt(localStorage.getItem(KEY_ECO_POINTS)) || 0;
                if (currentEcoPoints < voucher.reqPoints) {
                    feedback.innerHTML = `<span style="color: #ff4d4f;">Poin Eco kurang (${currentEcoPoints}/${voucher.reqPoints}).</span>`; return;
                }
            }

            feedback.innerHTML = ''; inputEl.value = ''; appliedVoucher = voucher;

            document.getElementById('voucherInputArea').style.display = 'none';
            document.getElementById('voucherAppliedBanner').style.display = 'block';
            document.getElementById('voucherAppliedBanner').innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; background:#f4fbf4; border:1px solid #c3e6cb; border-radius:8px; padding:12px;">
                    <div><span style="font-weight:bold; color:var(--primary);">${voucher.code}</span><div style="font-size:12px; color:#555; margin-top:2px;">${voucher.label} diterapkan.</div></div>
                    <span onclick="removeManualVoucher()" style="color:#ff4d4f; font-size:20px; cursor:pointer; font-weight:bold; padding:0 4px;" title="Hapus Voucher">×</span>
                </div>`;
            updateSummary();
        }

        function removeManualVoucher() {
            appliedVoucher = null; discountAmount = 0;
            document.getElementById('voucherAppliedBanner').style.display = 'none';
            document.getElementById('voucherInputArea').style.display = 'flex';
            updateSummary();
        }

        function formatRupiah(amount) { return `Rp ${amount.toLocaleString('id-ID')}`; }

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
        function openAddressModal() { document.getElementById('addressModal').classList.add('active'); document.getElementById('addressListView').style.display = 'block'; document.getElementById('addressFormView').style.display = 'none'; document.getElementById('modalTitle').innerText = 'Daftar Alamat'; renderAddressList(); }
        function closeAddressModal() { document.getElementById('addressModal').classList.remove('active'); }
        function renderAddressList() {
            const container = document.getElementById('addressListContainer');
            container.innerHTML = "";
            if (addresses.length === 0) { container.innerHTML = `<p style="text-align:center; color:#888; font-size:14px; padding: 20px;">Belum ada alamat tersimpan.</p>`; return; }
            addresses.forEach(addr => {
                const isActive = (addr.id == selectedAddressId);
                const borderStyle = isActive ? 'border: 1px solid var(--primary); background-color: #f4fbf4;' : 'border: 1px solid #ddd;';
                const actionBtn = isActive ? `<span style="color: var(--primary); font-size: 20px; font-weight: bold;">✓</span>` : `<button class="btn btn-primary" style="padding: 8px 20px; width: auto; font-size: 12px; margin:0;" onclick="selectAddress(${addr.id})">Pilih</button>`;
                container.innerHTML += `<div style="${borderStyle} padding: 15px; border-radius: 8px; margin-bottom: 15px;"><p style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">${addr.name}</p><p style="font-size: 13px; color: var(--text-main); margin-bottom: 5px;">+${addr.phone}</p><p style="font-size: 13px; color: var(--text-main); line-height: 1.4; margin-bottom: 15px;">${addr.address}</p><div class="flex-between" style="border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px;"><div style="display: flex; gap: 15px;"><span style="color: var(--primary); font-size: 12px; font-weight: bold; cursor: pointer;">Ubah</span><span style="color: #ff4d4f; font-size: 12px; font-weight: bold; cursor: pointer;" onclick="deleteAddress(${addr.id})">Hapus</span></div>${actionBtn}</div></div>`;
            });
        }
        function selectAddress(id) { selectedAddressId = id; localStorage.setItem('mountsterSelectedAddress', id); renderActiveAddress(); closeAddressModal(); }
        function deleteAddress(id) { pendingDeleteId = id; document.getElementById('confirmDeleteModal').classList.add('active'); }
        function closeConfirmModal() { pendingDeleteId = null; document.getElementById('confirmDeleteModal').classList.remove('active'); }
        function executeDeleteAddress() {
            if (pendingDeleteId !== null) {
                addresses = addresses.filter(a => a.id !== pendingDeleteId);
                localStorage.setItem('mountsterAddresses', JSON.stringify(addresses));
                if (selectedAddressId == pendingDeleteId) { selectedAddressId = null; localStorage.removeItem('mountsterSelectedAddress'); }
                renderAddressList(); renderActiveAddress(); closeConfirmModal();
            }
        }
        function showAlert(message) { document.getElementById('alertModalMessage').innerText = message; document.getElementById('alertModal').classList.add('active'); }
        function closeAlertModal() { document.getElementById('alertModal').classList.remove('active'); }

        // --- 5. PETA LOKASI DAN PENCARIAN (AUTOCOMPLETE) ---
        let map, marker;
        function showAddForm() {
            document.getElementById('addressListView').style.display = 'none';
            document.getElementById('addressFormView').style.display = 'block';
            document.getElementById('modalTitle').innerText = 'Tambah Alamat Baru';
            
            if (!map) {
                map = L.map('mapContainer').setView([-6.238270, 106.975573], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                marker = L.marker([-6.238270, 106.975573], { draggable: true }).addTo(map);
                
                // Coba dapatkan lokasi pengguna otomatis dengan Geolocation API
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        let lat = position.coords.latitude;
                        let lon = position.coords.longitude;
                        map.setView([lat, lon], 16);
                        marker.setLatLng([lat, lon]);
                        
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(res => res.json())
                        .then(data => { document.getElementById('formDetail').value = data.display_name; });
                    }, function(error) {
                        console.log("Akses lokasi ditolak atau gagal.");
                    });
                }

                marker.on('dragend', function(e) {
                    let pos = marker.getLatLng();
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.lat}&lon=${pos.lng}`)
                    .then(res => res.json())
                    .then(data => { document.getElementById('formDetail').value = data.display_name; });
                });
            }
            setTimeout(() => { map.invalidateSize(); }, 300);
        }

        let searchTimeout;
        document.getElementById('mapSearchInput').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            let query = this.value.trim();
            let suggestionsBox = document.getElementById('searchSuggestions');
            
            if (query.length < 3) {
                suggestionsBox.style.display = 'none';
                return;
            }

            // Gunakan Debounce agar tidak spam API terlalu cepat (tunggu 500ms setelah selesai mengetik)
            searchTimeout = setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
                .then(res => res.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            let div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.innerText = item.display_name;
                            div.onclick = function() {
                                let lat = parseFloat(item.lat);
                                let lon = parseFloat(item.lon);
                                map.setView([lat, lon], 16);
                                marker.setLatLng([lat, lon]);
                                document.getElementById('formDetail').value = item.display_name;
                                document.getElementById('mapSearchInput').value = item.display_name;
                                suggestionsBox.style.display = 'none';
                            };
                            suggestionsBox.appendChild(div);
                        });
                        suggestionsBox.style.display = 'block';
                    } else {
                        suggestionsBox.innerHTML = '<div class="suggestion-item" style="color:red; text-align:center;">Alamat tidak ditemukan</div>';
                        suggestionsBox.style.display = 'block';
                    }
                });
            }, 500); 
        });

        // Sembunyikan daftar pencarian jika mengklik di luar area input
        document.addEventListener('click', function(e) {
            let searchInput = document.getElementById('mapSearchInput');
            let suggestionsBox = document.getElementById('searchSuggestions');
            if (e.target !== searchInput && e.target !== suggestionsBox && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });

        function saveNewAddress() {
            const name = document.getElementById('formName').value, phone = document.getElementById('formPhone').value, detail = document.getElementById('formDetail').value;
            if (!name || !phone || !detail) { showAlert("Harap lengkapi semua data alamat!"); return; }
            const newId = Date.now();
            addresses.push({ id: newId, name: name, phone: phone, address: detail });
            localStorage.setItem('mountsterAddresses', JSON.stringify(addresses));
            document.getElementById('formName').value = ""; document.getElementById('formPhone').value = ""; document.getElementById('formDetail').value = "";
            document.getElementById('mapSearchInput').value = "";
            selectAddress(newId);
        }

        // --- 6. PEMBAYARAN ---
        function renderPaymentUI() {
            const container = document.getElementById('selectedPaymentContainer');
            if (!finalPaymentMethod) {
                container.innerHTML = `<div style="padding: 12px; border: 1px dashed var(--primary); border-radius: 8px; text-align: center; background: #f4fbf4;"><span style="font-size: 14px; font-weight: bold; color: var(--primary);">+ Pilih Metode Pembayaran</span></div>`;
            } else if (isFinalPaymentCOD) {
                container.innerHTML = `<div style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">Pembayaran COD</div><label class="custom-radio" style="padding: 0; border: none; pointer-events: none;"><span style="color: #333; font-size: 14px;">${finalPaymentDisplayName}</span><input type="radio" checked></label>`;
            } else {
                container.innerHTML = `<label class="custom-radio" style="padding: 0; border: none; pointer-events: none;"><span style="font-size: 14px;">${finalPaymentDisplayName}</span><input type="radio" checked></label>`;
            }
            updateSubmitButtonState();
        }
        function openPaymentModal() { document.getElementById('paymentModal').classList.add('active'); syncPaymentSelectionUI(); }
        function closePaymentModal() { document.getElementById('paymentModal').classList.remove('active'); }
        function selectPayment(displayName, systemName, isCOD) { finalPaymentMethod = systemName; finalPaymentDisplayName = displayName; isFinalPaymentCOD = isCOD; renderPaymentUI(); syncPaymentSelectionUI(); closePaymentModal(); }

        // QRIS Logic
        function renderQrisWaitingState() {
            document.getElementById('qrisModalBody').innerHTML = `
                <div class="qris-chip" style="margin-bottom: 10px;"><span class="qris-chip-dot"></span>Menunggu Pembayaran</div>
                <p class="qris-amount" style="font-size: 32px; color: #1b4332; margin-bottom: 20px;">${formatRupiah(currentQrisAmount)}</p>
                <div class="qris-code-wrap" style="cursor: pointer;" onclick="window.open('${currentQrisPaymentUrl}', '_blank')" title="Klik untuk simulasi bayar">
                    <div id="qrisCode"></div>
                </div>
                <p class="qris-subtext">Scan QR ini dengan kamera HP Anda.<br>Pembayaran otomatis ter-trigger saat<br>halaman scan terbuka di HP.</p>
                <div class="qris-meta">
                    <div class="qris-meta-row"><span>Order ID</span><span id="qrisOrderIdText">${currentQrisOrderId}</span></div>
                    <div class="qris-meta-row"><span>Status</span><span id="qrisStatusText">Menunggu pembayaran</span></div>
                    <div class="qris-meta-row"><span>Berlaku sampai</span><span id="qrisExpiryText">10:00</span></div>
                </div>
            `;
            const qrisCodeEl = document.getElementById('qrisCode');
            if (qrisCodeEl) {
                qrisCodeEl.innerHTML = '';
                new QRCode(qrisCodeEl, { text: currentQrisPaymentUrl, width: 200, height: 200 });
            }
        }
        function showQrisLoadingState() { document.getElementById('qrisModalBody').innerHTML = `<div class="qris-chip"><span class="qris-chip-dot"></span>Mempersiapkan QR Demo</div><p class="qris-amount">${formatRupiah(currentQrisAmount)}</p><div class="qris-loading"></div>`; }
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
        
        function startQrisStatusPolling() { checkPaymentStatus(); qrisStatusPollingTimer = setInterval(checkPaymentStatus, 2000); }
        function checkPaymentStatus() {
            if (!currentQrisOrderId) return;
            const status = localStorage.getItem('payment_status_' + currentQrisOrderId);
            const statusEl = document.getElementById('qrisStatusText');
            if (status === 'paid') {
                if (statusEl) statusEl.innerText = 'Pembayaran terdeteksi';
                stopQrisRealtimeChecks(); 
                localStorage.removeItem('payment_status_' + currentQrisOrderId);
                showQrisSuccess(); 
            }
        }
        function stopQrisRealtimeChecks() {
            if (qrisStatusPollingTimer) clearInterval(qrisStatusPollingTimer);
            if (qrisExpiryTimer) clearInterval(qrisExpiryTimer);
            qrisStatusPollingTimer = null; qrisExpiryTimer = null;
        }

       function openQrisModal() {
    currentQrisOrderId = qrisTransactionId;
    currentQrisAmount = getGrandTotalValue();
    currentQrisExpiryAt = Date.now() + (10 * 60 * 1000);
    
    // Perbaikan: Ubah 'orderId=' menjadi 'id=' agar cocok dengan payment.php
    currentQrisPaymentUrl = `${window.location.origin}/payment.php?id=${encodeURIComponent(currentQrisOrderId)}&amount=${encodeURIComponent(currentQrisAmount)}`;
    
    document.getElementById('qrisModal').classList.add('active');
    showQrisLoadingState();

    setTimeout(() => {
        renderQrisWaitingState();
        startQrisExpiryCountdown();
        startQrisStatusPolling();
    }, 500);
}
        function closeQrisModal() {
            if (qrisCountdownTimer) clearInterval(qrisCountdownTimer);
            stopQrisRealtimeChecks();
            currentQrisOrderId = null; currentQrisExpiryAt = null; currentQrisPaymentToken = null; currentQrisAmount = 0; currentQrisPaymentUrl = '';
            document.getElementById('qrisModal').classList.remove('active');
            window.location.href = 'order.php';
        }

        function normalizeOrderItems(items) {
            return (Array.isArray(items) ? items : []).map(item => ({
                id: item?.id ?? Date.now(), name: item?.name || 'Produk Mountster', price: item?.price || 'Rp0', image: getProductImage(item?.name || ''), qty: Number.isFinite(Number(item?.qty)) && Number(item.qty) > 0 ? Number(item.qty) : 1
            }));
        }
        function formatOrderDateRange(isoString) {
            // Karena sudah ditambahkan input durasi, waktu tibanya tetap tapi batas pengembaliannya nambah
            const date = new Date(isoString); 
            const nextDate = new Date(date); 
            nextDate.setDate(nextDate.getDate() + rentalDays);
            const options = { day: 'numeric', month: 'short' };
            return `${date.toLocaleDateString('id-ID', options)} - ${nextDate.toLocaleDateString('id-ID', options)}`;
        }

        function markVoucherAsUsed() {
            if (appliedVoucher) {
                const allVouchers = JSON.parse(localStorage.getItem(KEY_VOUCHER) || '[]');
                const idx = allVouchers.findIndex(v => v.code === appliedVoucher.code);
                if (idx !== -1) {
                    allVouchers[idx].used = true;
                    allVouchers[idx].usedAt = new Date().toISOString();
                } else {
                    allVouchers.push({ code: appliedVoucher.code, title: appliedVoucher.label, used: true, usedAt: new Date().toISOString() });
                }
                localStorage.setItem(KEY_VOUCHER, JSON.stringify(allVouchers));
            }
        }

       function savePendingQrisOrder() {
    const activeAddr = addresses.find(a => a.id == selectedAddressId) || null;
    const normalizedItems = normalizeOrderItems(cartToCheckout);
    const createdAt = new Date().toISOString();
    
    // Perbaikan: Tambahkan field yang dibutuhkan oleh payment.php
    const orderPayload = {
        id: qrisTransactionId, 
        paymentMethod: 'QRIS', 
        paymentName: 'QRIS Demo Payment', // Dibaca oleh payment.php
        isVA: false,                      // Dibaca oleh payment.php
        status: 'Belum Dibayar',
        createdAt, 
        estimatedArrival: formatOrderDateRange(createdAt), 
        deadline: Date.now() + (24 * 60 * 60 * 1000),
        items: normalizedItems, 
        productName: normalizedItems[0]?.name || 'Produk Mountster', 
        productImage: normalizedItems[0]?.image || '', 
        productPrice: normalizedItems[0]?.price || 'Rp0',
        address: activeAddr, 
        totalItemPrice, 
        shippingCost: parseInt(document.getElementById('shippingSelect').value),
        shippingType: document.getElementById('shippingSelect').value === '0' ? 'pickup' : 'delivery', // Dibaca oleh payment.php
        grandTotal: getGrandTotalValue(), 
        totalPay: getGrandTotalValue(), // Dibaca oleh payment.php
        voucherCode: appliedVoucher ? appliedVoucher.code : null, 
        discountAmount: discountAmount, 
        rentalDays: rentalDays
    };

    localStorage.setItem('mountsterLastOrder', JSON.stringify(orderPayload));
    const existingOrders = JSON.parse(localStorage.getItem('mountsterOrders') || '[]');
    existingOrders.unshift(orderPayload);
    localStorage.setItem('mountsterOrders', JSON.stringify(existingOrders));
    
    markVoucherAsUsed();
    if (!directBuy) localStorage.removeItem('mountsterCart');
}

        function saveSuccessfulOrder() {
            const activeAddr = addresses.find(a => a.id == selectedAddressId) || null;
            const normalizedItems = normalizeOrderItems(cartToCheckout);
            const createdAt = new Date().toISOString();
            const orderPayload = {
                id: qrisTransactionId, paymentMethod: finalPaymentMethod === 'QRIS' ? 'QRIS Demo Payment' : finalPaymentDisplayName.replace(/<[^>]*>?/gm, ''), status: 'Dikemas',
                createdAt, estimatedArrival: formatOrderDateRange(createdAt), items: normalizedItems,
                productName: normalizedItems[0]?.name || 'Produk Mountster', productImage: normalizedItems[0]?.image || '', productPrice: normalizedItems[0]?.price || 'Rp0',
                address: activeAddr, totalItemPrice, shippingCost: parseInt(document.getElementById('shippingSelect').value),
                grandTotal: getGrandTotalValue(), voucherCode: appliedVoucher ? appliedVoucher.code : null, discountAmount: discountAmount, rentalDays: rentalDays
            };

            localStorage.setItem('mountsterLastOrder', JSON.stringify(orderPayload));
            const existingOrders = JSON.parse(localStorage.getItem('mountsterOrders') || '[]');
            existingOrders.unshift(orderPayload);
            localStorage.setItem('mountsterOrders', JSON.stringify(existingOrders));
            
            markVoucherAsUsed();
            if (!directBuy) localStorage.removeItem('mountsterCart');
        }

        function redirectToOrderPage() { window.location.href = 'order.php'; }

        function renderPaymentSuccessModal(title, message) {
            const closeBtn = document.getElementById('qrisModalClose');
            if (closeBtn) closeBtn.style.display = 'none';
            document.getElementById('qrisModal').style.pointerEvents = 'all';
            document.getElementById('qrisModal').style.background = 'rgba(0,0,0,0.55)';

            document.getElementById('qrisModal').classList.add('active');
            document.getElementById('qrisModalBody').innerHTML = `
                <div class="success-shell">
                    <div class="success-ring"><div class="success-check"></div></div>
                    <h3 class="success-title">${title}</h3>
                    <p class="success-caption">${message}</p>
                    <p class="success-countdown" id="successCountdownText">Beralih ke Pesanan Saya dalam 5 detik...</p>
                    <button class="btn btn-primary" type="button" style="margin-top: 16px;" onclick="redirectToOrderPage()">Lihat Pesanan</button>
                </div>
            `;

            let countdown = 5;
            qrisCountdownTimer = setInterval(() => {
                countdown -= 1;
                if (countdown <= 0) { clearInterval(qrisCountdownTimer); redirectToOrderPage(); return; }
                const el = document.getElementById('successCountdownText');
                if (el) el.innerText = `Beralih ke Pesanan Saya dalam ${countdown} detik...`;
            }, 1000);
        }

        function showQrisSuccess() {
            stopQrisRealtimeChecks();
            
            // Update status yang ada di localStorage
            let existingOrders = JSON.parse(localStorage.getItem('mountsterOrders')) || [];
            const idx = existingOrders.findIndex(o => String(o.id) === String(currentQrisOrderId || qrisTransactionId));
            if (idx !== -1) {
                existingOrders[idx].status = 'Dikemas'; 
                existingOrders[idx].paidAt = new Date().toISOString();
                localStorage.setItem('mountsterOrders', JSON.stringify(existingOrders));
            }
            
            renderPaymentSuccessModal('Pembayaran Berhasil!', 'Pesananmu sedang diproses. Terima kasih!');
        }

        function showCodSuccess() {
            currentQrisAmount = getGrandTotalValue();
            renderPaymentSuccessModal('Pesanan COD Berhasil!', 'Silakan siapkan pembayaran saat pesanan diantar kurir atau saat Anda mengambil barang ke toko.');
        }

        // --- 7. PROSES BAYAR ---
        function processPayment() {
            // TAMBAHAN: Cek verifikasi
            if (localStorage.getItem(KEY_VERIF) !== 'true') {
                document.getElementById('verifAlertModal').classList.add('active');
                return;
            }

            if (!selectedAddressId) { showAlert('Harap isi alamat pengiriman terlebih dahulu sebelum membayar!'); return; }
            if (!finalPaymentMethod) { showAlert('Harap pilih metode pembayaran terlebih dahulu!'); return; }

            qrisTransactionId = createDummyTransactionId();

            if (finalPaymentMethod === 'QRIS') {
                savePendingQrisOrder();
                openQrisModal();
                return;
            }

            saveSuccessfulOrder();
            showCodSuccess();
        }

        const checkoutSubmitBtn = document.getElementById('checkoutSubmitBtn');
        if (checkoutSubmitBtn) checkoutSubmitBtn.addEventListener('click', processPayment);

        loadCheckout();
    </script>
</body>

</html>