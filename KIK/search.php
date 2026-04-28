<?php 
include 'auth.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css">
    <script src="database.js"></script>
    <script src="wishlist.js"></script>
</head>
<body>
    <div class="app-container" style="padding-bottom: 80px;">
        <div class="p-20 flex-between">
            <a href="javascript:history.back()" style="text-decoration: none; color: black; font-size: 20px;">←</a>
            <h2 id="pageTitle" style="font-size: 18px;">Search</h2>
            <a href="cart.php" style="text-decoration: none; font-size: 20px;">🛒</a>
        </div>

        <div style="padding: 0 20px;">
            <input type="text" id="searchInput" class="search-bar" placeholder="Search Tenda, Carrier Atau Sepatu..." style="margin-top: 0; background-color: #f0f0f0; color: black;">
        </div>

        <div class="p-20" id="latestSearchContainer" style="display:none;">
            <h3 style="font-size: 14px; margin-bottom: 10px;">Latest search</h3>
            <div id="latestSearchList"></div>
        </div>

        <div class="p-20">
            <h3 id="resultText" style="font-size: 14px; margin-bottom: 15px;">Menampilkan Produk</h3>
        </div>

        <div class="product-grid" id="productContainer"></div>
    </div>

    <script>
        // Fungsi pemanggil gambar yang lengkap
        function getProductImage(productName) {
            if (productName === 'Eiger Wanderlust 60') return 'images/eiger-wanderlust-60.jpeg';
            if (productName === 'Consina Magnum 4') return 'images/consina-magnum-4.jpeg';
            if (productName === 'Great Outdoor Java 4') return 'images/great-outdoor-java-4.jpeg';
            if (productName === 'Naturehike Cloud Up 2') return 'images/naturehike-cloud-up-2.jpeg';
            if (productName === 'Antarestar') return 'images/antarestar.png';
            if (productName === 'Merapi Mountain Half Moon') return 'images/merapi-mountain-half-moon.jpeg';
            if (productName === 'Tenda Pramuka Regu') return 'images/tenda-pramuka-regu.jpg';
            if (productName === 'Tenda Dome 2 Orang') return 'images/tenda-dome-2-orang.jpg';
            if (productName === 'Naturehike Village 5') return 'images/naturehike-village-5.jpg';
            if (productName === 'Eiger Shira 1P') return 'images/eiger-shira-1p.jpg';
            if (productName === 'Osprey Aether 65L') return 'images/osprey-aether-65l.jpg';
            if (productName === 'Deuter Futura Pro 40') return 'images/deuter-futura-pro-40.jpg';
            if (productName === 'Eiger Eliptic Solaris 65L') return 'images/eiger-eliptic-solaris-65l.jpg';
            if (productName === 'Consina Tarebbi 60L') return 'images/consina-tarebbi-60l.jpg';
            if (productName === 'Arei Ramandika 60L') return 'images/arei-ramandika-60l.jpg';
            if (productName === 'Eiger Rhinos 60L') return 'images/eiger-rhinos-60l.jpg';
            if (productName === 'Osprey Ariel 55L (Women)') return 'images/osprey-ariel-55l-women.jpg';
            if (productName === 'Consina Extraterrestrial 60L') return 'images/consina-extraterrestrial-60l.jpg';
            if (productName === 'Deuter Aircontact 50+10') return 'images/deuter-aircontact-50plus10.jpg';
            if (productName === 'Naturehike Rock 60L') return 'images/naturehike-rock-60l.jpg';
            if (productName === 'Salomon Quest 4 GTX') return 'images/salomon-quest-4-gtx.jpg';
            if (productName === 'Eiger Pollock') return 'images/eiger-pollock.jpg';
            if (productName === 'Consina Alpine') return 'images/consina-alpine.jpg';
            if (productName === 'SNTA 471') return 'images/snta-471.jpg';
            if (productName === 'La Sportiva TX4') return 'images/la-sportiva-tx4.jpg';
            if (productName === 'Merrell Moab 3') return 'images/merrell-moab-3.jpg';
            if (productName === 'Eiger Anaconda') return 'images/eiger-anaconda.jpg';
            if (productName === 'Columbia Newton Ridge') return 'images/columbia-newton-ridge.png';
            if (productName === 'Arei Outdoorgear Shoes') return 'images/arei-outdoorgear-shoes.jpg';
            if (productName === 'Karrimor Bodmin') return 'images/karrimor-bodmin.jpg';
            if (productName === 'Kompor Portable Kotak') return 'images/kompor-portable-kotak.jpg';
            if (productName === 'Trangia 27-1 UL') return 'images/trangia-27-1-ul.jpg';
            if (productName === 'Nesting Bulat 4 in 1') return 'images/nesting-bulat-4-in-1.png';
            if (productName === 'Nesting Kotak TNI') return 'images/nesting-kotak-tni.jpg';
            if (productName === 'Kompor Mawar (Windproof)') return 'images/kompor-mawar-windproof.jpg';
            if (productName === 'Panci Lipat Naturehike') return 'images/panci-lipat-naturehike.png';
            if (productName === 'Gas Kaleng Hi-Cook') return 'images/gas-kaleng-hi-cook.jpg';
            if (productName === 'Windshield (Pelindung Angin)') return 'images/windshield-pelindung-angin.jpg';
            if (productName === 'Jerigen Air Lipat 5L') return 'images/jerigen-air-lipat-5l.jpg';
            if (productName === 'Set Alat Makan (Sendok Garpu Pisau)') return 'images/set-alat-makan-sendok-garpu-pisau.jpg';
            if (productName === 'Jaket Eiger Tropic') return 'images/jaket-eiger-tropic.jpg';
            if (productName === 'Celana Sambung Consina') return 'images/celana-sambung-consina.jpg';
            if (productName === 'Jas Hujan Arei Ponco') return 'images/jas-hujan-arei-ponco.jpg';
            if (productName === 'Base Layer Thermal') return 'images/base-layer-thermal.jpg';
            if (productName === 'Kupluk Rajut (Beanie)') return 'images/kupluk-rajut-beanie.jpg';
            if (productName === 'Sarung Tangan Polar') return 'images/sarung-tangan-polar.jpg';
            if (productName === 'Jaket Bulang (Down Jacket)') return 'images/jaket-bulang-down-jacket.jpg';
            if (productName === 'Kaos Kaki Trekking Tebal') return 'images/kaos-kaki-trekking-tebal.jpg';
            if (productName === 'Gaiter Anti Pacet') return 'images/gaiter-anti-pacet.jpg';
            if (productName === 'Topi Rimba Eiger') return 'images/topi-rimba-eiger.jpg';
            
            return 'logo_mountster.png'; 
        }

        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && this.value.trim() !== "") {
                try { if (typeof handleLatestSearch === "function") { handleLatestSearch(this.value); } } catch(err) {}
                window.location.href = `search.php?q=${this.value}`;
            }
        });

        // Cegah error JS yang membuat page blank jika fungsi ini tidak ada di database.js
        try {
            if (typeof renderLatestSearches === "function") {
                renderLatestSearches();
            }
        } catch(e) {
            console.warn("renderLatestSearches tidak ditemukan.");
        }

        // Pastikan produk memiliki rating default agar filter bisa berjalan
        products.forEach((p, i) => {
            p.rating = p.rating || (i % 3 === 0 ? 4.8 : (i % 2 === 0 ? 5.0 : 4.5));
            p.reviews = p.reviews || Math.floor(Math.random() * 100) + 10;
        });

        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('q');
        const catQuery = urlParams.get('cat');
        const ratingQuery = urlParams.get('rating');

        let filteredProducts = products;
        let resultTitle = "Semua Produk";

        if (searchQuery) {
            filteredProducts = products.filter(p => p.name.toLowerCase().includes(searchQuery.toLowerCase()) || p.category.toLowerCase().includes(searchQuery.toLowerCase()));
            resultTitle = `Hasil untuk "${searchQuery}"`;
            document.getElementById('searchInput').value = searchQuery;
        } else if (catQuery) {
            filteredProducts = products.filter(p => p.category === catQuery);
            resultTitle = `Kategori: ${catQuery}`;
            document.getElementById('pageTitle').innerText = catQuery;
        } else if (ratingQuery) {
            // Memastikan filternya benar-benar membaca angka Float
            filteredProducts = products.filter(p => parseFloat(p.rating) === parseFloat(ratingQuery));
            resultTitle = `Produk Best Seller (Rating ${ratingQuery})`;
            document.getElementById('pageTitle').innerText = "Best Seller";
        }

        document.getElementById('resultText').innerText = resultTitle;
        const container = document.getElementById('productContainer');

        if (filteredProducts.length === 0) {
            container.innerHTML = `<div style="grid-column: span 2; text-align:center; padding: 40px;"><h3>Not Found!</h3><p>Produk tidak ditemukan.</p></div>`;
        } else {
            filteredProducts.forEach(p => {
                const imageSrc = p.image ? p.image : getProductImage(p.name);
                
                container.innerHTML += `
                    <div class="card-new">
                        <a href="detail.php?id=${p.id}" style="text-decoration:none; color:inherit; display:block;">
                            <div style="width: 100%; height: 100px; display:flex; justify-content:center; align-items:center; margin-bottom: 10px;">
                                <img src="${imageSrc}" alt="${p.name}" style="width: 100%; height: 100%; object-fit: contain;">
                            </div>
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
        }
    </script>
</body>
</html>