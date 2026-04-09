<?php
include_once 'auth.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

$userEmail = $_SESSION['user_email'];
$userName = $_SESSION['user_name'];
$userAvatar = $_SESSION['user_avatar'] ?? 'https://api.dicebear.com/8.x/lorelei/svg?seed=Guest';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Mountster</title>
    <link rel="stylesheet" href="style.css">
    <script src="database.js"></script>
    <script src="wishlist.js"></script>
    <style>
        /* --- Core Layout --- */
        .profile-header-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            background: #eee;
        }

        .profile-header-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* --- Modal System --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease;
        }

        .modal-box {
            background: white;
            width: 85%;
            max-width: 340px;
            padding: 30px 25px;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transform: scale(0.8);
            animation: scaleUp 0.3s forwards;
        }

        .sub-page {
            display: none;
            animation: slideUp 0.3s ease-out;
        }

        .sub-page.active {
            display: block;
        }

        /* --- Notifications --- */
        .notif-badge {
            width: 8px;
            height: 8px;
            background: #ff4d4f;
            border-radius: 50%;
            display: none;
            margin-left: 5px;
            vertical-align: middle;
        }

        .notif-item {
            display: flex;
            gap: 15px;
            padding: 18px;
            background: #fff;
            border-radius: 20px;
            margin-bottom: 12px;
            border: 1px solid #f2f2f2;
            position: relative;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .notif-item.unread {
            background: #f9fffb;
            border-color: #dcf2e3;
        }

        .notif-icon-box {
            width: 48px;
            height: 48px;
            background: #e9f5ee;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .notif-content {
            flex: 1;
            text-align: left;
        }

        .notif-title {
            font-size: 14px;
            font-weight: 700;
            color: #1b4332;
            margin-bottom: 3px;
        }

        .notif-text {
            font-size: 12px;
            color: #666;
            line-height: 1.4;
        }

        .unread-dot {
            width: 10px;
            height: 10px;
            background: #27ae60;
            border-radius: 50%;
            position: absolute;
            top: 20px;
            right: 20px;
        }

        /* --- Camera & AI --- */
        .camera-container {
            position: relative;
            width: 100%;
            border-radius: 18px;
            overflow: hidden;
            background: #000;
            line-height: 0;
        }

        #videoFeed {
            width: 100%;
            transform: scaleX(-1);
        }

        .camera-guide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            display: none;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.8;
            transition: all 0.4s ease;
        }

        .camera-guide.detected {
            filter: invert(48%) sepia(79%) saturate(2476%) hue-rotate(118deg) brightness(118%) contrast(119%);
            opacity: 1;
        }

        .guide-face {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 500'%3E%3Cpath d='M200,100 c-50,0 -80,40 -80,100 s30,120 80,120 s80,-60 80,-120 s-30,-100 -80,-100' fill='none' stroke='white' stroke-width='3' stroke-dasharray='10'/%3E%3C/svg%3E");
        }

        .guide-ktp {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 500'%3E%3Crect x='50' y='150' width='300' height='200' rx='15' fill='none' stroke='white' stroke-width='3' stroke-dasharray='10'/%3E%3C/svg%3E");
        }

        .guide-selfie {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 500'%3E%3Ccircle cx='200' cy='180' r='70' fill='none' stroke='white' stroke-width='3' stroke-dasharray='10'/%3E%3Crect x='100' y='280' width='200' height='120' rx='10' fill='none' stroke='white' stroke-width='3' stroke-dasharray='10'/%3E%3C/svg%3E");
        }

        /* --- Menu Items --- */
        .menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            padding: 12px 0;
            border: none;
        }

        .menu-item.disabled {
            cursor: default;
            opacity: 0.5;
        }

        .menu-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #AEC3B0;
            text-transform: uppercase;
            margin-top: 25px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        /* --- Style Tambahan: VOUCHER --- */
        .voucher-card {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            position: relative;
            margin-bottom: 15px;
            box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .voucher-card::before,
        .voucher-card::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            background-color: var(--bg-color);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1;
        }

        .voucher-card::before {
            left: -15px;
            box-shadow: inset -3px 0 5px rgba(0, 0, 0, 0.1);
        }

        .voucher-card::after {
            right: -15px;
            box-shadow: inset 3px 0 5px rgba(0, 0, 0, 0.1);
        }

        .voucher-code {
            display: inline-block;
            background: rgba(0, 0, 0, 0.15);
            border: 1px dashed rgba(255, 255, 255, 0.6);
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: bold;
            font-family: monospace;
            font-size: 14px;
            letter-spacing: 2px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .voucher-code:hover {
            background: rgba(0, 0, 0, 0.25);
            transform: scale(1.05);
        }

        /* Label Verified Header */
        .status-verified {
            color: #1b4332;
            font-weight: 800;
            font-size: 11px;
            display: none;
            margin-top: 4px;
        }

        /* Buttons */
        .btn-camera-trigger {
            background: #f8f8f8;
            border: 1px solid #ddd;
            padding: 10px 15px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            color: #555;
            margin-top: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .upload-box {
            border: 2px dashed #ccc;
            padding: 15px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 10px;
            cursor: pointer;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .upload-preview {
            width: 100%;
            height: 120px;
            object-fit: contain;
            border-radius: 10px;
            display: none;
            margin-top: 10px;
        }

        .modal-btn-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            margin-top: 15px;
        }

        .modal-btn {
            border: none;
            padding: 14px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            width: 100%;
            transition: 0.2s;
        }

        .btn-potret {
            background: #1b4332;
            color: white;
        }

        .btn-potret:disabled {
            background: #ccc !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-batal {
            background: #f0f0f0;
            color: #666;
        }

        .success-checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: #e9f5ee;
            color: #27ae60;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleUp { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>

<body>
    <div class="app-container" style="padding-bottom: 50px; min-height: 100vh;">

        <div class="p-20 flex-between">
            <span id="backBtn" style="cursor: pointer; font-size: 20px; font-weight: bold;">&larr;</span>
            <h2 id="pageTitle" style="font-size: 18px;">Profile</h2>
            <div style="width: 20px;"></div>
        </div>

        <div id="profileHeaderInfo" class="p-20" style="display: flex; align-items: center; gap: 15px;">
            <div class="profile-header-icon"><img id="mainAvatar" src="<?php echo htmlspecialchars($userAvatar); ?>" alt="Avatar"></div>
            <div>
                <h3 id="profileName" style="font-size: 18px; margin: 0; text-transform: lowercase;"><?php echo htmlspecialchars($userName); ?></h3>
                <p id="profileEmail" style="color: var(--text-muted); font-size: 12px; margin: 2px 0 0 0;"><?php echo htmlspecialchars($userEmail); ?></p>
                <div id="verifiedLabel" class="status-verified">&#10003; Verified</div>
            </div>
        </div>

        <div id="mainProfileMenu" style="padding: 0 20px;">
            <p class="menu-section-title">General</p>
            <div class="menu-item" onclick="openSubPage('editProfilePage', 'Edit Profile')"><span>Edit Profile</span> <span style="color:#ccc;">&rsaquo;</span></div>

            <div class="menu-item" id="verifMenu" onclick="openSubPage('verifPage', 'Verifikasi 2 Langkah')">
                <span>Verifikasi 2 Langkah</span>
                <span id="verifArrow" style="color:#ccc;">&rsaquo;</span>
            </div>

            <div class="menu-item" onclick="openSubPage('notificationsPage', 'Notifications')"><span>Notifications <span class="notif-badge" id="menuNotifDot"></span></span> <span style="color:#ccc;">&rsaquo;</span></div>

            <div class="menu-item" onclick="openSubPage('voucherPage', 'Voucher Saya'); renderVouchers();">
                <span>Voucher Saya <span id="voucherBadge" style="font-size:10px; background:#ff4d4f; color:white; padding:2px 8px; border-radius:10px; margin-left: 8px; display: none;">Baru</span></span>
                <span style="color:#ccc;">&rsaquo;</span>
            </div>

            <div class="menu-item" onclick="openSubPage('wishlistPage', 'Wishlist')"><span>Wishlist</span> <span style="color:#ccc;">&rsaquo;</span></div>

            <p class="menu-section-title">Legal</p>
            <div class="menu-item" onclick="openSubPage('termsPage', 'Terms of Use')"><span>Terms of Use</span> <span style="color:#ccc;">&rsaquo;</span></div>
            <div class="menu-item" onclick="openSubPage('privacyPage', 'Privacy Policy')"><span>Privacy Policy</span> <span style="color:#ccc;">&rsaquo;</span></div>

            <p class="menu-section-title">Personal</p>
            <div class="menu-item" onclick="showBugModal()"><span>Report a Bug</span> <span style="color:#ccc;">&rsaquo;</span></div>
            <a href="logout.php" class="menu-item" style="color: red; text-decoration: none;">Logout</a>
        </div>

        <div style="padding: 0 20px;">
            <div id="notificationsPage" class="sub-page">
                <div id="notifContainer"></div>
            </div>

            <div id="voucherPage" class="sub-page">
                <div id="voucherContainer"></div>
            </div>

            <div id="editProfilePage" class="sub-page">
                <div style="text-align: center; margin-bottom: 25px;">
                    <div class="profile-header-icon" style="margin: 0 auto 15px;"><img id="editAvatarPreview" src="<?php echo htmlspecialchars($userAvatar); ?>"></div>
                    <div style="display: flex; justify-content: center; gap: 10px;">
                        <button class="btn-camera-trigger" onclick="startCamera('editAvatarPreview', 'face')">📸 Ambil Foto</button>
                        <input type="file" id="galleryInput" accept="image/*" style="display: none;" onchange="handleGalleryUpload(this, 'editAvatarPreview')">
                        <button class="btn-camera-trigger" onclick="document.getElementById('galleryInput').click()">🖼️ Galeri</button>
                    </div>
                </div>
                <div class="input-group"><label>Display Name</label><input type="text" id="editNameInput" class="input-form" value="<?php echo htmlspecialchars($userName); ?>"></div>
                <button class="btn btn-primary" onclick="showSuccessModal()">Save Changes</button>
            </div>

            <div id="verifPage" class="sub-page">
                <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Verifikasi wajib diambil secara Live.</p>
                <div class="form-group"><label style="font-size: 11px; font-weight: bold; display: block; margin-bottom: 5px;">1. Foto KTP</label>
                    <div class="upload-box"><img id="ktpPreview" class="upload-preview">
                        <div style="display: flex; justify-content: center;"><button class="btn-camera-trigger" onclick="startCamera('ktpPreview', 'ktp')">📸 Ambil Foto KTP</button></div>
                    </div>
                </div>
                <div class="form-group"><label style="font-size: 11px; font-weight: bold; display: block; margin-bottom: 5px;">2. Selfie + KTP</label>
                    <div class="upload-box"><img id="selfiePreview" class="upload-preview">
                        <div style="display: flex; justify-content: center;"><button class="btn-camera-trigger" onclick="startCamera('selfiePreview', 'selfie')">📸 Ambil Selfie</button></div>
                    </div>
                </div>
                <button id="btnSubmitVerif" class="btn btn-primary" onclick="submitVerifikasi()">Ajukan Verifikasi</button>
            </div>

            <div id="wishlistPage" class="sub-page">
                <div id="wishlistContainer"></div>
            </div>

            <div id="termsPage" class="sub-page">
                <div style="padding: 10px 5px; text-align: left; font-size: 13px; line-height: 1.6; color: #444;">
                    
                    <p style="margin-bottom: 15px; text-align: center; color: #666;">Dengan menggunakan layanan rental alat hiking pada website ini, pengguna dianggap telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku.</p>

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">1. Ketentuan Umum</h4>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Penyewa wajib berusia minimal 17 tahun atau telah memiliki identitas resmi yang sah.</li>
                        <li style="margin-bottom: 5px;">Penyewa wajib memberikan data diri yang benar, lengkap, dan dapat dipertanggungjawabkan.</li>
                        <li style="margin-bottom: 5px;">Pihak penyedia berhak menolak permintaan sewa tanpa memberikan alasan tertentu.</li>
                    </ol>

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">2. Pemesanan dan Pembayaran</h4>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Pemesanan dilakukan melalui website sesuai prosedur yang tersedia.</li>
                        <li style="margin-bottom: 5px;">Pembayaran dapat dilakukan secara penuh (full payment) atau uang muka (DP) sesuai ketentuan.</li>
                        <li style="margin-bottom: 5px;">Pemesanan dianggap sah setelah pembayaran diterima.</li>
                        <li style="margin-bottom: 5px;">Keterlambatan pembayaran dapat mengakibatkan pembatalan otomatis.</li>
                    </ol>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">3. Durasi Sewa</h4>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Masa sewa dihitung berdasarkan tanggal pengambilan hingga tanggal pengembalian.</li>
                        <li style="margin-bottom: 5px;">Perhitungan sewa menggunakan sistem harian.</li>
                        <li style="margin-bottom: 5px;">Keterlambatan pengembalian akan dikenakan denda sesuai tarif yang berlaku.</li>
                    </ol>

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">4. Penggunaan Barang</h4>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Barang hanya digunakan untuk kegiatan hiking, camping, atau aktivitas luar ruangan yang wajar.</li>
                        <li style="margin-bottom: 5px;">Penyewa dilarang memindahtangankan atau meminjamkan barang kepada pihak lain tanpa izin.</li>
                        <li style="margin-bottom: 5px;">Penyewa wajib menggunakan barang sesuai fungsi dan petunjuk penggunaan.</li>
                    </ol>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">5. Kerusakan dan Kehilangan</h4>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Penyewa bertanggung jawab penuh atas kondisi barang selama masa sewa.</li>
                        <li style="margin-bottom: 5px;">Kerusakan ringan maupun berat menjadi tanggung jawab penyewa.</li>
                        <li style="margin-bottom: 5px;">Kehilangan barang wajib diganti sesuai harga barang yang berlaku.</li>
                        <li style="margin-bottom: 5px;">Biaya perbaikan atau penggantian ditentukan oleh pihak penyedia.</li>
                    </ol>

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">6. Pengembalian Barang</h4>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Barang harus dikembalikan tepat waktu sesuai perjanjian.</li>
                        <li style="margin-bottom: 5px;">Barang harus dalam kondisi yang sama seperti saat diterima.</li>
                        <li style="margin-bottom: 5px;">Pihak penyedia berhak melakukan pemeriksaan saat pengembalian.</li>
                    </ol>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">7. Pembatalan dan Pengembalian Dana</h4>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Pembatalan oleh penyewa dapat dikenakan potongan biaya sesuai kebijakan.</li>
                        <li style="margin-bottom: 5px;">Pengembalian dana (refund) mengikuti ketentuan yang berlaku.</li>
                        <li style="margin-bottom: 5px;">Pembatalan oleh pihak penyedia akan disertai pengembalian dana penuh.</li>
                    </ol>

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">8. Tanggung Jawab dan Risiko</h4>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Penyewa memahami bahwa penggunaan alat outdoor memiliki risiko.</li>
                        <li style="margin-bottom: 5px;">Pihak penyedia tidak bertanggung jawab atas kecelakaan, cedera, atau kerugian yang terjadi selama penggunaan barang.</li>
                    </ol>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">9. Perubahan Ketentuan</h4>
                    <p style="margin-bottom: 15px;">Pihak penyedia berhak untuk mengubah syarat dan ketentuan sewaktu-waktu tanpa pemberitahuan sebelumnya.</p>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">10. Kontak</h4>
                    <p style="margin-bottom: 15px;">Untuk informasi lebih lanjut, silakan menghubungi layanan pelanggan melalui kontak yang tersedia di website.</p>
                </div>
            </div>
            
            <div id="privacyPage" class="sub-page">
                <div style="padding: 10px 5px; text-align: left; font-size: 13px; line-height: 1.6; color: #444;">
                    <p style="margin-bottom: 15px; text-align: center; color: #666;">Kami menghargai dan melindungi privasi setiap pengguna layanan pada website rental alat hiking ini. Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi pengguna.</p>
                    <p style="margin-bottom: 15px; text-align: center; color: #666;">Dengan menggunakan layanan kami, Anda dianggap telah membaca dan menyetujui kebijakan privasi ini.</p>

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">1. Informasi yang Dikumpulkan</h4>
                    <p style="margin-bottom: 5px;">Kami dapat mengumpulkan informasi berikut:</p>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Data pribadi seperti nama, nomor telepon, alamat, dan identitas (KTP atau sejenisnya).</li>
                        <li style="margin-bottom: 5px;">Informasi transaksi seperti riwayat pemesanan dan pembayaran.</li>
                        <li style="margin-bottom: 5px;">Data teknis seperti alamat IP, jenis perangkat, dan aktivitas penggunaan website.</li>
                    </ol>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">2. Penggunaan Informasi</h4>
                    <p style="margin-bottom: 5px;">Informasi yang dikumpulkan digunakan untuk:</p>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Memproses pemesanan dan transaksi penyewaan.</li>
                        <li style="margin-bottom: 5px;">Menghubungi pengguna terkait layanan.</li>
                        <li style="margin-bottom: 5px;">Meningkatkan kualitas layanan dan pengalaman pengguna.</li>
                        <li style="margin-bottom: 5px;">Keperluan administrasi dan keamanan.</li>
                    </ol>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">3. Perlindungan Data</h4>
                    <p style="margin-bottom: 5px;">Kami berkomitmen untuk menjaga keamanan data pengguna dengan:</p>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Menggunakan sistem keamanan yang memadai.</li>
                        <li style="margin-bottom: 5px;">Membatasi akses terhadap data pribadi hanya kepada pihak yang berwenang.</li>
                        <li style="margin-bottom: 5px;">Melakukan upaya pencegahan terhadap akses, penggunaan, atau pengungkapan data tanpa izin.</li>
                    </ol>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">4. Pembagian Informasi</h4>
                    <p style="margin-bottom: 5px;">Kami tidak akan menjual atau menyewakan data pribadi pengguna kepada pihak lain. Informasi hanya dapat dibagikan apabila:</p>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Diperlukan untuk proses layanan (misalnya pembayaran atau pengiriman).</li>
                        <li style="margin-bottom: 5px;">Diperlukan oleh hukum atau otoritas yang berwenang.</li>
                    </ol>

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">5. Cookies dan Teknologi Serupa</h4>
                    <p style="margin-bottom: 5px;">Website kami dapat menggunakan cookies untuk:</p>
                    <ol style="margin-left: 20px; margin-bottom: 10px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Menyimpan preferensi pengguna.</li>
                        <li style="margin-bottom: 5px;">Menganalisis penggunaan website.</li>
                        <li style="margin-bottom: 5px;">Meningkatkan pengalaman pengguna.</li>
                    </ol>
                    <p style="margin-bottom: 15px;">Pengguna dapat mengatur penggunaan cookies melalui pengaturan browser masing-masing.</p>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">6. Hak Pengguna</h4>
                    <p style="margin-bottom: 5px;">Pengguna berhak untuk:</p>
                    <ol style="margin-left: 20px; margin-bottom: 15px; padding-left: 0;">
                        <li style="margin-bottom: 5px;">Mengakses data pribadi yang dimiliki.</li>
                        <li style="margin-bottom: 5px;">Meminta perbaikan atau penghapusan data.</li>
                        <li style="margin-bottom: 5px;">Menarik persetujuan penggunaan data (dengan konsekuensi layanan mungkin tidak dapat digunakan secara optimal).</li>
                    </ol>

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">7. Penyimpanan Data</h4>
                    <p style="margin-bottom: 15px;">Data pengguna akan disimpan selama diperlukan untuk keperluan layanan atau sesuai dengan ketentuan hukum yang berlaku.</p>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">8. Perubahan Kebijakan</h4>
                    <p style="margin-bottom: 15px;">Kami berhak untuk mengubah kebijakan privasi ini sewaktu-waktu. Perubahan akan berlaku setelah dipublikasikan di website.</p>

                    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

                    <h4 style="font-size: 14px; color: #1b4332; margin-top: 25px; margin-bottom: 10px;">9. Kontak</h4>
                    <p style="margin-bottom: 15px;">Jika memiliki pertanyaan terkait kebijakan privasi ini, silakan menghubungi kami melalui kontak yang tersedia di website.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="cameraModal" class="modal-overlay">
        <div class="modal-box">
            <h3 id="cameraStatusTitle" style="margin-bottom: 15px; font-size: 16px; color: #1b4332;">Mencari Objek...</h3>
            <div class="camera-container"><video id="videoFeed" autoplay playsinline></video>
                <div id="guideOverlay" class="camera-guide"></div>
            </div>
            <canvas id="photoCanvas" style="display:none;"></canvas>
            <div class="modal-btn-group"><button class="modal-btn btn-potret" id="btnCapture" disabled onclick="takeSnapshot()">Potret</button><button class="modal-btn btn-batal" onclick="stopCamera()">Batal</button></div>
        </div>
    </div>

    <div id="bugModal" class="modal-overlay">
        <div class="modal-box">
            <span class="modal-icon" style="font-size: 40px; display: block; margin-bottom: 10px;">🐛</span>
            <h3 class="modal-title">Report a Bug</h3>
            <p class="modal-text">Ada kendala? Kirim laporan ke email:<br><b>admin@mountster.com</b></p>
            <div class="modal-btn-group"><button class="modal-btn btn-potret" onclick="reportViaGmail()">Lapor via Gmail</button><button class="modal-btn btn-batal" onclick="closeBugModal()">Batal</button></div>
        </div>
    </div>

    <div id="successModal" class="modal-overlay">
        <div class="modal-box">
            <div class="success-checkmark">✓</div>
            <h3 class="modal-title">Berhasil!</h3>
            <p class="modal-text">Data Anda telah disimpan dengan aman.</p>
            <div class="modal-btn-group"><button class="modal-btn btn-potret" onclick="closeSuccessModal()">Selesai</button></div>
        </div>
    </div>

    <script>
        const VOUCHER_LIST = {
            "ECO20": {
                label: "Diskon 20%",
                type: "percent",
                value: 20,
                title: "Diskon 20% Eco-Warrior",
                desc: "Voucher khusus Pahlawan Gunung! Kumpulkan 25 Poin Eco untuk klaim voucher diskon ini."
            }
        };

        const USER_ID = "<?php echo $userEmail; ?>";
        const KEY_NAME = 'mountsterUserName_' + USER_ID;
        const KEY_AVATAR = 'mountsterUserAvatar_' + USER_ID;
        const KEY_VERIF = 'is_verified_' + USER_ID;
        const KEY_VOUCHER = 'mountsterVouchers_' + USER_ID;
        const KEY_ECO_POINTS = 'mountsterEcoPoints_' + USER_ID;

        let currentPreviewTarget = '';
        let stream = null;
        let scanInterval = null;

        function getStoredVouchers() {
            return JSON.parse(localStorage.getItem(KEY_VOUCHER) || '[]');
        }

        function setStoredVouchers(vouchers) {
            localStorage.setItem(KEY_VOUCHER, JSON.stringify(vouchers));
        }

        function syncStoredVouchers() {
            const validCodes = Object.keys(VOUCHER_LIST);
            const sanitized = getStoredVouchers().filter(voucher => validCodes.includes(voucher.code));
            setStoredVouchers(sanitized);
            return sanitized;
        }

        function updateVoucherBadge() {
            const badge = document.getElementById('voucherBadge');
            if (!badge) return;
            const hasNewVoucher = getStoredVouchers().some(voucher => !voucher.used);
            badge.style.display = hasNewVoucher ? 'inline-block' : 'none';
        }

        // ==========================================
        // 1. SINKRONISASI DATA AWAL
        // ==========================================
        document.addEventListener('DOMContentLoaded', () => {
            syncStoredVouchers();
            const savedName = localStorage.getItem(KEY_NAME);
            const savedAvatar = localStorage.getItem(KEY_AVATAR);

            if (savedName) {
                document.getElementById('profileName').innerText = savedName;
                document.getElementById('editNameInput').value = savedName;
            }
            if (savedAvatar) {
                document.getElementById('mainAvatar').src = savedAvatar;
                document.getElementById('editAvatarPreview').src = savedAvatar;
            }

            updateVerificationUI();
            updateVoucherBadge();

            const params = new URLSearchParams(window.location.search);
            if (params.get('require') === 'verif') openSubPage('verifPage', 'Verifikasi 2 Langkah');
        });

        // ==========================================
        // 2. FUNGSI SAVE CHANGES KE MEMORI BROWSER
        // ==========================================
        function showSuccessModal() {
            let nameVal = document.getElementById('editNameInput').value;
            const avatarSrc = document.getElementById('mainAvatar').src;

            if (nameVal && nameVal.trim() !== "") {
                nameVal = nameVal.toLowerCase(); 
                document.getElementById('profileName').innerText = nameVal;
                localStorage.setItem(KEY_NAME, nameVal);
            }
            localStorage.setItem(KEY_AVATAR, avatarSrc);
            document.getElementById('successModal').style.display = 'flex';
        }

        // --- Logika Voucher ---
        function renderVouchers() {
            const container = document.getElementById('voucherContainer');
            const claimedVouchers = JSON.parse(localStorage.getItem(KEY_VOUCHER) || '[]');
            const claimedCodes = claimedVouchers.map(v => v.code);
            const currentEcoPoints = parseInt(localStorage.getItem(KEY_ECO_POINTS)) || 0;

            let html = `<h3 style="margin-bottom: 6px; font-size: 15px; font-weight: 800;">Voucher Tersedia</h3>
                <p style="font-size: 12px; color: #888; margin-bottom: 18px;">Klaim voucher untuk digunakan saat checkout.</p>`;

            let hasAny = false;

            Object.entries(VOUCHER_LIST).forEach(([code, v]) => {
                hasAny = true;
                const isClaimed = claimedCodes.includes(code);
                const claimedData = claimedVouchers.find(cv => cv.code === code);
                const isUsed = claimedData?.used === true;

                let badgeHtml = '';
                let btnHtml = '';
                let cardStyle = '';

                if (isUsed) {
                    badgeHtml = `<span style="font-size:10px; background:rgba(255,255,255,0.2); padding:3px 8px; border-radius:10px; font-weight:bold;">Sudah Dipakai</span>`;
                    btnHtml = `<button disabled style="margin-top:12px; padding:8px 20px; background:rgba(255,255,255,0.2); color:white; border:none; border-radius:10px; font-size:12px; font-weight:bold; cursor:not-allowed; width:100%;">Sudah Dipakai</button>`;
                    cardStyle = 'opacity:0.6;';
                } else if (isClaimed) {
                    badgeHtml = `<span style="font-size:10px; background:rgba(255,255,255,0.3); padding:3px 8px; border-radius:10px; font-weight:bold;">Sudah Diklaim</span>`;
                    btnHtml = `<button disabled style="margin-top:12px; padding:8px 20px; background:rgba(255,255,255,0.25); color:white; border:none; border-radius:10px; font-size:12px; font-weight:bold; cursor:not-allowed; width:100%;">Sudah Diklaim ✓</button>`;
                } else {
                    // Logic Khusus Eco-Warrior (Kunci Voucher Jika Poin < 25)
                    if (code === 'ECO20') {
                        if (currentEcoPoints < 25) {
                            badgeHtml = `<span style="font-size:10px; background:rgba(255,255,255,0.2); padding:3px 8px; border-radius:10px; font-weight:bold;">Terkunci (${currentEcoPoints}/25 Poin)</span>`;
                            btnHtml = `<button disabled style="margin-top:12px; padding:8px 20px; background:rgba(255,255,255,0.2); color:white; border:none; border-radius:10px; font-size:12px; font-weight:bold; cursor:not-allowed; width:100%;">Poin Belum Cukup</button>`;
                            cardStyle = 'opacity:0.6; filter:grayscale(80%);';
                        } else {
                            badgeHtml = `<span style="font-size:10px; background:rgba(255,255,255,0.3); padding:3px 8px; border-radius:10px; font-weight:bold;">Siap Diklaim</span>`;
                            btnHtml = `<button onclick="claimVoucher('${code}')" style="margin-top:12px; padding:8px 20px; background:white; color:#11998e; border:none; border-radius:10px; font-size:12px; font-weight:800; cursor:pointer; width:100%; transition:0.2s;">Klaim Sekarang</button>`;
                        }
                    } else {
                        badgeHtml = `<span style="font-size:10px; background:rgba(255,255,255,0.3); padding:3px 8px; border-radius:10px; font-weight:bold;">Tersedia</span>`;
                        btnHtml = `<button onclick="claimVoucher('${code}')" style="margin-top:12px; padding:8px 20px; background:white; color:#11998e; border:none; border-radius:10px; font-size:12px; font-weight:800; cursor:pointer; width:100%; transition:0.2s;">Klaim Sekarang</button>`;
                    }
                }

                html += `
                  <div class="voucher-card" style="${cardStyle}">
                    <div style="z-index:2; width:100%;">
                      ${badgeHtml}
                      <h4 style="margin:8px 0 4px; font-size:16px; text-shadow:1px 1px 2px rgba(0,0,0,0.2);">${v.title}</h4>
                      <p style="font-size:11px; color:#f0fff0; line-height:1.4;">${v.desc}</p>
                      <div style="display:inline-block; background:rgba(0,0,0,0.15); border:1px dashed rgba(255,255,255,0.6); padding:5px 12px; border-radius:8px; font-weight:bold; font-family:monospace; font-size:13px; letter-spacing:2px; margin-top:8px;">${code}</div>
                      ${btnHtml}
                    </div>
                    <div style="font-size:40px; opacity:0.8; z-index:2; transform:rotate(-15deg); position:absolute; right:20px; top:50%; transform:translateY(-50%) rotate(-15deg);">VC</div>
                  </div>`;
            });

            if (!hasAny) {
                html = `<div style="text-align:center; padding:40px 20px;">
                  <span style="font-size:50px;">VC</span>
                  <p style="color:#888; margin-top:10px; font-size:13px;">Belum ada voucher tersedia saat ini.</p>
                </div>`;
            }

            container.innerHTML = html;
        }

        function claimVoucher(code) {
            const voucher = VOUCHER_LIST[code];
            if (!voucher) return;

            let claimedVouchers = JSON.parse(localStorage.getItem(KEY_VOUCHER) || '[]');
            const alreadyClaimed = claimedVouchers.find(v => v.code === code);
            if (alreadyClaimed) return;

            claimedVouchers.push({
                code,
                title: voucher.title,
                desc: voucher.desc,
                label: voucher.label,
                type: voucher.type,
                value: voucher.value || 0,
                shippingFree: voucher.shippingFree || false,
                percentValue: voucher.percentValue || 0,
                used: false,
                claimedAt: new Date().toISOString()
            });

            localStorage.setItem(KEY_VOUCHER, JSON.stringify(claimedVouchers));

            const badge = document.getElementById('voucherBadge');
            if (badge) badge.style.display = 'inline-block';

            renderVouchers();

            const notif = document.createElement('div');
            notif.style = `
                position:fixed; bottom:30px; left:50%; transform:translateX(-50%);
                background:#1b4332; color:white; padding:12px 22px; border-radius:12px;
                font-size:13px; font-weight:bold; z-index:9999;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                animation: fadeIn 0.3s ease;
            `;
            notif.innerText = `Voucher ${code} berhasil diklaim!`;
            document.body.appendChild(notif);
            setTimeout(() => notif.remove(), 2500);
        }

        // --- Logika Status Verifikasi ---
        function updateVerificationUI() {
            const isVerified = localStorage.getItem(KEY_VERIF) === 'true';
            const verifMenu = document.getElementById('verifMenu');
            const verifArrow = document.getElementById('verifArrow');
            if (isVerified) {
                document.getElementById('verifiedLabel').style.display = 'block';
                document.getElementById('menuNotifDot').style.display = 'none';
                verifMenu.classList.add('disabled');
                verifMenu.onclick = null;
                verifArrow.innerHTML = '&#10003;';
                verifArrow.style.color = '#27ae60';
                verifArrow.style.fontWeight = 'bold';
            } else {
                document.getElementById('verifiedLabel').style.display = 'none';
                document.getElementById('menuNotifDot').style.display = 'inline-block';
                verifMenu.classList.remove('disabled');
                verifMenu.onclick = () => openSubPage('verifPage', 'Verifikasi 2 Langkah');
                verifArrow.innerHTML = '&rsaquo;';
            }
        }

        function submitVerifikasi() {
            if (!document.getElementById('ktpPreview').src || !document.getElementById('selfiePreview').src) return alert("Ambil foto identitas dulu!");
            localStorage.setItem(KEY_VERIF, 'true');
            updateVerificationUI();
            showSuccessModal();
        }

        // --- Logika Bug Report (Gmail) ---
        // --- Logika Bug Report (Gmail) ---
        function reportViaGmail() {
            // Ambil nama terbaru yang tampil di halaman profil saat ini
            const currentName = document.getElementById('profileName').innerText || "<?= $userName ?>";
            const currentEmail = "<?= $userEmail ?>";

            const subject = encodeURIComponent(`Bug Report - Mountster [${currentName}]`);
            const body = encodeURIComponent(
                "Halo Admin Mountster,\n\n" +
                "Saya menemukan kendala pada aplikasi menggunakan akun:\n" +
                `Nama: ${currentName}\n` +
                `Email: <?= $userEmail ?>\n\n` +
                "Deskripsi Kendala:\n" +
                "(Silakan ketik detail masalah Anda di sini...)\n\n" +
                "Terima kasih atas bantuannya.\n\n" +
                "Salam,\n" +
                `${currentName}`
            );
            
            window.open(`https://mail.google.com/mail/?view=cm&fs=1&to=admin@mountster.com&su=${subject}&body=${body}`, '_blank');
            closeBugModal();
        }

        // --- Kamera & Galeri Logic ---
        function handleGalleryUpload(input, targetId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById(targetId).src = e.target.result;
                    document.getElementById(targetId).style.display = 'block';
                    if (targetId === 'editAvatarPreview') document.getElementById('mainAvatar').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }

        async function startCamera(targetId, type) {
            currentPreviewTarget = targetId;
            const overlay = document.getElementById('guideOverlay');
            const title = document.getElementById('cameraStatusTitle');
            const btn = document.getElementById('btnCapture');
            overlay.className = 'camera-guide';
            if (type === 'face') overlay.classList.add('guide-face');
            else if (type === 'ktp') overlay.classList.add('guide-ktp');
            else if (type === 'selfie') overlay.classList.add('guide-selfie');
            overlay.style.display = 'block';
            document.getElementById('cameraModal').style.display = 'flex';
            btn.disabled = true;
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                document.getElementById('videoFeed').srcObject = stream;

                function startScanningLoop() {
                    overlay.classList.remove('detected');
                    btn.disabled = true;
                    title.innerText = "Mencari Objek...";
                    title.style.color = "#1b4332";
                    setTimeout(() => {
                        overlay.classList.add('detected');
                        btn.disabled = false;
                        title.innerText = "Posisi Pas! Silakan Potret";
                        title.style.color = "#27ae60";
                    }, 1500);
                }
                startScanningLoop();
                scanInterval = setInterval(startScanningLoop, 5000);
            } catch (err) {
                alert("Kamera tidak tersedia.");
                stopCamera();
            }
        }

        function takeSnapshot() {
            const video = document.getElementById('videoFeed');
            const canvas = document.getElementById('photoCanvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext('2d');
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const dataUrl = canvas.toDataURL('image/png');
            document.getElementById(currentPreviewTarget).src = dataUrl;
            document.getElementById(currentPreviewTarget).style.display = 'block';
            if (currentPreviewTarget === 'editAvatarPreview') document.getElementById('mainAvatar').src = dataUrl;
            stopCamera();
        }

        function stopCamera() {
            if (stream) stream.getTracks().forEach(track => track.stop());
            if (scanInterval) clearInterval(scanInterval);
            document.getElementById('cameraModal').style.display = 'none';
        }

        function renderNotifications() {
            const container = document.getElementById('notifContainer');
            const isVerified = localStorage.getItem(KEY_VERIF) === 'true';
            if (isVerified) {
                container.innerHTML = `<div class="notif-item"><div class="notif-icon-box">🎉</div><div class="notif-content"><p class="notif-title">Verifikasi Berhasil!</p><p class="notif-text">Akun Anda telah terverifikasi. Transaksi sewa kini lebih aman.</p></div></div>`;
            } else {
                container.innerHTML = `<div class="notif-item unread"><div class="notif-icon-box">⚠️</div><div class="notif-content"><p class="notif-title">Verifikasi Akun!</p><p class="notif-text">Segera lakukan verifikasi 2 langkah sebelum menyewa alat.</p></div><div class="unread-dot"></div></div>`;
            }
        }

        function openSubPage(pageId, titleText) {
            document.getElementById('pageTitle').innerText = titleText;
            document.getElementById('profileHeaderInfo').style.display = 'none';
            document.getElementById('mainProfileMenu').style.display = 'none';
            document.querySelectorAll('.sub-page').forEach(p => p.classList.remove('active'));
            document.getElementById(pageId).classList.add('active');
            if (pageId === 'notificationsPage') {
                renderNotifications();
                document.getElementById('menuNotifDot').style.display = 'none';
            }
        }

        function closeSubPages() {
            document.getElementById('pageTitle').innerText = "Profile";
            document.getElementById('profileHeaderInfo').style.display = 'flex';
            document.getElementById('mainProfileMenu').style.display = 'block';
            document.querySelectorAll('.sub-page').forEach(p => p.classList.remove('active'));
        }

        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
            closeSubPages();
        }

        function showBugModal() { document.getElementById('bugModal').style.display = 'flex'; }
        function closeBugModal() { document.getElementById('bugModal').style.display = 'none'; }

        document.getElementById('backBtn').onclick = () => {
            if (document.querySelector('.sub-page.active')) closeSubPages();
            else window.location.href = 'home.php';
        };
    </script>
</body>

</html>