<?php
session_start(); // Запускаем сессию
include './includes/db.php';
include './includes/header.php';

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
        // Если товар уже есть в корзине, увеличиваем количество
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += 1;
        } else {
            // Иначе добавляем товар в корзину
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
            ];
        }

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

// Функция для подсчета общего количества товаров в корзине
function getTotalCartQuantity()
{
    $totalQuantity = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $totalQuantity += $item['quantity'];
        }
    }
    return $totalQuantity;
}

// Подсчет количества товаров
$totalQuantity = getTotalCartQuantity();

?>


<section class="bg-[url('./assets/img/welcome.jpg')] w-full min-h-[300px] md:min-h-[600px] py-20 px-10 text-center">
    <div class="text-left max-w-[800px]">
        <h1 class="heading-lg text-white">Тепло и уют</h1>
        <p class="mt-5 subtitle text-white">
            Мы создали Тепло и уют для тех, кто ценит качество и уют. В нашем пространстве соединились искусство
            кофейного мастерства и теплая домашняя атмосфера. Приходите насладиться тщательно сваренным кофе из отборных
            зерен, попробовать наши фирменные десерты и зарядиться позитивной энергией в окружении приятной музыки и
            стильного интерьера.
        </p>
    </div>
</section>


<article class="container mx-auto p-4 mt-10">
    <?php foreach ($categories as $category): ?>
        <!-- Вывод названия категории -->
        <div class="mb-8">
            <h2 class="heading-md text-center font-bold text-accent mb-10">
                <?= htmlspecialchars($category['name']) ?>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                    <?php if ($product['category_id'] == $category['id']): ?>
                        <div class="border rounded-lg overflow-hidden shadow-md relative group">
                            <a href="product.php?id=<?= $product['id'] ?>" class="absolute inset-0 z-10"></a>
                            <img src="<?= htmlspecialchars($product['image_url']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover">
                            <div class="p-5 relative z-20">
                                <h2 class="text-xl font-bold line-clamp-2 h-[60px]"><?= htmlspecialchars($product['name']) ?></h2>
                                <p class="text-gray-600 h-[48px] line-clamp-2"><?= htmlspecialchars($product['description']) ?></p>
                                <div class="w-full flex justify-between mt-2">
                                    <p class="text-accent font-bold "><?= htmlspecialchars($product['price']) ?>₽</p>
                                    <p class="text-secondary font-medium"><?= htmlspecialchars($product['weight']) ?>г.</p>
                                </div>
                                <button class="btn mt-5 add-to-cart relative z-30" data-product-id="<?= $product['id'] ?>">
                                    Добавить в корзину
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

        </div>
    <?php endforeach; ?>
</article>


<a href="./cart.php" class="count-cart hidden w-full fixed bottom-5 z-50">
    <div
        class="flex flex-nowrap text-center items-center justify-center gap-4 bg-primary transition hover:bg-secondary px-5 py-2 rounded-full w-fit mx-auto shadow-lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="32px" height="32px" viewBox="0 0 24 24" fill="none">
            <path
                d="M6.29977 5H21L19 12H7.37671M20 16H8L6 3H3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z"
                stroke="#b89c7d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>

        <p class="text-2xl text-accent cart-total-quantity"><?php echo $totalQuantity ?></p>

    </div>
</a>




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
                if (response.status !== 200) {
                    alert("Ошибка при добавлении товара, попробуйте снова")
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Функция для обновления количества товаров в корзине
        async function updateCartQuantity() {
            try {
                const response = await fetch('./includes/update-cart-quantity.php');
                const data = await response.json();

                if (data.success) {
                    const cartTotalQuantityElement = document.querySelector('.cart-total-quantity');
                    if (cartTotalQuantityElement) {
                        cartTotalQuantityElement.textContent = data.totalQuantity;

                        // Показываем или скрываем блок корзины
                        const cartBlock = document.querySelector('.count-cart');
                        if (data.totalQuantity > 0) {
                            cartBlock.style.display = 'block';
                        } else {
                            cartBlock.style.display = 'none';
                        }
                    }
                }
            } catch (error) {
                console.error('Ошибка при обновлении количества товаров:', error);
            }
        }

        // Обновляем количество товаров при загрузке страницы
        updateCartQuantity();

        // Добавляем обработчики событий для кнопок корзины
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', () => {
                setTimeout(() => {
                    updateCartQuantity();
                }, 100); // Задержка для обработки изменений на сервере
            });
        });
    });
</script>



<?php include './includes/footer.php'; ?>