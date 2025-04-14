<?php
// Запускаем сессию
session_start();

// Подключаем библиотеку для генерации QR-кодов
require_once('phpqrcode/qrlib.php');

// Проверяем, что папка для хранения QR-кодов существует, если нет — создаем
$qrFolder = 'qrcodes/';
if (!is_dir($qrFolder)) {
    mkdir($qrFolder, 0777, true);
}

// Получаем ID заказа из сессии или базы данных
$order_id = 8;  // Здесь пример ID заказа (можно получать из сессии или базы данных)
$qr_data = 'Цифровой ключ для заказа: ' . $order_id;  // Данные для QR-кода (например, цифровой ключ)

// Путь, по которому будет сохранен QR-код
$filename = $qrFolder . 'order_' . $order_id . '.png';

// Генерация QR-кода и его сохранение
QRcode::png($qr_data, $filename, QR_ECLEVEL_L, 10, 2);

// Путь для отображения изображения
$qr_image_path = $qrFolder . 'order_' . $order_id . '.png';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR-код для заказа</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="dark.css" id="theme-link">
</head>
<body>
    <h1>Ваш QR-код для заказа</h1>

    <p>Для вашего заказа с ID <?= $order_id; ?> был сгенерирован следующий QR-код:</p>
    <img src="<?= $qr_image_path; ?>" alt="QR-код" />

    <p>Вы можете скачать или распечатать этот QR-код для дальнейшего использования.</p>

    <!--<p><a href="<?= $qr_image_path; ?>" download>Скачать QR-код</a></p>-->

    <?php include 'footer.php';?>
    <script src="script.js"></script>
</body>
</html>
