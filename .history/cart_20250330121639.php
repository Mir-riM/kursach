<div class="flex flex-col min-h-[100vh]">
<?php
session_start(); // Запускаем сессию
include 'includes/db.php';

// Инициализация корзины в сессии
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка AJAX-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);

    if ($action === 'increase' && $productId > 0) {
        // Увеличение количества товара
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += 1;
            echo json_encode(['success' => true, 'message' => 'Количество товара увеличено']);
            exit;
        }
    } elseif ($action === 'decrease' && $productId > 0) {
        // Уменьшение количества товара
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] -= 1;

            if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                unset($_SESSION['cart'][$productId]);
            }

            echo json_encode(['success' => true, 'message' => 'Количество товара уменьшено']);
            exit;
        }
    } elseif ($action === 'remove' && $productId > 0) {
        // Удаление товара из корзины
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            echo json_encode(['success' => true, 'message' => 'Товар удален из корзины']);
            exit;
        }
    }       
}
    ?>
    
   <div class="flex flex-col min-h-[100vh]">
    <?php
    session_start();
    include 'includes/header.php';
    ?>
    
    <section class="container mx-auto p-4 mt-10 flex-[1]">
        <h1 class="text-3xl font-bold text-center mb-6">Корзина</h1>
    
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="space-y-4">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="border rounded-lg p-4 flex justify-between items-center" id="cart-item-<?= $item['id'] ?>">
                        <div>
                            <h2 class="text-xl font-bold"><?= htmlspecialchars($item['name']) ?></h2>
                            <p class="text-gray-600"><?= htmlspecialchars($item['price']) ?>₽</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="" data-product-id="<?= $item['id'] ?>">-</button>
                            <span class="text-accent font-bold quantity-display" data-product-id="<?= $item['id'] ?>">Количество: <?= $item['quantity'] ?></span>
                            <button class="" data-product-id="<?= $item['id'] ?>">+</button>
                            <button class="" data-product-id="<?= $item['id'] ?>">Удалить</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-600">Корзина пуста.</p>
        <?php endif; ?>
    </section>
    
    <?php include 'includes/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Увеличение количества товара
    document.querySelectorAll('.btn-increase').forEach(button => {
        button.addEventListener('click', async (e) => {
            const productId = e.target.dataset.productId;

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=increase&product_id=${productId}`,
                });

                if (response.status === 200) {
                    // Обновляем отображение количества товара
                    const quantityDisplay = document.querySelector(`.quantity-display[data-product-id="${productId}"]`);
                    if (quantityDisplay) {
                        const currentQuantity = parseInt(quantityDisplay.textContent.split(': ')[1], 10);
                        quantityDisplay.textContent = `Количество: ${currentQuantity + 1}`;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Успешно!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            } catch (error) {
                console.error('Ошибка при увеличении количества:', error);
            }
        });
    });

    // Уменьшение количества товара
    document.querySelectorAll('.btn-decrease').forEach(button => {
        button.addEventListener('click', async (e) => {
            const productId = e.target.dataset.productId;

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=decrease&product_id=${productId}`,
                });

                if (response.status === 200) {
                    // Обновляем отображение количества товара
                    const quantityDisplay = document.querySelector(`.quantity-display[data-product-id="${productId}"]`);
                    if (quantityDisplay) {
                        const currentQuantity = parseInt(quantityDisplay.textContent.split(': ')[1], 10);
                        if (currentQuantity > 1) {
                            quantityDisplay.textContent = `Количество: ${currentQuantity - 1}`;
                        } else {
                            // Если количество становится 0, удаляем элемент из DOM
                            const cartItem = document.getElementById(`cart-item-${productId}`);
                            if (cartItem) {
                                cartItem.remove();
                            }
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Успешно!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            } catch (error) {
                console.error('Ошибка при уменьшении количества:', error);
            }
        });
    });

    // Удаление товара
    document.querySelectorAll('.btn-remove').forEach(button => {
        button.addEventListener('click', async (e) => {
            const productId = e.target.dataset.productId;

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove&product_id=${productId}`,
                });

                if (response.status === 200) {
                    // Удаляем элемент из DOM
                    const cartItem = document.getElementById(`cart-item-${productId}`);
                    if (cartItem) {
                        cartItem.remove();
                    }

                    // Проверяем, остались ли товары в корзине
                    const cartItems = document.querySelectorAll('.border.rounded-lg');
                    if (cartItems.length === 0) {
                        document.querySelector('section.container').innerHTML = `
                            <p class="text-center text-gray-600">Корзина пуста.</p>
                        `;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Успешно!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            } catch (error) {
                console.error('Ошибка при удалении товара:', error);
            }
        });
    });
});
</script>