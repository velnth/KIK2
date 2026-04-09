// database.js
const products = [
    // TENDA
    { id: 1, name: "Eiger Wanderlust 60", category: "Tas Carrier", price: "Rp 50.000 / Hari" },
    { id: 2, name: "Consina Magnum 4", category: "Tenda", price: "Rp 45.000 / Hari" },
    { id: 3, name: "Naturehike Cloud Up 2", category: "Tenda", price: "Rp 60.000 / Hari" },
    { id: 4, name: "Great Outdoor Java 4", category: "Tenda", price: "Rp 40.000 / Hari" },
    { id: 5, name: "Antarestar", category: "Tenda", price: "Rp 45.000 / Hari" },
    { id: 6, name: "Merapi Mountain Half Moon", category: "Tenda", price: "Rp 55.000 / Hari" },
    { id: 7, name: "Tenda Pramuka Regu", category: "Tenda", price: "Rp 30.000 / Hari" },
    { id: 8, name: "Tenda Dome 2 Orang", category: "Tenda", price: "Rp 25.000 / Hari" },
    { id: 9, name: "Naturehike Village 5", category: "Tenda", price: "Rp 80.000 / Hari" },
    { id: 10, name: "Eiger Shira 1P", category: "Tenda", price: "Rp 35.000 / Hari" },

    // TAS CARRIER
    { id: 11, name: "Osprey Aether 65L", category: "Tas Carrier", price: "Rp 70.000 / Hari" },
    { id: 12, name: "Deuter Futura Pro 40", category: "Tas Carrier", price: "Rp 65.000 / Hari" },
    { id: 13, name: "Eiger Eliptic Solaris 65L", category: "Tas Carrier", price: "Rp 50.000 / Hari" },
    { id: 14, name: "Consina Tarebbi 60L", category: "Tas Carrier", price: "Rp 40.000 / Hari" },
    { id: 15, name: "Arei Ramandika 60L", category: "Tas Carrier", price: "Rp 40.000 / Hari" },
    { id: 16, name: "Eiger Rhinos 60L", category: "Tas Carrier", price: "Rp 45.000 / Hari" },
    { id: 17, name: "Osprey Ariel 55L (Women)", category: "Tas Carrier", price: "Rp 70.000 / Hari" },
    { id: 18, name: "Consina Extraterrestrial 60L", category: "Tas Carrier", price: "Rp 45.000 / Hari" },
    { id: 19, name: "Deuter Aircontact 50+10", category: "Tas Carrier", price: "Rp 75.000 / Hari" },
    { id: 20, name: "Naturehike Rock 60L", category: "Tas Carrier", price: "Rp 55.000 / Hari" },

    // SEPATU
    { id: 21, name: "Salomon Quest 4 GTX", category: "Sepatu", price: "Rp 80.000 / Hari" },
    { id: 22, name: "Eiger Pollock", category: "Sepatu", price: "Rp 45.000 / Hari" },
    { id: 23, name: "Consina Alpine", category: "Sepatu", price: "Rp 40.000 / Hari" },
    { id: 24, name: "SNTA 471", category: "Sepatu", price: "Rp 30.000 / Hari" },
    { id: 25, name: "La Sportiva TX4", category: "Sepatu", price: "Rp 85.000 / Hari" },
    { id: 26, name: "Merrell Moab 3", category: "Sepatu", price: "Rp 70.000 / Hari" },
    { id: 27, name: "Eiger Anaconda", category: "Sepatu", price: "Rp 50.000 / Hari" },
    { id: 28, name: "Columbia Newton Ridge", category: "Sepatu", price: "Rp 60.000 / Hari" },
    { id: 29, name: "Arei Outdoorgear Shoes", category: "Sepatu", price: "Rp 35.000 / Hari" },
    { id: 30, name: "Karrimor Bodmin", category: "Sepatu", price: "Rp 40.000 / Hari" },

    // ALAT MASAK
    { id: 31, name: "Kompor Portable Kotak", category: "Alat Masak", price: "Rp 15.000 / Hari" },
    { id: 32, name: "Trangia 27-1 UL", category: "Alat Masak", price: "Rp 40.000 / Hari" },
    { id: 33, name: "Nesting Bulat 4 in 1", category: "Alat Masak", price: "Rp 20.000 / Hari" },
    { id: 34, name: "Nesting Kotak TNI", category: "Alat Masak", price: "Rp 15.000 / Hari" },
    { id: 35, name: "Kompor Mawar (Windproof)", category: "Alat Masak", price: "Rp 20.000 / Hari" },
    { id: 36, name: "Panci Lipat Naturehike", category: "Alat Masak", price: "Rp 25.000 / Hari" },
    { id: 37, name: "Gas Kaleng Hi-Cook", category: "Alat Masak", price: "Beli - Rp 25.000" },
    { id: 38, name: "Windshield (Pelindung Angin)", category: "Alat Masak", price: "Rp 5.000 / Hari" },
    { id: 39, name: "Jerigen Air Lipat 5L", category: "Alat Masak", price: "Rp 5.000 / Hari" },
    { id: 40, name: "Set Alat Makan (Sendok Garpu Pisau)", category: "Alat Masak", price: "Rp 5.000 / Hari" },

    // APPAREL
    { id: 41, name: "Jaket Eiger Tropic", category: "Apparel", price: "Rp 35.000 / Hari" },
    { id: 42, name: "Celana Sambung Consina", category: "Apparel", price: "Rp 20.000 / Hari" },
    { id: 43, name: "Jas Hujan Arei Ponco", category: "Apparel", price: "Rp 15.000 / Hari" },
    { id: 44, name: "Base Layer Thermal", category: "Apparel", price: "Rp 20.000 / Hari" },
    { id: 45, name: "Kupluk Rajut (Beanie)", category: "Apparel", price: "Rp 5.000 / Hari" },
    { id: 46, name: "Sarung Tangan Polar", category: "Apparel", price: "Rp 10.000 / Hari" },
    { id: 47, name: "Jaket Bulang (Down Jacket)", category: "Apparel", price: "Rp 50.000 / Hari" },
    { id: 48, name: "Kaos Kaki Trekking Tebal", category: "Apparel", price: "Beli - Rp 35.000" },
    { id: 49, name: "Gaiter Anti Pacet", category: "Apparel", price: "Rp 10.000 / Hari" },
    { id: 50, name: "Topi Rimba Eiger", category: "Apparel", price: "Rp 10.000 / Hari" }
];

