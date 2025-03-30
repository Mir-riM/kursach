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
                        <div>
                            <p class="text-accent font-bold">Количество: <?= $item['quantity'] ?></p>
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