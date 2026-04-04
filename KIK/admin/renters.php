<?php
include_once '../auth.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Data Renters (Simulasi Database)
$renters = [
    ['id' => 'USR-001', 'name' => 'Nyoman Ayu Carmenita', 'email' => 'carmenita@gmail.com', 'phone' => '081234567890', 'joined' => '12 Jan 2026', 'total_rent' => 5, 'status' => 'Verified', 'avatar' => 'https://i.pinimg.com/736x/05/54/ff/0554ffc8047ea9b2f5bbe3bba9563010.jpg', 'ktp' => '../uploads/ktp_carmen.jpg', 'selfie' => '../uploads/selfie_carmen.jpg'],
    ['id' => 'USR-002', 'name' => 'Zavi Andri', 'email' => 'zavi@gmail.com', 'phone' => '085711223344', 'joined' => '05 Feb 2026', 'total_rent' => 2, 'status' => 'Verified', 'avatar' => 'https://api.dicebear.com/8.x/avataaars/svg?seed=Zavi', 'ktp' => 'https://api.dicebear.com/8.x/identicon/svg?seed=KTP2', 'selfie' => 'https://api.dicebear.com/8.x/avataaars/svg?seed=Zavi'],
    ['id' => 'USR-003', 'name' => 'Ravi Putra', 'email' => 'ravi@gmail.com', 'phone' => '081399887766', 'joined' => '20 Feb 2026', 'total_rent' => 1, 'status' => 'Pending', 'avatar' => 'https://api.dicebear.com/8.x/avataaars/svg?seed=Ravi', 'ktp' => 'https://api.dicebear.com/8.x/identicon/svg?seed=KTP3', 'selfie' => 'https://api.dicebear.com/8.x/avataaars/svg?seed=Ravi'],
    ['id' => 'USR-004', 'name' => 'Irfan Hakim', 'email' => 'irfan@gmail.com', 'phone' => '089900112233', 'joined' => '01 Mar 2026', 'total_rent' => 0, 'status' => 'Suspended', 'avatar' => 'https://api.dicebear.com/8.x/avataaars/svg?seed=Irfan', 'ktp' => '', 'selfie' => ''],
];

