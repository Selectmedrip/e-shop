<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link type="image/png" sizes="16x16" rel="icon" href="icons/web/16.png">
    <link type="image/png" sizes="32x32" rel="icon" href="icons/web/32.png">
    <link type="image/png" sizes="96x96" rel="icon" href="icons/web/96.png">
    <link type="image/png" sizes="72x72" rel="icon" href="icons/web/72.png">
    <link type="image/png" sizes="96x96" rel="icon" href="icons/web/96.png">
    <link rel="apple-touch-icon" type="image/png" sizes="57x57" href="icons/web/57.png">
    <link rel="apple-touch-icon" type="image/png" sizes="72x72" href="icons/web/72.png">
    <meta name="msapplication-square70x70logo" content="icons/web/70.png">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="dark.css" id="theme-link">
</head>
<body>
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
                <li><a href="logout.php">Выход</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <h2>Что вас интересует?</h2>
        <p>Вы можете зарегистрироваться или войти, чтобы начать покупки. Уже зарегистрированы? Перейдите в каталог и оформите заказ!</p>
    </main>
        <button id="scrollToTop">&#129081;</button>
    <?php include 'footer.php';?>
    <script src="script.js"></script>
</body>
</html>
