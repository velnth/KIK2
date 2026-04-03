<?php
include '../auth.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'Admin';
}

$rentals = [
    [
        'id' => 1,
        'customer' => 'Asananda KSMA',
        'product' => 'Naturehike Cloud-Up 3P Tent',
        'phone' => '6285219478152',
        'rental_date' => '2026-04-01',
        'due_date' => '2026-04-03'
    ],
    [
        'id' => 2,
        'customer' => 'Hanafdl',
        'product' => 'Osprey Aether 65L Rucksack',
        'phone' => '6289652443371',
        'rental_date' => '2026-03-28',
        'due_date' => '2026-04-01'
    ],
];

$today = date('Y-m-d');
$store_gmaps = "https://maps.google.com/mountsterstore";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountster Admin - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #0F2A1D;
            --primary-mid: #375534;
            --accent: #6B9071;
            --soft-green: #AEC3B0;
            --bg-light: #E3EED4;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            --transition: 0.3s ease;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--primary-dark);
            background-color: var(--bg-light);
            display: flex;
            min-height: 100vh;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1549880181-56a44cf4a9a1?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            filter: blur(25px);
            opacity: 0.15;
            z-index: -1;
        }

        .sidebar {
            width: 280px;
            background-color: var(--white);
            height: 100vh;
            position: fixed;
            padding: 40px 20px;
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar-logo {
            font-size: 30px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 50px;
            text-align: center;
            text-decoration: none;
        }

        .sidebar-logo span {
            color: var(--accent);
        }

        .nav-item {
            color: var(--primary-dark);
            text-decoration: none;
            padding: 15px 22px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 8px;
        }

        .nav-item:hover:not(.active) {
            background-color: #f2f7ec;
        }

        .nav-item.active {
            background-color: var(--primary-dark);
            color: var(--white);
            box-shadow: var(--shadow);
        }

        .nav-item i {
            font-size: 18px;
            color: var(--accent);
            width: 25px;
            text-align: center;
        }

        .nav-item.active i {
            color: var(--soft-green);
        }

        .logout-nav {
            margin-top: auto;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding-top: 20px;
        }

        .nav-item.logout {
            color: #ff4d4f;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 40px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .welcome-msg h1 {
            margin: 5px 0 0 0;
            font-size: 30px;
            color: var(--primary-dark);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background-color: var(--primary-dark);
            border-radius: 50%;
            color: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background-color: var(--white);
            padding: 25px;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }

        .stat-card.dark {
            background-color: var(--primary-dark);
            color: var(--white);
        }

        .stat-card.dark h2 {
            color: var(--white);
        }

        .stat-card p {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
            margin: 0 0 10px 0;
            opacity: 0.7;
            letter-spacing: 1px;
        }

        .stat-card h2 {
            font-size: 28px;
            margin: 0;
        }

        .table-card {
            background-color: var(--white);
            border-radius: 24px;
            padding: 30px;
            box-shadow: var(--shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            color: var(--accent);
            font-size: 11px;
            padding-bottom: 15px;
            text-transform: uppercase;
        }

        td {
            padding: 18px 0;
            border-bottom: 1px solid #f9f9f9;
            font-size: 14px;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            color: white;
        }

        .btn-wa {
            background-color: var(--bg-light);
            color: var(--primary-dark);
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-wa:hover {
            background-color: var(--soft-green);
        }

        .wa-icon {
            color: #25D366;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <a href="#" class="sidebar-logo">M<span>ST</span></a>
        <nav style="flex: 1;">
            <a href="#" class="nav-item active"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="#" class="nav-item"><i class="fas fa-mountain"></i> Inventory</a>
            <a href="#" class="nav-item"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="#" class="nav-item"><i class="fas fa-user-tag"></i> Renters</a>
            <a href="#" class="nav-item"><i class="fas fa-comment-alt"></i> Messages</a>
            <a href="#" class="nav-item"><i class="fas fa-sliders-h"></i> Settings</a>
        </nav>
        <div class="logout-nav">
            <a href="logout.php" class="nav-item logout"><i class="fas fa-power-off"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="content-header">
            <div class="welcome-msg">
                <p>Welcome back, <?php echo $_SESSION['user_name']; ?> 👋</p>
                <h1>Dashboard Overview</h1>
            </div>
            <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 2)); ?></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card dark">
                <p>Total Revenue</p>
                <h2>Rp 18.5M</h2>
            </div>
            <div class="stat-card">
                <p>Active Renters</p>
                <h2>42 People</h2>
            </div>
            <div class="stat-card">
                <p>Due Today</p>
                <h2>01</h2>
            </div>
            <div class="stat-card dark" style="background-color: #ff4d4f;">
                <p>Overdue</p>
                <h2>01</h2>
            </div>
        </div>

        <div class="table-card">
            <h3 style="margin-bottom: 25px;">Return Alerts</h3>
            <table>
                <thead>
                    <tr>
                        <th>CUSTOMER</th>
                        <th>PRODUCT</th>
                        <th>DUE DATE</th>
                        <th>STATUS</th>
                        <th style="text-align: right;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rentals as $rent):
                        $is_overdue = ($rent['due_date'] < $today);
                        $is_due_today = ($rent['due_date'] == $today);

                        $status_label = "";
                        $status_color = "";
                        $msg = "";
                        $start = date('d M', strtotime($rent['rental_date']));
                        $due = date('d M Y', strtotime($rent['due_date']));

                        if ($is_overdue) {
                            $status_label = "OVERDUE";
                            $status_color = "#ff4d4f";
                            $msg = "Halo " . $rent['customer'] . "! aku Asa ingin menginfokan bahwa Rental:\n\n*" . $rent['product'] . "*\nDisewa pada: " . $start . "\nDikembalikan pada: " . $due . "\n\nSudah MELEWATI batas waktu pengembalian nih. Mohon segera dikembalikan ya kak agar denda tidak terus bertambah.\n\nRencana dikembalikan lewat apa?\n\n01. Antar ke Store : " . $store_gmaps . "\n02. ⁠Jasa Jemput : Minta kurir untuk menjemput barang ke rumah (Biaya disesuaikan dengan jarak).\n\nBoleh tolong dikonfirmasi ya kak, terima kasih! :]";
                        } elseif ($is_due_today) {
                            $status_label = "DUE TODAY";
                            $status_color = "#faad14";
                            $msg = "Halo " . $rent['customer'] . "! aku Asa ingin mengingatkan bahwa Rental:\n\n*" . $rent['product'] . "*\nDisewa pada: " . $start . "\nDikembalikan pada: " . $due . "\n\nSudah harus dikembalikan hari ini, ya! Batas pengembaliannya paling lambat sampai pukul 15.00. Jika lewat dari jam tersebut, akan dikenakan biaya denda. Tolong dikembalikan sesuai dengan waktunya ya, kak.\n\nRencana dikembalikan lewat apa?\n\n01. Antar ke Store : " . $store_gmaps . "\n02. ⁠Jasa Jemput : Minta kurir untuk menjemput barang ke rumah (Biaya disesuaikan dengan jarak).\n\nBoleh tolong dikonfirmasi ya kak kalau sudah siap, terima kasih! :]";
                        } else {
                            continue;
                        }

                        $wa_link = "https://wa.me/" . $rent['phone'] . "?text=" . urlencode($msg);
                    ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo $rent['customer']; ?></td>
                            <td><?php echo $rent['product']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($rent['due_date'])); ?></td>
                            <td><span class="status-badge" style="background: <?php echo $status_color; ?>;"><?php echo $status_label; ?></span></td>
                            <td style="text-align: right;">
                                <a href="<?php echo $wa_link; ?>" target="_blank" class="btn-wa">
                                    <i class="fab fa-whatsapp wa-icon"></i> Chat WA
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
