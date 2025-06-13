document.addEventListener("DOMContentLoaded", () => {
  // Увеличение количества товара

  document.querySelectorAll(".btn-increase").forEach((button) => {
    button.addEventListener("click", async (e) => {
      const productId = e.target.dataset.productId;
      console.log("goida");
      try {
        const response = await fetch("/includes/cart-actions.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `action=increase&product_id=${productId}`,
        });

        if (response.status === 200) {
          // Обновляем отображение количества товара
          let quantityDisplay = Number(
            document.querySelector(
              `.quantity-display[data-product-id="${productId}"]`
            ).innerText
          );
          if (quantityDisplay) {
            document.querySelector(
              `.quantity-display[data-product-id="${productId}"]`
            ).innerText = `${quantityDisplay + 1}`;
          }
          const cart = await response.json();
          updateQuantityDisplay(productId, cart.quantity ?? 1);
        }
      } catch (error) {
        console.error("Ошибка при увеличении количества:", error);
      }
    });
  });

  // Уменьшение количества товара
  document.querySelectorAll(".btn-decrease").forEach((button) => {
    button.addEventListener("click", async (e) => {
      const productId = e.target.dataset.productId;

      try {
        const response = await fetch("/includes/cart-actions.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `action=decrease&product_id=${productId}`,
        });

        if (response.status === 200) {
          // Обновляем отображение количества товара
          let quantityDisplay = Number(
            document.querySelector(
              `.quantity-display[data-product-id="${productId}"]`
            ).innerText
          );
          if (quantityDisplay) {
            document.querySelector(
              `.quantity-display[data-product-id="${productId}"]`
            ).innerText = `${quantityDisplay - 1}`;
          } else {
            // Если количество становится 0, удаляем элемент из DOM
            const cartItem = document.getElementById(`cart-item-${productId}`);
            if (cartItem) {
              cartItem.remove();
            }
          }
          const cart = await response.json();
          updateQuantityDisplay(productId, cart.quantity ?? 0);
        }
      } catch (error) {
        console.error("Ошибка при уменьшении количества:", error);
      }
    });
  });

  // Удаление товара
  document.querySelectorAll(".btn-remove").forEach((button) => {
    button.addEventListener("click", async (e) => {
      const productId = e.target.dataset.productId;

      try {
        const response = await fetch("/includes/cart-actions.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
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
          const cartItems = document.querySelectorAll(".border.rounded-lg");
          if (cartItems.length === 0) {
            document.querySelector("section.container").innerHTML = `
                    <p class="text-center text-gray-600">Корзина пуста.</p>
                `;
          }
        }
      } catch (error) {
        console.error("Ошибка при удалении товара:", error);
      }
    });
  });

  function updateQuantityDisplay(productId, newQuantity) {
    const display = document.querySelector(
      `.quantity-display[data-product-id="${productId}"]`
    );
    if (display) {
      display.innerText = newQuantity;
    }
  }
});
