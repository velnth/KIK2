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
                    <img src="" alt="Product" style="width: 100%; height: 200px; background-color: #eee; border-radius: 15px; object-fit: contain;">
                    <span class="heart-icon" id="detHeartIcon" style="position: absolute; bottom: 15px; right: 15px; background: white; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor:pointer; font-size: 24px;">♥</span>
                </div>
                
                <h3 style="font-size: 16px; margin-bottom: 15px;">Review (102)</h3>
                
                <div class="review-item">
                    <div class="review-header">
                        <div class="review-avatar"></div>
                        <div><p class="bold">Madelina</p><p style="font-size:10px; color:#888;">1 Month ago</p></div>
                    </div>
                    <p style="color: #ffc107; font-size:12px;">⭐⭐⭐⭐⭐</p>
                    <p style="color: var(--text-muted); margin-top: 5px;">Sangat bagus, tendanya luas dan anti bocor. Sangat direkomendasikan.</p>
                </div>

                <div class="review-item">
                    <div class="review-header">
                        <div class="review-avatar"></div>
                        <div><p class="bold">Irfan</p><p style="font-size:10px; color:#888;">1 Month ago</p></div>
                    </div>
                    <p style="color: #ffc107; font-size:12px;">⭐⭐⭐⭐⭐</p>
                    <p style="color: var(--text-muted); margin-top: 5px;">Barang terawat, wangi, pelayanan juga ramah.</p>
                </div>

                <div class="review-item">
                    <div class="review-header">
                        <div class="review-avatar"></div>
                        <div><p class="bold">Ravi Putra</p><p style="font-size:10px; color:#888;">2 Months ago</p></div>
                    </div>
                    <p style="color: #ffc107; font-size:12px;">⭐⭐⭐⭐</p>
                    <p style="color: var(--text-muted); margin-top: 5px;">Oke banget buat naik ke Merbabu kemarin.</p>
                </div>

                <div class="text-center mt-20 mb-20">
                    <span style="font-size: 12px; color: var(--text-muted);">See All Reviews</span>
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
            <button id="btnBuyNow" class="btn btn-outline-primary" style="margin: 0; flex: 1; font-size: 14px;">Beli Langsung</button>
            <button id="btnAddToCart" class="btn btn-primary" style="margin: 0; flex: 1; font-size: 14px;">Add To Cart</button>
        </div>
    </div>

    <script>
        products.forEach((p, i) => {
            p.rating = p.rating || (i % 3 === 0 ? 4.8 : (i % 2 === 0 ? 5.0 : 4.5));
            p.reviews = p.reviews || Math.floor(Math.random() * 100) + 10;
        });

        const urlParams = new URLSearchParams(window.location.search);
        const productId = parseInt(urlParams.get('id'));
        const product = products.find(p => p.id === productId) || products[0];

        document.getElementById('detHeartIcon').setAttribute('onclick', `toggleWishlist(${product.id})`);
        document.getElementById('detName').innerText = product.name;
        document.getElementById('detPrice').innerText = product.price;
        document.getElementById('detDesc').innerText = `${product.name} adalah salah satu perlengkapan terbaik di kategori ${product.category}. Dirancang dengan material berkualitas tinggi untuk menunjang keamanan dan kenyamanan aktivitas outdoor Anda di segala cuaca. Pastikan perlengkapan ini masuk dalam list penyewaan Anda!`;
        
        function switchTab(tabId) {
            document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            event.target.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        const randomProds = products.sort(() => 0.5 - Math.random()).slice(0, 2);
        const randomContainer = document.getElementById('randomProducts');
        randomProds.forEach(p => {
            randomContainer.innerHTML += `
                <div class="card-new">
                    <a href="detail.php?id=${p.id}" style="text-decoration:none; color:inherit; display:block;">
                        <img src="" alt="IMG" style="height: 80px;">
                        <h4 class="card-new-title" style="font-size:12px;">${p.name}</h4>
                        <p class="card-new-price" style="font-size:12px;">${p.price}</p>
                    </a>
                </div>
            `;
        });

        document.getElementById('btnAddToCart').addEventListener('click', function() {
            handleAddToCart(product);
            this.innerText = "✓ Added to Cart!";
            this.style.backgroundColor = "#333";
            setTimeout(() => {
                this.innerText = "Add To Cart";
                this.style.backgroundColor = "var(--primary)";
            }, 1500);
        });

        document.getElementById('btnBuyNow').addEventListener('click', function() {
            let directItem = [{ id: product.id, name: product.name, price: product.price, qty: 1 }];
            localStorage.setItem('mountsterDirectBuy', JSON.stringify(directItem));
            window.location.href = 'checkout.php'; 
        });
        
        updateWishlistIcons();
    </script>
</body>
</html>