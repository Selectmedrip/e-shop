<?php
session_start();
require_once('includes/db.php');

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем все товары из корзины пользователя
$query = "SELECT cart.id, products.name, products.price, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.id 
          WHERE cart.user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;

// Обработка изменения количества товара в корзине
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $cart_item_id = $_POST['cart_item_id'];
    $new_quantity = $_POST['quantity'];

    // Обновление количества товара в корзине
    if ($new_quantity > 0) {
        $update_query = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param("iii", $new_quantity, $cart_item_id, $user_id);
        $update_stmt->execute();
    } else {
        // Если количество равно 0, удаляем товар из корзины
        $delete_query = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $delete_stmt = $mysqli->prepare($delete_query);
        $delete_stmt->bind_param("ii", $cart_item_id, $user_id);
        $delete_stmt->execute();
    }

    // Перенаправление на ту же страницу, чтобы обновить корзину
    header('Location: cart.php');
    exit;
}

// Обработка удаления товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $cart_item_id = $_POST['cart_item_id'];

    // Удаляем товар из корзины
    $delete_query = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $delete_stmt = $mysqli->prepare($delete_query);
    $delete_stmt->bind_param("ii", $cart_item_id, $user_id);
    $delete_stmt->execute();

    // Перенаправление на ту же страницу, чтобы обновить корзину
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Ваша корзина</h1>

    <?php if ($result->num_rows > 0): ?>
        <form action="cart.php" method="POST">
            <ul>
                <?php while ($item = $result->fetch_assoc()): ?>
                    <li>
                        <?= htmlspecialchars($item['name']); ?> - $<?= number_format($item['price'], 2); ?>

                        <!-- Количество товара с возможностью изменения -->
                        <label for="quantity">Количество:</label>
                        <input type="number" name="quantity" value="<?= $item['quantity']; ?>" min="1" required>

                        <input type="hidden" name="cart_item_id" value="<?= $item['id']; ?>"> <!-- ID товара в корзине -->

                        <button type="submit" name="update_quantity">Изменить количество</button>

                        <!-- Кнопка для удаления товара из корзины -->
                        <button type="submit" name="remove_item" onclick="return confirm('Вы уверены, что хотите удалить этот товар?');">Удалить</button>
                    </li>
                    <?php $total_price += $item['price'] * $item['quantity']; ?>
                <?php endwhile; ?>
            </ul>

            <h3>Общая стоимость: $<?= number_format($total_price, 2); ?></h3>

            <button type="submit" formaction="checkout.php">Оформить заказ</button>
        </form>
    <?php else: ?>
        <p>Ваша корзина пуста.</p>
    <?php endif; ?>

    <a href="catalog.php">Перейти в каталог</a>
</body>
</html>

<?php
$stmt->close();
$mysqli->close();
?>
