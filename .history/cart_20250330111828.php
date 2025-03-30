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
                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold"><?= htmlspecialchars($item['name']) ?></h2>
                            <p class="text-gray-600"><?= htmlspecialchars($item['price']) ?>₽</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="btn-decrease bg-red-500 text-white px-2 py-1 rounded" data-product-id="<?= $item['id'] ?>">-</button>
                            <span class="text-accent font-bold">Количество: <?= $item['quantity'] ?></span>
                            <button class="btn-increase bg-green-500 text-white px-2 py-1 rounded" data-product-id="<?= $item['id'] ?>">+</button>
                            <button class="btn-remove bg-red-700 text-white px-2 py-1 rounded" data-product-id="<?= $item['id'] ?>">Удалить</button>
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
                    body: `action=add&product_id=${productId}`,
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Успешно!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => {
                        location.reload(); // Обновляем страницу
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

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Успешно!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => {
                        location.reload(); // Обновляем страницу
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

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Успешно!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => {
                        location.reload(); // Обновляем страницу
                    });
                }
            } catch (error) {
                console.error('Ошибка при удалении товара:', error);
            }
        });
    });
});
</script>