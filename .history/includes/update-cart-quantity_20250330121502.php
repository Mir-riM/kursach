<?php
session_start();

// Функция для подсчета общего количества товаров в корзине
function getTotalCartQuantity() {
    $totalQuantity = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $totalQuantity += $item['quantity'];
        }
    }
    return $totalQuantity;
}

// Получаем общее количество товаров
$totalQuantity = getTotalCartQuantity();

// Возвращаем результат в формате JSON
echo json_encode([
    'success' => true,
    'totalQuantity' => $totalQuantity,
]);
?>