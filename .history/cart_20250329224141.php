<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $_SESSION['cart'][] = $product_id;
}

$cart_items = [];
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($_SESSION['cart']);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">Корзина</h1>
    <?php if (empty($cart_items)): ?>
        <p>Ваша корзина пуста.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($cart_items as $item): ?>
                <li><?= htmlspecialchars($item['name']) ?> - $<?= htmlspecialchars($item['price']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>