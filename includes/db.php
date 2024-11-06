<?php
$mysqli = new mysqli('localhost', 'root', '', 'digital_store');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
