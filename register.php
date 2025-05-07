<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $password);
        $stmt->execute();
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $error_message = $e->getCode() == 23000 ? "Bu kullanıcı adı zaten var." : "Hata: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<img src="shape-left.png" alt="arka plan şekli" class="bg-shape-left">
<img src="shape-right.png" alt="arka plan şekli" class="bg-shape-right">

<header>
    <h1>Book Tracker</h1>
    <img src="logo.jpg" alt="Logo">
</header>

<div class="container">
    <h2>Kayıt Ol</h2>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?= $error_message ?></p>
    <?php endif; ?>

    <form method="POST">
        <input name="username" placeholder="Kullanıcı Adı" required>
        <input name="password" type="password" placeholder="Şifre" required>
        <button type="submit">Kayıt Ol</button>
    </form>

    <a href="login.php">⬅ Girişe Git</a>
</div>
</body>
</html>
