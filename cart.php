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

// Обработка увеличения количества товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['increase_quantity'])) {
    $cart_item_id = $_POST['cart_item_id'];

    // Увеличиваем количество, но не больше 5
    $update_query = "UPDATE cart SET quantity = LEAST(quantity + 1, 5) WHERE id = ? AND user_id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("ii", $cart_item_id, $user_id);
    $update_stmt->execute();

    header('Location: cart.php');
    exit;
}

// Обработка уменьшения количества товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decrease_quantity'])) {
    $cart_item_id = $_POST['cart_item_id'];

    // Уменьшаем количество, но не меньше 1
    $update_query = "UPDATE cart SET quantity = GREATEST(quantity - 1, 1) WHERE id = ? AND user_id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("ii", $cart_item_id, $user_id);
    $update_stmt->execute();

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
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="dark.css" id="theme-link">
</head>
<body>
    <?php include 'navi/header.php'; ?>
    <h1>Ваша корзина</h1>

    <?php if ($result->num_rows > 0): ?>
        <ul class="cart-items">
            <?php while ($item = $result->fetch_assoc()): ?>
                <li class="product">
                    <p style=" margin-bottom:10px;"><?= htmlspecialchars($item['name']); ?> - <?= number_format($item['price'], 2); ?> ₽</p>
                    
                    <!-- Форма для изменения количества -->
                    <form action="cart.php" method="POST" style="display: flex; align-items: center; gap: 10px;">
                        <input type="hidden" name="cart_item_id" value="<?= $item['id']; ?>">
                        
                        <!-- Кнопка уменьшения количества -->
                        <button type="submit" name="decrease_quantity" class="quantity-button">
                            <img src="icons/minus.svg" alt="-" style="width: 16px; height: 16px;">
                        </button>

                        <!-- Поле для отображения текущего количества -->
                        <input type="number" name="quantity" value="<?= $item['quantity']; ?>" readonly style="width: 40px; text-align: center;">

                        <!-- Кнопка увеличения количества -->
                        <button type="submit" name="increase_quantity" class="quantity-button">
                            <img src="icons/plus.svg" alt="+" style="width: 16px; height: 16px;">
                        </button>
                    </form>

                    <!-- Форма для удаления товара -->
                    <form style="max-width:400px; margin-top:10px;" action="cart.php" method="POST" style="display: inline;">
                        <input type="hidden" name="cart_item_id" value="<?= $item['id']; ?>">
                        <button type="submit" name="remove_item" class="delete-button" onclick="return confirm('Вы уверены, что хотите удалить этот товар?');">
                            <img src="icons/trash.svg" alt="Удалить" style="width: 16px; height: 16px;">
                        </button>
                    </form>
                </li>
                <?php $total_price += $item['price'] * $item['quantity']; ?>
            <?php endwhile; ?>
        </ul>

        <!--<h3 class="hsum">Общая стоимость: <?= number_format($total_price, 2); ?> ₽</h3>-->
            <br><link rel="stylesheet" href="https://yookassa.ru/integration/simplepay/css/yookassa_construct_form.css?v=1.24.0">
                <form class="yoomoney-payment-form" action="https://yookassa.ru/integration/simplepay/payment" method="post" accept-charset="utf-8" >
                    <div class="ym-hidden-inputs">
                    <input name="shopSuccessURL" type="hidden" value="e-shop/checkout.php">
                    <input name="shopFailURL" type="hidden" value="e-shop/cart.php">
                </div>
                <h3 class="hsum">Общая стоимость: <?= number_format($total_price, 2); ?> ₽</h3>
                    <div class="ym-payment-btn-block">
                        <div class="ym-input-icon-rub ym-display-none">
                            <input name="sum" placeholder="0.00" class="ym-input ym-sum-input ym-required-input" type="number" step="any" value="<?= number_format($total_price, 2 , '.', ''); ?>">
                        </div>
                        <button data-text="Оплатить" class="ym-btn-pay ym-result-price"><span class="ym-text-crop">Оплатить</span></button><img src="https://yookassa.ru/integration/simplepay/img/iokassa-gray.svg?v=1.24.0" class="ym-logo" width="114" height="27" alt="ЮKassa">
                    </div>
                <input name="shopId" type="hidden" value="1103663"></>
                <script src="https://yookassa.ru/integration/simplepay/js/yookassa_construct_form.js?v=1.24.0"></script>
        <!--<form action="checkout.php" method="POST">
            <button type="submit">Оформить заказ</button>-->
                </form>
    <?php else: ?>
        <p class="text">Ваша корзина пуста.</p>
    <?php endif; ?>
        <button id="scrollToTop">&#129081;</button>
    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
<?php
// Закрываем подготовленный запрос и соединение с базой данных
$stmt->close();
$mysqli->close();
?>