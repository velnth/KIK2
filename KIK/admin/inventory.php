<?php
include_once '../auth.php';

// Proteksi Halaman
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Data Awal (Simulasi Database)
$items = [
    ['id' => 1, 'name' => 'Naturehike Cloud-Up 2P', 'category' => 'Tent', 'stock' => 5, 'price' => 60000, 'status' => 'Available'],
    ['id' => 2, 'name' => 'Osprey Aether 65L', 'category' => 'Carrier', 'stock' => 3, 'price' => 45000, 'status' => 'Available'],
    ['id' => 3, 'name' => 'Consina Magnum 4', 'category' => 'Tent', 'stock' => 0, 'price' => 50000, 'status' => 'Out of Stock'],
    ['id' => 4, 'name' => 'Eiger Wanderlust 60', 'category' => 'Carrier', 'stock' => 2, 'price' => 40000, 'status' => 'Maintenance'],
    ['id' => 5, 'name' => 'Salomon Quest 4 GTX', 'category' => 'Shoes', 'stock' => 8, 'price' => 35000, 'status' => 'Available'],
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountster Admin - Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-content: #E3EED4;
            --sidebar-bg: #FFFFFF;
            --primary-dark: #1B4332;
            --accent-green: #6B9071;
            --text-main: #1B4332;
            --white: #FFFFFF;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
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
            overflow-x: hidden;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            padding: 40px 25px;
            display: flex;
            flex-direction: column;
            z-index: 100;
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

        .nav-item i {
            width: 20px;
            font-size: 18px;
            color: #6B9071;
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

        /* --- MAIN CONTENT --- */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 50px;
            width: calc(100% - 280px);
        }

        .header-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .header-area h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .btn-add {
            background: var(--primary-dark);
            color: white;
            padding: 12px 25px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        .btn-add:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* --- SEARCH & FILTER --- */
        .tools-bar {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .search-box {
            flex: 1;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 15px 25px;
            border-radius: 15px;
            border: none;
            outline: none;
            box-shadow: var(--shadow);
            font-family: inherit;
        }

        .filter-box select {
            padding: 15px 25px;
            border-radius: 15px;
            border: none;
            outline: none;
            box-shadow: var(--shadow);
            background: white;
            cursor: pointer;
            font-family: inherit;
        }

        /* --- TABLE CARD --- */
        .inventory-card {
            background: var(--white);
            border-radius: 30px;
            padding: 40px;
            box-shadow: var(--shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding-bottom: 25px;
            color: #84a187;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        td {
            padding: 20px 0;
            border-bottom: 1px solid #F8F8F8;
            font-size: 14px;
            color: var(--primary-dark);
            vertical-align: middle;
        }

        .td-name {
            font-weight: 700;
            width: 30%;
        }

        .td-cat {
            color: #666;
            width: 15%;
        }

        .td-stock {
            font-weight: 600;
            width: 10%;
        }

        .td-price {
            font-weight: 700;
            width: 15%;
            color: #375534;
        }

        /* --- STATUS BADGES --- */
        .badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }

        .badge-available {
            background: #E3F9E5;
            color: #1F7A33;
        }

        .badge-out {
            background: #FFEBEB;
            color: #CC3333;
        }

        .badge-maintenance {
            background: #FFF4E5;
            color: #B26B00;
        }

        /* --- ACTION BUTTONS --- */
        .actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding-right: 10px;
        }

        .btn-action {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            font-size: 14px;
        }

        .btn-edit {
            background: #Eef2ff;
            color: #4F46E5;
        }

        .btn-delete {
            background: #FFF1F0;
            color: #F5222D;
        }

        .btn-action:hover {
            transform: scale(1.1);
        }

        /* --- MODAL --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-box {
            background: white;
            width: 90%;
            max-width: 400px;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            animation: pop 0.3s ease;
        }

        @keyframes pop {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #666;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #EEE;
            border-radius: 12px;
            outline: none;
            font-family: inherit;
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-save {
            background: var(--primary-dark);
            color: white;
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-close {
            background: #F5F5F5;
            color: #666;
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
        }

        /* TOAST */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--primary-dark);
            color: white;
            padding: 15px 30px;
            border-radius: 15px;
            z-index: 2000;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            display: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body>

    <div class="toast" id="toast">Operasi Berhasil!</div>

    <div class="sidebar">
        <a href="#" class="logo">M<span>ST</span></a>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="inventory.php" class="nav-item active"><i class="fas fa-mountain"></i> Inventory</a>
            <a href="orders.php" class="nav-item"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="renters.php" class="nav-item"><i class="fas fa-user-tag"></i> Renters</a>
            <a href="messages.php" class="nav-item"><i class="fas fa-comment-alt"></i> Messages</a>
            <a href="settings.php" class="nav-item"><i class="fas fa-sliders-h"></i> Settings</a>
        </div>
        <a href="../logout.php" class="nav-item logout"><i class="fas fa-power-off"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header-area">
            <h1>Inventory Management</h1>
            <button class="btn-add" onclick="toggleModal('addModal', true)"><i class="fas fa-plus"></i> Add New Item</button>
        </div>

        <div class="tools-bar">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search product name..." onkeyup="filterTable()">
            </div>
            <div class="filter-box">
                <select id="categoryFilter" onchange="filterTable()">
                    <option value="">All Categories</option>
                    <option value="Tent">Tent</option>
                    <option value="Carrier">Carrier</option>
                    <option value="Shoes">Shoes</option>
                </select>
            </div>
        </div>

        <div class="inventory-card">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Price/Day</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryBody">
                    <?php foreach ($items as $item): ?>
                        <tr id="row-<?= $item['id'] ?>">
                            <td class="td-name"><?= $item['name'] ?></td>
                            <td class="td-cat"><?= $item['category'] ?></td>
                            <td class="td-stock"><?= $item['stock'] ?> pcs</td>
                            <td class="td-price" data-raw="<?= $item['price'] ?>">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td>
                                <?php
                                $cls = "badge-available";
                                if ($item['status'] == 'Out of Stock') $cls = "badge-out";
                                if ($item['status'] == 'Maintenance') $cls = "badge-maintenance";
                                ?>
                                <span class="badge <?= $cls ?>"><?= $item['status'] ?></span>
                            </td>
                            <td class="actions">
                                <button class="btn-action btn-edit" onclick="openEdit(<?= $item['id'] ?>)"><i class="fas fa-edit"></i></button>
                                <button class="btn-action btn-delete" onclick="deleteItem(<?= $item['id'] ?>)"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addModal" class="modal-overlay">
        <div class="modal-box">
            <h3>Add New Item</h3>
            <div class="form-group"><label>Name</label><input type="text" id="addName" class="form-control"></div>
            <div class="form-group"><label>Category</label><select id="addCat" class="form-control">
                    <option>Tent</option>
                    <option>Carrier</option>
                    <option>Shoes</option>
                </select></div>
            <div class="form-group"><label>Stock</label><input type="number" id="addStock" class="form-control"></div>
            <div class="form-group"><label>Price/Day</label><input type="number" id="addPrice" class="form-control"></div>
            <div class="modal-footer">
                <button class="btn-save" onclick="addItem()">Save Item</button>
                <button class="btn-close" onclick="toggleModal('addModal', false)">Cancel</button>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-box">
            <h3>Edit Item</h3>
            <input type="hidden" id="editId">
            <div class="form-group"><label>Name</label><input type="text" id="editName" class="form-control"></div>
            <div class="form-group"><label>Category</label><select id="editCat" class="form-control">
                    <option>Tent</option>
                    <option>Carrier</option>
                    <option>Shoes</option>
                </select></div>
            <div class="form-group"><label>Stock</label><input type="number" id="editStock" class="form-control"></div>
            <div class="form-group"><label>Price/Day</label><input type="number" id="editPrice" class="form-control"></div>
            <div class="modal-footer">
                <button class="btn-save" onclick="saveEdit()">Update Item</button>
                <button class="btn-close" onclick="toggleModal('editModal', false)">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        function toggleModal(id, show) {
            document.getElementById(id).style.display = show ? 'flex' : 'none';
        }

        function showNotif(msg) {
            const t = document.getElementById('toast');
            t.innerText = msg;
            t.style.display = 'block';
            setTimeout(() => t.style.display = 'none', 3000);
        }

        function filterTable() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const cat = document.getElementById('categoryFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#inventoryBody tr');
            rows.forEach(row => {
                const name = row.querySelector('.td-name').innerText.toLowerCase();
                const category = row.querySelector('.td-cat').innerText.toLowerCase();
                const matchesSearch = name.includes(q);
                const matchesCat = cat === "" || category === cat;
                row.style.display = (matchesSearch && matchesCat) ? "" : "none";
            });
        }

        function addItem() {
            const name = document.getElementById('addName').value;
            const cat = document.getElementById('addCat').value;
            const stock = document.getElementById('addStock').value;
            const price = document.getElementById('addPrice').value;
            if (!name || !stock || !price) return alert("Isi semua data!");

            const id = Date.now();
            const body = document.getElementById('inventoryBody');
            const row = document.createElement('tr');
            row.id = `row-${id}`;
            row.innerHTML = `
                <td class="td-name">${name}</td>
                <td class="td-cat">${cat}</td>
                <td class="td-stock">${stock} pcs</td>
                <td class="td-price" data-raw="${price}">Rp ${parseInt(price).toLocaleString('id-ID')}</td>
                <td><span class="badge badge-available">Available</span></td>
                <td class="actions">
                    <button class="btn-action btn-edit" onclick="openEdit(${id})"><i class="fas fa-edit"></i></button>
                    <button class="btn-action btn-delete" onclick="deleteItem(${id})"><i class="fas fa-trash"></i></button>
                </td>
            `;
            body.prepend(row);
            toggleModal('addModal', false);
            showNotif("Barang Berhasil Ditambah!");
        }

        function deleteItem(id) {
            if (confirm("Hapus barang ini?")) {
                document.getElementById(`row-${id}`).remove();
                showNotif("Barang Dihapus!");
            }
        }

        function openEdit(id) {
            const row = document.getElementById(`row-${id}`);
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = row.querySelector('.td-name').innerText;
            document.getElementById('editCat').value = row.querySelector('.td-cat').innerText;
            document.getElementById('editStock').value = parseInt(row.querySelector('.td-stock').innerText);
            document.getElementById('editPrice').value = row.querySelector('.td-price').getAttribute('data-raw');
            toggleModal('editModal', true);
        }

        function saveEdit() {
            const id = document.getElementById('editId').value;
            const row = document.getElementById(`row-${id}`);
            const name = document.getElementById('editName').value;
            const cat = document.getElementById('editCat').value;
            const stock = document.getElementById('editStock').value;
            const price = document.getElementById('editPrice').value;

            row.querySelector('.td-name').innerText = name;
            row.querySelector('.td-cat').innerText = cat;
            row.querySelector('.td-stock').innerText = stock + " pcs";
            row.querySelector('.td-price').innerText = "Rp " + parseInt(price).toLocaleString('id-ID');
            row.querySelector('.td-price').setAttribute('data-raw', price);

            toggleModal('editModal', false);
            showNotif("Data Diperbarui!");
        }
    </script>
</body>

</html>