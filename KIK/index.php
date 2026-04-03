<?php 
include 'auth.php'; 

$error = "";
if (isset($_POST['login'])) {
    $role = login($_POST['email'], $_POST['password']);
    
    if ($role == 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($role == 'user') {
        header("Location: home.html");
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
    <div class="app-container" style="background: url('TARUH_LINK_GAMBAR_BACKGROUND_GUNUNG_DISINI') center/cover;">

        <div style="background: rgba(0,0,0,0.5); width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; padding: 40px 20px;">

            <div class="text-center" style="margin-bottom: 40px;">
                <h1 style="color: var(--primary); font-size: 45px; text-shadow: 1px 1px 5px rgba(0,0,0,0.8); letter-spacing: 1px;">
                    MOUNTSTER</h1>
                <p style="color: white; font-size: 14px; margin-top: 5px; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                    Ready to explore the peaks?</p>
            </div>

            <div class="login-card">
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

                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" class="input-form" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 0;">
                        Masuk ke Akun
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>

</html>