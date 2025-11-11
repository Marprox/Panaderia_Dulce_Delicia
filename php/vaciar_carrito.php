<?php
session_start();
$_SESSION['carrito'] = [];
header("Location: ../productos.php");
exit();
?>