$counts = ['Verified' => 0, 'Pending' => 0, 'Suspended' => 0, 'Total' => count($renters)];
foreach ($renters as $r) {
    if (isset($counts[$r['status']])) $counts[$r['status']]++;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountster Admin - Renters</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-content: #E3EED4;
            --sidebar-bg: #FFFFFF;
            --primary-dark: #1B4332;
            --accent-green: #6B9071;
            --white: #FFFFFF;
            --pending: #B26B00;
            --verified: #1F7A33;
            --suspended: #F5222D;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-content);
            display: flex;
            min-height: 100vh;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            padding: 40px 25px;
            display: flex;
            flex-direction: column;
            z-index: 100;
            border-right: 1px solid rgba(0, 0, 0, 0.05);
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            color: #1B4332;
            text-align: center;
            margin-bottom: 50px;
            text-decoration: none;
        }

        .logo span {
            color: #6B9071;
        }

        .nav-menu {
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            border-radius: 15px;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .nav-item.active {
            background: var(--primary-dark);
            color: white;
        }

        .nav-item.active i {
            color: #AEC3B0;
        }

        .nav-item:hover:not(.active) {
            background: #F4F7F1;
        }

        .logout {
            color: #FF4D4F;
            border-top: 1px solid #EEE;
            padding-top: 20px;
            margin-top: auto;
        }

        /* --- Main Content --- */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 50px;
        }

        .header-area h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 30px;
        }

        /* --- Stats Cards Per-Category --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 24px;
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: 0.3s;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }

        .stat-card.active {
            border-color: var(--primary-dark);
            background: #f9fffb;
        }

        .stat-card label {
            font-size: 11px;
            font-weight: 700;
            color: #AEC3B0;
            text-transform: uppercase;
            cursor: pointer;
            display: block;
            margin-bottom: 8px;
        }

        .stat-card h2 {
            font-size: 32px;
            color: var(--primary-dark);
            font-weight: 800;
        }

        /* Pulse Alert for Pending */
        .pulse-alert {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 10px;
            height: 10px;
            background: var(--suspended);
            border-radius: 50%;
            display: <?php echo ($counts['Pending'] > 0) ? 'block' : 'none'; ?>;
        }

        .pulse-alert::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: var(--suspended);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
            opacity: 0.5;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }

            100% {
                transform: scale(3);
                opacity: 0;
            }
        }

        /* Icon Indikator di kanan bawah kartu */
        .card-icon {
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 60px;
            color: rgba(0, 0, 0, 0.03);
            transform: rotate(-15deg);
        }

        /* --- Search Bar --- */
        .search-container {
            position: relative;
            margin-bottom: 30px;
        }

        .search-container input {
            width: 100%;
            padding: 18px 30px 18px 60px;
            border-radius: 20px;
            border: none;
            outline: none;
            box-shadow: var(--shadow);
            font-family: inherit;
            font-size: 15px;
        }

        .search-container i {
            position: absolute;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
            color: #AEC3B0;
            font-size: 18px;
        }

        /* --- Table Styling --- */
        .renter-card {
            background: var(--white);
            border-radius: 30px;
            padding: 30px;
            box-shadow: var(--shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px 10px;
            color: #AEC3B0;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            border-bottom: 2px solid #F8F8F8;
        }

        td {
            padding: 20px 10px;
            border-bottom: 1px solid #F8F8F8;
            font-size: 14px;
            vertical-align: middle;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-cell img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            background: #F4F7F1;
        }

        .user-name {
            font-weight: 700;
            color: var(--primary-dark);
            display: block;
        }

        .user-email {
            font-size: 12px;
            color: #888;
        }

        .badge {
            padding: 8px 18px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-verified {
            background: #E3F9E5;
            color: var(--verified);
        }

        .badge-pending {
            background: #FFF4E5;
            color: var(--pending);
        }

        .badge-suspended {
            background: #FFF1F0;
            color: var(--suspended);
        }

        .actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .btn-action {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            font-size: 15px;
        }

        .btn-detail {
            background: #F0F7F4;
            color: var(--primary-dark);
        }

        .btn-approve {
            background: #E3F9E5;
            color: var(--verified);
        }

        .btn-reject {
            background: #FFF1F0;
            color: var(--suspended);
        }

        .btn-suspend {
            background: #F8F8F8;
            color: #BBB;
        }

        .btn-action:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        /* --- Custom Modals --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(27, 67, 50, 0.4);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            backdrop-filter: blur(8px);
        }

        .modal-box {
            background: white;
            width: 95%;
            max-width: 480px;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            animation: popUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popUp {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        #toast {
            position: fixed;
            top: 30px;
            right: 30px;
            background: var(--primary-dark);
            color: white;
            padding: 18px 35px;
            border-radius: 20px;
            z-index: 3000;
            display: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        #imagePreviewModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 3000;
            align-items: center;
            justify-content: center;
            cursor: zoom-out;
        }

        #imagePreviewModal img {
            max-width: 85%;
            max-height: 85%;
            border-radius: 15px;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body>

    <div id="toast">Update Berhasil!</div>

    <div class="modal-overlay" id="confirmModal">
        <div class="modal-box" style="text-align: center;">
            <div style="width: 70px; height: 70px; background: #F4F7F1; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fas fa-question-circle" style="font-size: 30px; color: var(--primary-dark);"></i>
            </div>
            <h2 style="font-size: 20px; color: var(--primary-dark); margin-bottom: 10px;">Konfirmasi</h2>
            <p id="confirmMsg" style="color: #666; font-size: 14px; margin-bottom: 30px;"></p>
            <div style="display: flex; gap: 12px;">
                <button class="btn-action" style="flex:1; height: 50px; background: #F0F0F0; color: #666; border-radius: 15px; font-weight: 600;" onclick="closeConfirmModal()">Batal</button>
                <button class="btn-action" id="btnExecute" style="flex:1; height: 50px; background: var(--primary-dark); color: white; border-radius: 15px; font-weight: 600;">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalRenter">
        <div class="modal-box">
            <h2 style="font-size: 18px; font-weight: 700; color: var(--primary-dark); margin-bottom: 25px; border-bottom: 1px solid #F0F0F0; padding-bottom: 15px;">Detail Penyewa</h2>
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                <img id="detAvatar" src="" style="width: 70px; height: 70px; border-radius: 50%; border: 4px solid #F4F7F1;">
                <div>
                    <span id="detName" style="font-weight: 700; font-size: 18px; color: var(--primary-dark);"></span>
                    <p id="detEmail" style="font-size: 13px; color: #888;"></p>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div><label style="font-size:10px; color:#AEC3B0; font-weight:700; text-transform:uppercase;">User ID</label>
                    <p id="detId" style="font-weight:600;"></p>
                </div>
                <div><label style="font-size:10px; color:#AEC3B0; font-weight:700; text-transform:uppercase;">No. Telepon</label>
                    <p id="detPhone" style="font-weight:600;"></p>
                </div>
            </div>
            <div style="border-top: 1px solid #F0F0F0; padding-top: 20px;">
                <h3 style="font-size: 11px; margin-bottom: 15px; color: var(--accent-green); font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Dokumen Identitas</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div style="text-align: center;">
                        <img id="detKtp" src="" class="clickable-image" style="width:100%; height:120px; object-fit:contain; background:#F9F9F9; border-radius:15px;" onclick="openImagePreview(this.src)">
                        <p style="font-size: 9px; margin-top: 8px; font-weight: 600; color: #AEC3B0;">FOTO KTP</p>
                    </div>
                    <div style="text-align: center;">
                        <img id="detSelfie" src="" class="clickable-image" style="width:100%; height:120px; object-fit:contain; background:#F9F9F9; border-radius:15px;" onclick="openImagePreview(this.src)">
                        <p style="font-size: 9px; margin-top: 8px; font-weight: 600; color: #AEC3B0;">SELFIE + KTP</p>
                    </div>
                </div>
            </div>
            <button class="btn-action" style="width:100%; height: 50px; margin-top: 30px; background: var(--primary-dark); color: white; border-radius: 15px; font-weight: 600;" onclick="closeModal()">Tutup</button>
        </div>
    </div>

    <div class="sidebar">
        <a href="#" class="logo">M<span>ST</span></a>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="inventory.php" class="nav-item"><i class="fas fa-mountain"></i> Inventory</a>
            <a href="orders.php" class="nav-item"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="renters.php" class="nav-item active"><i class="fas fa-user-tag"></i> Renters</a>
            <a href="settings.php" class="nav-item"><i class="fas fa-sliders-h"></i> Settings</a>
        </div>
        <a href="../logout.php" class="nav-item logout"><i class="fas fa-power-off"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header-area">
            <h1>Renters Management</h1>

            <div class="stats-grid">
                <div class="stat-card active" id="filter-all" onclick="setFilter('all')">
                    <i class="fas fa-users card-icon"></i>
                    <label>Total Renters</label>
                    <h2><?= $counts['Total'] ?></h2>
                </div>
                <div class="stat-card" id="filter-verified" onclick="setFilter('Verified')">
                    <i class="fas fa-user-check card-icon"></i>
                    <label>Verified</label>
                    <h2 style="color: var(--verified);"><?= $counts['Verified'] ?></h2>
                </div>
                <div class="stat-card" id="filter-pending" onclick="setFilter('Pending')" style="border-left: 4px solid var(--pending);">
                    <div class="pulse-alert"></div>
                    <i class="fas fa-user-clock card-icon"></i>
                    <label>Pending Approval</label>
                    <h2 style="color: var(--pending);"><?= $counts['Pending'] ?></h2>
                </div>
                <div class="stat-card" id="filter-suspended" onclick="setFilter('Suspended')">
                    <i class="fas fa-user-slash card-icon"></i>
                    <label>Suspended</label>
                    <h2 style="color: var(--suspended);"><?= $counts['Suspended'] ?></h2>
                </div>
            </div>
        </div>

        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="renterSearch" placeholder="Search by name or email..." onkeyup="filterRenters()">
        </div>

        <div class="renter-card">
            <table>
                <thead>
                    <tr>
                        <th>Renter Info</th>
                        <th>Contact</th>
                        <th>Activity</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="renterBody">
                    <?php foreach ($renters as $user): ?>
                        <tr id="renter-<?= $user['id'] ?>">
                            <td class="user-cell">
                                <img src="<?= $user['avatar'] ?>">
                                <div>
                                    <span class="user-name"><?= $user['name'] ?></span>
                                    <span class="user-email"><?= $user['email'] ?></span>
                                </div>
                            </td>
                            <td><?= $user['phone'] ?></td>
                            <td><strong><?= $user['total_rent'] ?></strong> Sewa</td>
                            <td><span class="badge badge-<?= strtolower($user['status']) ?>"><?= $user['status'] ?></span></td>
                            <td class="actions">
                                <button class="btn-action btn-detail" title="Lihat Profil" onclick='openDetail(<?= json_encode($user) ?>)'><i class="fas fa-eye"></i></button>
                                <?php if ($user['status'] == 'Pending'): ?>
                                    <button class="btn-action btn-approve" title="Approve" onclick="triggerConfirm('<?= $user['id'] ?>', 'Verified')"><i class="fas fa-check"></i></button>
                                    <button class="btn-action btn-reject" title="Reject" onclick="triggerConfirm('<?= $user['id'] ?>', 'Suspended')"><i class="fas fa-times"></i></button>
                                <?php else: ?>
                                    <button class="btn-action btn-suspend" title="Suspend Akun" onclick="triggerConfirm('<?= $user['id'] ?>', 'Suspended')"><i class="fas fa-ban"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="imagePreviewModal" onclick="this.style.display='none'"><img id="fullSizeImage" src=""></div>

    <script>
        let currentAction = null;
        let activeStatusFilter = 'all';

        function showNotif(msg) {
            const t = document.getElementById('toast');
            t.innerText = msg;
            t.style.display = 'block';
            setTimeout(() => t.style.display = 'none', 3000);
        }

        function setFilter(status) {
            activeStatusFilter = status;
            document.querySelectorAll('.stat-card').forEach(card => card.classList.remove('active'));
            const activeId = (status === 'all') ? 'filter-all' : `filter-${status.toLowerCase()}`;
            document.getElementById(activeId).classList.add('active');
            filterRenters();
        }

        function filterRenters() {
            const search = document.getElementById('renterSearch').value.toLowerCase();
            const rows = document.querySelectorAll('#renterBody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const rowStatus = row.querySelector('.badge').innerText;
                const matchesSearch = text.includes(search);
                const matchesStatus = (activeStatusFilter === 'all' || rowStatus === activeStatusFilter);
                row.style.display = (matchesSearch && matchesStatus) ? "" : "none";
            });
        }

        function triggerConfirm(id, status) {
            let msg = (status === 'Verified') ? `Approve identitas <b>${id}</b>?` : `Tangguhkan akun <b>${id}</b>?`;
            document.getElementById('confirmMsg').innerHTML = msg;
            document.getElementById('confirmModal').style.display = 'flex';
            currentAction = () => {
                const row = document.getElementById(`renter-${id}`);
                const badge = row.querySelector('.badge');
                badge.className = 'badge badge-' + status.toLowerCase();
                badge.innerText = status;
                showNotif(`Status ${id} berhasil diupdate!`);
                closeConfirmModal();
            };
        }

        document.getElementById('btnExecute').onclick = () => {
            if (currentAction) currentAction();
        };

        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        function openDetail(data) {
            document.getElementById('detAvatar').src = data.avatar;
            document.getElementById('detName').innerText = data.name;
            document.getElementById('detEmail').innerText = data.email;
            document.getElementById('detId').innerText = data.id;
            document.getElementById('detPhone').innerText = data.phone;
            document.getElementById('detKtp').src = data.ktp || "https://placehold.co/400x250?text=Tanpa+KTP";
            document.getElementById('detSelfie').src = data.selfie || "https://placehold.co/400x250?text=Tanpa+Selfie";
            document.getElementById('modalRenter').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modalRenter').style.display = 'none';
        }

        function openImagePreview(src) {
            if (!src || src.includes('placehold.co')) return;
            document.getElementById('fullSizeImage').src = src;
            document.getElementById('imagePreviewModal').style.display = 'flex';
        }
    </script>
</body>

</html>