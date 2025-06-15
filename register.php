<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'digital_store');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);
    $stmt->execute();
    
    $_SESSION['user_id'] = $mysqli->insert_id;
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
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
    <?php include 'navi/header.php';?>
    <h2>Регистрация</h2>
    <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Имя пользователя" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Пароль" required><br>
        <button type="submit">Зарегистрироваться</button>
    </form>
    <p class="text">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    <script src="script.js"></script>
    <?php include 'footer.php' ?>
</body>
</html>
