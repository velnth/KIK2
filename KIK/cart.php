<?php 
include 'auth.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container" style="padding-bottom: 100px;">
        <div class="p-20 flex-between">
            <a href="javascript:history.back()" style="text-decoration: none; color: black; font-size: 20px;">←</a>
            <h2 style="font-size: 18px;">Shopping Cart</h2>
            <div style="width: 20px;"></div>
        </div>

        <div class="p-20" id="cartContainer"></div>

        <div style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 400px; padding: 20px; background: white; box-shadow: 0 -5px 10px rgba(0,0,0,0.05); z-index: 10;">
            <div class="flex-between mb-10" style="margin-bottom: 15px;">
                <span style="color: var(--text-muted);" id="cartTotalItems">Total 0 Item</span>
                <span class="bold" style="font-size: 18px; color: var(--primary);" id="cartTotalPrice">Rp 0</span>
            </div>
            <a href="checkout.php" class="btn btn-primary" style="margin: 0;">Checkout Sekarang</a>
        </div>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('mountsterCart')) || [];

        function renderCart() {
            const container = document.getElementById('cartContainer');
            container.innerHTML = "";

            if(cart.length === 0) {
                container.innerHTML = `<div style="text-align:center; padding: 40px;"><h3 style="color:#888;">Keranjang Kosong</h3><p style="font-size:12px; margin-top:10px;">Yuk cari barang dulu!</p></div>`;
                document.getElementById('cartTotalItems').innerText = `Total 0 Item`;
                document.getElementById('cartTotalPrice').innerText = `Rp 0`;
                return;
            }

            let totalItems = 0;
            let totalPrice = 0;

            cart.forEach((item, index) => {
                let priceNumber = parseInt(item.price.replace(/[^0-9]/g, ''));
                if (isNaN(priceNumber)) priceNumber = 0;

                totalItems += item.qty;
                totalPrice += (priceNumber * item.qty);

                container.innerHTML += `
                    <div style="display: flex; gap: 15px; margin-bottom: 15px; background: white; padding: 15px; border-radius: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <div style="width: 60px; height: 60px; background: #eee; border-radius: 10px; display:flex; justify-content:center; align-items:center; font-size:20px;">⛺</div>
                        <div style="flex: 1;">
                            <h4 style="font-size: 14px; margin-bottom: 5px;">${item.name}</h4>
                            <p style="color: var(--primary); font-weight: bold; font-size: 12px;">${item.price}</p>
                            
                            <div style="display: flex; align-items: center; gap: 15px; margin-top: 10px;">
                                <button class="qty-btn" onclick="updateQty(${index}, -1)">-</button>
                                <span style="font-size: 14px; font-weight: bold;">${item.qty}</span>
                                <button class="qty-btn" onclick="updateQty(${index}, 1)">+</button>
                            </div>
                        </div>
                    </div>
                `;
            });

            document.getElementById('cartTotalItems').innerText = `Total ${totalItems} Item`;
            document.getElementById('cartTotalPrice').innerText = `Rp ` + totalPrice.toLocaleString('id-ID');
        }

        // Fungsi Tambah/Kurang
        function updateQty(index, change) {
            cart[index].qty += change;
            
            if(cart[index].qty <= 0) {
                cart.splice(index, 1); // Hapus produk jika qty 0
            }

            localStorage.setItem('mountsterCart', JSON.stringify(cart));
            renderCart();
        }

        renderCart();
    </script>
</body>
</html>