<?php
session_start();
require_once('includes/db.php');

// Получаем все товары из базы данных
$query = "SELECT * FROM products";
$result = $mysqli->query($query);

if (!$result) {
    die("Ошибка запроса: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="dark.css" id="theme-link">
</head>
<body>
    <?php include 'navi/header.php';?>
    
    <h1>Каталог товаров</h1>

    <!-- Вывод сообщений об ошибке или успехе -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error">
            <?= htmlspecialchars($_SESSION['error_message']); ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success">
            <?= htmlspecialchars($_SESSION['success_message']); ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <p class="text">Выберите товары, которые хотите купить. После добавления в корзину вы сможете оформить заказ.</p>
    <div class="product-container">
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="product">
                <h2><?= htmlspecialchars($product['name']); ?></h2>
                <p class="text"><?= htmlspecialchars($product['description']); ?></p>
                <p class="text">Цена: <?= number_format($product['price'], 2); ?> ₽</p>
                
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>"> <!-- ID товара -->
                    <label class="text" for="quantity">Количество:</label>
                    <input type="number" placeholder="Максимум – 5 для одного заказа" name="quantity"  min="1" max="5" required>
                    <button type="submit">Добавить в корзину</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
    <a href="cart.php" class="cart-button">Перейти в корзину</a>
    <script src="script.js"></script>
    <?php include 'footer.php' ?>
</body>
</html>

<?php $mysqli->close(); ?>