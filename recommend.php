<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT title FROM books WHERE user_id = ? AND status = 'Okundu'");
$stmt->execute([$user_id]);
$books = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($books)) {
    $reply = "Henüz okuduğun kitap yok. Lütfen bazı kitapları 'Okundu' olarak işaretle.";
} else {
    $book_list = implode(", ", $books);
    $api_key = "//apikeyburadaydı."
; 

    $prompt = "Ben şu kitapları okudum: $book_list.Bana benzer 3 kitap önerir misin? Sadece kitap isimleri ve yazarları ve çok kısa bir cümlelik tanıtım olsun, kısa yaz. Okuyucuya iyi okumalar de.";

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer $api_key"
        ],
        CURLOPT_POSTFIELDS => json_encode([
            "model" => "gpt-3.5-turbo",
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ],
            "temperature" => 0.7,
        ])
    ]);

    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($http_status == 200 && isset($data['choices'][0]['message']['content'])) {
        $reply = $data['choices'][0]['message']['content'];
    } else {
        $reply = "Öneriler alınamadı. Lütfen daha sonra tekrar deneyin.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kitap Önerileri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<img src="shape-left.png" alt="arka plan şekli" class="bg-shape-left">
<img src="shape-right.png" alt="arka plan şekli" class="bg-shape-right">
<img src="left-banner.png" alt="sol şekil" class="bg-left-banner">
<header>
    <h1>Book Tracker</h1>
    <img src="logo.jpg" alt="Logo">
</header>

<div class="container">
    <h2>📚 Kitap Önerileri</h2>

    <p><?= nl2br(htmlspecialchars($reply)) ?></p>

    <a href="dashboard.php">⬅ Geri Dön</a>
</div>

</body>
</html>
