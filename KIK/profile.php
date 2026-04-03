<?php 
include 'auth.php'; 

// Proteksi Halaman: Wajib login
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
        /* --- Avatar Styling --- */
        .profile-header-icon {
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .profile-header-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* --- Custom Modal Report Bug --- */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
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
            max-width: 320px;
            padding: 30px 25px;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            transform: scale(0.8);
            animation: scaleUp 0.3s forwards;
        }

        .modal-icon {
            font-size: 45px;
            margin-bottom: 15px;
            display: block;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1b4332;
        }

        .modal-text {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        /* Tombol Group */
        .modal-btn-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
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

        .btn-gmail {
            background: #1b4332; /* Hijau Mountster */
            color: white;
        }

        .btn-batal {
            background: #f0f0f0;
            color: #666;
        }

        .modal-btn:active {
            transform: scale(0.96);
        }

        /* Animasi */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleUp { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .sub-page { display: none; animation: slideUp 0.3s ease-out; }
        .sub-page.active { display: block; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body>
    <div class="app-container" style="padding-bottom: 50px; min-height: 100vh;">
        
        <div class="p-20 flex-between">
            <span id="backBtn" style="cursor: pointer; font-size: 20px; font-weight: bold;">←</span>
            <h2 id="pageTitle" style="font-size: 18px;">Profile</h2>
            <div style="width: 20px;"></div> 
        </div>

        <div id="profileHeaderInfo" class="p-20" style="display: flex; align-items: center; gap: 15px;">
            <div class="profile-header-icon">
                <img src="<?php echo htmlspecialchars($userAvatar); ?>" alt="Avatar">
            </div>
            <div>
                <h3 id="profileName" style="font-size: 18px; margin: 0; text-transform: capitalize;">
                    <?php echo htmlspecialchars($userName); ?>
                </h3>
                <p id="profileEmail" style="color: var(--text-muted); font-size: 12px; margin: 5px 0 0 0;">
                    <?php echo htmlspecialchars($userEmail); ?>
                </p>
            </div>
        </div>

        <div id="mainProfileMenu" style="padding: 0 20px;">
            <p class="menu-section-title">General</p>
            <div class="menu-item" onclick="openSubPage('editProfilePage', 'Edit Profile')">
                <span>Edit Profile</span> <span style="color:#ccc;">›</span>
            </div>
            <div class="menu-item" onclick="openSubPage('notificationsPage', 'Notifications')">
                <span>Notifications <span id="notifDotMaster" class="unread-dot"></span></span> 
                <span style="color:#ccc;">›</span>
            </div>
            <div class="menu-item" onclick="openSubPage('wishlistPage', 'Wishlist')">
                <span>Wishlist</span> <span style="color:#ccc;">›</span>
            </div>

            <p class="menu-section-title">Legal</p>
            <div class="menu-item" onclick="openSubPage('termsPage', 'Terms of Use')">
                <span>Terms of Use</span> <span style="color:#ccc;">›</span>
            </div>
            <div class="menu-item" onclick="openSubPage('privacyPage', 'Privacy Policy')">
                <span>Privacy Policy</span> <span style="color:#ccc;">›</span>
            </div>

            <p class="menu-section-title">Personal</p>
            <div class="menu-item" onclick="showBugModal()">
                <span>Report a Bug</span> <span style="color:#ccc;">›</span>
            </div>
            <a href="logout.php" id="btnLogout" class="menu-item" style="color: red; text-decoration: none;">Logout</a>
        </div>

        <div style="padding: 0 20px;">
            <div id="editProfilePage" class="sub-page">
                <div class="input-group"><label>Display Name</label><input type="text" id="editNameInput" class="input-form" value="<?php echo htmlspecialchars($userName); ?>"></div>
                <div class="input-group"><label>Email Address</label><input type="email" id="editEmailInput" class="input-form" value="<?php echo htmlspecialchars($userEmail); ?>" readonly></div>
                <button class="btn btn-primary" onclick="alert('Profil berhasil disimpan!'); closeSubPages();">Save Changes</button>
            </div>
            <div id="notificationsPage" class="sub-page">
                <p style="text-align:center; color:#888; padding:20px;">Tidak ada notifikasi baru.</p>
            </div>
            <div id="wishlistPage" class="sub-page">
                <div id="wishlistContainer"></div>
            </div>
        </div>
    </div>

    <div id="bugModal" class="modal-overlay">
        <div class="modal-box">
            <span class="modal-icon">✉️</span>
            <h3 class="modal-title">Report a Bug</h3>
           <p class="modal-text">Temukan kendala teknis? Silakan kirimkan laporan kamu ke email:<br><b>admin@mountster.com</b></p>
            
            <div class="modal-btn-group">
                <button class="modal-btn btn-gmail" onclick="reportViaGmail()">
                    Lapor via Gmail
                </button>
                
                <button class="modal-btn btn-batal" onclick="closeBugModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        const backBtn = document.getElementById('backBtn');
        const pageTitle = document.getElementById('pageTitle');
        const profileHeaderInfo = document.getElementById('profileHeaderInfo');
        const mainProfileMenu = document.getElementById('mainProfileMenu');
        let currentSubPage = null; 

        backBtn.addEventListener('click', function() {
            if (currentSubPage !== null) { closeSubPages(); } else { window.location.href = 'home.php'; }
        });

        function openSubPage(pageId, titleText) {
            currentSubPage = pageId;
            pageTitle.innerText = titleText; 
            profileHeaderInfo.style.display = 'none'; 
            mainProfileMenu.style.display = 'none'; 
            document.querySelectorAll('.sub-page').forEach(page => page.classList.remove('active'));
            setTimeout(() => { document.getElementById(pageId).classList.add('active'); }, 10);
            if(pageId === 'wishlistPage') renderProfileWishlist();
        }

        function closeSubPages() {
            currentSubPage = null;
            pageTitle.innerText = "Profile"; 
            profileHeaderInfo.style.display = 'flex'; 
            mainProfileMenu.style.display = 'block'; 
            document.querySelectorAll('.sub-page').forEach(page => page.classList.remove('active'));
        }

        // --- Kontrol Modal Bug ---
        function showBugModal() {
            document.getElementById('bugModal').style.display = 'flex';
        }

        function closeBugModal() {
            document.getElementById('bugModal').style.display = 'none';
        }

        // Fungsi Buka Gmail Otomatis
        function reportViaGmail() {
            const adminEmail = "admin@mountster.com";
            const userName = "<?php echo $userName; ?>";
            const userEmail = "<?php echo $userEmail; ?>";
            
            const subject = encodeURIComponent("Bug Report - Mountster [" + userName + "]");
            const body = encodeURIComponent(
                "Halo Admin Mountster,\n\n" +
                "Saya ingin melaporkan kendala pada aplikasi.\n\n" +
                "Detail Pelapor:\n" +
                "- Nama: " + userName + "\n" +
                "- Akun: " + userEmail + "\n\n" +
                "Deskripsi Kendala:\n" +
                "(Tulis detail bug yang kamu temukan di sini...)\n\n" +
                "Terima kasih."
            );

            // Buka Gmail Compose mode
            const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${adminEmail}&su=${subject}&body=${body}`;
            window.open(gmailUrl, '_blank');
            
            closeBugModal();
        }

        // Tutup modal jika klik area luar
        window.onclick = function(event) {
            const modal = document.getElementById('bugModal');
            if (event.target == modal) { closeBugModal(); }
        }

        function renderProfileWishlist() {
            let wishlist = JSON.parse(localStorage.getItem('mountsterWishlist')) || [];
            const container = document.getElementById('wishlistContainer');
            container.innerHTML = "";
            if (wishlist.length === 0) {
                container.innerHTML = `<p style="text-align:center; color:#888; font-size:14px; margin-top:20px;">Belum ada favorit.</p>`;
                return;
            }
        }
    </script>
</body>
</html>