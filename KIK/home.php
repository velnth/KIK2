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
        .profile-icon {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            background-image: url('<?php echo $userAvatar; ?>');
            background-size: cover;
            background-position: center;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .weather-banner {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 20px;
            padding: 20px;
            color: white;
            position: relative;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
        }

        .weather-select {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: none;
            outline: none;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }

        .btn-weather {
            background: #ffc107;
            color: #000;
            font-weight: bold;
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .weather-result {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            padding: 15px;
            margin-top: 15px;
            display: none;
            animation: fadeIn 0.5s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
        }

        .eco-banner {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 20px;
            padding: 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 15px rgba(17, 153, 142, 0.3);
            margin-bottom: 20px;
            cursor: pointer;
            transition: filter 0.3s ease;
        }

        .compare-section {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin: 0 20px 20px 20px;
        }

        .compare-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 10px;
            align-items: center;
        }

        .vs-badge {
            background: #ff4d4f;
            color: white;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 50%;
            font-size: 12px;
        }

        .compare-result table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }

        .compare-result th,
        .compare-result td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: center;
        }

        .compare-result th {
            background: #f9f9f9;
            color: var(--text-muted);
            font-size: 11px;
        }

        .winner {
            background: #e6f7eb;
            font-weight: bold;
            color: #009933;
        }

        /* --- CSS BARU UNTUK SLIDER PAKET HEMAT (FULL WIDTH & DOTS) --- */
        .carousel-wrapper {
            position: relative;
            margin-bottom: 25px;
            margin-top: 10px;
            width: 100%; 
        }

        .slider-paket {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scrollbar-width: none; 
            -ms-overflow-style: none;  
            scroll-behavior: smooth;
            padding: 0 20px; 
            gap: 15px; 
        }
        
        .slider-paket::-webkit-scrollbar {
            display: none; 
        }

        .slider-item {
            flex: 0 0 100%; 
            scroll-snap-align: center;
            border-radius: 18px;
            overflow: hidden;
            position: relative;
            height: 210px; 
            background-size: cover;
            background-position: center;
            cursor: pointer;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        .slider-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
        }

        .slider-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #d1d5db;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .slider-dot.active {
            background-color: var(--primary);
            transform: scale(1.3);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* SOS CSS */
        .sos-container {
            position: fixed;
            bottom: 85px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 100%;
            pointer-events: none;
            z-index: 1000;
        }

        @media (min-width: 768px) {
            .sos-container { max-width: 800px; }
        }

        @media (min-width: 1024px) {
            .sos-container { max-width: 1000px; }
        }

        .sos-floating-btn {
            position: absolute;
            right: 20px;
            bottom: 0;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ff4d4f, #d9363e);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(255, 77, 79, 0.5);
            cursor: pointer;
            border: 3px solid white;
            pointer-events: auto;
            animation: pulseSOS 2s infinite;
        }

        @keyframes pulseSOS {
            0% { box-shadow: 0 0 0 0 rgba(255, 77, 79, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(255, 77, 79, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 77, 79, 0); }
        }

        .sos-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #121212;
            color: white;
            z-index: 9999;
            display: none;
            flex-direction: column;
            padding: 20px;
            overflow-y: auto;
        }

        .sos-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .compass-box {
            width: 150px;
            height: 150px;
            border: 4px solid #333;
            border-radius: 50%;
            margin: 0 auto 20px auto;
            position: relative;
            background: #1a1a1a;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .compass-needle {
            width: 4px;
            height: 120px;
            background: linear-gradient(to bottom, #ff4d4f 50%, #ffffff 50%);
            position: absolute;
            border-radius: 4px;
            transition: transform 0.2s ease-out;
        }

        .compass-label { position: absolute; font-size: 14px; font-weight: bold; color: #888; }
        .survival-card { background: #1a1a1a; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .survival-card h4 { margin: 0 0 5px 0; font-size: 14px; color: #ffc107; }
        .survival-card p { margin: 0; font-size: 12px; color: #ccc; line-height: 1.5; }
        .btn-flash { background: white; color: black; font-weight: bold; padding: 15px; border-radius: 12px; border: none; width: 100%; font-size: 16px; margin-bottom: 20px; cursor: pointer; text-transform: uppercase; }
        .screen-flash-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: white; z-index: 10000; display: none; }
        
        /* Modal General (Tengah Layar) */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: none; align-items: center; justify-content: center; z-index: 9000; backdrop-filter: blur(4px); }
        .modal-box-alert { background: white; width: 85%; max-width: 320px; padding: 25px; border-radius: 20px; text-align: center; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2); animation: scaleUp 0.3s forwards; }
        @keyframes scaleUp { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        /* Kamera Eco */
        .camera-container { position: relative; width: 100%; border-radius: 18px; overflow: hidden; background: #000; line-height: 0; margin-bottom: 15px; }
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
                    <a href="profile.php">
                        <div class="profile-icon"></div>
                    </a>
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

        <div class="carousel-wrapper">
            <div class="slider-paket" id="paketSlider">
                
                <div class="slider-item" style="background-image: url('images/Tent_aglow.jpg');" onclick="beliPaketSemeru()">
                    <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.1) 100%);"></div>
                    <div style="position: relative; z-index: 2; padding: 25px; height: 100%; display: flex; flex-direction: column; justify-content: center; color: white;">
                        <h3 style="margin: 0; font-weight: 800; font-size: 28px; line-height: 1.2;">Paket Hemat<br>Semeru</h3>
                        <p style="margin: 8px 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">Sewa lengkap Rp 150.000</p>
                    </div>
                </div>

                <div class="slider-item" style="background-image: url('https://i.pinimg.com/1200x/33/a6/68/33a6682ba6589535ebdc8d816917748a.jpg');" onclick="beliPaketRinjani()">
                    <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.1) 100%);"></div>
                    <div style="position: relative; z-index: 2; padding: 25px; height: 100%; display: flex; flex-direction: column; justify-content: center; color: white;">
                        <h3 style="margin: 0; font-weight: 800; font-size: 28px; line-height: 1.2; color: #ffeb3b;">Paket Hemat<br>Rinjani</h3>
                        <p style="margin: 8px 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">Premium Gear Rp 200.000</p>
                    </div>
                </div>

                <div class="slider-item" style="background-image: url('https://i.pinimg.com/1200x/08/5c/f6/085cf63f1d2bada08adc7ac4f31ee27b.jpg');" onclick="beliPaketPrau()">
                    <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.1) 100%);"></div>
                    <div style="position: relative; z-index: 2; padding: 25px; height: 100%; display: flex; flex-direction: column; justify-content: center; color: white;">
                        <h3 style="margin: 0; font-weight: 800; font-size: 28px; line-height: 1.2; color: #a5d6a7;">Paket Santai<br>Prau</h3>
                        <p style="margin: 8px 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">Sewa tektok Rp 100.000</p>
                    </div>
                </div>

            </div>

            <div class="slider-dots" id="sliderDots">
                <div class="slider-dot active" onclick="goToSlide(0)"></div>
                <div class="slider-dot" onclick="goToSlide(1)"></div>
                <div class="slider-dot" onclick="goToSlide(2)"></div>
            </div>
        </div>
        <div class="p-20" style="padding-top: 0;">
            <div class="weather-banner">
                <div style="text-align: center;">
                    <span style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 12px; font-size: 10px; font-weight: bold; border: 1px solid rgba(255,255,255,0.3);">🌦️ PREDIKSI CUACA CERDAS</span>
                    <h3 style="margin: 12px 0 15px 0; font-size: 16px;">Cek Cuaca & Siapkan Gearmu!</h3>
                </div>
                <select id="selectGunung" class="weather-select">
                </select>
                <div id="hasilCuaca" style="margin-top: 15px;"></div>
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
                    <thead>
                        <tr>
                            <th>Spesifikasi</th>
                            <th id="titleA">Gear A</th>
                            <th id="titleB">Gear B</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Berat</td>
                            <td id="wA">...</td>
                            <td id="wB">...</td>
                        </tr>
                        <tr>
                            <td>Harga</td>
                            <td id="priceA" style="font-weight: bold;">...</td>
                            <td id="priceB" style="font-weight: bold;">...</td>
                        </tr>
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

    <div class="sos-container">
        <div class="sos-floating-btn" onclick="openSOSMode()">SOS</div>
    </div>
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
        <div class="survival-card">
            <h4>🥶 Gejala Hipotermia</h4>
            <p>Ganti baju basah dengan kering. Peluk penderita. Jangan beri minuman keras.</p>
        </div>
        <div class="survival-card">
            <h4>🐍 Gigitan Ular</h4>
            <p>Tenangkan korban, bidai area tergigit. Jangan disedot! Segera evakuasi turun.</p>
        </div>
    </div>
    <div class="screen-flash-overlay" id="flashOverlay" onclick="toggleScreenFlash()">
        <h1 style="color: black; text-align: center; margin-top: 50vh; transform: translateY(-50%); font-size: 40px;">TAP UNTUK MATIKAN</h1>
    </div>

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
            if (editedName) {
                document.getElementById('greetingName').innerText = "Hi, " + editedName;
            }
            if (editedAvatar) {
                const profileIcon = document.querySelector('.profile-icon');
                if (profileIcon) {
                    profileIcon.style.backgroundImage = `url('${editedAvatar}')`;
                }
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

        // --- SCRIPT AUTO SLIDER PAKET HEMAT ---
        const slider = document.getElementById('paketSlider');
        const dots = document.querySelectorAll('.slider-dot');
        let currentSlide = 0;
        const totalSlides = dots.length;
        let slideInterval;

        function updateDots(index) {
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        }

        function goToSlide(index) {
            currentSlide = index;
            // Kita ukur lebar satu item secara dinamis
            const slideWidth = document.querySelector('.slider-item').offsetWidth + 15; // 15px adalah gap
            slider.scrollTo({ left: slideWidth * index, behavior: 'smooth' });
            updateDots(index);
            resetInterval();
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            goToSlide(currentSlide);
        }

        function resetInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 3500); // Ganti slide setiap 3.5 detik
        }

        // Sinkronisasi manual scroll/swipe dengan titik indikator
        slider.addEventListener('scroll', () => {
            const scrollLeft = slider.scrollLeft;
            const slideWidth = document.querySelector('.slider-item').offsetWidth + 15;
            const newIndex = Math.round(scrollLeft / slideWidth);
            if (newIndex !== currentSlide && newIndex >= 0 && newIndex < totalSlides) {
                currentSlide = newIndex;
                updateDots(currentSlide);
                resetInterval();
            }
        });

        // Mulai auto slide
        resetInterval();
        // --- END SCRIPT SLIDER ---

        function showCustomAlert(message, title = "Perhatian", emoji = "⚠️") {
            document.getElementById('alertEmoji').innerText = emoji;
            document.getElementById('alertTitle').innerText = title;
            document.getElementById('alertMessage').innerText = message;
            document.getElementById('customAlertModal').style.display = 'flex';
        }

        function closeCustomAlert() {
            document.getElementById('customAlertModal').style.display = 'none';
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.trim() !== "") {
                try {
                    if (typeof handleLatestSearch === "function") {
                        handleLatestSearch(this.value);
                    }
                } catch (err) {}
                window.location.href = 'search.php?q=' + encodeURIComponent(this.value.trim());
            }
        });

        const databaseGunung = {
            // --- SUMATERA ---
            "Gunung Kerinci (Jambi)": { lat: -1.6966, lon: 101.2642 },
            "Gunung Leuser (Aceh)": { lat: 3.7547, lon: 97.1683 },
            "Gunung Dempo (Sumatera Selatan)": { lat: -4.0150, lon: 103.1119 },

            // --- JAWA BARAT & BANTEN ---
            "Gunung Gede (Jawa Barat)": { lat: -6.7844, lon: 106.9840 },
            "Gunung Pangrango (Jawa Barat)": { lat: -6.7758, lon: 106.9639 },
            "Gunung Salak (Jawa Barat)": { lat: -6.7150, lon: 106.7328 },
            "Gunung Ciremai (Jawa Barat)": { lat: -6.8922, lon: 108.4064 },
            "Gunung Papandayan (Jawa Barat)": { lat: -7.3200, lon: 107.7300 },
            "Gunung Cikuray (Jawa Barat)": { lat: -7.3217, lon: 107.8600 },

            // --- JAWA TENGAH & DIY ---
            "Gunung Slamet (Jawa Tengah)": { lat: -7.2420, lon: 109.2212 },
            "Gunung Sindoro (Jawa Tengah)": { lat: -7.3005, lon: 109.9961 },
            "Gunung Sumbing (Jawa Tengah)": { lat: -7.3844, lon: 110.0767 },
            "Gunung Prau (Jawa Tengah)": { lat: -7.1873, lon: 109.9213 },
            "Gunung Merbabu (Jawa Tengah)": { lat: -7.4533, lon: 110.4394 },
            "Gunung Lawu (Jawa Tengah/Jatim)": { lat: -7.6272, lon: 111.1920 },

            // --- JAWA TIMUR ---
            "Gunung Semeru (Jawa Timur)": { lat: -8.1077, lon: 112.9223 },
            "Gunung Arjuno (Jawa Timur)": { lat: -7.7642, lon: 112.5892 },
            "Gunung Welirang (Jawa Timur)": { lat: -7.7292, lon: 112.5800 },
            "Gunung Raung (Jawa Timur)": { lat: -8.1256, lon: 114.0456 },
            "Gunung Argopuro (Jawa Timur)": { lat: -7.9653, lon: 113.5658 },

            // --- BALI & NUSA TENGGARA ---
            "Gunung Agung (Bali)": { lat: -8.3433, lon: 115.5072 },
            "Gunung Batur (Bali)": { lat: -8.2394, lon: 115.3775 },
            "Gunung Rinjani (Lombok)": { lat: -8.4116, lon: 116.4574 },
            "Gunung Tambora (Sumbawa)": { lat: -8.2478, lon: 117.9922 },

            // --- KALIMANTAN, SULAWESI, MALUKU, PAPUA ---
            "Gunung Bukit Raya (Kalimantan)": { lat: -0.6611, lon: 112.6861 },
            "Gunung Latimojong (Sulawesi Selatan)": { lat: -3.3933, lon: 120.0247 },
            "Gunung Binaiya (Maluku)": { lat: -3.1722, lon: 129.4536 },
            "Puncak Jaya / Carstensz (Papua)": { lat: -4.0833, lon: 137.1833 }
        };

        const selectGunung = document.getElementById('selectGunung');
        const wadahHasil = document.getElementById('hasilCuaca');

        if (selectGunung) {
            Object.keys(databaseGunung).sort().forEach(namaGunung => {
                const optionBaru = document.createElement('option');
                optionBaru.value = namaGunung;
                optionBaru.textContent = namaGunung;
                selectGunung.appendChild(optionBaru);
            });
        }

        function terjemahkanCuaca(code) {
            if (code === 0) return "Cerah ☀️";
            if (code >= 1 && code <= 3) return "Berawan ⛅";
            if (code === 45 || code === 48) return "Berkabut 🌫️";
            if (code >= 51 && code <= 67) return "Hujan Ringan/Sedang 🌧️";
            if (code >= 80 && code <= 82) return "Hujan Lebat 🌧️☔";
            if (code >= 95) return "Badai Petir ⛈️";
            return "Tidak Diketahui";
        }

        if (selectGunung && wadahHasil) {
            selectGunung.addEventListener('change', async function() {
                const namaPilihan = this.value;
                const data = databaseGunung[namaPilihan];

                if (!data) return;

                wadahHasil.innerHTML = "<p style='color: white; font-size: 14px;'>Mengecek satelit cuaca untuk " + namaPilihan + "...</p>";

                try {
                    const response = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${data.lat}&longitude=${data.lon}&current_weather=true`);
                    const result = await response.json();
                    const cuaca = result.current_weather;

                    const suhu = Math.round(cuaca.temperature);
                    const kondisi = terjemahkanCuaca(cuaca.weathercode);

                    let rekomendasi = "";
                    if (suhu <= 12) {
                        rekomendasi = "🥶 <b>Suhu Ekstrem:</b> Wajib sewa Down Jacket tebal & Sleeping Bag Polar.";
                    } else if (suhu > 12 && suhu <= 18) {
                        rekomendasi = "🧥 <b>Suhu Dingin:</b> Siapkan Jaket Gunung Windproof & Baju Hangat.";
                    } else {
                        rekomendasi = "👕 <b>Suhu Aman:</b> Gunakan pakaian base layer yang menyerap keringat.";
                    }

                    if (cuaca.weathercode >= 51) {
                        rekomendasi += "<br>☔ <b>Curah Hujan Tinggi:</b> Wajib sedia Jas Hujan (Raincoat) setelan & Cover Bag.";
                    }

                    wadahHasil.innerHTML = `
                <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; color: white; text-align: left; margin-top: 10px;">
                    <h3 style="margin: 0 0 10px 0; font-size: 20px;">${kondisi} | ${suhu}°C</h3>
                    <p style="margin: 0; font-size: 14px; line-height: 1.5;">${rekomendasi}</p>
                </div>
            `;

                } catch (err) {
                    wadahHasil.innerHTML = "<p style='color: red;'>Gagal mengambil data cuaca, cek koneksi internet.</p>";
                }
            });
        }

        const gearDB = {
            'naturehike_cloudup2': { name: 'Naturehike Cloud Up 2', weight: 1.5, weightTxt: '1.5 kg', price: 'Rp 60.000' },
            'eiger_shira1p': { name: 'Eiger Shira 1P', weight: 2.1, weightTxt: '2.1 kg', price: 'Rp 45.000' },
            'consina_magnum4': { name: 'Consina Magnum 4', weight: 3.9, weightTxt: '3.9 kg', price: 'Rp 80.000' },
            'arei_ds': { name: 'Arei Discovery 2', weight: 2.5, weightTxt: '2.5 kg', price: 'Rp 50.000' },
            'great_outdoor': { name: 'Great Outdoor Java 4', weight: 4.1, weightTxt: '4.1 kg', price: 'Rp 75.000' }
        };

        function compareGear() {
            const g1 = document.getElementById('gear1').value;
            const g2 = document.getElementById('gear2').value;
            if (g1 === g2) {
                showCustomAlert("Jangan pilih tenda yang sama buat diadu!", "Nggak Bisa Diadu", "⚖️");
                return;
            }
            document.getElementById('titleA').innerText = gearDB[g1].name;
            document.getElementById('titleB').innerText = gearDB[g2].name;
            document.getElementById('wA').innerText = gearDB[g1].weightTxt;
            document.getElementById('wB').innerText = gearDB[g2].weightTxt;
            document.getElementById('wA').className = (gearDB[g1].weight < gearDB[g2].weight) ? 'winner' : '';
            document.getElementById('wB').className = (gearDB[g2].weight < gearDB[g1].weight) ? 'winner' : '';
            document.getElementById('priceA').innerText = gearDB[g1].price;
            document.getElementById('priceB').innerText = gearDB[g2].price;
            document.getElementById('compareResult').style.display = 'block';
        }

        function getProductImage(product) {
            if (product.name === 'Eiger Wanderlust 60') return 'images/eiger-wanderlust-60.jpeg';
            if (product.name === 'Consina Magnum 4') return 'images/consina-magnum-4.jpeg';
            if (product.name === 'Naturehike Cloud Up 2') return 'images/naturehike-cloud-up-2.jpeg';
            // Tambahkan baris di bawah ini agar gambar Great Outdoor muncul
            if (product.name === 'Great Outdoor Java 4') return 'images/great-outdoor-java-4.jpeg'; 
            if (product.name === 'Salomon Quest 4 GTX') return 'images/salomon-quest-4-gtx.jpg';
            
            return product.image || 'logo_mountster.png';
        }

        products.forEach((p, i) => {
            p.rating = p.rating || 4.8;
            p.reviews = p.reviews || 102;
        });
        const container = document.getElementById('homeProductGrid');
        products.slice(0, 4).forEach((p, index) => {
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

        function openSOSMode() {
            document.getElementById('sosModal').style.display = 'flex';
            if (window.DeviceOrientationEvent) {
                window.addEventListener('deviceorientation', handleOrientation);
            }
        }

        function closeSOSMode() {
            document.getElementById('sosModal').style.display = 'none';
            window.removeEventListener('deviceorientation', handleOrientation);
            clearInterval(flashInterval);
            document.getElementById('flashOverlay').style.display = 'none';
        }

        function handleOrientation(event) {
            let compassNeedle = document.getElementById('compassNeedle');
            let dir = event.webkitCompassHeading || Math.abs(event.alpha - 360);
            if (dir) {
                compassNeedle.style.transform = `rotate(${-dir}deg)`;
            }
        }
        let flashInterval;
        let isFlashing = false;

        function toggleScreenFlash() {
            const overlay = document.getElementById('flashOverlay');
            if (isFlashing) {
                clearInterval(flashInterval);
                overlay.style.display = 'none';
                isFlashing = false;
            } else {
                isFlashing = true;
                overlay.style.display = 'block';
                let isWhite = true;
                flashInterval = setInterval(() => {
                    overlay.style.background = isWhite ? 'black' : 'white';
                    overlay.style.color = isWhite ? 'white' : 'black';
                    isWhite = !isWhite;
                }, 200);
            }
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
                ecoStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "environment"
                    }
                });
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
                if (currentEcoPoints >= 25) {
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

        // --- FUNGSI PAKET HEMAT (SEMERU, RINJANI, PRAU) ---

        function beliPaketSemeru() {
            const paketSemeru = [
                { id: 'ps_01', name: 'Star River 2P Naturehike', price: 'Rp 60.000', qty: 1, image: 'images/naturehike-star-river.png' },
                { id: 'ps_02', name: 'Consina Tarebbi 60L', price: 'Rp 40.000', qty: 1, image: 'images/consina-tarebbi-60l.jpg' },
                { id: 'ps_03', name: 'Consina sleep warmer - sleeping bag', price: 'Rp 25.000', qty: 1, image: 'images/consina-sleepwarmer.png' },
                { id: 'ps_04', name: 'Kompor Mawar (Windproof)', price: 'Rp 7.500', qty: 1, image: 'images/kompor-mawar-windproof.jpg' },
                { id: 'ps_05', name: 'Nesting Bulat 4 in 1', price: 'Rp 7.500', qty: 1, image: 'images/nesting-bulat-4-in-1.png' },
                { id: 'ps_06', name: 'Consina Matras Alumunium', price: 'Rp 10.000', qty: 1, image: 'images/consina-matras-alumunium.png' }
            ];
            localStorage.setItem('mountsterDirectBuy', JSON.stringify(paketSemeru));
            window.location.href = 'checkout.php';
        }

        function beliPaketRinjani() {
            const paketRinjani = [
                { id: 'pr_01', name: 'Naturehike Cloud Up 2', price: 'Rp 60.000', qty: 1, image: 'images/naturehike-cloud-up-2.jpeg' },
                { id: 'pr_02', name: 'Osprey Aether 65L', price: 'Rp 80.000', qty: 1, image: 'images/osprey-aether-65l.jpg' },
                { id: 'pr_03', name: 'Sleeping Bag Polar', price: 'Rp 25.000', qty: 1, image: 'images/consina-sleepwarmer.png' },
                { id: 'pr_04', name: 'Trangia 27-1 UL', price: 'Rp 25.000', qty: 1, image: 'images/trangia-27-1-ul.jpg' },
                { id: 'pr_05', name: 'Matras Gulung', price: 'Rp 10.000', qty: 1, image: 'images/consina-matras-alumunium.png' }
            ];
            localStorage.setItem('mountsterDirectBuy', JSON.stringify(paketRinjani));
            window.location.href = 'checkout.php';
        }

        function beliPaketPrau() {
            const paketPrau = [
                { id: 'pp_01', name: 'Eiger Shira 1P', price: 'Rp 45.000', qty: 1, image: 'images/eiger-shira-1p.jpg' },
                { id: 'pp_02', name: 'Arei Ramandika 60L', price: 'Rp 30.000', qty: 1, image: 'images/arei-ramandika-60l.jpg' },
                { id: 'pp_03', name: 'Kompor Portable Kotak', price: 'Rp 15.000', qty: 1, image: 'images/kompor-portable-kotak.jpg' },
                { id: 'pp_04', name: 'Matras Gulung', price: 'Rp 10.000', qty: 1, image: 'images/consina-matras-alumunium.png' }
            ];
            localStorage.setItem('mountsterDirectBuy', JSON.stringify(paketPrau));
            window.location.href = 'checkout.php';
        }

    </script>
</body>

</html>