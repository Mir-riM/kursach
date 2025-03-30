<?php
session_start(); // Запускаем сессию
?>

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
            <p class="text-2xl font-black text-white text-accent">Coffee & Cozy</p>
        </div>
        <nav class="flex flex-col md:flex-row justify-between items-center gap-5">
            <a class="link" href="./index.php">Меню</a>
            <a class="link" href="./cart.php">Корзина</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Пользователь авторизован -->
            <a class="link" href="./account.php">Личный кабинет</a>

                <form action="./includes/logout.php" method="POST" class="flex items-ce justify-center">
                    <button type="submit" class="link">Выйти</button>
                </form>
            <?php else: ?>
                <!-- Пользователь не авторизован -->
                <a class="link" href="./login.php">Вход</a>
                <a class="link" href="./register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </header>
</body>

</html>