<?php
require_once 'config/database.php';

// Requiere acceso de admin
requireAdmin();

$error = '';
$success = '';

// Obtener todos los productos
$conn = getConnection();
$query = "SELECT * FROM Productos ORDER BY ID_Producto DESC";
$result = $conn->query($query);
$productos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

// Panel de estadisticas
$stats = [
    'total_productos' => 0,
    'total_usuarios' => 0,
    'productos_stock' => 0,
    'productos_sin_stock' => 0,
    'total_ordenes' => 0,
    'ingresos_totales' => 0
];

$result = $conn->query("SELECT COUNT(*) as total FROM Productos");
if ($result) {
    $stats['total_productos'] = $result->fetch_assoc()['total'];
}

$result = $conn->query("SELECT COUNT(*) as total FROM Usuarios WHERE id_rol = 1");
if ($result) {
    $stats['total_usuarios'] = $result->fetch_assoc()['total'];
}

$result = $conn->query("SELECT COUNT(*) as total FROM Productos WHERE Cantidad_Almacen > 0");
if ($result) {
    $stats['productos_stock'] = $result->fetch_assoc()['total'];
}

$result = $conn->query("SELECT COUNT(*) as total FROM Productos WHERE Cantidad_Almacen = 0");
if ($result) {
    $stats['productos_sin_stock'] = $result->fetch_assoc()['total'];
}

// Estadísticas de órdenes
$result = $conn->query("SELECT COUNT(*) as total FROM Ordenes");
if ($result) {
    $stats['total_ordenes'] = $result->fetch_assoc()['total'];
}

$result = $conn->query("SELECT COALESCE(SUM(Total_Orden), 0) as total FROM Ordenes");
if ($result) {
    $stats['ingresos_totales'] = $result->fetch_assoc()['total'];
}

// Obtener historial de órdenes con detalles
$queryOrdenes = "SELECT o.ID_Orden, o.Fecha_Orden, o.Total_Orden, o.Estado_Orden, 
                        o.Direccion_Envio_Snapshot, u.Nombre_Usuario, u.Correo_Electronico,
                        (SELECT COUNT(*) FROM Ordenes_Detalles WHERE ID_Orden_FK = o.ID_Orden) as total_items
                 FROM Ordenes o 
                 JOIN Usuarios u ON o.ID_Usuario_FK = u.ID_Usuario 
                 ORDER BY o.Fecha_Orden DESC 
                 LIMIT 50";
$resultOrdenes = $conn->query($queryOrdenes);
$ordenes = [];
if ($resultOrdenes) {
    while ($row = $resultOrdenes->fetch_assoc()) {
        $ordenes[] = $row;
    }
}

$conn->close();

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - WigNight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Admin page specific overrides */
        .table-responsive {
            background: rgba(44, 24, 16, 0.9);
            border-radius: 15px;
            padding: 0;
            border: 2px solid var(--wood-accent);
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
            background: transparent !important;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #2c1810, #4a2c1a) !important;
            color: #f4e4c1 !important;
            border-bottom: 2px solid #b87333 !important;
        }
        
        .table tbody {
            background: rgba(44, 24, 16, 0.95) !important;
        }
        
        .table tbody tr {
            background: transparent !important;
        }
        
        .table tbody tr:hover {
            background: rgba(212, 175, 55, 0.15) !important;
        }
        
        .table tbody td {
            color: #e8e8e8 !important;
            background: transparent !important;
            border-color: rgba(139, 111, 71, 0.3) !important;
        }
        
        .table-hover > tbody > tr:hover > td {
            background: transparent !important;
        }
    </style>
