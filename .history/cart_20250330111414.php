<div class="flex flex-col min-h-[100vh]">
<?php
session_start(); // Запускаем сессию
include 'includes/db.php';

// Инициализация корзины в сессии
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка AJAX-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);

    if ($action === 'add' && $productId > 0) {
        // Добавление товара в корзину
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            if (!isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 0,
                ];
            }
            $_SESSION['cart'][$productId]['quantity'] += 1;
            echo json_encode(['success' => true, 'message' => 'Товар добавлен в корзину']);
            exit;
        }
    } elseif ($action === 'remove' && $productId > 0) {
        // Удаление товара из корзины
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            echo json_encode(['success' => true, 'message' => 'Товар удален из корзины']);
            exit;
        }
    } elseif ($action === 'decrease' && $productId > 0) {
        // Уменьшение количества товара
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] -= 1;

            if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                unset($_SESSION['cart'][$productId]);
            }

            echo json_encode(['success' => true, 'message' => 'Количество товара уменьшено']);
            exit;
        }
    }
}
</div>