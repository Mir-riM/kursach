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

foreach ($quantity_and_product_id as $item) {
    $product_id = $item['product_id'];
    $quantity = $item['quantity'];

    // Получаем данные о товаре
    $fetch_product = $pdo->prepare("
        SELECT name, id, price
        FROM products 
        WHERE id = ?
    ");
    $fetch_product->execute([$product_id]);
    $product = $fetch_product->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Если товар уже есть в корзине, увеличиваем количество
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Иначе добавляем товар в корзину
            $_SESSION['cart'][$product_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
            ];
        }
    }
}

header("Location: ../cart.php");
exit;
