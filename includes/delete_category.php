<?php
session_start();
include './db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
  $category_id = (int)$_POST['category_id'];

  // Проверяем, есть ли товары в категории
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
  $stmt->execute([$category_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION['success'] = "Невозможно удалить категорию: существуют товары.";
  } else {
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$category_id]);
    $_SESSION['success'] = "Категория успешно удалена.";
  }

  header("Location: ../admin.php?tab=categories");
  exit;
}
