<?php
session_start();

$users = [
    ['name' => 'Admin', 'email' => 'admin@mountster.com', 'password' => 'admin123', 'role' => 'admin'],
    ['name' => 'Customer 1', 'email' => 'cust1@gmail.com', 'password' => 'user123', 'role' => 'user', 'gender'=> 'Male', 'avatar_url' => 'https://api.dicebear.com/8.x/notionists/svg?seed=Erick'],
    ['name' => 'Customer 2', 'email' => 'cust2@gmail.com', 'password' => 'user456', 'role' => 'user', 'gender'=> 'Female', 'avatar_url' => 'https://api.dicebear.com/8.x/notionists/svg?seed=Anya']
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
?>