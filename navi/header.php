<header>
    <img src="icons/logo.png" alt="Логотип магазина" width="100" height="100">
        <h1>Добро пожаловать в магазин цифровых товаров</h1>
        <label class="switch">
            <input type="checkbox" id="theme-switch" />
            <span class="slider"></span>
        </label>
    </header>
    <nav>
        <ul>
            <?php 
            $currentPage = basename($_SERVER['PHP_SELF']); 

            if (!isset($_SESSION['user_id'])): ?>
                <?php if ($currentPage !== 'login.php' && $currentPage !== 'register.php'): ?>
                    <li><a href="register.php">Регистрация</a></li>
                    <li><a href="login.php">Вход</a></li>
                <?php else: ?>
                    <li><a href="index.php">Вернуться на главную</a></li>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($currentPage !== 'catalog.php'): ?>
                    <li><a href="catalog.php">Каталог товаров</a></li>
                <?php endif; ?>
                
                <?php if ($currentPage !== 'my_orders.php'): ?>
                    <li><a href="my_orders.php">Мои заказы</a></li>
                <?php endif; ?>

                <?php if ($currentPage !== 'cart.php'): ?>
                    <li><a href="cart.php">Перейти в корзину</a></li>
                <?php endif; ?>

                <li><a href="logout.php">Выход</a></li>
            <?php endif; ?>
        </ul>
    </nav>
