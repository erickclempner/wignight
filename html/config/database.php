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

// Get cart item count for current user or guest
function getCartCount() {
    if (!isLoggedIn()) {
        // Return guest cart count
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

// Merge guest cart into user cart after login
function mergeGuestCartToUser($userId) {
    if (!isset($_SESSION['guest_cart']) || empty($_SESSION['guest_cart'])) {
        return;
    }
    
    $conn = getConnection();
    
    foreach ($_SESSION['guest_cart'] as $item) {
        // Check if item already exists in user cart
        $stmt = $conn->prepare("SELECT ID_Carrito, Cantidad FROM Carrito_Compras WHERE ID_Usuario_FK = ? AND ID_Producto_FK = ?");
        $stmt->bind_param("ii", $userId, $item['id_producto']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing item
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
    
    // Clear guest cart
    $_SESSION['guest_cart'] = [];
}
?>
