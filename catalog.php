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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Каталог товаров</h1>

    <!-- Кнопка для перехода в корзину -->
    <a href="cart.php" class="cart-button">Перейти в корзину</a>
    
    <?php while ($product = $result->fetch_assoc()): ?>
        <div class="product">
            <h2><?= htmlspecialchars($product['name']); ?></h2>
            <p><?= htmlspecialchars($product['description']); ?></p>
            <p>Цена: $<?= number_format($product['price'], 2); ?></p>
            
            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?= $product['id']; ?>"> <!-- ID товара -->
                <label for="quantity">Количество:</label>
                <input type="number" name="quantity" value="1" min="1" required>
                <button type="submit">Добавить в корзину</button>
            </form>
        </div>
    <?php endwhile; ?>
</body>
</html>

<?php $mysqli->close(); ?>
