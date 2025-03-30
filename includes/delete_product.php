<?php
session_start();
include '../includes/db.php';

// Проверка авторизации и роли
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);

// Удаление товара
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$product_id]);

header("Location: ../admin.php");
exit;
