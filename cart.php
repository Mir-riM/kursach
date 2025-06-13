<?php
session_start();
include './includes/db.php';

// Инициализация корзины в сессии
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка создания заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $customer_name = trim($_POST['customer_name']);
    $phone = trim($_POST['phone']);
    $address = htmlspecialchars(trim($_POST['address']));
    $payment_type = $_POST['payment_type'];
    $comment = !empty($_POST['comment']) ? htmlspecialchars(trim($_POST['comment'])) : null;

    if ($payment_type === 'Онлайн') {
        $card_number = trim($_POST['card_number'] ?? '');
        $card_expiry = trim($_POST['card_expiry'] ?? '');
        $card_cvv = trim($_POST['card_cvv'] ?? '');

        if (empty($card_number) || empty($card_expiry) || empty($card_cvv)) {
            $error = "Пожалуйста, заполните все поля данных карты.";
        }
    }

    // Валидация имени
    if (!preg_match('/^[a-zA-Zа-яА-ЯёЁ ]+$/u', $customer_name)) {
        $error = "Имя может содержать только буквы и пробелы.";
    }
    // Валидация телефона
    elseif (!preg_match('/^\+?[78]\d{10}$/', $phone)) {
        $error = "Номер телефона должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX.";
    }
    // Проверка адреса
    elseif (empty($address)) {
        $error = "Адрес доставки обязателен для заполнения.";
    }
    // Проверка типа оплаты
    elseif ($payment_type !== 'Онлайн' && $payment_type !== 'Наличными') {
        $error = "Некорректный тип оплаты.";
    }
    // Проверка корзины
    elseif (empty($_SESSION['cart'])) {
        $error = "Корзина пуста. Добавьте товары перед оформлением заказа.";
    } else {
        // Вычисляем общую стоимость заказа
        $total_price = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        // Создаем заказ
        $stmt = $pdo->prepare("
            INSERT INTO orders 
            (user_id, customer_name, phone, address, payment_type, comment, total_price) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$_SESSION['user_id'] ?? null, $customer_name, $phone, $address, $payment_type, $comment, $total_price]);
        $order_id = $pdo->lastInsertId();

        // Добавляем товары в таблицу order_items
        foreach ($_SESSION['cart'] as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        }

        // Очищаем корзину
        $_SESSION['cart'] = [];


        $_SESSION['success'] = "Заказ успешно создан! Курьер свяжется с вами для подтверждения.";
        header("Location: cart.php");
        exit;
    }
}

$total_cart_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_cart_price += $item['price'] * $item['quantity'];
}
?>


