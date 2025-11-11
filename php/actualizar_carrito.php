<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_index = $_POST['item_index'];
    $accion = $_POST['accion'];
    
    if (isset($_SESSION['carrito'][$item_index])) {
        switch ($accion) {
            case 'aumentar':
                $_SESSION['carrito'][$item_index]['cantidad']++;
                break;
            case 'disminuir':
                if ($_SESSION['carrito'][$item_index]['cantidad'] > 1) {
                    $_SESSION['carrito'][$item_index]['cantidad']--;
                } else {
                    unset($_SESSION['carrito'][$item_index]);
                }
                break;
            case 'eliminar':
                unset($_SESSION['carrito'][$item_index]);
                break;
        }
    }
    
    // Reindexar array
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
}

header("Location: ../productos.php");
exit();
?>