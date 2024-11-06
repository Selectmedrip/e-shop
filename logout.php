<?php
session_start();

// Удаляем все данные сессии
session_unset();
session_destroy();

// Перенаправляем на главную страницу
header('Location: index.php');
exit;
?>