<main class="flex flex-col min-h-[100vh]">
    <?php include './includes/header.php'; ?>

    <section class="container flex-[1] mx-auto p-4 mt-10">

        <h1 class="text-3xl font-bold text-center mb-6">Корзина</h1>


        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Успех!</strong>
                <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="space-y-4">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="border rounded-lg p-4 flex justify-between items-center" id="cart-item-<?= $item['id'] ?>">
                        <div>
                            <h2 class="text-xl font-bold"><?= htmlspecialchars($item['name']) ?></h2>
                            <p class="text-gray-600"><?= htmlspecialchars($item['price']) ?>₽</p>
                        </div>
                        <div class="flex items-center space-x-2 gap-5">
                            <button class="btn-increase size-8 flex leading-[0] justify-center items-center rounded-full bg-gray-200 hover:bg-gray-300 active:bg-gray-400 transition text-2xl" data-product-id="<?= $item['id'] ?>">+</button>
                            <p class="text-accent font-bold quantity-display" data-product-id="<?= $item['id'] ?>"><?= $item['quantity'] ?></p>
                            <button class="btn-decrease size-8 flex leading-[0] justify-center items-center rounded-full bg-gray-200 hover:bg-gray-300 active:bg-gray-400 transition text-2xl" data-product-id="<?= $item['id'] ?>">-</button>
                            <button class="btn-remove !ml-5 size-8 flex leading-[0] justify-center items-center rounded-full bg-gray-200 hover:bg-gray-300 active:bg-gray-400 transition text-l" data-product-id="<?= $item['id'] ?>">✕</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <!-- Общая сумма -->
            <div class="mt-6 text-right">
                <p class="text-xl font-bold">Итого: <span id="total-price"><?= number_format($total_cart_price, 2, ',', ' ') ?></span> ₽</p>
            </div>


        <?php else: ?>
            <p class="text-center text-gray-600">Корзина пуста.</p>
        <?php endif; ?>

        <?php

        if (isset($_SESSION['user_id'])) {
        ?>

            <!-- Форма для оформления заказа -->
            <form method="POST" action="" class="mt-10 <?php if (empty($_SESSION['cart'])) {
                                                            echo "hidden";
                                                        } ?> " id="order-form">
                <h2 class="text-2xl font-bold mb-4">Оформление заказа</h2>

                <?php if (isset($error)): ?>
                    <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <div class="mb-4">
                    <label for="customer_name" class="block text-gray-700 font-bold mb-2">Имя</label>
                    <input placeholder="Иван" type="text" name="customer_name" id="customer_name" class="w-full px-3 py-2 border rounded-lg" required pattern="^[a-zA-Zа-яА-ЯёЁ\s]+$" title="Имя может содержать только буквы.">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 font-bold mb-2">Телефон</label>
                    <input placeholder="+7 (XXX) XXX-XX-XX" type="text" name="phone" id="phone" class="w-full px-3 py-2 border rounded-lg" required pattern="^\+?[78]\d{10}$" title="Номер телефона должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX.">
                </div>

                <div class="mb-4">
                    <label for="payment_type" class="block text-gray-700 font-bold mb-2">Тип оплаты</label>
                    <div class="select-wrapper">
                        <select name="payment_type" id="payment_type" class="w-full px-3 py-2 border rounded-lg" required>
                            <option value="">Выберите способ оплаты</option>
                            <option value="Онлайн">Онлайн</option>
                            <option value="Наличными">Наличными</option>
                        </select>
                    </div>
                </div>

                <div id="payment-card-block" class="hidden mt-6 mb-4 p-4 border rounded-lg bg-gray-50">
                    <h3 class="text-lg font-bold mb-4">Введите данные карты</h3>

                    <div class="mb-4">
                        <label for="card-number" class="block text-gray-700 font-bold mb-2">Номер карты</label>
                        <input type="text" id="card-number" name="card_number" placeholder="1234 1234 1234 1234" class="w-full px-3 py-2 border rounded-lg" maxlength="19">
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="w-1/2">
                            <label for="card-expiry" class="block text-gray-700 font-bold mb-2">Срок действия</label>
                            <input type="text" id="card-expiry" name="card_expiry" placeholder="ММ/ГГ" class="w-full px-3 py-2 border rounded-lg" maxlength="5">
                        </div>
                        <div class="w-1/2">
                            <label for="card-cvv" class="block text-gray-700 font-bold mb-2">CVV</label>
                            <input type="text" id="card-cvv" name="card_cvv" placeholder="123" class="w-full px-3 py-2 border rounded-lg" maxlength="3">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="address" class="block text-gray-700 font-bold mb-2">Адрес доставки</label>
                    <textarea placeholder="Ул. Пушкина дом 14 квартира 88" name="address" id="address" rows="3" class="w-full px-3 py-2 border rounded-lg" required></textarea>
                </div>

                <div class="mb-4">
                    <label for="comment" class="block text-gray-700 font-bold mb-2">Комментарий к заказу (необязательно)</label>
                    <textarea name="comment" id="comment" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Например: положить побольше соуса или не звонить при доставке"></textarea>
                </div>

                <button type="submit" name="create_order" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-secondary transition">
                    Оформить заказ
                </button>
            </form>
        <?php
        } else {
        ?>

            <div class="w-full m-5">
                <div class="w-fit mx-auto p-5 bg-yellow-100">
                    <p class="text-[#333333] font-medium">Чтобы подать заявку на заказ нужно <a class="link !text-yellow-900" href="./login.php">войти</a> или <a class="link !text-yellow-900" href="./register.php">зарегистрироваться</a></p>
                </div>
            </div>

        <?php
        }

        ?>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('order-form');

                form.addEventListener('submit', (e) => {
                    const customerName = document.getElementById('customer_name').value.trim();
                    const phone = document.getElementById('phone').value.trim();
                    const address = document.getElementById('address').value.trim();

                    // Валидация имени
                    if (!/^[a-zA-Zа-яА-ЯёЁ\s]+$/.test(customerName)) {
                        alert('Имя может содержать только буквы.');
                        e.preventDefault();
                        return;
                    }

                    // Валидация телефона
                    if (!/^\+?[78]\d{10}$/.test(phone)) {
                        alert('Номер телефона должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX.');
                        e.preventDefault();
                        return;
                    }

                    // Валидация адреса
                    if (address.trim() === '') {
                        alert('Адрес доставки обязателен для заполнения.');
                        e.preventDefault();
                        return;
                    }
                });
            });
        </script>
    </section>

    <?php include './includes/footer.php'; ?>


    <script defer src="/js/cart-handler.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const totalPriceEl = document.getElementById('total-price');

            function updateTotalPrice() {
                let total = 0;

                document.querySelectorAll('.quantity-display').forEach(el => {
                    const quantity = parseInt(el.textContent);
                    const productId = el.getAttribute('data-product-id');
                    const priceEl = document.querySelector(`#cart-item-${productId} .text-gray-600`);
                    const price = parseFloat(priceEl.textContent);

                    if (!isNaN(quantity) && !isNaN(price)) {
                        total += price * quantity;
                    }
                });

                totalPriceEl.textContent = total.toLocaleString('ru-RU', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Увеличение количества
            document.querySelectorAll('.btn-increase').forEach(btn => {
                btn.addEventListener('click', () => {
                    const productId = btn.getAttribute('data-product-id');
                    const qtyDisplay = document.querySelector(`.quantity-display[data-product-id="${productId}"]`);
                    let qty = parseInt(qtyDisplay.textContent);
                    qty++;
                    qtyDisplay.textContent = qty;

                    fetch('/cart-update.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: qty
                        })
                    }).then(updateTotalPrice);
                });
            });

            // Уменьшение количества
            document.querySelectorAll('.btn-decrease').forEach(btn => {
                btn.addEventListener('click', () => {
                    const productId = btn.getAttribute('data-product-id');
                    const qtyDisplay = document.querySelector(`.quantity-display[data-product-id="${productId}"]`);
                    let qty = parseInt(qtyDisplay.textContent);
                    if (qty > 1) {
                        qty--;
                        qtyDisplay.textContent = qty;

                        fetch('/cart-update.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                product_id: productId,
                                quantity: qty
                            })
                        }).then(updateTotalPrice);
                    }
                });
            });

            // Удаление товара
            document.querySelectorAll('.btn-remove').forEach(btn => {
                btn.addEventListener('click', () => {
                    const productId = btn.getAttribute('data-product-id');
                    const itemEl = document.getElementById(`cart-item-${productId}`);

                    fetch('/cart-remove.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId
                        })
                    }).then(() => {
                        itemEl.remove();
                        updateTotalPrice();
                    });
                });
            });

            updateTotalPrice();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const paymentType = document.getElementById('payment_type');
            const cardBlock = document.getElementById('payment-card-block');
            const cardNumber = document.getElementById('card-number');
            const cardExpiry = document.getElementById('card-expiry');
            const cardCvv = document.getElementById('card-cvv');

            // Показывать/скрывать поля карты и ставить required
            paymentType.addEventListener('change', () => {
                if (paymentType.value === 'Онлайн') {
                    cardBlock.classList.remove('hidden');
                    cardNumber.setAttribute('required', 'required');
                    cardExpiry.setAttribute('required', 'required');
                    cardCvv.setAttribute('required', 'required');
                } else {
                    cardBlock.classList.add('hidden');
                    cardNumber.removeAttribute('required');
                    cardExpiry.removeAttribute('required');
                    cardCvv.removeAttribute('required');
                }
            });

            // Инициализация при загрузке
            if (paymentType.value === 'Онлайн') {
                cardBlock.classList.remove('hidden');
                cardNumber.setAttribute('required', 'required');
                cardExpiry.setAttribute('required', 'required');
                cardCvv.setAttribute('required', 'required');
            }
        });

        form.addEventListener('submit', function(e) {
            if (paymentType.value === 'Онлайн') {
                const cardNumber = cardNumber.value.replace(/\s+/g, '');
                const cardExpiry = cardExpiry.value;
                const cardCvv = cardCvv.value;

                if (!/^\d{16}$/.test(cardNumber)) {
                    alert('Неверный формат номера карты.');
                    e.preventDefault();
                }

                if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(cardExpiry)) {
                    alert('Неверный срок действия карты.');
                    e.preventDefault();
                }

                if (!/^\d{3}$/.test(cardCvv)) {
                    alert('Неверный CVV код.');
                    e.preventDefault();
                }
            }
        });
    </script>

</main>