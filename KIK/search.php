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
    <div class="app-container">
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
        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && this.value.trim() !== "") {
                handleLatestSearch(this.value);
                window.location.href = `search.php?q=${this.value}`;
            }
        });

        renderLatestSearches();

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
            filteredProducts = products.filter(p => p.rating == ratingQuery);
            resultTitle = `Produk Best Seller (Rating ${ratingQuery})`;
            document.getElementById('pageTitle').innerText = "Best Seller";
        }

        document.getElementById('resultText').innerText = resultTitle;
        const container = document.getElementById('productContainer');

        if (filteredProducts.length === 0) {
            container.innerHTML = `<div style="grid-column: span 2; text-align:center; padding: 40px;"><h3>Not Found!</h3><p>Produk tidak ada di gunung ini.</p></div>`;
        } else {
            filteredProducts.forEach(p => {
                container.innerHTML += `
                    <div class="card-new">
                        <a href="detail.php?id=${p.id}" style="text-decoration:none; color:inherit; display:block;">
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
        }
    </script>
</body>
</html>