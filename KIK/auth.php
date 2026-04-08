<?php
session_start();

// --- 1. DATA USER & FUNGSI LOGIN ---
$users = [
    ['name' => 'Admin', 'email' => 'admin@mountster.com', 'password' => 'admin123', 'role' => 'admin'],
    ['name' => 'Customer 1', 'email' => 'cust1@gmail.com', 'password' => 'user123', 'role' => 'user', 'gender'=> 'Male', 'avatar_url' => 'https://api.dicebear.com/8.x/notionists/svg?seed=Erick'],
    ['name' => 'Carmen', 'email' => 'carmenita@gmail.com', 'password' => 'carmenheart2hearts', 'role' => 'user', 'gender'=> 'Female', 'avatar_url' => 'https://i.pinimg.com/736x/67/ba/9b/67ba9baf20594962401c2c46ab136dca.jpg']
];

function login($email, $password) {
    global $users;
    foreach ($users as $user) {
        if ($user['email'] == $email && $user['password'] == $password) {
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_gender'] = $user['gender'] ?? null;
            $_SESSION['user_avatar'] = $user['avatar_url'] ?? null;
            return $user['role'];
        }
    }
    return false;
}

function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}

// --- 2. SISTEM PROTEKSI OTOMATIS TERPUSAT ---
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['index.php']; // Halaman yang bebas dibuka tanpa login

// Aturan 1: Jika belum login, tendang ke index.php
if (!in_array($current_page, $public_pages) && !isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

// Aturan 2: Jika SUDAH login tapi iseng buka halaman index.php lagi, tendang ke home.php
if (in_array($current_page, $public_pages) && isset($_SESSION['user_email'])) {
    if (!isset($_GET['action']) || $_GET['action'] !== 'logout') {
        header("Location: home.php");
        exit();
    }
}
?>