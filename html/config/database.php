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

// Empezar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Checar si el usuario ya está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Checar si el usuario es admin
function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 2;
}

// Redireccionar si no está logueado
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Redireccionar si no es admin
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

// Obtener información del usuario actual
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

// Formato para los precios
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// conteo de artículos en el carrito
function getCartCount() {
    if (!isLoggedIn()) {
        // el return del conteo
        return isset($_SESSION['guest_cart']) ? count($_SESSION['guest_cart']) : 0;
    }
    
    $conn = getConnection();
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Carrito_Compras WHERE ID_Usuario_FK = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $row['count'];
}

// hacer el merge del carrito de invitado a una cuenta de usuario cuando inicie sesión o cree una cuenta
function mergeGuestCartToUser($userId) {
    if (!isset($_SESSION['guest_cart']) || empty($_SESSION['guest_cart'])) {
        return;
    }
    
    $conn = getConnection();
    
    foreach ($_SESSION['guest_cart'] as $item) {
        // checar si el artículo ya está en el carrito
        $stmt = $conn->prepare("SELECT ID_Carrito, Cantidad FROM Carrito_Compras WHERE ID_Usuario_FK = ? AND ID_Producto_FK = ?");
        $stmt->bind_param("ii", $userId, $item['id_producto']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // actualizar el artículo
            $cart_item = $result->fetch_assoc();
            $nueva_cantidad = $cart_item['Cantidad'] + $item['cantidad'];
            $stmt->close();
            
            $stmt = $conn->prepare("UPDATE Carrito_Compras SET Cantidad = ? WHERE ID_Carrito = ?");
            $stmt->bind_param("ii", $nueva_cantidad, $cart_item['ID_Carrito']);
            $stmt->execute();
        } else {
            // Add new item
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO Carrito_Compras (ID_Usuario_FK, ID_Producto_FK, Cantidad) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $userId, $item['id_producto'], $item['cantidad']);
            $stmt->execute();
        }
        $stmt->close();
    }
    
    $conn->close();
    
    // limpiar el carrito de invitado
    $_SESSION['guest_cart'] = [];
}
?>
