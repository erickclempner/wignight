<?php
require_once '../config/database.php';

// Require admin access for most operations
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'details':
        getOrderDetails();
        break;
    case 'list':
        getOrdersList();
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        exit();
}

function getOrderDetails() {
    // Require admin access
    if (!isAdmin()) {
        echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
        exit();
    }
    
    $orderId = intval($_GET['id'] ?? 0);
    
    if ($orderId <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID de orden no válido']);
        exit();
    }
    
    $conn = getConnection();
    
    // Get order info
    $stmt = $conn->prepare("SELECT o.*, u.Nombre_Usuario, u.Correo_Electronico 
                            FROM Ordenes o 
                            JOIN Usuarios u ON o.ID_Usuario_FK = u.ID_Usuario 
                            WHERE o.ID_Orden = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orden = $result->fetch_assoc();
    
    if (!$orden) {
        echo json_encode(['success' => false, 'error' => 'Orden no encontrada']);
        $conn->close();
        exit();
    }
    
    // Get order details
    $stmt = $conn->prepare("SELECT od.*, p.Nombre as Nombre_Producto 
                            FROM Ordenes_Detalles od 
                            JOIN Productos p ON od.ID_Producto_FK = p.ID_Producto 
                            WHERE od.ID_Orden_FK = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $detalles = [];
    while ($row = $result->fetch_assoc()) {
        $detalles[] = $row;
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'orden' => $orden,
        'detalles' => $detalles
    ]);
}

function getOrdersList() {
    // Require admin access
    if (!isAdmin()) {
        echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
        exit();
    }
    
    $conn = getConnection();
    
    $limit = intval($_GET['limit'] ?? 50);
    $offset = intval($_GET['offset'] ?? 0);
    
    $stmt = $conn->prepare("SELECT o.ID_Orden, o.Fecha_Orden, o.Total_Orden, o.Estado_Orden, 
                                   o.Direccion_Envio_Snapshot, u.Nombre_Usuario, u.Correo_Electronico,
                                   (SELECT COUNT(*) FROM Ordenes_Detalles WHERE ID_Orden_FK = o.ID_Orden) as total_items
                            FROM Ordenes o 
                            JOIN Usuarios u ON o.ID_Usuario_FK = u.ID_Usuario 
                            ORDER BY o.Fecha_Orden DESC 
                            LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $ordenes = [];
    while ($row = $result->fetch_assoc()) {
        $ordenes[] = $row;
    }
    
    // Get total count
    $countResult = $conn->query("SELECT COUNT(*) as total FROM Ordenes");
    $total = $countResult->fetch_assoc()['total'];
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'ordenes' => $ordenes,
        'total' => $total
    ]);
}
?>
