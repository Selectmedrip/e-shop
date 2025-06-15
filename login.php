<?php
// Подключение к базе данных
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'digital_store');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit;
        }
    }
    $error = "Неверный email или пароль! Попробуйте еще раз.";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
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
    <h2>Вход</h2>
    <?php if (isset($error)) { echo "<p class='text'>$error</p>"; } ?>
    <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Пароль" required><br>
        <button type="submit">Войти</button>
    </form>
    <p class="text">Еще нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    <script src="script.js"></script>
    <?php include 'footer.php' ?>
</body>
</html>
