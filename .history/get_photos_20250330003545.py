import os
import requests

# Настройки
UNSPLASH_ACCESS_KEY = "MQb-HMQQX28LUvENxmu98AEJElTlXjx5r0R9c93xtkU"
OUTPUT_DIR = "./assets/img/product"

# Создаем директорию, если её нет
os.makedirs(OUTPUT_DIR, exist_ok=True)

# Список товаров
products = [
    "Латте", "Капучино", "Эспрессо", "Мокко", "Американо",
    "Черный чай", "Зеленый чай", "Фруктовый чай", "Чай Матча", "Ройбуш",
    "Чизкейк", "Шоколадный торт", "Макарун", "Эклер", "Брауни",
    "Сэндвич с авокадо", "Круассан", "Багет с сыром", "Тосты с лососем", "Салат Цезарь",
    "Лимонад", "Смузи", "Горячий шоколад", "Молочный коктейль", "Апельсиновый сок", "Ягодный морс"
]

# Функция для скачивания изображений
def download_image(query, output_path):
    url = f"https://api.unsplash.com/search/photos"
    params = {
        "query": query,
        "client_id": UNSPLASH_ACCESS_KEY
    }
    response = requests.get(url, params=params).json()
    
    if "results" in response and len(response["results"]) > 0:
        # Берем первую найденную картинку
        image_url = response["results"][0]["urls"]["raw"]
        image_data = requests.get(image_url).content
        
        # Сохраняем изображение
        with open(output_path, "wb") as f:
            f.write(image_data)
        
        print(f"Скачано: {output_path}")
    else:
        print(f"Изображение для '{query}' не найдено.")

# Скачиваем изображения для каждого товара
for product in products:
    filename = f"{product.replace(' ', '-')}.jpg"  # Сохраняем в формате JPG
    output_path = os.path.join(OUTPUT_DIR, filename)
    download_image(product, output_path)