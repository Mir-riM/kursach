<?php
session_start();
require_once __DIR__ . '/db.php';

// Инициализация корзины
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Обработка AJAX-действий
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  $action = $_POST['action'];
  $productId = (int)($_POST['product_id'] ?? 0);

  if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Некорректный ID',  'quantity' => $_SESSION['cart'][$productId]['quantity'] ?? 0]);
    exit;
  }

  if (!isset($_SESSION['cart'][$productId])) {
    // Получаем товар из БД, если еще нет в корзине
    $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => 'Товар не найден',  'quantity' => $_SESSION['cart'][$productId]['quantity'] ?? 0]);
      exit;
    }

    $_SESSION['cart'][$productId] = [
      'id' => $product['id'],
      'name' => $product['name'],
      'price' => $product['price'],
      'quantity' => 0,
    ];
  }

  if ($action === 'increase') {
    $_SESSION['cart'][$productId]['quantity'] += 1;
    echo json_encode(['success' => true,  'quantity' => $_SESSION['cart'][$productId]['quantity'] ?? 0]);
    exit;
  }

  if ($action === 'decrease') {
    $_SESSION['cart'][$productId]['quantity'] -= 1;
    if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
      unset($_SESSION['cart'][$productId]);
    }
    echo json_encode(['success' => true,  'quantity' => $_SESSION['cart'][$productId]['quantity'] ?? 0]);
    exit;
  }

  if ($action === 'remove') {
    unset($_SESSION['cart'][$productId]);
    echo json_encode(['success' => true,  'quantity' => $_SESSION['cart'][$productId]['quantity'] ?? 0]);
    exit;
  }
}
