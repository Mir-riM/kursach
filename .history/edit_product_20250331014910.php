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
        $image_name = strtolower(transliterate(str_replace(' ', '-', $name))) . '.png';
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
