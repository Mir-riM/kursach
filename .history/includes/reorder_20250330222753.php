<?php
session_start();
include 'db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$order_id = (int)($_POST['order_id'] ?? 0);

// Получаем товары из заказа
$stmt = $pdo->prepare("
    SELECT product_id, quantity 
    FROM order_items 
    WHERE order_id = ?
");
$stmt->execute([$order_id]);
$quantity_and_product_id = $stmt->fetchAll(PDO::FETCH_ASSOC);

$items = [];

foreach ($quantity_and_product_id as $item) {
    $productId = $item['product_id'];
    $quantity = $item['quantity'];

    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'quantity' => 0,
        ];
    }
    $_SESSION['cart'][$productId]['quantity'] += $quantity;
}

if (!empty($items)) {
    // Добавляем товары в корзину
    foreach ($items as $item) {
        $productId = $item['product_id'];
        $quantity = $item['quantity'];

        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = [
                'id' => $productId,
                'quantity' => 0,
            ];
        }
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    }

    $_SESSION['success'] = "Заказ успешно добавлен в корзину.";
} else {
    $_SESSION['error'] = "Не удалось найти товары для повторения заказа.";
}

header("Location: ../cart.php");
exit;
