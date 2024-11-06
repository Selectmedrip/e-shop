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
    $total_price += $item['price'] * $item['quantity'];
    $order_items[] = [
        'product_id' => $item['product_id'],
        'quantity' => $item['quantity'],
        'price' => $item['price']
    ];
}

// Создаем заказ
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

// Генерация цифрового ключа
$key_code = bin2hex(random_bytes(16));
$stmt = $mysqli->prepare("INSERT INTO `keys` (order_id, key_code) VALUES (?, ?)");
$stmt->bind_param("is", $order_id, $key_code);
$stmt->execute();

// Генерация QR-кода
require_once('phpqrcode/qrlib.php');
$filename = 'qrcodes/order_' . $order_id . '.png';
QRcode::png($key_code, $filename);

echo "<h1>Заказ оформлен</h1>";
echo "<p>Общая стоимость: $$total_price</p>";
echo "<p><a href='$filename'>QR-код для вашего ключа</a></p>";
?>
