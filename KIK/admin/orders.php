<?php
include_once '../auth.php';

// Proteksi Halaman: Hanya Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Data Pesanan (Simulasi Database - Ditambah field phone, ktp, dan selfie)
$orders = [
    [
        'id' => 'ORD-001', 
        'customer' => 'Asa Putri', 
        'phone' => '081234567890',
        'product' => 'Naturehike Cloud-Up 2P', 
        'date' => '04 Apr - 06 Apr', 
        'total' => 120000, 
        'status' => 'Pending',
        'ktp' => 'https://api.dicebear.com/8.x/identicon/svg?seed=KTP1', 
        'selfie' => 'https://api.dicebear.com/8.x/avataaars/svg?seed=Asa'
    ],
    [
        'id' => 'ORD-002', 
        'customer' => 'Zavi Andri', 
        'phone' => '085711223344',
        'product' => 'Osprey Aether 65L', 
        'date' => '03 Apr - 05 Apr', 
        'total' => 90000, 
        'status' => 'Active',
        'ktp' => 'https://api.dicebear.com/8.x/identicon/svg?seed=KTP2',
        'selfie' => 'https://api.dicebear.com/8.x/avataaars/svg?seed=Zavi'
    ],
    [
        'id' => 'ORD-003', 
        'customer' => 'Ravi Putra', 
        'phone' => '081399887766',
        'product' => 'Salomon Quest 4 GTX', 
        'date' => '01 Apr - 03 Apr', 
        'total' => 70000, 
        'status' => 'Completed',
        'ktp' => 'https://api.dicebear.com/8.x/identicon/svg?seed=KTP3',
        'selfie' => 'https://api.dicebear.com/8.x/avataaars/svg?seed=Ravi'
    ],
    [
        'id' => 'ORD-004', 
        'customer' => 'Irfan Hakim', 
        'phone' => '089900112233',
        'product' => 'Consina Magnum 4', 
        'date' => '02 Apr - 04 Apr', 
        'total' => 100000, 
        'status' => 'Canceled',
        'ktp' => 'https://api.dicebear.com/8.x/identicon/svg?seed=KTP4',
        'selfie' => 'https://api.dicebear.com/8.x/avataaars/svg?seed=Irfan'
    ],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountster Admin - Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-content: #E3EED4;
            --sidebar-bg: #FFFFFF;
            --primary-dark: #1B4332;
            --accent-green: #6B9071;
            --white: #FFFFFF;
            --shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-content); display: flex; min-height: 100vh; }

        .sidebar { width: 280px; background: var(--sidebar-bg); height: 100vh; position: fixed; padding: 40px 25px; display: flex; flex-direction: column; z-index: 100; }
        .logo { font-size: 32px; font-weight: 800; color: #1B4332; text-align: center; margin-bottom: 50px; text-decoration: none; }
        .logo span { color: #6B9071; }
        
        .nav-menu { flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 15px; padding: 15px 20px; color: #333; text-decoration: none; font-weight: 500; border-radius: 15px; margin-bottom: 10px; transition: 0.3s; }
        .nav-item i { width: 20px; font-size: 18px; color: #6B9071; }
        .nav-item.active { background: var(--primary-dark); color: white; }
        .nav-item.active i { color: #AEC3B0; }
        .nav-item:hover:not(.active) { background: #F4F7F1; }
        .logout { color: #FF4D4F; border-top: 1px solid #EEE; padding-top: 20px; margin-top: auto; }

        .main-content { flex: 1; margin-left: 280px; padding: 50px; }
        .header-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .header-area h1 { font-size: 28px; font-weight: 700; color: var(--primary-dark); }

        .tools-bar { display: flex; gap: 20px; margin-bottom: 30px; }
        .search-box { flex: 1; position: relative; }
        .search-box input { width: 100%; padding: 15px 25px; border-radius: 15px; border: none; outline: none; box-shadow: var(--shadow); font-family: inherit; }
        .filter-box select { padding: 15px 25px; border-radius: 15px; border: none; outline: none; box-shadow: var(--shadow); background: white; cursor: pointer; font-family: inherit; }

        .order-card { background: var(--white); border-radius: 30px; padding: 40px; box-shadow: var(--shadow); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding-bottom: 25px; color: #AEC3B0; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        td { padding: 20px 0; border-bottom: 1px solid #F8F8F8; font-size: 14px; color: var(--primary-dark); }
        
        .cust-name { font-weight: 700; }
        .order-id { font-size: 12px; color: #888; display: block; }
        .product-name { font-weight: 500; color: #444; }

        .badge { padding: 6px 16px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-block; }
        .badge-pending { background: #FFF4E5; color: #B26B00; }
        .badge-active { background: #E3F9E5; color: #1F7A33; }
        .badge-completed { background: #Eef2ff; color: #4F46E5; }
        .badge-canceled { background: #FFF1F0; color: #F5222D; }

        .actions { display: flex; gap: 10px; justify-content: flex-end; }
        .btn-action { width: 35px; height: 35px; border-radius: 8px; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; font-size: 14px; }
        .btn-detail { background: #F0F7F4; color: var(--primary-dark); }
        .btn-accept { background: #E3F9E5; color: #1F7A33; }
        .btn-reject { background: #FFF1F0; color: #F5222D; }
        .btn-action:hover { transform: scale(1.1); }
        .btn-action:disabled { opacity: 0.3; cursor: not-allowed; transform: none; }

        #toast { position: fixed; top: 20px; right: 20px; background: var(--primary-dark); color: white; padding: 15px 30px; border-radius: 15px; z-index: 2000; display: none; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }

        /* --- STYLES MODAL DETAIL --- */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 2000; backdrop-filter: blur(5px); }
        .modal-box { background: white; width: 90%; max-width: 550px; padding: 30px; border-radius: 25px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); animation: popUp 0.3s ease; position: relative; max-height: 90vh; overflow-y: auto; }
        @keyframes popUp { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        
        .modal-title { font-size: 20px; font-weight: 700; color: var(--primary-dark); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .detail-item label { font-size: 11px; font-weight: 700; color: #AEC3B0; text-transform: uppercase; display: block; }
        .detail-item span { font-weight: 600; color: #333; }

        .verif-section { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
        .verif-title { font-size: 12px; font-weight: 700; color: var(--primary-dark); margin-bottom: 15px; text-transform: uppercase; }
        .img-container { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .img-item { background: #f9f9f9; padding: 10px; border-radius: 15px; text-align: center; }
        .img-item img { width: 100%; height: 120px; object-fit: contain; border-radius: 10px; margin-bottom: 8px; }
        .img-item p { font-size: 10px; font-weight: 700; color: #888; }
        
        .btn-close-modal { width: 100%; padding: 12px; background: var(--primary-dark); color: white; border: none; border-radius: 12px; font-weight: 600; margin-top: 25px; cursor: pointer; }
    </style>
</head>
<body>

    <div id="toast">Update Berhasil!</div>

    <div class="modal-overlay" id="modalDetail">
        <div class="modal-box">
            <h2 class="modal-title">Detail Pesanan</h2>
            <div class="detail-grid">
                <div class="detail-item"><label>Customer</label><span id="detName"></span></div>
                <div class="detail-item"><label>No. Telepon</label><span id="detPhone"></span></div>
                <div class="detail-item"><label>Produk</label><span id="detProduct"></span></div>
                <div class="detail-item"><label>Periode Sewa</label><span id="detDate"></span></div>
                <div class="detail-item"><label>ID Pesanan</label><span id="detId"></span></div>
                <div class="detail-item"><label>Total Harga</label><span id="detTotal" style="color: #1F7A33; font-weight: 800;"></span></div>
            </div>

            <div class="verif-section">
                <h3 class="verif-title">Verifikasi Identitas</h3>
                <div class="img-container">
                    <div class="img-item">
                        <img src="" id="detKtp">
                        <p>Foto KTP</p>
                    </div>
                    <div class="img-item">
                        <img src="" id="detSelfie">
                        <p>Selfie dengan KTP</p>
                    </div>
                </div>
            </div>

            <button class="btn-close-modal" onclick="closeModal()">Tutup Detail</button>
        </div>
    </div>

    <div class="sidebar">
        <a href="#" class="logo">M<span>ST</span></a>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="inventory.php" class="nav-item"><i class="fas fa-mountain"></i> Inventory</a>
            <a href="orders.php" class="nav-item active"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="#" class="nav-item"><i class="fas fa-user-tag"></i> Renters</a>
            <a href="#" class="nav-item"><i class="fas fa-comment-alt"></i> Messages</a>
            <a href="#" class="nav-item"><i class="fas fa-sliders-h"></i> Settings</a>
        </div>
        <a href="../logout.php" class="nav-item logout"><i class="fas fa-power-off"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header-area">
            <h1>Orders Management</h1>
        </div>

        <div class="tools-bar">
            <div class="search-box">
                <input type="text" id="orderSearch" placeholder="Search customer or order ID..." onkeyup="filterOrders()">
            </div>
            <div class="filter-box">
                <select id="statusFilter" onchange="filterOrders()">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Active">Active</option>
                    <option value="Completed">Completed</option>
                    <option value="Canceled">Canceled</option>
                </select>
            </div>
        </div>

        <div class="order-card">
            <table>
                <thead>
                    <tr>
                        <th>Customer & ID</th>
                        <th>Product</th>
                        <th>Date Range</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="orderBody">
                    <?php foreach ($orders as $order): ?>
                    <tr id="order-<?= $order['id'] ?>">
                        <td>
                            <span class="cust-name"><?= $order['customer'] ?></span>
                            <span class="order-id"><?= $order['id'] ?></span>
                        </td>
                        <td class="product-name"><?= $order['product'] ?></td>
                        <td><?= $order['date'] ?></td>
                        <td style="font-weight: 700;">Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                        <td>
                            <?php 
                            $badgeCls = "badge-" . strtolower($order['status']);
                            ?>
                            <span class="badge <?= $badgeCls ?>"><?= $order['status'] ?></span>
                        </td>
                        <td class="actions">
                            <button class="btn-action btn-detail" title="Lihat Detail" onclick='openDetail(<?= json_encode($order) ?>)'><i class="fas fa-eye"></i></button>
                            
                            <button class="btn-action btn-accept" title="Terima Pesanan" 
                                onclick="updateStatus('<?= $order['id'] ?>', 'Active')" 
                                <?php echo ($order['status'] == 'Completed' || $order['status'] == 'Canceled') ? 'disabled' : ''; ?>>
                                <i class="fas fa-check"></i>
                            </button>

                            <button class="btn-action btn-reject" title="Tolak/Batalkan" onclick="updateStatus('<?= $order['id'] ?>', 'Canceled')"><i class="fas fa-times"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function showNotif(msg) {
            const t = document.getElementById('toast');
            t.innerText = msg;
            t.style.display = 'block';
            setTimeout(() => t.style.display = 'none', 3000);
        }

        function openDetail(data) {
            document.getElementById('detName').innerText = data.customer;
            document.getElementById('detPhone').innerText = data.phone;
            document.getElementById('detProduct').innerText = data.product;
            document.getElementById('detDate').innerText = data.date;
            document.getElementById('detId').innerText = data.id;
            document.getElementById('detTotal').innerText = "Rp " + data.total.toLocaleString('id-ID');
            document.getElementById('detKtp').src = data.ktp;
            document.getElementById('detSelfie').src = data.selfie;
            
            document.getElementById('modalDetail').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modalDetail').style.display = 'none';
        }

        function filterOrders() {
            const search = document.getElementById('orderSearch').value.toLowerCase();
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#orderBody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const rowStatus = row.querySelector('.badge').innerText.toLowerCase();
                const matchesSearch = text.includes(search);
                const matchesStatus = status === "" || rowStatus === status;
                row.style.display = (matchesSearch && matchesStatus) ? "" : "none";
            });
        }

        function updateStatus(id, newStatus) {
            if(confirm(`Ubah status pesanan ${id} menjadi ${newStatus}?`)) {
                const row = document.getElementById(`order-${id}`);
                const badge = row.querySelector('.badge');
                badge.className = 'badge badge-' + newStatus.toLowerCase();
                badge.innerText = newStatus;
                showNotif(`Status ${id} diperbarui ke ${newStatus}!`);
            }
        }
    </script>
</body>
</html>