<?php

session_start();
?>

<div class="min-h-[100vh] flex flex-col">
  <?php include './includes/header.php'; ?>

  <main class="flex-1">
    <section class="container mx-auto p-4 mt-10 mb-4">
      <h1 class="text-3xl font-bold text-center mb-8">Контакты</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">

        <!-- Информация -->
        <div class="space-y-6">
          <div>
            <img src="https://avatars.mds.yandex.net/get-altay/14105192/2a000001937c9a2a42b2292348b191ae5d27/XXXL" alt="Фото входа в кафе" class="w-full rounded-lg shadow-md max-h-[400px]">
          </div>
          <div>
            <h2 class="text-xl font-semibold mb-2">Кафе «Тепло и уют»</h2>
            <p class="text-gray-700">Мы всегда рады видеть вас у нас! Наши двери открыты ежедневно с 9:00 до 22:00.</p>
          </div>

          <div class="space-y-4">
            <p><strong>Адрес:</strong> г. Челябинск, ул. Пушкина, д. 14, кв. 88</p>
            <p><strong>Телефон:</strong> <a href="tel:+79000000000" class="text-blue-600 hover:underline">+7 (900) 000-00-00</a></p>
            <p><strong>Email:</strong> <a href="mailto:info@teploiyut.ru" class="text-blue-600 hover:underline">info@teploiyut.ru</a></p>
          </div>
        </div>

        <div id="map" class="w-full h-[400px] rounded-lg shadow-md">
          <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Ad0f368bb77c86a2406a2eb124975f13bae8b0232d171e74794b61b256a05ca4b&amp;source=constructor" width="100%" height="400" frameborder="0"></iframe>
        </div>

      </div>
    </section>
  </main>

  <?php include './includes/footer.php'; ?>
</div>