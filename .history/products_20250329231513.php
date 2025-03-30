<?php
include 'includes/db.php';
include 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="bg-[url('./assets/img/welcome.jpg')] w-full min-h-[300px] md:min-h-[600px] p">
    <h1 class="heading-lg text-white">Coffee & Cozy</h1>
    <p class=""></p>
</section>

<article class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-center mb-6">Меню</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($products as $product): ?>
            <div class="border rounded-lg overflow-hidden shadow-md">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-bold"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="text-gray-600"><?= htmlspecialchars($product['description']) ?></p>
                    <p class="text-green-600 font-bold mt-2">₽<?= htmlspecialchars($product['price']) ?></p>
                    <button class="btn">Добавить в корзину</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</article>

<?php include 'includes/footer.php'; ?>