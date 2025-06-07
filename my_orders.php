<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$mysqli = new mysqli('localhost', 'root', '', 'digital_store');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$user_id = $_SESSION['user_id'];
$result = $mysqli->query("SELECT * FROM orders WHERE user_id = $user_id");

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заказы</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="dark.css" id="theme-link">
</head>
<body>
    <?php include 'navi/header.php';?>
    <h2>Мои заказы</h2>
    <form>
        <ul class="cart-items">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <li>
                        <p>Заказ №<?= $order['id']; ?><br> на сумму: <?= $order['total_price']; ?> ₽</p>
                        <a href="generate_qr.php?order_id=<?= $order['id']; ?>">QR-код для заказа</a>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text">Сделайте свой первый заказ для отображения</p>
            <?php endif; ?>
        </ul>
    </form>
    <!--<p><a href="catalog.php">Вернуться в каталог</a></p>
    <p><a href="cart.php">Перейти в корзину</a></p>-->
    <?php include 'footer.php';?>
    <script src="script.js"></script>
</body>
</html>
