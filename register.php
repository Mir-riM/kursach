<?php
session_start();
include './includes/db.php';

// Если пользователь уже авторизован, перенаправляем на главную страницу
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $agree = isset($_POST['agree']); // Проверка согласия на обработку персональных данных

    // Проверка на пустые поля
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Все поля обязательны для заполнения.";
    } elseif ($password !== $confirm_password) {
        $error = "Пароли не совпадают.";
    } elseif (!$agree) {
        $error = "Вы должны принять условия обработки персональных данных.";
    } else {
        // Проверяем, существует ли пользователь с таким логином или email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            if ($existing_user['username'] === $username) {
                $error = "Пользователь с таким логином уже существует.";
            } else {
                $error = "Пользователь с таким email уже зарегистрирован.";
            }
        } else {
            // Хэшируем пароль и добавляем пользователя в базу данных
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $password_hash])) {
                $_SESSION['success'] = "Регистрация прошла успешно! Теперь вы можете войти.";
                header("Location: login.php");
                exit;
            } else {
                $error = "Ошибка при регистрации. Попробуйте снова.";
            }
        }
    }
}
?>

<div class="flex flex-col min-h-[100vh]">
    <?php include './includes/header.php'; ?>

    <main class="flex-[1] flex justify-center items-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h1 class="text-2xl font-bold text-center mb-6">Регистрация</h1>

            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mb-4"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 font-bold mb-2">Логин</label>
                    <input type="text" name="username" id="username" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                    <input type="email" name="email" id="email" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-bold mb-2">Пароль</label>
                    <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Подтвердите пароль</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="w-full px-3 py-2 border rounded-lg" required>
                </div>

                <!-- Чекбокс согласия -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="agree" class="mr-2" <?= isset($_POST['agree']) ? 'checked' : '' ?>>
                        <span class="text-sm text-gray-600">
                            Я принимаю условия <a href="#" class="text-accent hover:underline">обработки персональных данных</a>
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-secondary transition">Зарегистрироваться</button>
            </form>

            <p class="text-center mt-4">
                Уже есть аккаунт? <a href="login.php" class="text-accent hover:underline">Войдите здесь</a>.
            </p>
        </div>
    </main>

    <?php include './includes/footer.php'; ?>
</div>