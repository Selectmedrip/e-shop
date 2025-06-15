<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$mysqli = new mysqli('localhost', 'root', '', 'digital_store');
$order_id = (int)$_GET['order_id'];

// Проверяем, что заказ принадлежит пользователю
$stmt = $mysqli->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Заказ не найден";
    exit;
}

// Получаем ключи, закрепленные за этим заказом
$stmt = $mysqli->prepare("SELECT products.name, key_code FROM `keys` JOIN products ON keys.product_id = products.id WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$key_codes = [];
while ($row = $result->fetch_assoc()) {
    $key_codes[$row['name']][] = $row['key_code'];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Ваш заказ</title>
    <link type="image/png" sizes="16x16" rel="icon" href="icons/web/16.png">
    <link type="image/png" sizes="32x32" rel="icon" href="icons/web/32.png">
    <link type="image/png" sizes="96x96" rel="icon" href="icons/web/96.png">
    <link type="image/png" sizes="72x72" rel="icon" href="icons/web/72.png">
    <link type="image/png" sizes="96x96" rel="icon" href="icons/web/96.png">
    <link rel="apple-touch-icon" type="image/png" sizes="57x57" href="icons/web/57.png">
    <link rel="apple-touch-icon" type="image/png" sizes="72x72" href="icons/web/72.png">
    <meta name="msapplication-square70x70logo" content="icons/web/70.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dark.css" id="theme-link">
</head>
<body>
    <?php include 'navi/header.php'; ?>
    <div class="product">
        <h1>Заказ оформлен</h1>
        <p class="text">Общая стоимость: <strong><?= number_format($order['total_price'], 2); ?> ₽</strong></p>
        <p class="text">Ваши цифровые ключи:</p>
        <div class="key-grid">
            <div id="copyPopup" class="copy-popup" style="display:none;">
                Текст скопирован!
            </div>
            <?php foreach ($key_codes as $product_name => $codes): ?>
                <fieldset class="key-fieldset">
                    <legend><?= htmlspecialchars($product_name); ?></legend>
                    <?php foreach ($codes as $key_code): ?>
                        <input type="text" value="<?= htmlspecialchars($key_code); ?>" readonly class="key-input">
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
        </div>
    </div>
    <button id="scrollToTop">&#129081;</button>
    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
<?php $mysqli->close(); ?>
