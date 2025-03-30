<?php
include 'includes/db.php';
include 'includes/header.php';

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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', async (e) => {
                const productId = e.target.dataset.productId;

                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}`,
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert(result.message); // Можно заменить на более красивое уведомление
                    } else {
                        alert(result.message);
                    }
                } catch (error) {
                    console.error('Ошибка при добавлении товара:', error);
                }
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>