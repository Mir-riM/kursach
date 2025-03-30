<?php
session_start();
include 'includes/db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем все заказы пользователя
$stmtOrders = $pdo->prepare("
    SELECT o.id AS order_id, o.status, o.total_price, o.created_at 
    FROM orders o 
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
");
$stmtOrders->execute([$user_id]);
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// Функция для получения товаров в заказе
function getOrderItems($pdo, $order_id) {
    $stmt = $pdo->prepare("
        SELECT p.name, oi.quantity, oi.price 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<main class="flex flex-col min-h-[100vh]">
    
        <?php include 'includes/header.php'; ?>
    
        <section class="flex-[1] container mx-auto p-4 mt-10">
            <h1 class="text-3xl font-bold text-center mb-6">Личный кабинет</h1>
    
            <?php if (empty($orders)): ?>
                <p class="text-center text-gray-600">У вас пока нет заказов.</p>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($orders as $order): ?>
                        <div class="border rounded-lg p-4">
                            <h2 class="text-xl font-bold">Заказ №<?= htmlspecialchars($order['order_id']) ?></h2>
                            <p class="text-gray-600">Дата: <?= htmlspecialchars($order['created_at']) ?></p>
                            <p class="text-accent font-bold">Статус: <?= 
                            switch (htmlspecialchars($order['status'])) {
                                case 'approved':
                                    echo htmlspecialchars())
                                    break;
                                
                                default:
                                    # code...
                                    break;
                            }
                            ?></p>
                            <p class="text-accent font-bold">Общая стоимость: <?= htmlspecialchars($order['total_price']) ?>₽</p>
    
                            <h3 class="text-lg font-bold mt-4">Товары:</h3>
                            <ul class="list-disc list-inside">
                                <?php
                                $items = getOrderItems($pdo, $order['order_id']);
                                foreach ($items as $item): ?>
                                    <li>
                                        <?= htmlspecialchars($item['name']) ?> - <?= htmlspecialchars($item['quantity']) ?> шт. × <?= htmlspecialchars($item['price']) ?>₽
                                    </li>
                                <?php endforeach; ?>
                            </ul>
    
                            <form action="./includes/reorder.php" method="POST" class="mt-4">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                                <button type="submit" class="btn bg-primary text-white py-2 px-4 rounded-lg hover:bg-secondary transition">
                                    Повторить заказ
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    
        <?php include 'includes/footer.php'; ?>
</main class="flex flex-col min-w-[100vh]">
