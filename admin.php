<?php
session_start();
include './includes/db.php';

// Проверка авторизации и роли
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$error = null;

// Обработка создания категории
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_category'])) {
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$category_name]);
            $_SESSION['success'] = "Категория успешно создана!";
            header("Location: admin.php?tab=categories");
            exit;
        } catch (PDOException $e) {
            $error = "Ошибка при создании категории.";
        }
    } else {
        $error = "Название категории не может быть пустым.";
    }
}

// Получение списка категорий
$stmtCategories = $pdo->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Обработка создания товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_product'])) {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $weight = (float)$_POST['weight'];
    $description = trim($_POST['description']);
    $category_id = (int)($_POST['category_id'] ?? 0);

    if (empty($name) || empty($price)) {
        $error = "Имя и цена обязательны для заполнения.";
    } else {
        // Генерация имени файла
        $image_name = str_replace(' ', '-', $name) . '.png';
        $image_path = __DIR__ . '/assets/img/product/' . $image_name;

        // Создание директории
        if (!is_dir(__DIR__ . '/assets/img/product/')) {
            mkdir(__DIR__ . '/assets/img/product/', 0777, true);
        }

        // Загрузка изображения
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }

        // Добавление товара
        $stmt = $pdo->prepare("
            INSERT INTO products (name, price, description, image_url, category_id, weight)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $price, $description, '/assets/img/product/' . $image_name, $category_id, $weight]);

        $_SESSION['success'] = "Товар успешно создан!";
        header("Location: admin.php?tab=products");
        exit;
    }
}

// Получение списка товаров
$stmtProducts = $pdo->query("SELECT * FROM products");
$products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);
?>


<main class="flex flex-col min-h-screen">

    <?php include './includes/header.php'; ?>

    <section class="container mx-auto p-4 mt-10 flex-1">
        <h1 class="text-3xl font-bold text-center mb-6">Админка</h1>

        <nav class="w-full mx-auto p-5 flex justify-center items-center gap-5">
            <a href="./admin.php" class="btn">Товары</a>
            <a href="./admin_orders.php" class="btn">Заявки</a>
            <a href="./admin_statistics.php" class="btn">Статистика</a>
        </nav>

        <!-- Tabs -->
        <div class="mb-6 w-full rounded-xl bg-slate-100">
            <ul class="flex -mb-px text-sm font-medium text-center mx-4" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-400 active" id="products-tab" data-tabs-target="#products" type="button" role="tab" aria-controls="products" aria-selected="true">Товары</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-400" id="categories-tab" data-tabs-target="#categories" type="button" role="tab" aria-controls="categories" aria-selected="false">Категории</button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div id="default-tab-content">
            <!-- Таб "Товары" -->
            <div class="hidden p-4 rounded-lg bg-white shadow" id="products" role="tabpanel" aria-labelledby="products-tab">
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

                <!-- Форма создания товара -->
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
                        <label for="weight" class="block text-gray-700 font-bold mb-2">Вес</label>
                        <input type="number" step="1" name="weight" id="weight" class="w-full px-3 py-2 border rounded-lg" required>
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
                        <div class="border rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <h2 class="text-xl font-bold"><?= htmlspecialchars($product['name']) ?></h2>
                                <p><?= htmlspecialchars($product['price']) ?>₽</p>
                            </div>
                            <form action="includes/delete_product.php" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить этот товар?')">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Таб "Категории" -->
            <div class="hidden p-4 rounded-lg bg-white shadow" id="categories" role="tabpanel" aria-labelledby="categories-tab">
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

                <!-- Форма создания категории -->
                <form method="POST" action="" class="mb-10">
                    <h2 class="text-2xl font-bold mb-4">Создать категорию</h2>
                    <div class="mb-4">
                        <label for="category_name" class="block text-gray-700 font-bold mb-2">Название категории</label>
                        <input type="text" name="category_name" id="category_name" class="w-full px-3 py-2 border rounded-lg" required>
                    </div>
                    <button type="submit" name="create_category" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-secondary transition">
                        Создать категорию
                    </button>
                </form>

                <!-- Список категорий -->
                <h2 class="text-2xl font-bold mb-4">Категории</h2>
                <div class="space-y-4">
                    <?php foreach ($categories as $category): ?>
                        <div class="border rounded-lg p-4 flex justify-between items-center">
                            <h2 class="text-xl font-bold"><?= htmlspecialchars($category['name']) ?></h2>
                            <form action="includes/delete_category.php" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту категорию? Все товары этой категории будут удалены.')">
                                <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                <button type="submit" class="btn bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include './includes/footer.php'; ?>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash;
        const tabs = document.querySelectorAll('[data-tabs-target]');
        const tabContents = document.querySelectorAll('.hidden');

        function activateTab(tab) {
            tabs.forEach(t => t.classList.remove('active', 'border-accent'));
            tab.classList.add('active', 'border-accent');
            tabContents.forEach(content => content.classList.add('hidden'));
            document.querySelector(tab.dataset.tabsTarget).classList.remove('hidden');
        }

        if (hash) {
            const targetTab = document.querySelector(`[data-tabs-target='${hash}']`);
            if (targetTab) activateTab(targetTab);
        } else {
            document.getElementById('products').classList.remove('hidden');
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                activateTab(tab);
                history.pushState(null, '', tab.dataset.tabsTarget);
            });
        });
    });
</script>