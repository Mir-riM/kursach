<?php
$host = 'localhost';
$dbname = 'u3076466_coffeecozy';
$username = 'u3076466_root'; // Или ваш логин
$password = 'U122$2@07jtA'; // Или ваш пароль
// $host = 'localhost';
// $dbname = 'coffeeshop';
// $username = 'root'; // Или ваш логин
// $password = ''; // Или ваш пароль

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
