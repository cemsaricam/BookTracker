<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title']);
    $author = htmlspecialchars($_POST['author']);
    $status = $_POST['status'];
    $rating = !empty($_POST['rating']) ? (int)$_POST['rating'] : null;
    $user_id = $_SESSION['user_id'];

    try {
        $sql = "INSERT INTO books (title, author, status, rating, user_id)
                VALUES (:title, :author, :status, :rating, :user_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':author', $author);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':rating', $rating);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        header("Location: dashboard.php");
        exit;
    } catch (PDOException $e) {
        $error_message = $e->getCode() == 23000 ? "Bu kitap zaten mevcut." : "Hata: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<header>
<a href="dashboard.php">
  <img src="logo.jpg" alt="Logo">
</a>
<h1>Book Tracker</h1>
</header>

<head>
    <meta charset="UTF-8">
    <title>Kitap Ekle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<img src="shape-left.png" alt="arka plan şekli" class="bg-shape-left">
<img src="shape-right.png" alt="arka plan şekli" class="bg-shape-right">
<img src="left-banner.png" alt="sol şekil" class="bg-left-banner">
<div class="container">
    <h2>Yeni Kitap Ekle</h2>

    <?php if (isset($error_message)): ?>
        <p style="color:red;"><?= $error_message ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="title" placeholder="Kitap Adı" required>
        <input type="text" name="author" placeholder="Yazar" required>
        <select name="status" required>
            <option value="Okunacak">Okunacak</option>
            <option value="Okundu">Okundu</option>
        </select>
        <select name="rating">
            <option value="">Puan Ver (Opsiyonel)</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= str_repeat("⭐", $i) ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit">Ekle</button>
    </form>

    <a href="dashboard.php">⬅ Geri Dön</a>
</div>

</body>
</html>
