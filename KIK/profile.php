<?php 
include_once 'auth.php'; 

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
        /* --- Avatar & Header Styling --- */
        .profile-header-icon {
            width: 80px; height: 80px; border-radius: 50%;
            overflow: hidden; display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); background: #eee;
        }
        .profile-header-icon img { width: 100%; height: 100%; object-fit: cover; }

        /* --- Modal Overlays --- */
        .modal-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0, 0, 0, 0.5); display: none; align-items: center; 
            justify-content: center; z-index: 2000; backdrop-filter: blur(4px); 
            animation: fadeIn 0.3s ease; 
        }
        .modal-box { 
            background: white; width: 85%; max-width: 340px; padding: 30px 25px; 
            border-radius: 24px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.2); 
            transform: scale(0.8); animation: scaleUp 0.3s forwards; 
        }

        /* --- Sub-page Navigation --- */
        .sub-page { display: none; animation: slideUp 0.3s ease-out; }
        .sub-page.active { display: block; }

        /* --- Camera UI Styling --- */
        .upload-box { border: 2px dashed #ccc; padding: 15px; border-radius: 15px; text-align: center; margin-bottom: 10px; cursor: pointer; position: relative; }
        .upload-preview { width: 100%; height: 120px; object-fit: contain; border-radius: 10px; display: none; margin-top: 10px; }
        .btn-camera-trigger { background: #eee; border: none; padding: 8px 15px; border-radius: 10px; font-size: 11px; font-weight: bold; color: #555; margin-top: 5px; cursor: pointer; }
        
        #cameraModal .modal-box { max-width: 400px; width: 90%; padding: 25px; }
        #videoFeed { width: 100%; border-radius: 18px; background: #000; transform: scaleX(-1); margin-bottom: 10px; }
        
        /* --- Tombol Group (Konsisten dengan design lain) --- */
        .modal-btn-group { display: flex; flex-direction: column; gap: 10px; width: 100%; margin-top: 15px; }
        .modal-btn { border: none; padding: 14px; border-radius: 14px; font-weight: 600; font-size: 14px; cursor: pointer; width: 100%; transition: 0.2s; }
        .btn-potret { background: #1b4332; color: white; }
        .btn-batal { background: #f0f0f0; color: #666; }
        .modal-btn:active { transform: scale(0.96); }

        .status-verified { color: #1b4332; font-weight: bold; font-size: 12px; display: none; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleUp { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .menu-item { display: flex; align-items: center; justify-content: space-between; cursor: pointer; }
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
                <img id="mainAvatar" src="<?php echo htmlspecialchars($userAvatar); ?>" alt="Avatar">
            </div>
            <div>
                <h3 style="font-size: 18px; margin: 0; text-transform: capitalize;"><?php echo htmlspecialchars($userName); ?></h3>
                <p id="profileEmail" style="color: var(--text-muted); font-size: 12px; margin: 5px 0 0 0;"><?php echo htmlspecialchars($userEmail); ?></p>
                <p id="verifiedLabel" class="status-verified">✓ Terverifikasi</p>
            </div>
        </div>

        <div id="mainProfileMenu" style="padding: 0 20px;">
            <p class="menu-section-title">General</p>
            <div class="menu-item" onclick="openSubPage('editProfilePage', 'Edit Profile')">
                <span>Edit Profile</span> <span style="color:#ccc;">›</span>
            </div>
            <div class="menu-item" onclick="openSubPage('verifPage', 'Verifikasi 2 Langkah')">
                <span>Verifikasi 2 Langkah</span> 
                <span id="verifBadge" style="font-size:10px; background:orange; color:white; padding:2px 10px; border-radius:10px; margin-left: auto; margin-right: 10px;">Belum</span> 
                <span style="color:#ccc;">›</span>
            </div>
            <div class="menu-item" onclick="openSubPage('notificationsPage', 'Notifications')"><span>Notifications</span> <span style="color:#ccc;">›</span></div>
            <div class="menu-item" onclick="openSubPage('wishlistPage', 'Wishlist')"><span>Wishlist</span> <span style="color:#ccc;">›</span></div>

            <p class="menu-section-title">Legal</p>
            <div class="menu-item" onclick="openSubPage('termsPage', 'Terms of Use')"><span>Terms of Use</span> <span style="color:#ccc;">›</span></div>
            <div class="menu-item" onclick="openSubPage('privacyPage', 'Privacy Policy')"><span>Privacy Policy</span> <span style="color:#ccc;">›</span></div>

            <p class="menu-section-title">Personal</p>
            <div class="menu-item" onclick="showBugModal()"><span>Report a Bug</span> <span style="color:#ccc;">›</span></div>
            <a href="logout.php" class="menu-item" style="color: red; text-decoration: none;">Logout</a>
        </div>

        <div style="padding: 0 20px;">
            <div id="editProfilePage" class="sub-page">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div class="profile-header-icon" style="margin: 0 auto 10px;"><img id="editAvatarPreview" src="<?php echo htmlspecialchars($userAvatar); ?>"></div>
                    <button class="btn-camera-trigger" onclick="startCamera('editAvatarPreview')">📸 Ambil Foto Profil</button>
                </div>
                <div class="input-group"><label>Display Name</label><input type="text" id="editNameInput" class="input-form" value="<?php echo htmlspecialchars($userName); ?>"></div>
                <button class="btn btn-primary" onclick="alert('Perubahan disimpan!'); closeSubPages();">Save Changes</button>
            </div>

            <div id="verifPage" class="sub-page">
                <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Ambil foto identitas asli untuk keamanan penyewaan.</p>
                <div class="form-group">
                    <label style="font-size: 11px; font-weight: bold; display: block; margin-bottom: 5px;">1. Foto KTP</label>
                    <div class="upload-box">
                        <img id="ktpPreview" class="upload-preview">
                        <button class="btn-camera-trigger" onclick="startCamera('ktpPreview')">📸 Ambil Foto KTP</button>
                    </div>
                </div>
                <div class="form-group">
                    <label style="font-size: 11px; font-weight: bold; display: block; margin-bottom: 5px;">2. Selfie + KTP</label>
                    <div class="upload-box">
                        <img id="selfiePreview" class="upload-preview">
                        <button class="btn-camera-trigger" onclick="startCamera('selfiePreview')">📸 Ambil Foto Selfie</button>
                    </div>
                </div>
                <button id="btnSubmitVerif" class="btn btn-primary" onclick="submitVerifikasi()">Ajukan Verifikasi</button>
            </div>
            
            <div id="notificationsPage" class="sub-page"><p style="text-align:center; color:#888; padding:20px;">Tidak ada notifikasi.</p></div>
            <div id="wishlistPage" class="sub-page"><div id="wishlistContainer"></div></div>
            <div id="termsPage" class="sub-page"><p style="font-size:12px; color:#666; line-height: 1.6;">Syarat & Ketentuan Mountster...</p></div>
            <div id="privacyPage" class="sub-page"><p style="font-size:12px; color:#666; line-height: 1.6;">Kebijakan Privasi...</p></div>
        </div>
    </div>

    <div id="cameraModal" class="modal-overlay">
        <div class="modal-box">
            <h3 style="margin-bottom: 15px; font-size: 17px; color: #1b4332;">Ambil Gambar</h3>
            <video id="videoFeed" autoplay playsinline></video>
            <canvas id="photoCanvas" style="display:none;"></canvas>
            
            <div class="modal-btn-group">
                <button class="modal-btn btn-potret" onclick="takeSnapshot()">Potret</button>
                <button class="modal-btn btn-batal" onclick="stopCamera()">Batal</button>
            </div>
        </div>
    </div>

    <div id="bugModal" class="modal-overlay">
        <div class="modal-box">
            <span class="modal-icon">✉️</span>
            <h3 class="modal-title">Report a Bug</h3>
            <p class="modal-text">Ada kendala? Kirim laporan ke email:<br><b>admin@mountster.com</b></p>
            <div class="modal-btn-group">
                <button class="modal-btn btn-potret" onclick="reportViaGmail()">Lapor via Gmail</button>
                <button class="modal-btn btn-batal" onclick="closeBugModal()">Batal</button>
            </div>
        </div>
    </div>

    <script>
        let currentPreviewTarget = '';
        let stream = null;

        // --- Logika Kamera ---
        async function startCamera(targetId) {
            currentPreviewTarget = targetId;
            document.getElementById('cameraModal').style.display = 'flex';
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                document.getElementById('videoFeed').srcObject = stream;
            } catch (err) {
                alert("Izin kamera ditolak atau tidak tersedia.");
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
            const imgPreview = document.getElementById(currentPreviewTarget);
            imgPreview.src = dataUrl;
            imgPreview.style.display = 'block';

            if(currentPreviewTarget === 'editAvatarPreview') {
                document.getElementById('mainAvatar').src = dataUrl;
            }
            stopCamera();
        }

        function stopCamera() {
            if (stream) stream.getTracks().forEach(track => track.stop());
            document.getElementById('cameraModal').style.display = 'none';
        }

        // --- Navigasi & Verifikasi ---
        function openSubPage(pageId, titleText) {
            document.getElementById('pageTitle').innerText = titleText; 
            document.getElementById('profileHeaderInfo').style.display = 'none'; 
            document.getElementById('mainProfileMenu').style.display = 'none'; 
            document.querySelectorAll('.sub-page').forEach(p => p.classList.remove('active'));
            document.getElementById(pageId).classList.add('active');
        }

        function closeSubPages() {
            document.getElementById('pageTitle').innerText = "Profile"; 
            document.getElementById('profileHeaderInfo').style.display = 'flex'; 
            document.getElementById('mainProfileMenu').style.display = 'block'; 
            document.querySelectorAll('.sub-page').forEach(p => p.classList.remove('active'));
        }

        function submitVerifikasi() {
            localStorage.setItem('is_verified', 'true');
            alert('Verifikasi Berhasil!');
            location.reload();
        }

        function showBugModal() { document.getElementById('bugModal').style.display = 'flex'; }
        function closeBugModal() { document.getElementById('bugModal').style.display = 'none'; }

        function reportViaGmail() {
            const adminEmail = "admin@mountster.com";
            const subject = encodeURIComponent("Bug Report - Mountster [<?php echo $userName; ?>]");
            const body = encodeURIComponent("Halo Admin,\n\nSaya menemukan kendala...");
            window.open(`https://mail.google.com/mail/?view=cm&fs=1&to=${adminEmail}&su=${subject}&body=${body}`, '_blank');
            closeBugModal();
        }

        window.onload = function() {
            if(localStorage.getItem('is_verified') === 'true') {
                document.getElementById('verifiedLabel').style.display = 'block';
                const badge = document.getElementById('verifBadge');
                if(badge) {
                    badge.innerText = 'Sudah';
                    badge.style.background = '#1b4332';
                }
            }
            const params = new URLSearchParams(window.location.search);
            if(params.get('require') === 'verif') openSubPage('verifPage', 'Verifikasi 2 Langkah');
        };

        document.getElementById('backBtn').onclick = () => {
            if(document.querySelector('.sub-page.active')) closeSubPages();
            else window.location.href = 'home.php';
        };
    </script>
</body>
</html>