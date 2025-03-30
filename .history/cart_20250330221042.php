<?php
session_start();
include 'includes/db.php';

// Инициализация корзины в сессии
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка AJAX-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $productId = (int)($_POST['product_id'] ?? 0);

    if ($action === 'increase' && $productId > 0) {
        // Увеличение количества товара
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += 1;
            echo json_encode(['success' => true, 'message' => 'Количество товара увеличено']);
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
    } elseif ($action === 'remove' && $productId > 0) {
        // Удаление товара из корзины
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            echo json_encode(['success' => true, 'message' => 'Товар удален из корзины']);
            exit;
        }
    }
}

// Обработка создания заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $customer_name = trim($_POST['customer_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Валидация имени
    if (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s]+$/', $customer_name)) {
        $error = "Имя может содержать только буквы.";
    }
    // Валидация телефона
    elseif (!preg_match('/^\+?[78]\d{10}$/', $phone)) {
        $error = "Номер телефона должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX.";
    }
    // Проверка адреса
    elseif (empty($address)) {
        $error = "Адрес доставки обязателен для заполнения.";
    }
    // Проверка корзины
    elseif (empty($_SESSION['cart'])) {
        $error = "Корзина пуста. Добавьте товары перед оформлением заказа.";
    } else {
        // Вычисляем общую стоимость заказа
        $total_price = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        // Создаем заказ
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, customer_name, phone, address, total_price)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$_SESSION['user_id'] ?? null, $customer_name, $phone, $address, $total_price]);
        $order_id = $pdo->lastInsertId();

        // Добавляем товары в таблицу order_items
        foreach ($_SESSION['cart'] as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        }

        // Очищаем корзину
        $_SESSION['cart'] = [];

        // Уведомление об успешном создании заказа
        $_SESSION['success'] = "Заказ успешно создан! Вы можете просмотреть его в личном кабинете.";
        header("Location: cart.php");
        exit;
    }
}
