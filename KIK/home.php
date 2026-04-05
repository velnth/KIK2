<?php 
include 'auth.php'; 

// Proteksi Halaman: Jika belum login, arahkan kembali ke login
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

// Ambil data dari session untuk kemudahan penggunaan
$userName = $_SESSION['user_name'];
$userAvatar = $_SESSION['user_avatar'] ?? 'https://api.dicebear.com/8.x/notionists/svg?seed=Admin'; // Default jika admin tidak ada avatar
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
        /* Memastikan avatar tampil bulat dan rapi di dalam div profile-icon */
        .profile-icon {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            background-image: url('<?php echo $userAvatar; ?>');
            background-size: cover;
            background-position: center;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="header-green">
            <div class="flex-between">
                <a href="cart.html" style="text-decoration: none; font-size: 24px;">🛒</a>
                <h1 style="font-size: 20px; flex: 1; text-align: center; margin: 0;">mountster</h1>
                <a href="profile.php"><div class="profile-icon"></div></a>
            </div>
            
            <div style="margin-top: 20px;">
                <p id="greetingName" style="font-size: 14px;">Hi, <?php echo htmlspecialchars($userName); ?></p>
                <h2 style="font-size: 22px; margin-top: 5px;" class="hero-title">Mau Muncak<br class="mobile-br"> Kemana Hari Ini?</h2>
            </div>
            <input type="text" id="searchInput" class="search-bar" placeholder="Search Tenda, Carrier Atau Sepatu...">
        </div>

        <div class="categories">
            <a href="search.html?cat=Tenda" class="cat-chip" style="text-decoration: none;">Tenda</a>
            <a href="search.html?cat=Tas Carrier" class="cat-chip" style="text-decoration: none;">Carrier</a>
            <a href="search.html?cat=Sepatu" class="cat-chip" style="text-decoration: none;">Sepatu</a>
            <a href="search.html?cat=Alat Masak" class="cat-chip" style="text-decoration: none;">Alat Masak</a>
            <a href="search.html?cat=Apparel" class="cat-chip" style="text-decoration: none;">Apparel</a>
        </div>

        <div class="p-20" style="padding-top: 0;">
            <div style="width: 100%; height: 150px; background-color: #333; border-radius: 15px; position: relative; overflow: hidden;">
                <div style="position: relative; z-index: 2; padding: 20px; color: white; background: linear-gradient(to right, rgba(0,0,0,0.7), transparent); height: 100%;">
                    <h3 style="margin-top: 20px;">Paket Hemat<br>Semeru</h3>
                </div>
            </div>
        </div>

        <div class="p-20 flex-between" style="padding-top: 0;">
            <h3 style="font-size: 16px;">Best Seller Rental</h3>
            <a href="search.html?rating=4.8" style="font-size: 12px; color: var(--text-muted); text-decoration: none;">See All</a>
        </div>

        <div class="product-grid" id="homeProductGrid"></div>
    </div>

    <script>
        // Logika greeting localStorage dihapus karena sudah digantikan PHP Session di atas

        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && this.value.trim() !== "") {
                handleLatestSearch(this.value);
                window.location.href = `search.html?q=${this.value}`;
            }
        });

        products.forEach((p, i) => {
            p.rating = p.rating || (i % 3 === 0 ? 4.8 : (i % 2 === 0 ? 5.0 : 4.5));
            p.reviews = p.reviews || Math.floor(Math.random() * 100) + 10;
        });

        const container = document.getElementById('homeProductGrid');
        const bestSellers = products.filter(p => p.rating === 4.8).slice(0, 4);
        
        bestSellers.forEach((p, index) => {
            let hideClass = (index > 1) ? 'hide-on-mobile' : '';
            container.innerHTML += `
                <div class="card-new ${hideClass}">
                    <a href="detail.html?id=${p.id}" style="text-decoration:none; color:inherit; display:block;">
                        <img src="" alt="IMG">
                        <h4 class="card-new-title">${p.name}</h4>
                        <p class="card-new-price">${p.price}</p>
                    </a>
                    <div class="card-new-footer">
                        <span>⭐ ${p.rating}</span> 
                        <span>${p.reviews} Reviews</span>
                    </div>
                </div>
            `;
        });
    </script>

    <div class="bottom-nav">
        <a href="home.php" class="nav-item active"><span>🏠</span>Beranda</a>
        <a href="order.html" class="nav-item"><span>📋</span>Order</a>
        <a href="profile.php" class="nav-item"><span>👤</span>Saya</a>
    </div>
</body>
</html>