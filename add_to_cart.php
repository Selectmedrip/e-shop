<?php
session_start();
require_once('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id']; // ID товара
$quantity_to_add = $_POST['quantity']; // Количество товара, которое пользователь хочет добавить

// Проверяем, есть ли товар уже в корзине
$query = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Если товар уже есть в корзине
    $row = $result->fetch_assoc();
    $current_quantity = $row['quantity'];

    if ($current_quantity >= 5) {
        // Если количество товара уже достигло максимума, блокируем добавление
        $_SESSION['error_message'] = "Вы не можете добавить больше 5 единиц товара в корзину.";
        header('Location: catalog.php'); // Перенаправляем обратно в каталог
        exit;
    } else {
        // Если общее количество не превышает 5, обновляем количество
        $new_quantity = $current_quantity + $quantity_to_add;
        if ($new_quantity > 5) {
            $new_quantity = 5; // Ограничиваем количество до 5
        }

        $update_query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        $update_stmt->execute();
        $_SESSION['success_message'] = "Количество товара обновлено в корзине!";
    }
} else {
    // Если товара еще нет в корзине, добавляем его
    if ($quantity_to_add > 5) {
        $quantity_to_add = 5; // Ограничиваем количество до 5
    }

    $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = $mysqli->prepare($insert_query);
    $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity_to_add);
    $insert_stmt->execute();
    $_SESSION['success_message'] = "Товар добавлен в корзину!";
}

$stmt->close();
$mysqli->close();

// Перенаправление обратно на каталог товаров
header('Location: catalog.php');
exit;
?>