const productImageMap = {
    "Eiger Wanderlust 60": "images/eiger-wanderlust-60.jpeg",
    "Consina Magnum 4": "images/consina-magnum-4.jpeg",
    "Naturehike Cloud Up 2": "images/naturehike-cloud-up-2.jpeg",
    "Great Outdoor Java 4": "images/great-outdoor-java-4.jpeg",
    "Antarestar": "images/antarestar.png",
    "Merapi Mountain Half Moon": "images/merapi-mountain-half-moon.jpeg",
    "Tenda Pramuka Regu": "images/tenda-pramuka-regu.jpg",
    "Tenda Dome 2 Orang": "images/tenda-dome-2-orang.jpg",
    "Naturehike Village 5": "images/naturehike-village-5.jpg",
    "Eiger Shira 1P": "images/eiger-shira-1p.jpg",
    "Osprey Aether 65L": "images/osprey-aether-65l.jpg",
    "Deuter Futura Pro 40": "images/deuter-futura-pro-40.jpg",
    "Eiger Eliptic Solaris 65L": "images/eiger-eliptic-solaris-65l.jpg",
    "Consina Tarebbi 60L": "images/consina-tarebbi-60l.jpg",
    "Arei Ramandika 60L": "images/arei-ramandika-60l.jpg",
    "Eiger Rhinos 60L": "images/eiger-rhinos-60l.jpg",
    "Osprey Ariel 55L (Women)": "images/osprey-ariel-55l-women.jpg",
    "Consina Extraterrestrial 60L": "images/consina-extraterrestrial-60l.jpg",
    "Deuter Aircontact 50+10": "images/deuter-aircontact-50plus10.jpg",
    "Naturehike Rock 60L": "images/naturehike-rock-60l.jpg",
    "Salomon Quest 4 GTX": "images/salomon-quest-4-gtx.jpg",
    "Eiger Pollock": "images/eiger-pollock.jpg",
    "Consina Alpine": "images/consina-alpine.jpg",
    "SNTA 471": "images/snta-471.jpg",
    "La Sportiva TX4": "images/la-sportiva-tx4.jpg",
    "Merrell Moab 3": "images/merrell-moab-3.jpg",
    "Eiger Anaconda": "images/eiger-anaconda.jpg",
    "Columbia Newton Ridge": "images/columbia-newton-ridge.png",
    "Arei Outdoorgear Shoes": "images/arei-outdoorgear-shoes.jpg",
    "Karrimor Bodmin": "images/karrimor-bodmin.jpg",
    "Kompor Portable Kotak": "images/kompor-portable-kotak.jpg",
    "Trangia 27-1 UL": "images/trangia-27-1-ul.jpg",
    "Nesting Bulat 4 in 1": "images/nesting-bulat-4-in-1.png",
    "Nesting Kotak TNI": "images/nesting-kotak-tni.jpg",
    "Kompor Mawar (Windproof)": "images/kompor-mawar-windproof.jpg",
    "Panci Lipat Naturehike": "images/panci-lipat-naturehike.png",
    "Gas Kaleng Hi-Cook": "images/gas-kaleng-hi-cook.jpg",
    "Windshield (Pelindung Angin)": "images/windshield-pelindung-angin.jpg",
    "Jerigen Air Lipat 5L": "images/jerigen-air-lipat-5l.jpg",
    "Set Alat Makan (Sendok Garpu Pisau)": "images/set-alat-makan-sendok-garpu-pisau.jpg",
    "Jaket Eiger Tropic": "images/jaket-eiger-tropic.jpg",
    "Celana Sambung Consina": "images/celana-sambung-consina.jpg",
    "Jas Hujan Arei Ponco": "images/jas-hujan-arei-ponco.jpg",
    "Base Layer Thermal": "images/base-layer-thermal.jpg",
    "Kupluk Rajut (Beanie)": "images/kupluk-rajut-beanie.jpg",
    "Sarung Tangan Polar": "images/sarung-tangan-polar.jpg",
    "Jaket Bulang (Down Jacket)": "images/jaket-bulang-down-jacket.jpg",
    "Kaos Kaki Trekking Tebal": "images/kaos-kaki-trekking-tebal.jpg",
    "Gaiter Anti Pacet": "images/gaiter-anti-pacet.jpg",
    "Topi Rimba Eiger": "images/topi-rimba-eiger.jpg"
};

function getProductImageByName(productName) {
    return productImageMap[productName] || "";
}

products.forEach(product => {
    product.image = getProductImageByName(product.name);
});
