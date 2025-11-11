<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$carrito = $_SESSION['carrito'];

// Calcular total
$total = 0;
foreach ($carrito as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// Crear pedido
$stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, 'pendiente')");
$stmt->bind_param("id", $usuario_id, $total);
$stmt->execute();
$pedido_id = $conn->insert_id;
$stmt->close();

// Insertar detalles del pedido
foreach ($carrito as $item) {
    $subtotal = $item['precio'] * $item['cantidad'];
    $stmt = $conn->prepare("INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiidd", $pedido_id, $item['id'], $item['cantidad'], $item['precio'], $subtotal);
    $stmt->execute();
    $stmt->close();
}

// Vaciar carrito
$_SESSION['carrito'] = [];

// Redirigir con mensaje de éxito
$_SESSION['pedido_exitoso'] = "¡Pedido #$pedido_id realizado con éxito! Total: $$total MXN";
header("Location: ../productos.php");
exit();
?>