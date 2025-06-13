<?php
// product.php

session_start();
require_once './includes/db.php'; // подключение к базе

$id = (int)($_GET['id'] ?? 0);
$product = null;

if ($id > 0) {
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
  $stmt->execute([$id]);
  $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$product) {
  http_response_code(404);
  echo "Товар не найден";
  exit;
}

// Получаем текущее количество из сессии
$product['quantity'] = $_SESSION['cart'][$id]['quantity'] ?? 0;
?>

<div class="flex flex-col min-h-[100vh]">

  <?php include './includes/header.php'; ?>


  <div class="container mx-auto px-4 py-8 max-w-4xl flex-1">
    <div class="grid md:grid-cols-2 gap-8">
      <div>
        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="rounded-2xl shadow-lg w-full object-cover">
      </div>
      <div>
        <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($product['name']) ?></h1>
        <p class="text-gray-600 mb-2"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <p class="text-gray-500 mb-6">Вес: <?= $product['weight'] ?> г.</p>
        <p class="text-2xl text-accent font-semibold mb-2"><?= number_format($product['price'], 2, '.', ' ') ?> ₽</p>

        <div class="flex items-center space-x-2 gap-5">
          <button class="btn-increase size-8 flex leading-[0] justify-center items-center rounded-full bg-gray-200 hover:bg-gray-400 active:bg-gray-600 transition text-2xl" data-product-id="<?= $product['id'] ?>">+</button>
          <p class="text-accent font-bold quantity-display" data-product-id="<?= $product['id'] ?>"><?= $product['quantity'] ?></p>
          <button class="btn-decrease size-8 flex leading-[0] justify-center items-center rounded-full bg-gray-200 hover:bg-gray-400 active:bg-gray-600 transition text-2xl" data-product-id="<?= $product['id'] ?>">-</button>
        </div>
      </div>
    </div>
  </div>

  <?php include './includes/footer.php'; ?>

</div>
<script src="/js/cart-handler.js"></script>