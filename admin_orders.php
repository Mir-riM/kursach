<?php
session_start();
include './includes/db.php';

// Проверка авторизации и роли
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Получаем текущий статус фильтра из GET
$status_filter = $_GET['status'] ?? null;

// Получаем текущую страницу
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // количество заказов на странице
$offset = ($page - 1) * $limit;

// Подготавливаем запрос с возможным фильтром по статусу
$query = "FROM orders o LEFT JOIN users u ON o.user_id = u.id";
$params = [];

if ($status_filter) {
    $query .= " WHERE o.status = ?";
    $params[] = $status_filter;
}

// Считаем общее количество записей
$stmtCount = $pdo->prepare("SELECT COUNT(*) " . $query);
$stmtCount->execute($params);
$total_orders = $stmtCount->fetchColumn();
$total_pages = ceil($total_orders / $limit);

// Получаем заказы с пагинацией
$stmtOrders = $pdo->prepare("
    SELECT o.id AS order_id, o.status, o.total_price, o.created_at, u.username, o.payment_type, o.comment 
    $query 
    ORDER BY o.created_at DESC 
    LIMIT $limit OFFSET $offset
");
$stmtOrders->execute($params);
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// Обработка изменения статуса заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);

    $_SESSION['success'] = "Статус заказа обновлен!";
    header("Location: admin_orders.php" . ($status_filter ? "?status=" . urlencode($status_filter) : ""));
    exit;
}
?>

<main class="flex flex-col min-h-[100vh]">

    <?php include './includes/header.php'; ?>

    <section class="container mx-auto p-4 mt-10 flex-[1]">
        <h1 class="text-3xl font-bold text-center mb-6">Подтверждение заявок</h1>

        <nav class="w-full mx-auto p-5 flex justify-center items-center gap-5">
            <a href="./admin.php" class="btn">Товары</a>
            <a href="./admin_orders.php" class="btn">Заявки</a>
            <a href="./admin_statistic.php" class="btn">Статистика</a>
        </nav>

        <!-- Форма фильтрации -->
        <div class="mb-4 mt-6 flex justify-between items-center">
            <p class="text-xl">
                Сортировка
            </p>
            <form method="GET" action="" class="flex gap-2">
                <div class="select-wrapper">
                    <select name="status" class="px-4 py-2 border rounded-lg bg-white text-gray-700">
                        <option value="">Все статусы</option>
                        <option value="В ожидании" <?= ($status_filter === 'В ожидании') ? 'selected' : '' ?>>В ожидании</option>
                        <option value="Завершен" <?= ($status_filter === 'Завершен') ? 'selected' : '' ?>>Завершен</option>
                        <option value="Отклонён" <?= ($status_filter === 'Отклонён') ? 'selected' : '' ?>>Отклонён</option>
                        <option value="Готовится" <?= ($status_filter === 'Готовится') ? 'selected' : '' ?>>Готовится</option>
                        <option value="В доставке" <?= ($status_filter === 'В доставке') ? 'selected' : '' ?>>В доставке</option>
                    </select>
                </div>
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-secondary transition">
                    Применить
                </button>
                <a href="admin_orders.php" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">
                    Сбросить
                </a>
            </form>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="space-y-6">
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="border rounded-lg p-4">
                        <h2 class="text-xl font-bold">Заказ №<?= htmlspecialchars($order['order_id']) ?></h2>
                        <p class="text-gray-600">Пользователь: <?= htmlspecialchars($order['username'] ?? 'Гость') ?></p>
                        <p class="text-accent font-bold">Статус: <?= htmlspecialchars(ucfirst($order['status'])) ?></p>
                        <p class="text-accent font-bold">Общая стоимость: <?= htmlspecialchars($order['total_price']) ?>₽</p>
                        <p class="text-gray-600">Дата: <?= htmlspecialchars($order['created_at']) ?></p>
                        <p class="text-gray-600">Способ оплаты: <?= htmlspecialchars($order['payment_type']) ?></p>
                        <p class="text-gray-600">Комментарий:
                            <?= htmlspecialchars($order['comment'] ?: 'Без комментария') ?></p>

                        <form method="POST" action="" class="mt-4 flex gap-5">
                            <input type="hidden" name="order_id"
                                value="<?= htmlspecialchars($order['order_id']) ?>">
                            <div class="select-wrapper">
                                <select name="status"
                                    class="w-full md:w-auto px-4 py-2 border rounded-lg bg-white text-gray-700">
                                    <option value="В ожидании" <?= $order['status'] === 'В ожидании' ? 'selected' : '' ?>>
                                        В ожидании
                                    </option>
                                    <option value="Завершен" <?= $order['status'] === 'Завершен' ? 'selected' : '' ?>>
                                        Завершен
                                    </option>
                                    <option value="Отклонён" <?= $order['status'] === 'Отклонён' ? 'selected' : '' ?>>
                                        Отклонён
                                    </option>
                                    <option value="Готовится" <?= $order['status'] === 'Готовится' ? 'selected' : '' ?>>
                                        Готовится
                                    </option>
                                    <option value="В доставке" <?= $order['status'] === 'В доставке' ? 'selected' : '' ?>>
                                        В доставке
                                    </option>
                                </select>
                            </div>
                            <button type="submit" name="update_status"
                                class="btn bg-primary text-white py-2 px-4 rounded-lg hover:bg-secondary transition">
                                Обновить статус
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-500">Нет заказов для отображения.</p>
            <?php endif; ?>
        </div>

        <!-- Пагинация -->
        <?php if ($total_pages > 1): ?>
            <div class="flex justify-center mt-6 space-x-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                        class="<?= $i === $page ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700' ?> px-4 py-2 rounded hover:bg-gray-300">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php include './includes/footer.php'; ?>
</main>