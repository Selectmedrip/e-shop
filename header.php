<header>
        <h1>Добро пожаловать в магазин цифровых товаров</h1>
        <label class="switch">
            <input type="checkbox" id="theme-switch" />
            <span class="slider"></span>
        </label>
    </header>
    <nav>
        <ul>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <li><a href="register.php">Регистрация</a></li>
                <li><a href="login.php">Вход</a></li>
            <?php else: ?>
                <li><a href="catalog.php">Каталог товаров</a></li>
                <li><a href="my_orders.php">Мои заказы</a></li>
                <li><a href="cart.php">Перейти в корзину</a></li>
                <li><a href="logout.php">Выход</a></li>
            <?php endif; ?>
        </ul>
    </nav>
