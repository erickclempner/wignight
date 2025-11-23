<?php
require_once 'config/database.php';

// Logout user
unset($_SESSION['user_id']);
unset($_SESSION['nombre_usuario']);
unset($_SESSION['id_rol']);

session_destroy();

header("Location: index.php");
exit();
?>
