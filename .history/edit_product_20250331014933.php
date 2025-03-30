<?php
session_start();
include '../includes/db.php';

// Проверка авторизации и роли
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$product_id = (int)($_GET['id'] ?? 0);

// Получение данных товара
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: admin.php");
    exit;
}

// Получение списка категорий
$stmtCategories = $pdo->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Обработка обновления товара
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    $category_id = (int)($_POST['category_id'] ?? 0);

    if (empty($name) || empty($price)) {
        $error = "Имя и цена обязательны для заполнения.";
    } else {
        // Генерация имени файла
        $image_name = (str_replace(' ', '-', $name))) . '.png';
        $image_path = __DIR__ . '/../assets/img/product/' . $image_name;

        // Сохранение изображения
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }

        // Обновление товара в базе данных
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name = ?, price = ?, description = ?, image_url = ?, category_id = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $price, $description, '/assets/img/product/' . $image_name, $category_id, $product_id]);

        $_SESSION['success'] = "Товар успешно обновлен!";
        header("Location: admin.php");
        exit;
    }
}
?>

<main class="flex flex-col min-h-[100vh]">

    <?php include './includes/header.php'; ?>

    <section class="container mx-auto p-4 mt-10 flex-[1]">
        <h1 class="text-3xl font-bold text-center mb-6">Редактирование товара</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Форма для редактирования товара -->
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Имя товара</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700 font-bold mb-2">Цена</label>
                <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($product['price']) ?>" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Описание</label>
                <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border rounded-lg"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700 font-bold mb-2">Категория</label>
                <select name="category_id" id="category_id" class="w-full px-3 py-2 border rounded-lg" required>
                    <option value="">Выберите категорию</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']) ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-gray-700 font-bold mb-2">Изображение</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-secondary transition">
                Обновить товар
            </button>
        </form>
    </section>

    <?php include './includes/footer.php'; ?>

</main>