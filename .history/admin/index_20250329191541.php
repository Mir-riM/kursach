<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $image_url]);
}

$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">Админка</h1>
    <form method="POST" class="mb-8">
        <input type="text" name="name" placeholder="Название" class="block w-full p-2 border rounded mb-2">
        <textarea name="description" placeholder="Описание" class="block w-full p-2 border rounded mb-2"></textarea>
        <input type="number" step="0.01" name="price" placeholder="Цена" class="block w-full p-2 border rounded mb-2">
        <input type="text" name="image_url" placeholder="URL изображения" class="block w-full p-2 border rounded mb-2">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Добавить товар</button>
    </form>

    <h2 class="text-2xl font-bold mb-4">Товары</h2>
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Название</th>
                <th class="border p-2">Цена</th>
                <th class="border p-2">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td class="border p-2"><?= $product['id'] ?></td>
                    <td class="border p-2"><?= htmlspecialchars($product['name']) ?></td>
                    <td class="border p-2">$<?= htmlspecialchars($product['price']) ?></td>
                    <td class="border p-2">
                        <a href="edit_product.php?id=<?= $product['id'] ?>" class="text-blue-500">Редактировать</a>
                        <a href="delete_product.php?id=<?= $product['id'] ?>" class="text-red-500">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>