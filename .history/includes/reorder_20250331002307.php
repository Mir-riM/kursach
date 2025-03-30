<?php
session_start();
include 'db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$_SESSION['cart'] = {]}

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

var_dump($quantity_and_product_id);


foreach ($quantity_and_product_id as $item) {

    $fetch_product = $pdo->prepare("
    SELECT name, id, price
    FROM products 
    WHERE id = ?
");


    $fetch_product->execute([$item['product_id']]);

    $product = $fetch_product->fetchAll(PDO::FETCH_ASSOC);

    $product[0]['quantity'] = $item['quantity'];

    array_push($_SESSION['cart'], $product[0]);
}

header("Location: ../cart.php");
exit;
