// wishlist.js

// --- Fungsi Membuat Toast Notification ---
function showToast(pesan, tipe = 'tambah') {
    // Cek apakah wadah toast sudah ada, jika belum buat baru
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    // Buat elemen notifikasi baru
    const toast = document.createElement('div');
    toast.className = `toast-box ${tipe === 'hapus' ? 'hapus' : ''}`;
    toast.innerHTML = pesan;

    // Masukkan ke dalam wadah
    container.appendChild(toast);

    // Hapus elemen dari HTML setelah 3 detik (biar gak numpuk)
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// --- Logika Wishlist (Ikon Hati Merah) ---
function toggleWishlist(productId) {
    let wishlist = JSON.parse(localStorage.getItem('mountsterWishlist')) || [];
    const index = wishlist.indexOf(productId);
    
    if (index === -1) {
        wishlist.push(productId);
        // Panggil notifikasi hijau
        showToast("Berhasil ditambahkan ke Wishlist! ❤️", "tambah");
    } else {
        wishlist.splice(index, 1);
        // Panggil notifikasi merah
        showToast("Dihapus dari Wishlist! 💔", "hapus");
    }
    
    localStorage.setItem('mountsterWishlist', JSON.stringify(wishlist));
    updateWishlistIcons();
}

function updateWishlistIcons() {
    let wishlist = JSON.parse(localStorage.getItem('mountsterWishlist')) || [];
    const heartIcons = document.querySelectorAll('.heart-icon');
    
    heartIcons.forEach(icon => {
        const onclickAttr = icon.getAttribute('onclick');
        if (onclickAttr) {
            const match = onclickAttr.match(/\d+/); 
            if (match) {
                const id = parseInt(match[0]);
                if (wishlist.includes(id)) {
                    icon.classList.add('loved');
                    icon.style.color = 'red'; 
                } else {
                    icon.classList.remove('loved');
                    icon.style.color = '#ccc'; 
                }
            }
        }
    });
}

// --- Logika Latest Search (Cari Terbaru dengan 'x') ---
function handleLatestSearch(keyword) {
    let searches = JSON.parse(localStorage.getItem('mountsterLatestSearches')) || [];
    const index = searches.indexOf(keyword);
    if (index === -1) {
        searches.unshift(keyword); 
        if (searches.length > 5) searches.pop(); 
    } else {
        searches.splice(index, 1);
        searches.unshift(keyword);
    }
    localStorage.setItem('mountsterLatestSearches', JSON.stringify(searches));
}

function renderLatestSearches() {
    const searches = JSON.parse(localStorage.getItem('mountsterLatestSearches')) || [];
    const container = document.getElementById('latestSearchContainer');
    const list = document.getElementById('latestSearchList');
    if (container && list) {
        if (searches.length === 0) {
            container.style.display = 'none';
        } else {
            container.style.display = 'block';
            list.innerHTML = "";
            searches.forEach((keyword, index) => {
                list.innerHTML += `
                    <div class="latest-search-item">
                        <span>🕒 ${keyword}</span>
                        <span class="latest-search-del" onclick="deleteLatestSearch(${index})">x</span>
                    </div>
                `;
            });
        }
    }
}

function deleteLatestSearch(index) {
    let searches = JSON.parse(localStorage.getItem('mountsterLatestSearches')) || [];
    searches.splice(index, 1);
    localStorage.setItem('mountsterLatestSearches', JSON.stringify(searches));
    renderLatestSearches();
}

// --- Logika Add to Cart (Tambah ke Keranjang) ---
function handleAddToCart(product) {
    let cart = JSON.parse(localStorage.getItem('mountsterCart')) || [];
    const existingItem = cart.find(item => item.id === product.id);
    if(existingItem) {
        existingItem.qty += 1;
    } else {
        cart.push({ id: product.id, name: product.name, price: product.price, qty: 1 });
    }
    localStorage.setItem('mountsterCart', JSON.stringify(cart));
}