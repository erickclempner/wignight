<?php
require_once '../config/database.php';

header('Content-Type: application/json');

// Inicializar sesión de carrito de invitado
if (!isLoggedIn() && !isset($_SESSION['guest_cart'])) {
    $_SESSION['guest_cart'] = [];
}

$response = ['success' => false, 'message' => '', 'data' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            addToCart();
            break;
        case 'update':
            updateCartItem();
            break;
        case 'remove':
            removeFromCart();
            break;
        case 'clear':
            clearCart();
            break;
        case 'get':
            getCart();
            break;
        case 'checkout':
            checkout();
            break;
        case 'preview':
            getCartPreview();
            break;
        default:
            $response['message'] = 'Acción no válida';
            echo json_encode($response);
            exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    getCart();
} else {
    $response['message'] = 'Método no permitido';
    echo json_encode($response);
    exit();
}

function addToCart() {
    global $response;
    
    $id_producto = intval($_POST['id_producto']);
    $cantidad = intval($_POST['cantidad'] ?? 1);
    $is_logged_in = isLoggedIn();
    
    // manejar carrito de invitado
    if (!$is_logged_in) {
        addToGuestCart($id_producto, $cantidad);
        return;
    }
    
    $id_usuario = $_SESSION['user_id'];
    
    if ($cantidad < 1) {
        $response['message'] = 'La cantidad debe ser mayor a 0';
        echo json_encode($response);
        exit();
    }
    
    $conn = getConnection();
    
    // Checar si el producto existe y tiene suficiente stock
    $stmt = $conn->prepare("SELECT Cantidad_Almacen, Nombre FROM Productos WHERE ID_Producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'Producto no encontrado';
        echo json_encode($response);
        $stmt->close();
        $conn->close();
        exit();
    }
    
    $producto = $result->fetch_assoc();
    $stmt->close();
    
    // Checar si el artículo ya está en el carrito
    $stmt = $conn->prepare("SELECT ID_Carrito, Cantidad FROM Carrito_Compras WHERE ID_Usuario_FK = ? AND ID_Producto_FK = ?");
    $stmt->bind_param("ii", $id_usuario, $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // actualizar el artículo en el carrito
        $cart_item = $result->fetch_assoc();
        $nueva_cantidad = $cart_item['Cantidad'] + $cantidad;
        
        if ($nueva_cantidad > $producto['Cantidad_Almacen']) {
            $response['message'] = 'No hay suficiente stock disponible. Stock actual: ' . $producto['Cantidad_Almacen'];
            echo json_encode($response);
            $stmt->close();
            $conn->close();
            exit();
        }
        
        $stmt->close();
        $stmt = $conn->prepare("UPDATE Carrito_Compras SET Cantidad = ? WHERE ID_Carrito = ?");
        $stmt->bind_param("ii", $nueva_cantidad, $cart_item['ID_Carrito']);
        
            if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Cantidad actualizada en el carrito';
            $response['data'] = ['cart_count' => getCartCountConn($conn, $id_usuario)];
        } else {
            $response['message'] = 'Error al actualizar el carrito';
        }
    } else {
        // Checar stock
        if ($cantidad > $producto['Cantidad_Almacen']) {
            $response['message'] = 'No hay suficiente stock disponible. Stock actual: ' . $producto['Cantidad_Almacen'];
            echo json_encode($response);
            $stmt->close();
            $conn->close();
            exit();
        }
        
        // agregar artículo al carrito
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO Carrito_Compras (ID_Usuario_FK, ID_Producto_FK, Cantidad) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $id_usuario, $id_producto, $cantidad);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = $producto['Nombre'] . ' agregado al carrito';
            $response['data'] = ['cart_count' => getCartCountConn($conn, $id_usuario)];
        } else {
            $response['message'] = 'Error al agregar al carrito';
        }
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($response);
}

