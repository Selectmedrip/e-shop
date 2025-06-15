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
$result = $mysqli->query("SELECT * FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = $user_id");

$total_price = 0;
$order_items = [];
while ($item = $result->fetch_assoc()) {
    if ($item['quantity'] > 5) {
        $item['quantity'] = 5;
    }
    $total_price += $item['price'] * $item['quantity'];
    $order_items[] = [
        'product_id' => $item['product_id'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'name' => $item['name']
    ];
}

// Проверяем наличие ключей и формируем заказ
$key_codes = [];
$error_messages = [];
$keys_to_update = [];
foreach ($order_items as $item) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS available_keys FROM `keys` WHERE product_id = ? AND order_id IS NULL");
    $stmt->bind_param("i", $item['product_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $available_keys = $row['available_keys'];

    if ($available_keys < $item['quantity']) {
        $error_messages[] = "Для продукта \"{$item['name']}\" недостаточно ключей. Доступно только {$available_keys} из {$item['quantity']}. Удалите излишние товары из корзины и повторите попытку.";
        continue;
    }

    $stmt = $mysqli->prepare("SELECT key_code FROM `keys` WHERE product_id = ? AND order_id IS NULL LIMIT ?");
    $stmt->bind_param("ii", $item['product_id'], $item['quantity']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($key = $result->fetch_assoc()) {
        $key_codes[$item['name']][] = $key['key_code'];
        $keys_to_update[] = [
            'key_code' => $key['key_code'],
            'product_id' => $item['product_id']
        ];
    }
}

// Если есть ошибки — показываем их и не делаем редирект
if (!empty($error_messages)) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Оформление заказа</title>
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
        <?php include 'navi/header.php'; ?>
        <div class="product">
            <h1>Заказ не оформлен</h1>
            <?php foreach ($error_messages as $error): ?>
                <p class="text"><?= htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <button id="scrollToTop">&#129081;</button>
        <?php include 'footer.php'; ?>
        <script src="script.js"></script>
    </body>
    </html>
    <?php
    $mysqli->close();
    exit;
}

// --- Если ошибок нет, создаём заказ и делаем редирект ---
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

// Обновляем ключи, связывая их с заказом
if (!empty($keys_to_update)) {
    foreach ($keys_to_update as $key_info) {
        $stmt_update = $mysqli->prepare("UPDATE `keys` SET order_id = ? WHERE key_code = ? AND product_id = ?");
        $stmt_update->bind_param("isi", $order_id, $key_info['key_code'], $key_info['product_id']);
        $stmt_update->execute();
    }
}

// Очищаем корзину пользователя
$mysqli->query("DELETE FROM cart WHERE user_id = $user_id");
$mysqli->close();

// Показываем лоадер и редиректим на order_success.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление заказа...</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dark.css" id="theme-link">
    <script>
        setTimeout(function() {
            window.location.href = "order_success.php?order_id=<?= $order_id ?>";
        }, 2400);
    </script>
</head>
<body>
    <section class="containe">
        <section class="loader">
            <article style="--rot: 0" class="sphere sphere1">
                <div class="item" style="--rot-y: 1"></div>
                <div class="item" style="--rot-y: 2"></div>
                <div class="item" style="--rot-y: 3"></div>
                <div class="item" style="--rot-y: 4"></div>
                <div class="item" style="--rot-y: 5"></div>
                <div class="item" style="--rot-y: 6"></div>
                <div class="item" style="--rot-y: 7"></div>
                <div class="item" style="--rot-y: 8"></div>
                <div class="item" style="--rot-y: 9"></div>
            </article>
            <article style="--rot: 1" class="sphere sphere2">
                <div class="item" style="--rot-y: 1"></div>
                <div class="item" style="--rot-y: 2"></div>
                <div class="item" style="--rot-y: 3"></div>
                <div class="item" style="--rot-y: 4"></div>
                <div class="item" style="--rot-y: 5"></div>
                <div class="item" style="--rot-y: 6"></div>
                <div class="item" style="--rot-y: 7"></div>
                <div class="item" style="--rot-y: 8"></div>
                <div class="item" style="--rot-y: 9"></div>
            </article>
            <article style="--rot: 2" class="sphere sphere3">
                <div class="item" style="--rot-y: 1"></div>
                <div class="item" style="--rot-y: 2"></div>
                <div class="item" style="--rot-y: 3"></div>
                <div class="item" style="--rot-y: 4"></div>
                <div class="item" style="--rot-y: 5"></div>
                <div class="item" style="--rot-y: 6"></div>
                <div class="item" style="--rot-y: 7"></div>
                <div class="item" style="--rot-y: 8"></div>
                <div class="item" style="--rot-y: 9"></div>
            </article>
            <article style="--rot: 3" class="sphere sphere4">
                <div class="item" style="--rot-y: 1"></div>
                <div class="item" style="--rot-y: 2"></div>
                <div class="item" style="--rot-y: 3"></div>
                <div class="item" style="--rot-y: 4"></div>
                <div class="item" style="--rot-y: 5"></div>
                <div class="item" style="--rot-y: 6"></div>
                <div class="item" style="--rot-y: 7"></div>
                <div class="item" style="--rot-y: 8"></div>
                <div class="item" style="--rot-y: 9"></div>
            </article>
            <article style="--rot: 4" class="sphere sphere5">
                <div class="item" style="--rot-y: 1"></div>
                <div class="item" style="--rot-y: 2"></div>
                <div class="item" style="--rot-y: 3"></div>
                <div class="item" style="--rot-y: 4"></div>
                <div class="item" style="--rot-y: 5"></div>
                <div class="item" style="--rot-y: 6"></div>
                <div class="item" style="--rot-y: 7"></div>
                <div class="item" style="--rot-y: 8"></div>
                <div class="item" style="--rot-y: 9"></div>
            </article>
            <article style="--rot: 5" class="sphere sphere6">
                <div class="item" style="--rot-y: 1"></div>
                <div class="item" style="--rot-y: 2"></div>
                <div class="item" style="--rot-y: 3"></div>
                <div class="item" style="--rot-y: 4"></div>
                <div class="item" style="--rot-y: 5"></div>
                <div class="item" style="--rot-y: 6"></div>
                <div class="item" style="--rot-y: 7"></div>
                <div class="item" style="--rot-y: 8"></div>
                <div class="item" style="--rot-y: 9"></div>
            </article>
            <article style="--rot: 6" class="sphere sphere7">
                <div class="item" style="--rot-y: 1"></div>
                <div class="item" style="--rot-y: 2"></div>
                <div class="item" style="--rot-y: 3"></div>
                <div class="item" style="--rot-y: 4"></div>
                <div class="item" style="--rot-y: 5"></div>
                <div class="item" style="--rot-y: 6"></div>
                <div class="item" style="--rot-y: 7"></div>
                <div class="item" style="--rot-y: 8"></div>
                <div class="item" style="--rot-y: 9"></div>
            </article>
            <article style="--rot: 7" class="sphere sphere8">
                <div class="item" style="--rot-y: 1"></div>
                <div class="item" style="--rot-y: 2"></div>
                <div class="item" style="--rot-y: 3"></div>
                <div class="item" style="--rot-y: 4"></div>
                <div class="item" style="--rot-y: 5"></div>
                <div class="item" style="--rot-y: 6"></div>
                <div class="item" style="--rot-y: 7"></div>
                <div class="item" style="--rot-y: 8"></div>
                <div class="item" style="--rot-y: 9"></div>
            </article>
            <article style="--rot: 8" class="sphere sphere9">
                <div class="item" style="--rot-y: 1"></div>
                <div class="item" style="--rot-y: 2"></div>
                <div class="item" style="--rot-y: 3"></div>
                <div class="item" style="--rot-y: 4"></div>
                <div class="item" style="--rot-y: 5"></div>
                <div class="item" style="--rot-y: 6"></div>
                <div class="item" style="--rot-y: 7"></div>
                <div class="item" style="--rot-y: 8"></div>
                <div class="item" style="--rot-y: 9"></div>
            </article>
        </section>
        
    </section>
    <div class="loader-text">Ваш заказ оформляется...<br>Пожалуйста, подождите</div>
</body>
</html>