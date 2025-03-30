<?php
session_start(); // Запускаем сессию
include 'includes/db.php';
include 'includes/header.php';

// Инициализация корзины в сессии
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка добавления товара в корзину через POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Добавляем товар в корзину
        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 0,
            ];
        }
        $_SESSION['cart'][$productId]['quantity'] += 1;

        echo json_encode(['success' => true, 'message' => 'Товар добавлен в корзину']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Товар не найден']);
        exit;
    }
}

// Получаем все категории
$stmtCategories = $pdo->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Получаем все товары
$stmtProducts = $pdo->query("SELECT * FROM products");
$products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);
?>


<section class="bg-[url('./assets/img/welcome.jpg')] w-full min-h-[300px] md:min-h-[600px] py-20 px-10 text-center">
    <div class="text-left max-w-[800px]">
        <h1 class="heading-lg text-white">Coffee & Cozy</h1>
        <p class="mt-5 subtitle text-white">
            Мы создали Coffee & Cozy для тех, кто ценит качество и уют. В нашем пространстве соединились искусство кофейного мастерства и теплая домашняя атмосфера. Приходите насладиться тщательно сваренным кофе из отборных зерен, попробовать наши фирменные десерты и зарядиться позитивной энергией в окружении приятной музыки и стильного интерьера.
        </p>
    </div>
</section>


<article class="container mx-auto p-4 mt-10">
    <?php foreach ($categories as $category): ?>
        <!-- Вывод названия категории -->
        <div class="mb-8">
            <h2 class="heading-md text-center font-bold text-accent mb-10"><?= htmlspecialchars($category['name']) ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Вывод товаров для текущей категории -->
                <?php foreach ($products as $product): ?>
                    <?php if ($product['category_id'] == $category['id']): ?>
                        <div class="border rounded-lg overflow-hidden shadow-md product-card" data-product-id="<?= $product['id'] ?>">
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover">
                            <div class="p-5">
                                <h2 class="text-xl font-bold"><?= htmlspecialchars($product['name']) ?></h2>
                                <p class="text-gray-600 h-[48px] line-clamp-2"><?= htmlspecialchars($product['description']) ?></p>
                                <p class="text-accent font-bold mt-2"><?= htmlspecialchars($product['price']) ?>₽</p>
                                <button class="btn mt-5 add-to-cart" data-product-id="<?= $product['id'] ?>">Добавить в корзину</button>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</article>

<div class="fixed mx-auto bottom-5 bg-gra">
    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none">
<path d="M6.29977 5H21L19 12H7.37671M20 16H8L6 3H3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
[динамичное отображение товаров в корзине]
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', async (e) => {
                const productId = e.target.dataset.productId;

            
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}`,
                    });

                    if (response.status == 200) {

                    } else {

                    }
              
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>