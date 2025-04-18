<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
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

    <?php include 'footer.php';?>
    <script src="script.js"></script>
</body>
</html>
