<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error_message = "Kullanıcı adı veya şifre hatalı.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
<h1>Book Tracker</h1>
<img src="logo.jpg" alt="Logo">
</header>
<img src="shape-left.png" alt="arka plan şekli" class="bg-shape-left">
<img src="shape-right.png" alt="arka plan şekli" class="bg-shape-right">
<img src="left-banner.png" alt="sol şekil" class="bg-left-banner">
<div class="container">
    <h2>Giriş Yap</h2>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?= $error_message ?></p>
    <?php endif; ?>

    <form method="POST">
        <input name="username" placeholder="Kullanıcı Adı" required>
        <input name="password" type="password" placeholder="Şifre" required>
        <button type="submit">Giriş Yap</button>
    </form>

    <a href="register.php">⬅ Hesabın yok mu? Kayıt ol</a>
</div>
</body>
</html>
