<?php
session_start();

// Очищаем сессию
session_destroy();

// Перенаправляем на главную страницу
header("Location: ../index.php");
exit;
?>