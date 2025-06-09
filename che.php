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
// ...вся логика оформления заказа, как у вас сейчас...

if (!empty($error_messages)) {
    // Если есть ошибки — показываем их и не делаем редирект
    // ...вывод ошибок...
    foreach ($error_messages as $error) {
        echo "<p class='text'>" . htmlspecialchars($error) . "</p>";
    }
    exit;
} else {
    // После успешного оформления заказа
    // Очищаем корзину пользователя
    $mysqli->query("DELETE FROM cart WHERE user_id = $user_id");
    $mysqli->close();
    // Показываем лоадер и редиректим через 5 секунд
    $order_id = $order_id ?? 0;
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Оформление заказа...</title>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="dark.css" id="theme-link">
        <style>
            .loader {
                border: 8px solid #f3f3f3;
                border-top: 8px solid #3498db;
                border-radius: 50%;
                width: 80px;
                height: 80px;
                animation: spin 1s linear infinite;
                margin: 100px auto;
            }
            @keyframes spin {
                0% { transform: rotate(0deg);}
                100% { transform: rotate(360deg);}
            }
            .loader-text {
                text-align: center;
                color: #fff;
                font-size: 1.2em;
                margin-top: 20px;
            }
        </style>
        <script>
            setTimeout(function() {
                window.location.href = "order_success.php?order_id=<?= $order_id ?>";
            }, 5000);
        </script>
    </head>
    <body>
        <div class="loader"></div>
        <div class="loader-text">Ваш заказ оформляется...<br>Пожалуйста, подождите</div>
    </body>
    </html>
    <?php
    exit;
}
?>