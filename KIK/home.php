<?php 
include 'auth.php'; 

if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email']; // PENTING UNTUK VOUCHER
$userAvatar = $_SESSION['user_avatar'] ?? 'https://api.dicebear.com/8.x/notionists/svg?seed=Admin'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountster - Home</title>
    <link rel="stylesheet" href="style.css">
    <script src="database.js"></script>
    <script src="wishlist.js"></script>
    <style>
        .profile-icon { width: 40px; height: 40px; background-color: white; border-radius: 50%; background-image: url('<?php echo $userAvatar; ?>'); background-size: cover; background-position: center; border: 2px solid rgba(255,255,255,0.2); }
        .weather-banner { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 20px; padding: 20px; color: white; position: relative; box-shadow: 0 10px 20px rgba(0,0,0,0.15); margin-bottom: 20px; }
        .weather-select { width: 100%; padding: 10px; border-radius: 10px; border: none; outline: none; margin-bottom: 10px; font-weight: bold; color: #333; }
        .btn-weather { background: #ffc107; color: #000; font-weight: bold; width: 100%; padding: 10px; border-radius: 10px; border: none; cursor: pointer; transition: 0.3s; }
        .weather-result { background: rgba(0,0,0,0.3); border-radius: 15px; padding: 15px; margin-top: 15px; display: none; animation: fadeIn 0.5s ease; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(5px); }
        .eco-banner { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 20px; padding: 20px; color: white; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 8px 15px rgba(17, 153, 142, 0.3); margin-bottom: 20px; cursor: pointer; transition: filter 0.3s ease; }
        .compare-section { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin: 0 20px 20px 20px; }
        .compare-grid { display: grid; grid-template-columns: 1fr auto 1fr; gap: 10px; align-items: center; }
        .vs-badge { background: #ff4d4f; color: white; font-weight: bold; padding: 5px 10px; border-radius: 50%; font-size: 12px; }
        .compare-result table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 12px; }
        .compare-result th, .compare-result td { border: 1px solid #eee; padding: 8px; text-align: center; }
        .compare-result th { background: #f9f9f9; color: var(--text-muted); font-size: 11px; }
        .winner { background: #e6f7eb; font-weight: bold; color: #009933; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* SOS CSS */
        .sos-container { position: fixed; bottom: 85px; left: 50%; transform: translateX(-50%); width: 100%; max-width: 100%; pointer-events: none; z-index: 1000; }
        @media (min-width: 768px) { .sos-container { max-width: 800px; } }
        @media (min-width: 1024px) { .sos-container { max-width: 1000px; } }
        .sos-floating-btn { position: absolute; right: 20px; bottom: 0; width: 60px; height: 60px; background: linear-gradient(135deg, #ff4d4f, #d9363e); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; box-shadow: 0 4px 15px rgba(255, 77, 79, 0.5); cursor: pointer; border: 3px solid white; pointer-events: auto; animation: pulseSOS 2s infinite; }
        @keyframes pulseSOS { 0% { box-shadow: 0 0 0 0 rgba(255, 77, 79, 0.7); } 70% { box-shadow: 0 0 0 15px rgba(255, 77, 79, 0); } 100% { box-shadow: 0 0 0 0 rgba(255, 77, 79, 0); } }
        .sos-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #121212; color: white; z-index: 9999; display: none; flex-direction: column; padding: 20px; overflow-y: auto; }
        .sos-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .compass-box { width: 150px; height: 150px; border: 4px solid #333; border-radius: 50%; margin: 0 auto 20px auto; position: relative; background: #1a1a1a; display: flex; align-items: center; justify-content: center; }
        .compass-needle { width: 4px; height: 120px; background: linear-gradient(to bottom, #ff4d4f 50%, #ffffff 50%); position: absolute; border-radius: 4px; transition: transform 0.2s ease-out; }
        .compass-label { position: absolute; font-size: 14px; font-weight: bold; color: #888; }
        .survival-card { background: #1a1a1a; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .survival-card h4 { margin: 0 0 5px 0; font-size: 14px; color: #ffc107; }
        .survival-card p { margin: 0; font-size: 12px; color: #ccc; line-height: 1.5; }
        .btn-flash { background: white; color: black; font-weight: bold; padding: 15px; border-radius: 12px; border: none; width: 100%; font-size: 16px; margin-bottom: 20px; cursor: pointer; text-transform: uppercase; }
        .screen-flash-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: white; z-index: 10000; display: none; }
        
        /* Modal General (Tengah Layar) */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: none; align-items: center; justify-content: center; z-index: 9000; backdrop-filter: blur(4px); }
        .modal-box-alert { background: white; width: 85%; max-width: 320px; padding: 25px; border-radius: 20px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: scaleUp 0.3s forwards; }
        @keyframes scaleUp { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        /* Kamera Eco */
        .camera-container { position: relative; width: 100%; border-radius: 18px; overflow: hidden; background: #000; line-height: 0; margin-bottom:15px; }
        #ecoVideoFeed { width: 100%; transform: scaleX(-1); }
        .eco-preview-img { width: 100%; border-radius: 15px; display: none; margin-bottom: 15px; border: 2px solid #11998e; }
        .modal-btn-group { display: flex; flex-direction: column; gap: 10px; width: 100%; }
        .modal-btn { border: none; padding: 14px; border-radius: 14px; font-weight: 600; font-size: 14px; cursor: pointer; width: 100%; transition: 0.2s; }
        .btn-potret { background: #11998e; color: white; }
        .btn-batal { background: #f0f0f0; color: #666; }
    </style>
</head>
<body>
    <div class="app-container" style="padding-bottom: 100px;">
        <div class="header-green">
            <div class="flex-between">
                <a href="cart.php" style="text-decoration: none; font-size: 24px;">🛒</a>
                <h1 style="font-size: 20px; flex: 1; text-align: center; margin: 0;">mountster</h1>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div id="ecoBadgeHeader" style="background: rgba(255,255,255,0.25); color: white; padding: 6px 10px; border-radius: 14px; font-size: 12px; font-weight: bold; border: 1px solid rgba(255,255,255,0.4);">🌿 <span id="ecoPointsText">0</span>/25</div>
                    <a href="profile.php"><div class="profile-icon"></div></a>
                </div>
            </div>
            <div style="margin-top: 20px;">
                <p id="greetingName" style="font-size: 14px;">Hi, <?php echo htmlspecialchars($userName); ?></p>
                <h2 style="font-size: 22px; margin-top: 5px;" class="hero-title">Mau Muncak<br class="mobile-br"> Kemana Hari Ini?</h2>
            </div>
            <input type="text" id="searchInput" class="search-bar" placeholder="Search Tenda, Carrier Atau Sepatu...">
        </div>

        <div class="categories">
            <a href="search.php?cat=Tenda" class="cat-chip" style="text-decoration: none;">Tenda</a>
            <a href="search.php?cat=Tas Carrier" class="cat-chip" style="text-decoration: none;">Carrier</a>
            <a href="search.php?cat=Sepatu" class="cat-chip" style="text-decoration: none;">Sepatu</a>
            <a href="search.php?cat=Alat Masak" class="cat-chip" style="text-decoration: none;">Alat Masak</a>
            <a href="search.php?cat=Apparel" class="cat-chip" style="text-decoration: none;">Apparel</a>
        </div>

        <div class="p-20" style="padding-top: 0;">
            <div style="width: 100%; height: 150px; background-image: url('images/Tent_aglow.jpg'); background-size: cover; background-position: center; position: relative; border-radius: 15px; overflow: hidden; margin-bottom: 20px;">
                <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 100%);"></div>
                <div style="position: relative; z-index: 2; padding: 20px; height: 100%; display: flex; flex-direction: column; justify-content: center; color: white;">
                    <h3 style="margin: 0; font-weight: 800; font-size: 24px; line-height: 1.15;">Paket Hemat<br>Semeru</h3>
                    <p style="margin: 8px 0 0; color: rgba(255,255,255,0.8); font-size: 13px; line-height: 1.4;">Sewa lengkap mulai Rp 150.000</p>
                </div>
            </div>

            <div class="weather-banner">
                <div style="text-align: center;">
                    <span style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 12px; font-size: 10px; font-weight: bold; border: 1px solid rgba(255,255,255,0.3);">🌦️ PREDIKSI CUACA CERDAS</span>
                    <h3 style="margin: 12px 0 15px 0; font-size: 16px;">Cek Cuaca & Siapkan Gearmu!</h3>
                </div>
                <select id="mountainSelect" class="weather-select">
                    <option value="" disabled selected>Pilih Gunung Tujuanmu...</option>
                    <option value="semeru">Gunung Semeru (Jawa Timur)</option>
                    <option value="rinjani">Gunung Rinjani (Lombok)</option>
                    <option value="prau">Gunung Prau (Jawa Tengah)</option>
                    <option value="merbabu">Gunung Merbabu (Jawa Tengah)</option>
                    <option value="gede">Gunung Gede (Jawa Barat)</option>
                    <option value="papandayan">Gunung Papandayan (Jawa Barat)</option>
                </select>
                <button class="btn-weather" onclick="checkWeather()" id="btnCheckWeather">Cek Kondisi & Rekomendasi</button>
                <div class="weather-result" id="weatherResultBox">
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <div id="wIcon" style="font-size: 40px;">⛈️</div>
                        <div>
                            <p id="wStatus" style="color: #ff4d4f; font-weight: bold; font-size: 14px; margin-bottom: 2px;">Status</p>
                            <p id="wDesc" style="font-size: 11px; color: #ddd; margin-bottom: 5px;">Deskripsi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-20" style="padding-top: 0;">
            <div class="eco-banner" id="ecoBannerMain">
                <div style="flex: 1;">
                    <span style="background: rgba(255,255,255,0.3); padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; color: white; border: 1px solid rgba(255,255,255,0.5);">♻️ ECO-WARRIOR</span>
                    <h3 style="margin: 10px 0 5px 0; font-size: 16px; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Bawa Turun Sampahmu!</h3>
                    <p style="font-size: 11px; color: #f0fff0; margin-bottom: 12px; line-height: 1.4; max-width: 90%;">Kumpulkan 25 poin untuk voucher diskon 20%. 1 Foto = 1 Poin.</p>
                </div>
                <div style="font-size: 60px; filter: drop-shadow(2px 4px 6px rgba(0,0,0,0.2));">📸</div>
            </div>
        </div>

        <div class="compare-section">
            <h3 style="font-size: 16px; margin-bottom: 5px;">Adu Mekanik Alat ⚖️</h3>
            <p style="font-size: 11px; color: var(--text-muted); margin-bottom: 15px;">Bandingkan spek tenda di sini!</p>
            <div class="compare-grid">
                <select id="gear1" class="input-form" style="padding: 8px; font-size: 12px; border-radius: 8px;">
                    <option value="naturehike_cloudup2">Naturehike Cloud Up 2</option>
                    <option value="eiger_shira1p">Eiger Shira 1P</option>
                    <option value="consina_magnum4">Consina Magnum 4</option>
                    <option value="arei_ds">Arei Discovery 2</option>
                    <option value="great_outdoor">Great Outdoor Java 4</option>
                </select>
                <div class="vs-badge">VS</div>
                <select id="gear2" class="input-form" style="padding: 8px; font-size: 12px; border-radius: 8px;">
                    <option value="eiger_shira1p" selected>Eiger Shira 1P</option>
                    <option value="naturehike_cloudup2">Naturehike Cloud Up 2</option>
                    <option value="consina_magnum4">Consina Magnum 4</option>
                    <option value="arei_ds">Arei Discovery 2</option>
                    <option value="great_outdoor">Great Outdoor Java 4</option>
                </select>
            </div>
            <button class="btn btn-outline-primary" style="margin-top: 15px; padding: 10px; font-size: 12px;" onclick="compareGear()">Bandingkan Sekarang</button>
            <div id="compareResult" class="compare-result" style="display: none;">
                <table>
                    <thead><tr><th>Spesifikasi</th><th id="titleA">Gear A</th><th id="titleB">Gear B</th></tr></thead>
                    <tbody>
                        <tr><td>Berat</td><td id="wA">...</td><td id="wB">...</td></tr>
                        <tr><td>Harga</td><td id="priceA" style="font-weight: bold;">...</td><td id="priceB" style="font-weight: bold;">...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-20 flex-between" style="padding-top: 0;">
            <h3 style="font-size: 16px;">Best Seller Rental</h3>
            <a href="search.php?rating=4.8" style="font-size: 12px; color: var(--text-muted); text-decoration: none;">See All</a>
        </div>
        <div class="product-grid" id="homeProductGrid"></div>
    </div>

    <div id="customAlertModal" class="modal-overlay">
        <div class="modal-box-alert">
            <div id="alertEmoji" style="font-size: 40px; margin-bottom: 10px;">⚠️</div>
            <h3 id="alertTitle" style="margin-bottom: 10px; font-size: 16px; color: #333;">Perhatian</h3>
            <p id="alertMessage" style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px;">Pesan di sini.</p>
            <button class="btn btn-primary" onclick="closeCustomAlert()">Mengerti</button>
        </div>
    </div>

    <div id="ecoCameraModal" class="modal-overlay">
        <div class="modal-box-alert" style="max-width:350px;">
            <h3 style="margin-bottom: 10px; font-size: 16px; color: #11998e;">Potret Sampahmu 📸</h3>
            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Pastikan sampah terlihat jelas.</p>
            
            <div class="camera-container" id="ecoCamContainer">
                <video id="ecoVideoFeed" autoplay playsinline></video>
            </div>
            <img id="ecoPreviewImg" class="eco-preview-img">
            <canvas id="ecoCanvas" style="display:none;"></canvas>

            <div class="modal-btn-group">
                <button class="modal-btn btn-potret" id="btnPotretEco" onclick="takeEcoSnapshot()">Potret Sekarang</button>
                <button class="modal-btn btn-potret" id="btnVerifEco" style="display:none; background:#27ae60;" onclick="verifikasiEcoFoto()">Verifikasi Foto</button>
                <button class="modal-btn btn-batal" id="btnUlangEco" style="display:none;" onclick="ulangEcoSnapshot()">Ulangi Foto</button>
                <button class="modal-btn btn-batal" id="btnBatalEco" onclick="closeEcoCamera()">Batal</button>
            </div>
        </div>
    </div>

    <div class="sos-container"><div class="sos-floating-btn" onclick="openSOSMode()">SOS</div></div>
    <div class="sos-modal" id="sosModal">
        <div class="sos-header">
            <h2 style="margin: 0; font-size: 18px; color: #ff4d4f;">🆘 MODE DARURAT</h2>
            <span style="font-size: 28px; cursor: pointer; color: #888;" onclick="closeSOSMode()">×</span>
        </div>
        <button class="btn-flash" onclick="toggleScreenFlash()">🔦 NYALAKAN SENTER LAYAR</button>
        <h3 style="font-size: 14px; margin-bottom: 15px; text-align: center; color: #888;">KOMPAS DIGITAL</h3>
        <div class="compass-box">
            <span class="compass-label" style="top: 5px;">U</span><span class="compass-label" style="bottom: 5px;">S</span>
            <span class="compass-label" style="right: 10px;">T</span><span class="compass-label" style="left: 10px;">B</span>
            <div class="compass-needle" id="compassNeedle"></div>
        </div>
        <p style="text-align: center; font-size: 10px; color: #666; margin-bottom: 25px;">(Pastikan rotasi layar & sensor HP aktif)</p>
        <h3 style="font-size: 14px; margin-bottom: 10px; color: #888;">Buku Saku Survival (Offline)</h3>
        <div class="survival-card"><h4>🥶 Gejala Hipotermia</h4><p>Ganti baju basah dengan kering. Peluk penderita. Jangan beri minuman keras.</p></div>
        <div class="survival-card"><h4>🐍 Gigitan Ular</h4><p>Tenangkan korban, bidai area tergigit. Jangan disedot! Segera evakuasi turun.</p></div>
    </div>
    <div class="screen-flash-overlay" id="flashOverlay" onclick="toggleScreenFlash()"><h1 style="color: black; text-align: center; margin-top: 50vh; transform: translateY(-50%); font-size: 40px;">TAP UNTUK MATIKAN</h1></div>

    <div class="bottom-nav">
        <a href="home.php" class="nav-item active"><span>🏠</span>Beranda</a>
        <a href="order.php" class="nav-item"><span>📋</span>Order</a>
        <a href="profile.php" class="nav-item"><span>👤</span>Saya</a>
    </div>

    <script>
        // Data LocalStorage
        const userEmailKey = "<?php echo $userEmail; ?>";
        const KEY_ECO_POINTS = 'mountsterEcoPoints_' + userEmailKey;
        const KEY_VOUCHER = 'mountsterVouchers_' + userEmailKey;

        let currentEcoPoints = parseInt(localStorage.getItem(KEY_ECO_POINTS)) || 0;

        window.addEventListener('DOMContentLoaded', (event) => {
            const editedName = localStorage.getItem('mountsterUserName_' + userEmailKey);
            const editedAvatar = localStorage.getItem('mountsterUserAvatar_' + userEmailKey);
            if (editedName) { document.getElementById('greetingName').innerText = "Hi, " + editedName; }
            if (editedAvatar) {
                const profileIcon = document.querySelector('.profile-icon');
                if(profileIcon) { profileIcon.style.backgroundImage = `url('${editedAvatar}')`; }
            }

            // Render UI Poin Eco
            document.getElementById('ecoPointsText').innerText = currentEcoPoints;
            
            // Cek status Voucher Eco
            let vouchers = JSON.parse(localStorage.getItem(KEY_VOUCHER)) || [];
            let isEcoClaimed = vouchers.some(v => v.code === 'ECO20');

            const ecoBanner = document.getElementById('ecoBannerMain');
            if (isEcoClaimed) {
                // Tampilan Abu-abu jika sudah diklaim
                ecoBanner.style.filter = "grayscale(100%) opacity(0.8)";
                ecoBanner.style.cursor = "not-allowed";
                ecoBanner.onclick = function() {
                    showCustomAlert("Anda sudah mengklaim voucher bulan ini, silahkan coba lagi bulan depan.", "Voucher Habis", "🛑");
                }
            } else {
                // Normal
                ecoBanner.onclick = openEcoCamera;
            }
        });
        
        function showCustomAlert(message, title = "Perhatian", emoji = "⚠️") {
            document.getElementById('alertEmoji').innerText = emoji;
            document.getElementById('alertTitle').innerText = title;
            document.getElementById('alertMessage').innerText = message;
            document.getElementById('customAlertModal').style.display = 'flex';
        }
        function closeCustomAlert() { document.getElementById('customAlertModal').style.display = 'none'; }

        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && this.value.trim() !== "") {
                try { if (typeof handleLatestSearch === "function") { handleLatestSearch(this.value); } } catch(err) {}
                window.location.href = 'search.php?q=' + encodeURIComponent(this.value.trim());
            }
        });

        const weatherDB = {
            'semeru': { icon: '⛈️', status: 'Potensi Badai', desc: 'Rawan badai pasir di puncak Mahameru.', color: '#ff4d4f' },
            'prau': { icon: '❄️', status: 'Suhu Sangat Dingin', desc: 'Suhu minus di pagi hari (Embun Es).', color: '#00bcd4' },
            'papandayan': { icon: '🌤️', status: 'Cerah Berawan', desc: 'Ideal untuk camping ceria. Terik siang hari.', color: '#8bc34a' },
            'rinjani': { icon: '☀️', status: 'Cerah Terik', desc: 'Sangat terik di savana. Siapkan air ekstra.', color: '#ff9800' },
            'merbabu': { icon: '🌧️', status: 'Hujan Ringan', desc: 'Siapkan jas hujan dan cover bag.', color: '#2196f3' },
            'gede': { icon: '🌫️', status: 'Kabut Tebal', desc: 'Jarak pandang terbatas. Bawa senter terang.', color: '#9e9e9e' }
        };
        function checkWeather() {
            const mnt = document.getElementById('mountainSelect').value;
            if(!mnt) { showCustomAlert("Pilih gunungnya dulu dong di kolom pilihan!", "Oops!", "⛰️"); return; }
            document.getElementById('weatherResultBox').style.display = 'block';
            document.getElementById('wIcon').innerText = weatherDB[mnt].icon;
            document.getElementById('wStatus').innerText = weatherDB[mnt].status;
            document.getElementById('wStatus').style.color = weatherDB[mnt].color;
            document.getElementById('wDesc').innerText = weatherDB[mnt].desc;
        }

        const gearDB = {
            'naturehike_cloudup2': { name: 'Naturehike Cloud Up 2', weight: 1.5, weightTxt: '1.5 kg', price: 'Rp 60.000' },
            'eiger_shira1p': { name: 'Eiger Shira 1P', weight: 2.1, weightTxt: '2.1 kg', price: 'Rp 45.000' },
            'consina_magnum4': { name: 'Consina Magnum 4', weight: 3.9, weightTxt: '3.9 kg', price: 'Rp 80.000' },
            'arei_ds': { name: 'Arei Discovery 2', weight: 2.5, weightTxt: '2.5 kg', price: 'Rp 50.000' },
            'great_outdoor': { name: 'Great Outdoor Java 4', weight: 4.1, weightTxt: '4.1 kg', price: 'Rp 75.000' }
        };
        function compareGear() {
            const g1 = document.getElementById('gear1').value; const g2 = document.getElementById('gear2').value;
            if(g1 === g2) { showCustomAlert("Jangan pilih tenda yang sama buat diadu!", "Nggak Bisa Diadu", "⚖️"); return; }
            document.getElementById('titleA').innerText = gearDB[g1].name; document.getElementById('titleB').innerText = gearDB[g2].name;
            document.getElementById('wA').innerText = gearDB[g1].weightTxt; document.getElementById('wB').innerText = gearDB[g2].weightTxt;
            document.getElementById('wA').className = (gearDB[g1].weight < gearDB[g2].weight) ? 'winner' : ''; 
            document.getElementById('wB').className = (gearDB[g2].weight < gearDB[g1].weight) ? 'winner' : '';
            document.getElementById('priceA').innerText = gearDB[g1].price; document.getElementById('priceB').innerText = gearDB[g2].price;
            document.getElementById('compareResult').style.display = 'block';
        }

        function getProductImage(product) {
            if (product.name === 'Eiger Wanderlust 60') return 'images/eiger-wanderlust-60.jpeg';
            if (product.name === 'Consina Magnum 4') return 'images/consina-magnum-4.jpeg';
            if (product.name === 'Naturehike Cloud Up 2') return 'images/naturehike-cloud-up-2.jpeg';
            if (product.name === 'Salomon Quest 4 GTX') return 'images/salomon-quest-4-gtx.jpg';
            return 'logo_mountster.png'; 
        }

        products.forEach((p, i) => { p.rating = p.rating || 4.8; p.reviews = p.reviews || 102; });
        const container = document.getElementById('homeProductGrid');
        products.slice(0,4).forEach((p, index) => {
            let hideClass = (index > 1) ? 'hide-on-mobile' : '';
            container.innerHTML += `
                <div class="card-new ${hideClass}">
                    <a href="detail.php?id=${p.id}" style="text-decoration:none; color:inherit; display:block;">
                        <img src="${getProductImage(p)}" alt="${p.name}" style="width: 100%; height: 100px; object-fit: contain; margin-bottom: 10px;">
                        <h4 class="card-new-title">${p.name}</h4>
                        <p class="card-new-price">${p.price}</p>
                    </a>
                    <div class="card-new-footer">
                        <span>⭐ ${p.rating}</span> <span>${p.reviews} Reviews</span>
                    </div>
                </div>`;
        });

        function openSOSMode() { document.getElementById('sosModal').style.display = 'flex'; if (window.DeviceOrientationEvent) { window.addEventListener('deviceorientation', handleOrientation); } }
        function closeSOSMode() { document.getElementById('sosModal').style.display = 'none'; window.removeEventListener('deviceorientation', handleOrientation); clearInterval(flashInterval); document.getElementById('flashOverlay').style.display = 'none'; }
        function handleOrientation(event) { let compassNeedle = document.getElementById('compassNeedle'); let dir = event.webkitCompassHeading || Math.abs(event.alpha - 360); if(dir) { compassNeedle.style.transform = `rotate(${-dir}deg)`; } }
        let flashInterval; let isFlashing = false;
        function toggleScreenFlash() {
            const overlay = document.getElementById('flashOverlay');
            if (isFlashing) { clearInterval(flashInterval); overlay.style.display = 'none'; isFlashing = false; } 
            else { isFlashing = true; overlay.style.display = 'block'; let isWhite = true; flashInterval = setInterval(() => { overlay.style.background = isWhite ? 'black' : 'white'; overlay.style.color = isWhite ? 'white' : 'black'; isWhite = !isWhite; }, 200); }
        }

        let ecoStream = null;
        
        async function openEcoCamera() {
            document.getElementById('ecoCameraModal').style.display = 'flex';
            document.getElementById('ecoCamContainer').style.display = 'block';
            document.getElementById('ecoPreviewImg').style.display = 'none';
            document.getElementById('btnPotretEco').style.display = 'block';
            document.getElementById('btnVerifEco').style.display = 'none';
            document.getElementById('btnUlangEco').style.display = 'none';
            document.getElementById('btnBatalEco').style.display = 'block';

            try {
                ecoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
                document.getElementById('ecoVideoFeed').srcObject = ecoStream;
            } catch (err) {
                showCustomAlert("Kamera tidak tersedia atau izin akses ditolak oleh browser.", "Kamera Gagal", "📸");
                closeEcoCamera();
            }
        }

        function takeEcoSnapshot() {
            const video = document.getElementById('ecoVideoFeed');
            const canvas = document.getElementById('ecoCanvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imgData = canvas.toDataURL('image/png');
            document.getElementById('ecoPreviewImg').src = imgData;
            
            document.getElementById('ecoCamContainer').style.display = 'none';
            document.getElementById('ecoPreviewImg').style.display = 'block';
            
            document.getElementById('btnPotretEco').style.display = 'none';
            document.getElementById('btnVerifEco').style.display = 'block';
            document.getElementById('btnUlangEco').style.display = 'block';
            document.getElementById('btnBatalEco').style.display = 'none';
        }

        function ulangEcoSnapshot() {
            document.getElementById('ecoCamContainer').style.display = 'block';
            document.getElementById('ecoPreviewImg').style.display = 'none';
            document.getElementById('btnPotretEco').style.display = 'block';
            document.getElementById('btnVerifEco').style.display = 'none';
            document.getElementById('btnUlangEco').style.display = 'none';
            document.getElementById('btnBatalEco').style.display = 'block';
        }

        function verifikasiEcoFoto() {
            const btnVerif = document.getElementById('btnVerifEco');
            btnVerif.innerText = "⏳ Sedang Memverifikasi AI...";
            btnVerif.style.background = "#f39c12"; 
            document.getElementById('btnUlangEco').style.display = 'none';

            setTimeout(() => {
                closeEcoCamera();
                
                // Tambah Poin
                currentEcoPoints += 1;
                localStorage.setItem(KEY_ECO_POINTS, currentEcoPoints);
                document.getElementById('ecoPointsText').innerText = currentEcoPoints;
                
                let message = `Mantap! Anda mendapatkan 1 Poin Eco. (Total: ${currentEcoPoints}/25).`;
                if(currentEcoPoints >= 25) {
                    message += " Poin sudah cukup, segera klaim voucher di Profil!";
                }

                showCustomAlert(message, "Verifikasi Berhasil!", "🌿");

                btnVerif.innerText = "Verifikasi Foto"; 
                btnVerif.style.background = "#27ae60"; 
            }, 3000); 
        }

        function closeEcoCamera() {
            if (ecoStream) ecoStream.getTracks().forEach(track => track.stop());
            document.getElementById('ecoCameraModal').style.display = 'none';
        }

        function getProductImage(product) {
            return product.image || getProductImageByName(product.name);
        }
    </script>
</body>
</html>