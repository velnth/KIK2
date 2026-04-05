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
