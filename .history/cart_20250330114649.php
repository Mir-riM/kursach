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

    include 'includes/header.php';
    ?>
    
    <section class="container mx-auto p-4 mt-10 flex-[1]">
        <h1 class="text-3xl font-bold text-center mb-6">Корзина</h1>
    
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="space-y-4">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold"><?= htmlspecialchars($item['name']) ?></h2>
                            <p class="text-gray-600"><?= htmlspecialchars($item['price']) ?>₽</p>
                        </div>
                        <div class="flex flex-row flex-nowrap gap-5 items-center">
                            <button class="transition size-8 flex justify-center items-center leading-[0] bg-gray-200 hover:bg-gray-400 active:bg-gray-600 rounded-full text-2xl">+</button>
                            <p class="text-accent font-bold"><?= $item['quantity'] ?></p>
                            <button class="transition size-8 flex justify-center items-center leading-[0] bg-gray-200 hover:bg-gray-400 active:bg-gray-600 rounded-full text-2xl"><p class="mt-[-3px]">-</p></button>
                            <button class="transition size-8 flex justify-center items-center leading-[0] bg-red-200 hover:bg-red-400 active:bg-red-600 rounded-full text-l">✕</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-600">Корзина пуста.</p>
        <?php endif; ?>
    </section>
    
    <?php include 'includes/footer.php'; ?>
</div>