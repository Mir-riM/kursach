<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кофейня</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwind.config.js"></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/png" href="../assets/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="../assets/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/apple-touch-icon.png" />
    <link rel="manifest" href="../assets/site.webmanifest" />
</head>

<body>
    <header class="bg-background-dark p-5 flex flex-col md:flex-row justify-between items-center gap-10">
        <div class="logo">
            <a href="/" class="text-2xl font-black text-white text-accent">Тепло и уют</a>
        </div>
        <nav class="flex flex-col md:flex-row justify-between items-center gap-5">
            <a class="link" href="./index.php">Меню</a>
            <a class="link" href="./cart.php">Корзина</a>
            <a class="link" href="./contacts.php">Контакты</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "admin") { ?>
                <a class="link" href="./admin.php">Админ панель</a>
            <?php } ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Пользователь авторизован -->
                <a class="link" href="./account.php">Личный кабинет</a>

                <form action="./includes/logout.php" method="POST" class="flex items-center m-0 justify-center">
                    <button type="submit" class="link">Выйти</button>
                </form>
            <?php else: ?>
                <!-- Пользователь не авторизован -->
                <a class="link" href="./login.php">Вход</a>
                <a class="link" href="./register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Всплывающее окно с уведомлением о куки -->
    <div id="cookie-banner" class="fixed bottom-0 left-0 w-full bg-primary text-white p-4 flex justify-between items-center">
        <div>
            Мы используем файлы cookie для улучшения вашего опыта на сайте. Продолжая пользоваться сайтом, вы соглашаетесь с нашей
            <a href="/privacy-policy.php" class="text-accent underline">политикой конфиденциальности</a>.
        </div>
        <button id="accept-cookies" class="ml-4 px-4 py-2 bg-secondary text-white rounded-lg hover:bg-accent transition">
            Принять
        </button>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cookieBanner = document.getElementById('cookie-banner');
            const acceptCookiesButton = document.getElementById('accept-cookies');

            // Проверяем, было ли уже принято соглашение
            if (!localStorage.getItem('cookiesAccepted')) {
                cookieBanner.style.display = 'flex'; // Показываем баннер
            } else {
                cookieBanner.style.display = 'none'; // Скрываем баннер, если соглашение уже принято
            }

            // Обработка нажатия на кнопку "Принять"
            acceptCookiesButton.addEventListener('click', () => {
                localStorage.setItem('cookiesAccepted', 'true'); // Сохраняем состояние в localStorage
                cookieBanner.style.display = 'none'; // Скрываем баннер
            });
        });
    </script>