function updateCartItem() {
    global $response;
    
    $id_carrito = intval($_POST['id_carrito']);
    $cantidad = intval($_POST['cantidad']);
    
    // manejar carrito de invitado
    if (!isLoggedIn()) {
        updateGuestCartItem($id_carrito, $cantidad);
        return;
    }
    
    $id_usuario = $_SESSION['user_id'];
    
    if ($cantidad < 1) {
        $response['message'] = 'La cantidad debe ser mayor a 0';
        echo json_encode($response);
        exit();
    }
    
    $conn = getConnection();
    
    // Verificar que el artículo del carrito pertenece al usuario y checar stock
    $stmt = $conn->prepare("
        SELECT c.ID_Carrito, p.Cantidad_Almacen, p.Nombre 
        FROM Carrito_Compras c 
        JOIN Productos p ON c.ID_Producto_FK = p.ID_Producto 
        WHERE c.ID_Carrito = ? AND c.ID_Usuario_FK = ?
    ");
    $stmt->bind_param("ii", $id_carrito, $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'Producto no encontrado en tu carrito';
        echo json_encode($response);
        $stmt->close();
        $conn->close();
        exit();
    }
    
    $item = $result->fetch_assoc();
    
    if ($cantidad > $item['Cantidad_Almacen']) {
        $response['message'] = 'No hay suficiente stock. Disponible: ' . $item['Cantidad_Almacen'];
        echo json_encode($response);
        $stmt->close();
        $conn->close();
        exit();
    }
    
    $stmt->close();
    
    $stmt = $conn->prepare("UPDATE Carrito_Compras SET Cantidad = ? WHERE ID_Carrito = ?");
    $stmt->bind_param("ii", $cantidad, $id_carrito);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Cantidad actualizada';
        $response['data'] = ['cart_count' => getCartCountConn($conn, $id_usuario)];
    } else {
        $response['message'] = 'Error al actualizar cantidad';
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($response);
}

function removeFromCart() {
    global $response;
    
    $id_carrito = intval($_POST['id_carrito']);
    
    if (!isLoggedIn()) {
        removeFromGuestCart($id_carrito);
        return;
    }
    
    $id_usuario = $_SESSION['user_id'];
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("DELETE FROM Carrito_Compras WHERE ID_Carrito = ? AND ID_Usuario_FK = ?");
    $stmt->bind_param("ii", $id_carrito, $id_usuario);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Producto eliminado del carrito';
        $response['data'] = ['cart_count' => getCartCountConn($conn, $id_usuario)];
    } else {
        $response['message'] = 'Error al eliminar producto';
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($response);
}

function clearCart() {
    global $response;
    
    // manejar carrito de invitado
    if (!isLoggedIn()) {
        $_SESSION['guest_cart'] = [];
        $response['success'] = true;
        $response['message'] = 'Carrito vaciado';
        $response['data'] = ['cart_count' => 0];
        echo json_encode($response);
        return;
    }
    
    $id_usuario = $_SESSION['user_id'];
    $conn = getConnection();
    
    $stmt = $conn->prepare("DELETE FROM Carrito_Compras WHERE ID_Usuario_FK = ?");
    $stmt->bind_param("i", $id_usuario);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Carrito vaciado';
        $response['data'] = ['cart_count' => 0];
    } else {
        $response['message'] = 'Error al vaciar carrito';
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($response);
}

function getCart() {
    global $response;
    
    if (!isLoggedIn()) {
        getGuestCart();
        return;
    }
    
    $id_usuario = $_SESSION['user_id'];
    $conn = getConnection();
    
    $stmt = $conn->prepare("
        SELECT 
            c.ID_Carrito,
            c.Cantidad,
            p.ID_Producto,
            p.Nombre,
            p.Descripcion,
            p.Precio,
            p.Cantidad_Almacen,
            p.Fotos,
            (c.Cantidad * p.Precio) as Subtotal
        FROM Carrito_Compras c
        JOIN Productos p ON c.ID_Producto_FK = p.ID_Producto
        WHERE c.ID_Usuario_FK = ?
        ORDER BY c.ID_Carrito DESC
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        if ($row['Fotos']) {
            $row['Fotos'] = base64_encode($row['Fotos']);
        }
        $total += $row['Subtotal'];
        $items[] = $row;
    }
    
    $response['success'] = true;
    $response['data'] = [
        'items' => $items,
        'total' => $total,
        'count' => count($items)
    ];
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($response);
}

function checkout() {
    global $response;
    
    // los invitados tienen que loguearse para comprar
    if (!isLoggedIn()) {
        $response['message'] = 'Debes iniciar sesión para completar la compra';
        $response['data'] = ['redirect' => 'login.php'];
        echo json_encode($response);
        return;
    }
    
    $id_usuario = $_SESSION['user_id'];
    $conn = getConnection();
    
    // transacción
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("
            SELECT 
                c.ID_Carrito,
                c.ID_Producto_FK,
                c.Cantidad,
                p.Precio,
                p.Cantidad_Almacen,
                p.Nombre
            FROM Carrito_Compras c
            JOIN Productos p ON c.ID_Producto_FK = p.ID_Producto
            WHERE c.ID_Usuario_FK = ?
            FOR UPDATE
        ");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('El carrito está vacío');
        }
        
        $cart_items = [];
        $total_orden = 0;
        
        while ($row = $result->fetch_assoc()) {
            // checar stock
            if ($row['Cantidad'] > $row['Cantidad_Almacen']) {
                throw new Exception('Stock insuficiente para: ' . $row['Nombre'] . '. Disponible: ' . $row['Cantidad_Almacen']);
            }
            
            $subtotal = $row['Cantidad'] * $row['Precio'];
            $total_orden += $subtotal;
            $cart_items[] = $row;
        }
        $stmt->close();
        
        // direccion del usuario
        $stmt = $conn->prepare("SELECT Direccion_Postal FROM Usuarios WHERE ID_Usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $direccion = $user['Direccion_Postal'] ?? 'Dirección no especificada';
        $stmt->close();
        
        // crear orden
        $stmt = $conn->prepare("INSERT INTO Ordenes (ID_Usuario_FK, Total_Orden, Estado_Orden, Direccion_Envio_Snapshot) VALUES (?, ?, 'Procesando', ?)");
        $stmt->bind_param("ids", $id_usuario, $total_orden, $direccion);
        $stmt->execute();
        $id_orden = $conn->insert_id;
        $stmt->close();
        
        // crear detalles de la orden y actualizar inventario
        foreach ($cart_items as $item) {
            // Insertar detalle de la orden
            $subtotal = $item['Cantidad'] * $item['Precio'];
            $stmt = $conn->prepare("INSERT INTO Ordenes_Detalles (ID_Orden_FK, ID_Producto_FK, Cantidad, Precio_Unitario_Snapshot, Subtotal_Linea) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiidd", $id_orden, $item['ID_Producto_FK'], $item['Cantidad'], $item['Precio'], $subtotal);
            $stmt->execute();
            $stmt->close();
            
            // actualizar inventario
            $nueva_cantidad = $item['Cantidad_Almacen'] - $item['Cantidad'];
            $stmt = $conn->prepare("UPDATE Productos SET Cantidad_Almacen = ? WHERE ID_Producto = ?");
            $stmt->bind_param("ii", $nueva_cantidad, $item['ID_Producto_FK']);
            $stmt->execute();
            $stmt->close();
        }
        
        // limpiar carrito
        $stmt = $conn->prepare("DELETE FROM Carrito_Compras WHERE ID_Usuario_FK = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->close();
        
        // hacer commit para mysql
        $conn->commit();
        
        $response['success'] = true;
        $response['message'] = 'Compra realizada exitosamente';
        $response['data'] = [
            'id_orden' => $id_orden,
            'total' => $total_orden,
            'cart_count' => 0
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = $e->getMessage();
    }
    
    $conn->close();
    echo json_encode($response);
}

function getCartCountConn($conn, $id_usuario) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Carrito_Compras WHERE ID_Usuario_FK = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['count'];
}

// estas son funciones del carrito para invitados, osea, que no se han logueado
function addToGuestCart($id_producto, $cantidad) {
    global $response;
    
    $conn = getConnection();
    
    // checar si el producto existe y tiene suficiente stock
    $stmt = $conn->prepare("SELECT Cantidad_Almacen, Nombre, Precio, Descripcion, Fotos FROM Productos WHERE ID_Producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'Producto no encontrado';
        echo json_encode($response);
        $stmt->close();
        $conn->close();
        return;
    }
    
    $producto = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    // checar si ya existe en el carrito de invitados
    $found = false;
    foreach ($_SESSION['guest_cart'] as &$item) {
        if ($item['id_producto'] == $id_producto) {
            $nueva_cantidad = $item['cantidad'] + $cantidad;
            
            if ($nueva_cantidad > $producto['Cantidad_Almacen']) {
                $response['message'] = 'No hay suficiente stock disponible. Stock actual: ' . $producto['Cantidad_Almacen'];
                echo json_encode($response);
                return;
            }
            
            $item['cantidad'] = $nueva_cantidad;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        // checar stock
        if ($cantidad > $producto['Cantidad_Almacen']) {
            $response['message'] = 'No hay suficiente stock disponible. Stock actual: ' . $producto['Cantidad_Almacen'];
            echo json_encode($response);
            return;
        }
        
        // agregar artículo
        $_SESSION['guest_cart'][] = [
            'id_carrito' => 'guest_' . $id_producto, // Unique ID for guest items
            'id_producto' => $id_producto,
            'cantidad' => $cantidad,
            'nombre' => $producto['Nombre'],
            'precio' => $producto['Precio'],
            'descripcion' => $producto['Descripcion'],
            'stock' => $producto['Cantidad_Almacen'],
            'fotos' => $producto['Fotos'] ? base64_encode($producto['Fotos']) : null
        ];
    }
    
    $response['success'] = true;
    $response['message'] = $producto['Nombre'] . ' agregado al carrito';
    $response['data'] = ['cart_count' => count($_SESSION['guest_cart'])];
    echo json_encode($response);
}

function updateGuestCartItem($id_carrito, $cantidad) {
    global $response;
    
    foreach ($_SESSION['guest_cart'] as &$item) {
        if ($item['id_carrito'] == $id_carrito) {
            if ($cantidad > $item['stock']) {
                $response['message'] = 'No hay suficiente stock. Disponible: ' . $item['stock'];
                echo json_encode($response);
                return;
            }
            
            $item['cantidad'] = $cantidad;
            $response['success'] = true;
            $response['message'] = 'Cantidad actualizada';
            $response['data'] = ['cart_count' => count($_SESSION['guest_cart'])];
            echo json_encode($response);
            return;
        }
    }
    
    $response['message'] = 'Producto no encontrado en tu carrito';
    echo json_encode($response);
}

function removeFromGuestCart($id_carrito) {
    global $response;
    
    foreach ($_SESSION['guest_cart'] as $key => $item) {
        if ($item['id_carrito'] == $id_carrito) {
            unset($_SESSION['guest_cart'][$key]);
            $_SESSION['guest_cart'] = array_values($_SESSION['guest_cart']); // Reindex array
            
            $response['success'] = true;
            $response['message'] = 'Producto eliminado del carrito';
            $response['data'] = ['cart_count' => count($_SESSION['guest_cart'])];
            echo json_encode($response);
            return;
        }
    }
    
    $response['message'] = 'Error al eliminar producto';
    echo json_encode($response);
}

function getGuestCart() {
    global $response;
    
    $items = [];
    $total = 0;
    
    foreach ($_SESSION['guest_cart'] as $item) {
        $subtotal = $item['cantidad'] * $item['precio'];
        $total += $subtotal;
        
        $items[] = [
            'ID_Carrito' => $item['id_carrito'],
            'Cantidad' => $item['cantidad'],
            'ID_Producto' => $item['id_producto'],
            'Nombre' => $item['nombre'],
            'Descripcion' => $item['descripcion'],
            'Precio' => $item['precio'],
            'Cantidad_Almacen' => $item['stock'],
            'Fotos' => $item['fotos'],
            'Subtotal' => $subtotal
        ];
    }
    
    $response['success'] = true;
    $response['data'] = [
        'items' => $items,
        'total' => $total,
        'count' => count($items)
    ];
    
    echo json_encode($response);
}

function getCartPreview() {
    global $response;
    
    if (!isLoggedIn()) {
        $items = [];
        $total = 0;
        $count = 0;
        
        if (isset($_SESSION['guest_cart'])) {
            foreach ($_SESSION['guest_cart'] as $item) {
                $subtotal = $item['cantidad'] * $item['precio'];
                $total += $subtotal;
                $count++;
                
                $items[] = [
                    'Nombre' => $item['nombre'],
                    'Cantidad' => $item['cantidad'],
                    'Precio' => $item['precio'],
                    'Fotos' => $item['fotos']
                ];
            }
        }
        
        $response['success'] = true;
        $response['data'] = [
            'items' => array_slice($items, 0, 3), // Only show first 3 items
            'total' => $total,
            'count' => $count
        ];
        echo json_encode($response);
        return;
    }
    
    $id_usuario = $_SESSION['user_id'];
    $conn = getConnection();
    
    $stmt = $conn->prepare("
        SELECT 
            p.Nombre,
            c.Cantidad,
            p.Precio,
            p.Fotos
        FROM Carrito_Compras c
        JOIN Productos p ON c.ID_Producto_FK = p.ID_Producto
        WHERE c.ID_Usuario_FK = ?
        ORDER BY c.ID_Carrito DESC
        LIMIT 3
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        if ($row['Fotos']) {
            $row['Fotos'] = base64_encode($row['Fotos']);
        }
        $items[] = $row;
    }
    
    $stmt->close();
    
    // obtener total y cantidad
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as count,
            SUM(c.Cantidad * p.Precio) as total
        FROM Carrito_Compras c
        JOIN Productos p ON c.ID_Producto_FK = p.ID_Producto
        WHERE c.ID_Usuario_FK = ?
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $summary = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    $response['success'] = true;
    $response['data'] = [
        'items' => $items,
        'total' => $summary['total'] ?? 0,
        'count' => $summary['count'] ?? 0
    ];
    
    echo json_encode($response);
}
?>
