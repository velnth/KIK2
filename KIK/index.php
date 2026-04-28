<?php 
include 'auth.php'; 

$error = "";
if (isset($_POST['login'])) {
    $role = login($_POST['email'], $_POST['password']);
    
    if ($role == 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($role == 'user') {
        header("Location: home.php");
        exit();
    } else {
        $error = "Email atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountster - Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Menambahkan align-items: center pada div overlay untuk menengahkan konten secara horisontal -->
    <div class="app-container" style="background: url('https://images.unsplash.com/photo-1511884642898-4c92249e20b6?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') center/cover;">

        <div style="background: rgba(0,0,0,0.5); width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px 20px;">

            <!-- Kontainer Teks -->
            <div class="text-center" style="margin-bottom: 40px; text-align: center;">
                <h1 style="color: var(--primary); font-size: 45px; text-shadow: 1px 1px 5px rgba(0,0,0,0.8); letter-spacing: 1px; margin: 0;">
                    MOUNTSTER</h1>
                <p style="color: white; font-size: 14px; margin-top: 5px; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                    Face the Wild, be the Mountster</p>
            </div>

            <!-- Kartu Login -->
            <div class="login-card" style="width: 100%; max-width: 400px;">
                <?php if ($error): ?>
                    <p style="color: #ff4d4f; font-size: 12px; text-align: center; margin-bottom: 15px; font-weight: bold;">
                        <?php echo $error; ?>
                    </p>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="input-form" placeholder="abcde@gmail.com" required>
                    </div>

                    <div class="input-group" style="margin-top: 15px;">
                        <label>Password</label>
                        <input type="password" name="password" class="input-form" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary" style="margin-top: 30px; width: 100%;">
                        Masuk ke Akun
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        // Hapus paksa semua riwayat di browser setiap kali halaman login ini dibuka
        localStorage.removeItem('mountsterOrders');
        localStorage.removeItem('mountsterCart');
        localStorage.removeItem('mountsterSelectedAddress');
    </script>
</body>
</html>