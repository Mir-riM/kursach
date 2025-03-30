<?php
session_start();
include 'includes/db.php';

// Проверка авторизации и роли
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Получение списка категорий
$stmtCategories = $pdo->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Обработка создания товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_product'])) {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    $category_id = (int)($_POST['category_id'] ?? 0);

    if (empty($name) || empty($price)) {
        $error = "Имя и цена обязательны для заполнения.";
    } else {
        // Генерация имени файла
        $image_name = str_replace(' ', '-', $name) . '.png';
        $image_path = __DIR__ . './assets/img/product/' . $image_name;

        // Создание директории, если её нет
        if (!is_dir(__DIR__ . './assets/img/product/')) {
            mkdir(__DIR__ . './assets/img/product/', 0777, true);
        }

        // Сохранение изображения
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }

        // Добавление товара в базу данных
        $stmt = $pdo->prepare("
            INSERT INTO products (name, price, description, image_url, category_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $price, $description, '/assets/img/product/' . $image_name, $category_id]);

        $_SESSION['success'] = "Товар успешно создан!";
        header("Location: admin.php");
        exit;
    }
}

// Получение списка товаров
$stmtProducts = $pdo->query("SELECT * FROM products");
$products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="flex flex-col min-h-[100vh]">

    <?php include 'includes/header.php'; ?>

    <section class="container mx-auto p-4 mt-10 flex-1">
        <h1 class="text-3xl font-bold text-center mb-6">Админка</h1>

        <nav class="w-full mx-auto p-5 flex justify-center items-center gap-5">
            <a href="./admin.php" class="dark-link">Товары</a>
            <a href="./admin_orders.php" class="dark-link">Заявки</a>
        </nav>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data" class="mb-10">
            <h2 class="text-2xl font-bold mb-4">Создать товар</h2>
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Имя товара</label>
                <input type="text" name="name" id="name" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700 font-bold mb-2">Цена</label>
                <input type="number" step="0.01" name="price" id="price" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Описание</label>
                <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700 font-bold mb-2">Категория</label>
                <select name="category_id" id="category_id" class="w-full px-3 py-2 border rounded-lg" required>
                    <option value="">Выберите категорию</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-gray-700 font-bold mb-2">Изображение</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <button type="submit" name="create_product" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-secondary transition">
                Создать товар
            </button>
        </form>

        <!-- Список товаров -->
        <h2 class="text-2xl font-bold mb-4">Товары</h2>
        <div class="space-y-4">
            <?php foreach ($products as $product): ?>
                <div class="border rounded-lg p-4 flex justify-between items-center" id="product-<?= $product['id'] ?>">
                    <div>
                        <h2 class="text-xl font-bold"><?= htmlspecialchars($product['name']) ?></h2>
                        <p class="text-gray-600"><?= htmlspecialchars($product['price']) ?>₽</p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($product['description']) ?></p>
                    </div>
                    <div class="flex gap-4">
                        <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn h-fit">
                            Редактировать
                        </a>
                        <form action="includes/delete_product.php" method="POST" class="m-0">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition">
                                Удалить
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</main class="flex flex-col ">