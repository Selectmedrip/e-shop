<?php
session_start();
require_once('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id']; // ID товара
$quantity = $_POST['quantity']; // Количество товара

// Проверяем, есть ли товар уже в корзине
$query = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Если товар уже есть в корзине, обновляем количество
    $update_query = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("iii", $quantity, $user_id, $product_id);
    $update_stmt->execute();
    echo "Количество товара обновлено в корзине!";
} else {
    // Если товара нет в корзине, добавляем его
    $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = $mysqli->prepare($insert_query);
    $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $insert_stmt->execute();
    echo "Товар добавлен в корзину!";
}

$stmt->close();
$mysqli->close();

// Перенаправление обратно на каталог товаров или на страницу корзины
header('Location: catalog.php');
exit;
?>
