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
                        <div>
                            <button class="size-10 flex justify-center items-center leading-[0] bg-gray-200 hover:bg-gray-400 active:bg-gray-600 rounded-1">+</button>
                            <p class="text-accent font-bold"><?= $item['quantity'] ?></p>
                            <button class="size-10 flex justify-center items-center leading-[0] bg-gray-200 hover:bg-gray-400 active:bg-gray-600 rounded-1">-</button>
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