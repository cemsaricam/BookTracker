<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM books WHERE user_id = ?";
$params = [$user_id];

if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $q = '%' . trim($_GET['q']) . '%';
    $query .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = $q;
    $params[] = $q;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<header>
    <h1>Book Tracker</h1>
    <a href="dashboard.php">
  <img src="logo.jpg" alt="Logo">
</a>
   <h3> <a href="add_book.php">➕ Yeni Kitap Ekle</a> | <a href="recommend.php">📚 Kitap Önerisi Al</a> | <a href="logout.php">Çıkış Yap</a> </h3>
</header>

<head>
    <meta charset="UTF-8">
    <title>Kitaplarım</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<img src="shape-left.png" alt="arka plan şekli" class="bg-shape-left">
<img src="shape-right.png" alt="arka plan şekli" class="bg-shape-right">
<img src="left-banner.png" alt="sol şekil" class="bg-left-banner">

<div class="container">
    <h2>Kitaplarım</h2>

    <form method="GET">
        <input type="text" name="q" placeholder="Kitap adı veya yazar" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button type="submit">Ara</button>
        <a href="dashboard.php">Tümünü Göster</a>
    </form>

    <table>
        <tr>
            <th>Başlık</th>
            <th>Yazar</th>
            <th>Durum</th>
            <th>Puan</th>
            <th>İşlemler</th>
        </tr>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><?= $book['status'] ?></td>
                <td>
                    <?php
                        if ($book['rating']) {
                            echo str_repeat("⭐", $book['rating']);
                        } else {
                            echo "-";
                        }
                    ?>
                </td>
                <td>
                    <a href="update_book.php?id=<?= $book['id'] ?>">Güncelle</a> | 
                    <a href="delete_book.php?id=<?= $book['id'] ?>" onclick="return confirm('Silmek istediğinize emin misiniz?');">Sil</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
