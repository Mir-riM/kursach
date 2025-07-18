<footer class="bg-background-dark text-white py-8">
    <div class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-5">
        <!-- Левая часть: Название сайта -->
        <div class="text-center md:text-left mb-4 md:mb-0 w-fit">
            <p class="text-lg font-bold">Тепло и уют</p>
            <p class="text-sm text-gray-400">Ваше уютное место для кофе и десертов</p>
        </div>


        <div class="flex flex-col md:flex-row gap-5 text-center">
            <a href="/privacy-policy.php" class="text-gray-300 hover:text-white transition duration-300 sm:text-nowrap">Политика конфиденциальности</a>
            <a href="../cart.php" class="text-gray-300 hover:text-white transition duration-300">Корзина</a>
            <a href="../index.php" class="text-gray-300 hover:text-white transition duration-300">Меню</a>
            <a href="../contacts.php" class="text-gray-300 hover:text-white transition duration-300">Контакты</a>
        </div>
    </div>


    <div class="mt-6 border-t border-gray-800 pt-4 text-center text-sm text-gray-400">
        &copy; <?= date('Y') ?> Тепло и уют. Все права защищены.
    </div>
</footer>
</body>

</html>