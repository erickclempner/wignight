<?php
require_once '../config/database.php';

// requiere acceso de admin
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            addProduct();
            break;
        case 'edit':
            editProduct();
            break;
        case 'delete':
            deleteProduct();
            break;
        default:
            $_SESSION['error_message'] = 'Acción no válida';
            header("Location: ../admin.php");
            exit();
    }
} else {
    header("Location: ../admin.php");
    exit();
}

function addProduct() {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio']);
    $cantidad_almacen = intval($_POST['cantidad_almacen']);
    $fabricante = trim($_POST['fabricante'] ?? '');
    $origen = trim($_POST['origen'] ?? '');
    
    // manejar subir imagenes
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['foto']['type'], $allowed_types)) {
            $_SESSION['error_message'] = 'Formato de imagen no válido. Use JPG, PNG o GIF.';
            header("Location: ../admin.php");
            exit();
        }
        
        if ($_FILES['foto']['size'] > $max_size) {
            $_SESSION['error_message'] = 'La imagen es demasiado grande. Máximo 2MB.';
            header("Location: ../admin.php");
            exit();
        }
        
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }
    
    $conn = getConnection();
    
    if ($foto) {
        $stmt = $conn->prepare("INSERT INTO Productos (Nombre, Descripcion, Fotos, Precio, Cantidad_Almacen, Fabricante, Origen) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssbdiss", $nombre, $descripcion, $foto, $precio, $cantidad_almacen, $fabricante, $origen);
        $stmt->send_long_data(2, $foto);
    } else {
        $stmt = $conn->prepare("INSERT INTO Productos (Nombre, Descripcion, Precio, Cantidad_Almacen, Fabricante, Origen) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $nombre, $descripcion, $precio, $cantidad_almacen, $fabricante, $origen);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Producto agregado exitosamente';
    } else {
        $_SESSION['error_message'] = 'Error al agregar el producto: ' . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: ../admin.php");
    exit();
}

function editProduct() {
    $id_producto = intval($_POST['id_producto']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio']);
    $cantidad_almacen = intval($_POST['cantidad_almacen']);
    $fabricante = trim($_POST['fabricante'] ?? '');
    $origen = trim($_POST['origen'] ?? '');
    
    $conn = getConnection();
    
    // checar si se subió la nueva imagen
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['foto']['type'], $allowed_types)) {
            $_SESSION['error_message'] = 'Formato de imagen no válido. Use JPG, PNG o GIF.';
            header("Location: ../admin.php");
            exit();
        }
        
        if ($_FILES['foto']['size'] > $max_size) {
            $_SESSION['error_message'] = 'La imagen es demasiado grande. Máximo 2MB.';
            header("Location: ../admin.php");
            exit();
        }
        
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
        
        $stmt = $conn->prepare("UPDATE Productos SET Nombre = ?, Descripcion = ?, Fotos = ?, Precio = ?, Cantidad_Almacen = ?, Fabricante = ?, Origen = ? WHERE ID_Producto = ?");
        $stmt->bind_param("ssbdissi", $nombre, $descripcion, $foto, $precio, $cantidad_almacen, $fabricante, $origen, $id_producto);
        $stmt->send_long_data(2, $foto);
    } else {
        $stmt = $conn->prepare("UPDATE Productos SET Nombre = ?, Descripcion = ?, Precio = ?, Cantidad_Almacen = ?, Fabricante = ?, Origen = ? WHERE ID_Producto = ?");
        $stmt->bind_param("ssdissi", $nombre, $descripcion, $precio, $cantidad_almacen, $fabricante, $origen, $id_producto);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Producto actualizado exitosamente';
    } else {
        $_SESSION['error_message'] = 'Error al actualizar el producto: ' . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: ../admin.php");
    exit();
}

function deleteProduct() {
    $id_producto = intval($_POST['id_producto']);
    
    $conn = getConnection();
    
    // Checar que exista el producto primero
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Carrito_Compras WHERE ID_Producto_FK = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_count = $result->fetch_assoc()['count'];
    $stmt->close();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Ordenes_Detalles WHERE ID_Producto_FK = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    $order_count = $result->fetch_assoc()['count'];
    $stmt->close();
    
    if ($cart_count > 0 || $order_count > 0) {
        $_SESSION['error_message'] = 'No se puede eliminar el producto porque está en carritos o pedidos existentes';
        $conn->close();
        header("Location: ../admin.php");
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM Productos WHERE ID_Producto = ?");
    $stmt->bind_param("i", $id_producto);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Producto eliminado exitosamente';
    } else {
        $_SESSION['error_message'] = 'Error al eliminar el producto: ' . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: ../admin.php");
    exit();
}
?>
