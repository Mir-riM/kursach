<?php
session_start();
include './includes/db.php';

$order_id = intval($_GET['order_id']);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order || $order['payment_type'] !== 'Онлайн') {
  header("Location: /cart.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <title>Оплата заказа #<?= $order_id ?></title>
  <script src="https://yookassa.ru/checkout-widget/v3/checkout-ui.js" type="text/javascript"></script>
</head>

<body class="bg-gray-100 p-4">

  <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Оплата заказа №<?= htmlspecialchars($order_id) ?></h1>
    <p>Сумма к оплате: <?= number_format($order['total_price'], 2, ',', ' ') ?> ₽</p>

    <!-- Пример интеграции YooKassa -->
    <button onclick="YooMoneyCheckoutWidget({
        shopId: 'YOUR_SHOP_ID',
        widgetId: 'YOUR_WIDGET_ID',
        orderSumAmount: <?= json_encode($order['total_price']) ?>,
        orderId: <?= json_encode($order_id) ?>,
        onSuccess: function() { alert('Оплата прошла успешно'); },
        onFail: function() { alert('Ошибка оплаты'); }
    })" class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
      Перейти к оплате
    </button>
  </div>

</body>

</html>