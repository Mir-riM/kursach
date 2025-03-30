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
    $productId = (int) ($_POST['product_id'] ?? 0);

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

    if (empty($customer_name) || empty($phone) || empty($address)) {
        $error = "Все поля обязательны для заполнения.";
    } elseif (empty($_SESSION['cart'])) {
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
        $_SESSION['success'] = "Заказ успешно создан!";
        header("Location: cart.php");
        exit;
    }
}
?>


<main class="flex flex-col min-h-[100vh]">
    <?php include 'includes/header.php'; ?>

    <section class="container flex-[1] mx-auto p-4 mt-10">
        <h1 class="text-3xl font-bold text-center mb-6">Корзина</h1>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Успех!</strong>
                <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="space-y-4">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="border rounded-lg p-4 flex justify-between items-center" id="cart-item-<?= $item['id'] ?>">
                        <div>
                            <h2 class="text-xl font-bold"><?= htmlspecialchars($item['name']) ?></h2>
                            <p class="text-gray-600"><?= htmlspecialchars($item['price']) ?>₽</p>
                        </div>
                        <div class="flex items-center space-x-2 gap-5">
                            <button
                                class="btn-increase size-8 flex leading-[0] justify-center items-center rounded-full bg-gray-200 hover:bg-gray-400 active:bg-gray-600 transition text-2xl"
                                data-product-id="<?= $item['id'] ?>">+</button>
                            <p class="text-accent font-bold quantity-display" data-product-id="<?= $item['id'] ?>">
                                <?= $item['quantity'] ?></p>
                            <button
                                class="btn-decrease size-8 flex leading-[0] justify-center items-center rounded-full bg-gray-200 hover:bg-gray-400 active:bg-gray-600 transition text-2xl"
                                data-product-id="<?= $item['id'] ?>">-</button>
                            <button
                                class="btn-remove !ml-5 size-8 flex leading-[0] justify-center items-center rounded-full bg-rose-500 hover:bg-rose-600 active:bg-rose-800 transition text-l"
                                data-product-id="<?= $item['id'] ?>">✕</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Форма для оформления заказа -->
            <form method="POST" action="" class="mt-10">
                <h2 class="text-2xl font-bold mb-4">Оформление заказа</h2>

                <?php if (isset($error)): ?>
                    <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <div class="mb-4">
                    <label for="customer_name" class="block text-gray-700 font-bold mb-2">Имя</label>
                    <input type="text" name="customer_name" id="customer_name" class="w-full px-3 py-2 border rounded-lg"
                        required>
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 font-bold mb-2">Телефон</label>
                    <input type="text" name="phone" id="phone" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="address" class="block text-gray-700 font-bold mb-2">Адрес доставки</label>
                    <textarea name="address" id="address" rows="3" class="w-full px-3 py-2 border rounded-lg"
                        required></textarea>
                </div>

                <button type="submit" name="create_order"
                    class="w-full bg-primary text-white py-2 rounded-lg hover:bg-secondary transition">
                    Оформить заказ
                </button>
            </form>
        <?php else: ?>
            <p class="text-center text-gray-600">Корзина пуста.</p>
        <?php endif; ?>
    </section>

    <?php include 'includes/footer.php'; ?>

</main>