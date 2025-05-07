<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM books WHERE id = ? AND user_id = ?");
$stmt->execute([$book_id, $user_id]);

header("Location: dashboard.php");
exit;
?>
