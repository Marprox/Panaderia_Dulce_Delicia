<?php
// php/logout.php
session_start();

// Limpiar localStorage via JavaScript antes de destruir la sesión
echo "<script>
    localStorage.removeItem('userSession');
    localStorage.removeItem('userName'); 
    localStorage.removeItem('userFoto');
</script>";

// Destruir sesión PHP
session_destroy();

// Redirigir al login
header("Location: ../login.php");
exit();
?>