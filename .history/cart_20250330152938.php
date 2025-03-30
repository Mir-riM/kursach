document.addEventListener('DOMContentLoaded', () => {
    let isProcessing = false; // Флаг для отслеживания состояния запроса

    // Функция для блокировки всех кнопок
    function disableButtons(disabled) {
        document.querySelectorAll('.btn-increase, .btn-decrease, .btn-remove').forEach(button => {
            button.disabled = disabled; // Блокируем/разблокируем кнопки
        });
    }

    // Увеличение количества товара
    document.querySelectorAll('.btn-increase').forEach(button => {
        button.addEventListener('click', async (e) => {
            if (isProcessing) return; // Если уже выполняется запрос, игнорируем клик

            const productId = e.target.dataset.productId;

            try {
                isProcessing = true; // Устанавливаем флаг обработки
                disableButtons(true); // Блокируем кнопки

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=increase&product_id=${productId}`,
                });

                if (response.status === 200) {
                    const result = await response.json();
                    const quantityDisplay = document.querySelector(`.quantity-display[data-product-id="${productId}"]`);
                    if (quantityDisplay) {
                        quantityDisplay.textContent = result.quantity || 1; // Обновляем количество
                    }
                }
            } catch (error) {
                console.error('Ошибка при увеличении количества:', error);
            } finally {
                isProcessing = false; // Снимаем флаг обработки
                disableButtons(false); // Разблокируем кнопки
            }
        });
    });

    // Уменьшение количества товара
    document.querySelectorAll('.btn-decrease').forEach(button => {
        button.addEventListener('click', async (e) => {
            if (isProcessing) return; // Если уже выполняется запрос, игнорируем клик

            const productId = e.target.dataset.productId;

            try {
                isProcessing = true; // Устанавливаем флаг обработки
                disableButtons(true); // Блокируем кнопки

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=decrease&product_id=${productId}`,
                });

                if (response.status === 200) {
                    const result = await response.json();
                    const quantityDisplay = document.querySelector(`.quantity-display[data-product-id="${productId}"]`);

                    if (result.quantity > 0) {
                        quantityDisplay.textContent = result.quantity; // Обновляем количество
                    } else {
                        // Если количество становится 0, удаляем элемент из DOM
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
                    }
                }
            } catch (error) {
                console.error('Ошибка при уменьшении количества:', error);
            } finally {
                isProcessing = false; // Снимаем флаг обработки
                disableButtons(false); // Разблокируем кнопки
            }
        });
    });

    // Удаление товара
    document.querySelectorAll('.btn-remove').forEach(button => {
        button.addEventListener('click', async (e) => {
            if (isProcessing) return; // Если уже выполняется запрос, игнорируем клик

            const productId = e.target.dataset.productId;

            try {
                isProcessing = true; // Устанавливаем флаг обработки
                disableButtons(true); // Блокируем кнопки

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
                }
            } catch (error) {
                console.error('Ошибка при удалении товара:', error);
            } finally {
                isProcessing = false; // Снимаем флаг обработки
                disableButtons(false); // Разблокируем кнопки
            }
        });
    });
});