<?php
session_start();
include './includes/db.php';

// Проверка авторизации и роли
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Обработка изменения статуса заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);

    $_SESSION['success'] = "Статус заказа обновлен!";
    header("Location: admin_orders.php");
    exit;
}

// Получение списка заказов
$stmtOrders = $pdo->query("
    SELECT o.id AS order_id, o.status, o.total_price, o.created_at, u.username, o.payment_type, o.comment 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at desc
");
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="flex flex-col min-h-[100vh]">

    <?php include './includes/header.php'; ?>

    <section class="container mx-auto p-4 mt-10 flex-[1]">
        <h1 class="text-3xl font-bold text-center mb-6">Подтверждение заявок<h1>

                <nav class="w-full mx-auto p-5 flex justify-center items-center gap-5">
                    <a href="./admin.php" class="btn">Товары</a>
                    <a href="./admin_orders.php" class="btn">Заявки</a>
                </nav>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <div class="space-y-6">
                    <?php foreach ($orders as $order): ?>
                        <div class="border rounded-lg p-4">
                            <h2 class="text-xl font-bold">Заказ №<?= htmlspecialchars($order['order_id']) ?></h2>
                            <p class="text-gray-600">Пользователь: <?= htmlspecialchars($order['username'] ?? 'Гость') ?></p>
                            <p class="text-accent font-bold">Статус:

                                <?php
                                echo $order['status'];
                                ?>

                            </p>
                            <p class="text-accent font-bold">Общая стоимость: <?= htmlspecialchars($order['total_price']) ?>₽</p>
                            <p class="text-gray-600">Дата: <?= htmlspecialchars($order['created_at']) ?></p>
                            <p class="text-gray-600">Способ оплаты: <?= htmlspecialchars($order['payment_type']) ?></p>
                            <p class="text-gray-600">Комментарий:
                                <?php if ($order['comment']) {
                                    echo htmlspecialchars($order['comment']);
                                } else {
                                    echo htmlspecialchars("Без комментария");
                                } ?></p>

                            <form method="POST" action="" class="mt-4 flex gap-5">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                                <div class="select-wrapper">
                                    <select name="status" class="w-full md:w-auto px-4 py-2 border rounded-lg  bg-white text-gray-700">
                                        <option value="В ожидании" <?= $order['status'] === 'В ожидании' ? 'selected' : '' ?>>В ожидании</option>
                                        <option value="Завершен" <?= $order['status'] === 'Завершен' ? 'selected' : ''  ?>>Завершен</option>
                                        <option value="Отклонён" <?= $order['status'] === 'Отклонён' ? 'selected' : '' ?>>Отклонён</option>
                                        <option value="Готовится" <?= $order['status'] === 'Готовится' ? 'selected' : '' ?>>Готовится</option>
                                        <option value="В доставке" <?= $order['status'] === 'В доставке' ? 'selected' : '' ?>>В доставке</option>

                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn bg-primary text-white py-2 px-4 rounded-lg hover:bg-secondary transition">
                                    Обновить статус
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
    </section>

    <?php include './includes/footer.php'; ?>

</main>