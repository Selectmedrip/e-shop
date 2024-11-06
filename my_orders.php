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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Мои заказы</h2>
    <ul>
        <?php while ($order = $result->fetch_assoc()): ?>
            <li>
                <p>Заказ №<?= $order['id']; ?> - $<?= $order['total_price']; ?></p>
                <a href="generate_qr.php?order_id=<?= $order['id']; ?>">QR-код для заказа</a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
