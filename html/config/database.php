<?php
// configuracion de mi base de datos
define('DB_HOST', 'db');
define('DB_USER', 'root');
define('DB_PASS', 'Pachicolipato24');
define('DB_NAME', 'ecommerce');

// Crear conexion
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // revisar conexion
    if ($conn->connect_error) {
        die("Falló la conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 2;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $conn = getConnection();
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT ID_Usuario, Nombre_Usuario, Correo_Electronico, id_rol FROM Usuarios WHERE ID_Usuario = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $user;
}

// Format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}
?>
