<?php 
include 'auth.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk</title>
    <link rel="stylesheet" href="style.css">
    <script src="database.js"></script>
    <script src="wishlist.js"></script>
</head>
<body>
    <div class="app-container" style="padding-bottom: 80px;">
        <div class="p-20 flex-between">
            <a href="javascript:history.back()" style="text-decoration: none; color: black; font-size: 20px;">←</a>
            <a href="cart.php" style="text-decoration: none; font-size: 20px;">🛒</a>
        </div>

        <div class="p-20" style="padding-top: 0;">
            <p id="detPrice" style="color: var(--primary); font-weight: bold;">Rp -</p>
            <h2 id="detName" style="font-size: 22px; margin-top: 5px; margin-bottom: 10px;">Nama Produk</h2>

            <div class="tab-container">
                <div class="tab-item active" onclick="switchTab('overview')">Overview</div>
                <div class="tab-item" onclick="switchTab('spec')">Specification</div>
            </div>

            <div id="overview" class="tab-content active">
                
                <div style="position: relative; margin-bottom: 20px;">
                    <img id="detImage" src="" alt="Product" style="width: 100%; height: 200px; background-color: #eee; border-radius: 15px; object-fit: contain;">
                </div>
                
                <h3 style="font-size: 16px; margin-bottom: 15px;" id="reviewTitleCount">Review (0)</h3>
                
                <div id="reviewContainer"></div>

                <div class="text-center mt-20 mb-20">
                    <span style="font-size: 12px; color: var(--text-muted); cursor: pointer;">See All Reviews</span>
                </div>

                <div class="flex-between" style="margin-bottom: 15px; margin-top: 30px;">
                    <h3 style="font-size: 16px;">Another Product</h3>
                    <a href="search.php" style="font-size: 12px; color: var(--text-muted); text-decoration: none;">See All</a>
                </div>
                <div class="product-grid" id="randomProducts" style="padding: 0;"></div>
            </div>

            <div id="spec" class="tab-content">
                <p id="detDesc" style="font-size: 14px; color: var(--text-muted); line-height: 1.6;">
                    Deskripsi produk akan muncul di sini...
                </p>
            </div>
        </div>

        <div style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 400px; padding: 20px; background: white; box-shadow: 0 -5px 10px rgba(0,0,0,0.05); z-index: 10; display: flex; gap: 10px;">
            <button id="btnBuyNow" class="btn btn-outline-primary" style="margin: 0; flex: 1; font-size: 14px;">Rental Sekarang</button>
            <button id="btnAddToCart" class="btn btn-primary" style="margin: 0; flex: 1; font-size: 14px;">Add To Cart</button>
        </div>
    </div>

    <script>
        // Fungsi untuk mengambil gambar
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

        // Database Review Acak
        const reviewDatabase = [
            { name: "Madelina", date: "1 Month ago", rating: "⭐⭐⭐⭐⭐", text: "Sangat bagus, barangnya terawat dan bersih. Sangat direkomendasikan." },
            { name: "Irfan", date: "1 Month ago", rating: "⭐⭐⭐⭐⭐", text: "Barang wangi, pelayanan juga ramah. Mantap Mountster!" },
            { name: "Ravi Putra", date: "2 Months ago", rating: "⭐⭐⭐⭐", text: "Oke banget buat naik ke Merbabu kemarin. Harga sewanya juga masuk akal." },
            { name: "Tania", date: "2 Weeks ago", rating: "⭐⭐⭐⭐⭐", text: "Kualitas barang seperti baru. Nggak nyesel sewa di sini." },
            { name: "Dika", date: "3 Weeks ago", rating: "⭐⭐⭐⭐", text: "Cukup memuaskan, walau ada sedikit goresan tapi fungsinya masih 100% normal." },
            { name: "Sarah V.", date: "1 Month ago", rating: "⭐⭐⭐⭐⭐", text: "Proses sewa dan pengembaliannya sangat mudah dan cepat." },
            { name: "Bima A.", date: "2 Months ago", rating: "⭐⭐⭐⭐⭐", text: "Aman dari badai! Sangat melindungi dari cuaca ekstrem di Rinjani." },
            { name: "Alif", date: "5 Days ago", rating: "⭐⭐⭐⭐", text: "Cocok untuk pemula. Penggunaannya mudah dan praktis." },
            { name: "Nisa", date: "1 Week ago", rating: "⭐⭐⭐⭐⭐", text: "Warnanya sesuai ekspektasi. Fotogenik banget pas difoto di puncak." },
            { name: "Kevin", date: "3 Months ago", rating: "⭐⭐⭐⭐⭐", text: "Penyelamat banget nyewa dadakan di Mountster, barangnya ready stock terus." }
        ];

        products.forEach((p, i) => {
            p.rating = p.rating || (i % 3 === 0 ? 4.8 : (i % 2 === 0 ? 5.0 : 4.5));
            p.reviews = p.reviews || Math.floor(Math.random() * 100) + 10;
        });

        const urlParams = new URLSearchParams(window.location.search);
        const productId = parseInt(urlParams.get('id'));
        const product = products.find(p => p.id === productId) || products[0];

        // Set Informasi Produk Utama
        document.getElementById('detName').innerText = product.name;
        document.getElementById('detPrice').innerText = product.price;
        document.getElementById('detImage').src = product.image || getProductImage(product.name);
        document.getElementById('detDesc').innerText = `${product.name} adalah salah satu perlengkapan terbaik di kategori ${product.category}. Dirancang dengan material berkualitas tinggi untuk menunjang keamanan dan kenyamanan aktivitas outdoor Anda di segala cuaca. Pastikan perlengkapan ini masuk dalam list penyewaan Anda!`;
        document.getElementById('reviewTitleCount').innerText = `Review (${product.reviews})`;
        
        // Render 3 Review Secara Acak
        const reviewContainer = document.getElementById('reviewContainer');
        // Shuffle (acak) urutan database review
        const shuffledReviews = reviewDatabase.sort(() => 0.5 - Math.random());
        // Ambil 3 data pertama
        const selectedReviews = shuffledReviews.slice(0, 3);
        
        selectedReviews.forEach(rev => {
            reviewContainer.innerHTML += `
                <div class="review-item">
                    <div class="review-header">
                        <div class="review-avatar"></div>
                        <div><p class="bold">${rev.name}</p><p style="font-size:10px; color:#888;">${rev.date}</p></div>
                    </div>
                    <p style="color: #ffc107; font-size:12px;">${rev.rating}</p>
                    <p style="color: var(--text-muted); margin-top: 5px;">${rev.text}</p>
                </div>
            `;
        });

        function switchTab(tabId) {
            document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            event.target.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        // Render Produk Rekomendasi (Another Product)
        const randomProds = products.sort(() => 0.5 - Math.random()).slice(0, 2);
        const randomContainer = document.getElementById('randomProducts');
        randomProds.forEach(p => {
            randomContainer.innerHTML += `
                <div class="card-new">
                    <a href="detail.php?id=${p.id}" style="text-decoration:none; color:inherit; display:block;">
                        <img src="${p.image || getProductImage(p.name)}" alt="${p.name}" style="width: 100%; height: 80px; object-fit: contain;">
                        <h4 class="card-new-title" style="font-size:12px;">${p.name}</h4>
                        <p class="card-new-price" style="font-size:12px;">${p.price}</p>
                    </a>
                </div>
            `;
        });

        document.getElementById('btnAddToCart').addEventListener('click', function() {
            // Pastikan fungsi handleAddToCart (dari database.js/wishlist.js) terpanggil dengan image yang benar
            let productToAdd = { ...product };
            productToAdd.image = productToAdd.image || getProductImage(productToAdd.name);
            
            if(typeof handleAddToCart === "function") {
                handleAddToCart(productToAdd);
            }
            
            this.innerText = "✓ Added to Cart!";
            this.style.backgroundColor = "#333";
            setTimeout(() => {
                this.innerText = "Add To Cart";
                this.style.backgroundColor = "var(--primary)";
            }, 1500);
        });

        document.getElementById('btnBuyNow').addEventListener('click', function() {
            let directItem = [{ 
                id: product.id, 
                name: product.name, 
                price: product.price, 
                qty: 1,
                image: product.image || getProductImage(product.name)
            }];
            localStorage.setItem('mountsterDirectBuy', JSON.stringify(directItem));
            window.location.href = 'checkout.php'; 
        });
        
    </script>
</body>
</html>