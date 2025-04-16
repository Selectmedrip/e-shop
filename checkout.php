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

// Получаем корзину пользователя
$user_id = $_SESSION['user_id'];
$result = $mysqli->query("SELECT * FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = $user_id");

$total_price = 0;
$order_items = [];
while ($item = $result->fetch_assoc()) {
    // Ограничиваем количество товаров до 5
    if ($item['quantity'] > 5) {
        $item['quantity'] = 5;
    }

    $total_price += $item['price'] * $item['quantity'];
    $order_items[] = [
        'product_id' => $item['product_id'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'name' => $item['name'] // Добавляем название продукта
    ];
}

// Проверяем наличие ключей и формируем заказ
$key_codes = [];
$error_messages = []; // Массив для хранения сообщений об ошибках
foreach ($order_items as $item) {
    // Проверяем, достаточно ли ключей для текущего продукта
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS available_keys FROM `keys` WHERE product_id = ? AND order_id IS NULL");
    $stmt->bind_param("i", $item['product_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $available_keys = $row['available_keys'];

    if ($available_keys < $item['quantity']) {
        // Используем название продукта вместо product_id
        $error_messages[] = "Для продукта \"{$item['name']}\" недостаточно ключей. Доступно только {$available_keys} из {$item['quantity']}. Удалите излишние товары из корзины и повторите попытку.";
        continue; // Пропускаем этот товар, если ключей недостаточно
    }

    // Получаем нужное количество ключей из таблицы keys
    $stmt = $mysqli->prepare("SELECT key_code FROM `keys` WHERE product_id = ? AND order_id IS NULL LIMIT ?");
    $stmt->bind_param("ii", $item['product_id'], $item['quantity']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($key = $result->fetch_assoc()) {
        $key_codes[] = $key['key_code'];

        // Обновляем ключ, связывая его с текущим заказом
        $stmt_update = $mysqli->prepare("UPDATE `keys` SET order_id = ? WHERE key_code = ?");
        $stmt_update->bind_param("is", $order_id, $key['key_code']);
        $stmt_update->execute();
    }
}

// Если есть ошибки, заказ не создается
if (!empty($error_messages)) {
    $order_created = false;
} else {
    // Создаем заказ, если ошибок нет
    $stmt = $mysqli->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
    $stmt->bind_param("id", $user_id, $total_price);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Добавляем товары в заказ
    foreach ($order_items as $item) {
        $stmt = $mysqli->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $order_id, $item['product_id'], $item['quantity']);
        $stmt->execute();
    }

    $order_created = true;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="dark.css" id="theme-link">
</head>
<body>
    <?php include 'navi/header.php'; ?>

    <div class="product">
        <?php if (!empty($error_messages)): ?>
            <h1>Заказ не оформлен</h1>
            <?php foreach ($error_messages as $error): ?>
                <p class="text"><?= htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        <?php else: ?>
            <h1>Заказ оформлен</h1>
            <p class="text">Общая стоимость: <strong><?= number_format($total_price, 2); ?> ₽</strong></p>
            <p class="text">Ваши цифровые ключи:</p>
            <?php foreach ($key_codes as $key_code): ?>
                <input type="text" value="<?= htmlspecialchars($key_code); ?>" readonly class="key-input">
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php $mysqli->close(); ?>