</head>
<body>
    <!-- Navegacion -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-moon"></i> WigNight
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="productos.php">
                            <i class="fas fa-bed"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">
                            <i class="fas fa-shopping-cart"></i> Carrito
                            <?php 
                            $cartCount = getCartCount();
                            if ($cartCount > 0):
                            ?>
                                <span class="badge bg-danger cart-badge"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="admin.php">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($currentUser['Nombre_Usuario']); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Admin Header -->
    <section class="py-4">
        <div class="container-fluid">
            <div class="admin-header">
                <h1 class="text-center" style="color: var(--moon-glow);">
                    <i class="fas fa-cog"></i> Panel de Administración
                </h1>
                <p class="text-center" style="color: var(--accent-cream); font-size: 1.1rem;">
                    Gestiona los productos de WigNight
                </p>
            </div>

            <!-- Statistics -->
            <div class="admin-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_productos']; ?></div>
                    <div class="stat-label">
                        <i class="fas fa-box"></i> Total Productos
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['productos_stock']; ?></div>
                    <div class="stat-label">
                        <i class="fas fa-check-circle"></i> Con Stock
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['productos_sin_stock']; ?></div>
                    <div class="stat-label">
                        <i class="fas fa-exclamation-triangle"></i> Sin Stock
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_usuarios']; ?></div>
                    <div class="stat-label">
                        <i class="fas fa-users"></i> Usuarios
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_ordenes']; ?></div>
                    <div class="stat-label">
                        <i class="fas fa-shopping-bag"></i> Órdenes
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo formatPrice($stats['ingresos_totales']); ?></div>
                    <div class="stat-label">
                        <i class="fas fa-dollar-sign"></i> Ingresos
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Management -->
    <section class="py-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: var(--moon-glow);">
                    <i class="fas fa-bed"></i> Gestión de Productos
                </h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Fabricante</th>
                            <th>Origen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x mb-3" style="color: var(--wood-lighter); display: block;"></i>
                                    <p style="color: var(--accent-cream);">No hay productos registrados</p>
                                    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                        <i class="fas fa-plus"></i> Agregar Primer Producto
                                    </button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?php echo $producto['ID_Producto']; ?></td>
                                    <td>
                                        <?php if ($producto['Fotos']): ?>
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['Fotos']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"
                                                 alt="<?php echo htmlspecialchars($producto['Nombre']); ?>">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: var(--wood-light); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image" style="color: var(--moon-glow);"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($producto['Nombre']); ?></td>
                                    <td style="color: var(--accent-gold); font-weight: bold;">
                                        <?php echo formatPrice($producto['Precio']); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $producto['Cantidad_Almacen'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $producto['Cantidad_Almacen']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($producto['Fabricante'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($producto['Origen'] ?? '-'); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary me-1" 
                                                onclick="editProduct(<?php echo htmlspecialchars(json_encode($producto)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?php echo $producto['ID_Producto']; ?>, '<?php echo htmlspecialchars($producto['Nombre']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Orders History Section -->
    <section class="py-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: var(--moon-glow);">
                    <i class="fas fa-history"></i> Historial de Compras
                </h2>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Orden</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ordenes)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x mb-3" style="color: var(--wood-lighter); display: block;"></i>
                                    <p style="color: var(--accent-cream);">No hay órdenes registradas</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ordenes as $orden): ?>
                                <tr>
                                    <td style="color: var(--moon-glow); font-weight: bold;">#<?php echo $orden['ID_Orden']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($orden['Fecha_Orden'])); ?></td>
                                    <td><?php echo htmlspecialchars($orden['Nombre_Usuario']); ?></td>
                                    <td style="color: var(--accent-cream);"><?php echo htmlspecialchars($orden['Correo_Electronico']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $orden['total_items']; ?> items</span>
                                    </td>
                                    <td style="color: var(--accent-gold); font-weight: bold;">
                                        <?php echo formatPrice($orden['Total_Orden']); ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $estadoClass = 'bg-secondary';
                                        switch(strtolower($orden['Estado_Orden'])) {
                                            case 'procesando':
                                                $estadoClass = 'bg-warning text-dark';
                                                break;
                                            case 'enviado':
                                                $estadoClass = 'bg-info';
                                                break;
                                            case 'entregado':
                                                $estadoClass = 'bg-success';
                                                break;
                                            case 'cancelado':
                                                $estadoClass = 'bg-danger';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?php echo $estadoClass; ?>"><?php echo htmlspecialchars($orden['Estado_Orden']); ?></span>
                                    </td>
                                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                        title="<?php echo htmlspecialchars($orden['Direccion_Envio_Snapshot']); ?>">
                                        <?php echo htmlspecialchars(substr($orden['Direccion_Envio_Snapshot'], 0, 30)) . (strlen($orden['Direccion_Envio_Snapshot']) > 30 ? '...' : ''); ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" 
                                                onclick="viewOrderDetails(<?php echo $orden['ID_Orden']; ?>)"
                                                title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: linear-gradient(135deg, rgba(61, 39, 35, 0.98), rgba(22, 33, 62, 0.98)); border: 2px solid var(--accent-gold);">
                <div class="modal-header" style="border-bottom: 2px solid var(--wood-lighter);">
                    <h5 class="modal-title" style="color: var(--moon-glow);">
                        <i class="fas fa-receipt"></i> Detalles de la Orden #<span id="modalOrderId"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetailsContent">
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--accent-gold);"></i>
                            <p style="color: var(--accent-cream); margin-top: 1rem;">Cargando detalles...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 2px solid var(--wood-lighter);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: linear-gradient(135deg, rgba(61, 39, 35, 0.98), rgba(22, 33, 62, 0.98)); border: 2px solid var(--accent-gold);">
                <div class="modal-header" style="border-bottom: 2px solid var(--wood-lighter);">
                    <h5 class="modal-title" style="color: var(--moon-glow);">
                        <i class="fas fa-plus"></i> Agregar Nuevo Producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="api/productos.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag"></i> Nombre del Producto *
                                </label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Precio *
                                </label>
                                <input type="number" step="0.01" class="form-control" name="precio" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-align-left"></i> Descripción
                            </label>
                            <textarea class="form-control" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-box"></i> Cantidad en Almacén *
                                </label>
                                <input type="number" class="form-control" name="cantidad_almacen" value="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-industry"></i> Fabricante
                                </label>
                                <input type="text" class="form-control" name="fabricante">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-globe"></i> Origen
                                </label>
                                <input type="text" class="form-control" name="origen">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-image"></i> Foto del Producto
                            </label>
                            <input type="file" class="form-control" name="foto" accept="image/*">
                            <small style="color: var(--wood-lighter);">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 2px solid var(--wood-lighter);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: linear-gradient(135deg, rgba(61, 39, 35, 0.98), rgba(22, 33, 62, 0.98)); border: 2px solid var(--accent-gold);">
                <div class="modal-header" style="border-bottom: 2px solid var(--wood-lighter);">
                    <h5 class="modal-title" style="color: var(--moon-glow);">
                        <i class="fas fa-edit"></i> Editar Producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="api/productos.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_producto" id="edit_id_producto">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag"></i> Nombre del Producto *
                                </label>
                                <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Precio *
                                </label>
                                <input type="number" step="0.01" class="form-control" name="precio" id="edit_precio" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-align-left"></i> Descripción
                            </label>
                            <textarea class="form-control" name="descripcion" id="edit_descripcion" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-box"></i> Cantidad en Almacén *
                                </label>
                                <input type="number" class="form-control" name="cantidad_almacen" id="edit_cantidad_almacen" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-industry"></i> Fabricante
                                </label>
                                <input type="text" class="form-control" name="fabricante" id="edit_fabricante">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-globe"></i> Origen
                                </label>
                                <input type="text" class="form-control" name="origen" id="edit_origen">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-image"></i> Nueva Foto (dejar vacío para mantener la actual)
                            </label>
                            <input type="file" class="form-control" name="foto" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 2px solid var(--wood-lighter);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background: linear-gradient(135deg, rgba(61, 39, 35, 0.98), rgba(22, 33, 62, 0.98)); border: 2px solid #ff6b6b;">
                <div class="modal-header" style="border-bottom: 2px solid var(--wood-lighter);">
                    <h5 class="modal-title" style="color: var(--moon-glow);">
                        <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="api/productos.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_producto" id="delete_id_producto">
                    <div class="modal-body">
                        <p style="color: var(--accent-cream);">
                            ¿Estás seguro de que deseas eliminar el producto <strong id="delete_producto_nombre"></strong>?
                        </p>
                        <p style="color: #ff6b6b;">
                            <i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <div class="modal-footer" style="border-top: 2px solid var(--wood-lighter);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
    <script>
        function editProduct(product) {
            document.getElementById('edit_id_producto').value = product.ID_Producto;
            document.getElementById('edit_nombre').value = product.Nombre;
            document.getElementById('edit_precio').value = product.Precio;
            document.getElementById('edit_descripcion').value = product.Descripcion || '';
            document.getElementById('edit_cantidad_almacen').value = product.Cantidad_Almacen;
            document.getElementById('edit_fabricante').value = product.Fabricante || '';
            document.getElementById('edit_origen').value = product.Origen || '';
            
            const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
            editModal.show();
        }

        function confirmDelete(id, nombre) {
            document.getElementById('delete_id_producto').value = id;
            document.getElementById('delete_producto_nombre').textContent = nombre;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        function viewOrderDetails(orderId) {
            document.getElementById('modalOrderId').textContent = orderId;
            document.getElementById('orderDetailsContent').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--accent-gold);"></i>
                    <p style="color: var(--accent-cream); margin-top: 1rem;">Cargando detalles...</p>
                </div>
            `;
            
            const orderModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
            orderModal.show();
            
            // Fetch order details
            fetch(`api/ordenes.php?action=details&id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = `
                            <div class="order-info mb-4" style="background: rgba(107, 68, 35, 0.3); padding: 1rem; border-radius: 10px; border: 1px solid var(--wood-accent);">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p style="color: var(--accent-cream); margin-bottom: 0.5rem;">
                                            <i class="fas fa-user"></i> <strong>Cliente:</strong> ${data.orden.Nombre_Usuario}
                                        </p>
                                        <p style="color: var(--accent-cream); margin-bottom: 0.5rem;">
                                            <i class="fas fa-envelope"></i> <strong>Email:</strong> ${data.orden.Correo_Electronico}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p style="color: var(--accent-cream); margin-bottom: 0.5rem;">
                                            <i class="fas fa-calendar"></i> <strong>Fecha:</strong> ${new Date(data.orden.Fecha_Orden).toLocaleString('es-MX')}
                                        </p>
                                        <p style="color: var(--accent-cream); margin-bottom: 0.5rem;">
                                            <i class="fas fa-map-marker-alt"></i> <strong>Dirección:</strong> ${data.orden.Direccion_Envio_Snapshot}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 style="color: var(--moon-glow); margin-bottom: 1rem;">
                                <i class="fas fa-list"></i> Productos de la Orden
                            </h6>
                            
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio Unit.</th>
                                            <th>Cantidad</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        
                        data.detalles.forEach(item => {
                            html += `
                                <tr>
                                    <td style="color: var(--accent-cream);">${item.Nombre_Producto}</td>
                                    <td style="color: var(--accent-gold);">$${parseFloat(item.Precio_Unitario_Snapshot).toFixed(2)}</td>
                                    <td>${item.Cantidad}</td>
                                    <td style="color: var(--accent-gold); font-weight: bold;">$${parseFloat(item.Subtotal_Linea).toFixed(2)}</td>
                                </tr>
                            `;
                        });
                        
                        html += `
                                    </tbody>
                                    <tfoot>
                                        <tr style="border-top: 2px solid var(--wood-accent);">
                                            <td colspan="3" style="text-align: right; color: var(--moon-glow); font-weight: bold;">
                                                <i class="fas fa-calculator"></i> Total:
                                            </td>
                                            <td style="color: var(--accent-gold); font-weight: bold; font-size: 1.2rem;">
                                                $${parseFloat(data.orden.Total_Orden).toFixed(2)}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        `;
                        
                        document.getElementById('orderDetailsContent').innerHTML = html;
                    } else {
                        document.getElementById('orderDetailsContent').innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-circle fa-2x" style="color: #ff6b6b;"></i>
                                <p style="color: #ff6b6b; margin-top: 1rem;">Error al cargar los detalles: ${data.error}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('orderDetailsContent').innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle fa-2x" style="color: #ff6b6b;"></i>
                            <p style="color: #ff6b6b; margin-top: 1rem;">Error de conexión</p>
                        </div>
                    `;
                });
        }
    </script>

    <!-- Cart Preview Component - Must be direct child of body for fixed positioning -->
    <?php include 'components/cart_preview.php'; ?>
</body>
</html>
