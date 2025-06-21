<?php

require "includes/db.php";

// Получаем общее количество пользователей
$stmt_users = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$total_users = $stmt_users->fetch(PDO::FETCH_ASSOC)['total_users'];

// Получаем общее количество заказов
$stmt_orders = $pdo->query("SELECT COUNT(*) AS total_orders FROM orders");
$total_orders = $stmt_orders->fetch(PDO::FETCH_ASSOC)['total_orders'];

// Получаем общую сумму заказов
$stmt_total_price = $pdo->query("SELECT SUM(total_price) AS total_revenue FROM orders");
$total_revenue = $stmt_total_price->fetch(PDO::FETCH_ASSOC)['total_revenue'];

// Получаем статистику по месяцам
$stmt_monthly = $pdo->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        COUNT(*) AS order_count,
        SUM(total_price) AS revenue
    FROM orders
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month
");

$monthly_stats = $stmt_monthly->fetchAll(PDO::FETCH_ASSOC);


?>

<div class="min-h-[100vh] flex flex-col">

  <?php
  include "includes/header.php";
  ?>
  <div class="flex-1 container mx-auto p-6">

    <h1 class="text-3xl text-center font-bold mb-4">Админ панель — Статистика</h1>

    <nav class="w-full mx-auto pb-10 flex justify-center items-center gap-5">
      <a href="./admin.php" class="btn">Товары</a>
      <a href="./admin_orders.php" class="btn">Заявки</a>
      <a href="./admin_statistic.php" class="btn">Статистика</a>
    </nav>

    <h2 class="text-xl text-center font-bold mb-5">Текущий месяц</h2>

    <!-- Общие метрики -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">Пользователи</h2>
        <p class="text-4xl"><?= htmlspecialchars($total_users) ?></p>
      </div>
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">Заказы</h2>
        <p class="text-4xl"><?= htmlspecialchars($total_orders) ?></p>
      </div>
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">Выручка</h2>
        <p class="text-4xl"><?= number_format($total_revenue, 2, ',', ' ') ?> ₽</p>
      </div>
    </div>

    <!-- Статистика по месяцам -->
    <h2 class="text-2xl font-semibold mb-4">Статистика по месяцам</h2>
    <div class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Месяц</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Кол-во заказов</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Выручка</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php foreach ($monthly_stats as $stat): ?>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($stat['month']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($stat['order_count']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap"><?= number_format($stat['revenue'], 2, ',', ' ') ?> ₽</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php
  include "includes/footer.php";
  ?>
</div>