<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id AND user_id = :user_id");
$stmt->bindValue(':id', $book_id);
$stmt->bindValue(':user_id', $user_id);
$stmt->execute();
$book = $stmt->fetch();

if (!$book) {
    die("Kitap bulunamadı.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title']);
    $author = htmlspecialchars($_POST['author']);
    $status = $_POST['status'];
    $rating = !empty($_POST['rating']) ? (int)$_POST['rating'] : null;

    try {
        $sql = "UPDATE books SET title = :title, author = :author, status = :status, rating = :rating
                WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':author', $author);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':rating', $rating);
        $stmt->bindValue(':id', $book_id);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        header("Location: dashboard.php");
        exit;
    } catch (PDOException $e) {
        $error_message = "Hata: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<header>
    <h1>Book Tracker</h1>
    <a href="dashboard.php">
  <img src="logo.jpg" alt="Logo">
</a>
</header>

<head>
    <meta charset="UTF-8">
    <title>Kitap Güncelle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<img src="shape-left.png" alt="arka plan şekli" class="bg-shape-left">
<img src="shape-right.png" alt="arka plan şekli" class="bg-shape-right">
<img src="left-banner.png" alt="sol şekil" class="bg-left-banner">

<div class="container">
    <h2>Kitap Bilgilerini Güncelle</h2>

    <?php if (isset($error_message)): ?>
        <p style="color:red;"><?= $error_message ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
        <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
        <select name="status" required>
            <option value="Okunacak" <?= $book['status'] == 'Okunacak' ? 'selected' : '' ?>>Okunacak</option>
            <option value="Okundu" <?= $book['status'] == 'Okundu' ? 'selected' : '' ?>>Okundu</option>
        </select>
        <select name="rating">
            <option value="">Puan Ver (Opsiyonel)</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>" <?= $book['rating'] == $i ? 'selected' : '' ?>><?= str_repeat("⭐", $i) ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit">Güncelle</button>
    </form>

    <a href="dashboard.php">⬅ Geri Dön</a>
</div>

</body>
</html>
