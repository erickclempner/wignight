<?php
require_once 'config/database.php';
requireLogin();

$user = getCurrentUser();
$conn = getConnection();

// Get user's order history
$stmt = $conn->prepare("
    SELECT 
        o.ID_Orden,
        o.Fecha_Orden,
        o.Total_Orden,
        o.Estado_Orden,
        o.Direccion_Envio_Snapshot,
        COUNT(od.ID_Detalle) as Total_Items
    FROM Ordenes o
    LEFT JOIN Ordenes_Detalles od ON o.ID_Orden = od.ID_Orden_FK
    WHERE o.ID_Usuario_FK = ?
    GROUP BY o.ID_Orden
    ORDER BY o.Fecha_Orden DESC
    LIMIT 10
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get full user data
$stmt = $conn->prepare("SELECT * FROM Usuarios WHERE ID_Usuario = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

$success_message = $_SESSION['register_success'] ?? '';
unset($_SESSION['register_success']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - WigNight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
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
                    <li class="nav-item cart-dropdown-container">
                        <a class="nav-link" href="carrito.php">
                            <i class="fas fa-shopping-cart"></i> Carrito
                            <?php 
                            $cart_count = getCartCount();
                            if ($cart_count > 0): 
                            ?>
                                <span class="badge bg-danger cart-badge"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="cart-dropdown" id="cartDropdown">
                            <div class="cart-dropdown-header">
                                <h6>Cantidad: <span id="cartDropdownCount">0</span></h6>
                            </div>
                            <div class="cart-dropdown-body" id="cartDropdownBody">
                                <div class="text-center py-3">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                            <div class="cart-dropdown-footer" id="cartDropdownFooter" style="display:none;">
                                <div class="cart-dropdown-total-section">
                                    <div class="cart-dropdown-total">
                                        <span>Total:</span>
                                        <span class="cart-dropdown-total-amount" id="cartDropdownTotal">$0.00</span>
                                    </div>
                                </div>
                                <div class="cart-dropdown-actions">
                                    <a href="carrito.php" class="cart-dropdown-btn cart-dropdown-btn-primary">
                                        <i class="fas fa-shopping-bag"></i> Ver Bolsa (2)
                                    </a>
                                    <button class="cart-dropdown-btn cart-dropdown-btn-primary" onclick="window.location.href='carrito.php'">
                                        <i class="fas fa-lock"></i> Iniciar Compra Segura
                                    </button>
                                </div>
                                <div class="cart-dropdown-payment-icons">
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 32'%3E%3Crect fill='%23f90' width='48' height='32' rx='4'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='white' font-family='Arial' font-weight='bold' font-size='10'%3ECard%3C/text%3E%3C/svg%3E" alt="Credit Card" title="Tarjetas de crédito">
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 32'%3E%3Crect fill='%230070ba' width='48' height='32' rx='4'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='white' font-family='Arial' font-weight='bold' font-size='8'%3EPayPal%3C/text%3E%3C/svg%3E" alt="PayPal" title="PayPal">
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 32'%3E%3Crect fill='%2300a3e0' width='48' height='32' rx='4'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='white' font-family='Arial' font-weight='bold' font-size='8'%3EMaestro%3C/text%3E%3C/svg%3E" alt="Maestro" title="Maestro">
                                </div>
                                <div class="cart-dropdown-security-badges">
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 40'%3E%3Crect fill='white' width='60' height='40' rx='4' stroke='%23ccc'/%3E%3Cpath d='M20,15 L20,25 M40,15 L40,25' stroke='%23333' stroke-width='2'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%23333' font-family='Arial' font-size='8'%3ESecure%3C/text%3E%3C/svg%3E" alt="Verificado" title="Compra Segura">
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 40'%3E%3Crect fill='white' width='60' height='40' rx='4' stroke='%23ccc'/%3E%3Ccircle cx='30' cy='20' r='8' fill='none' stroke='%23e63946' stroke-width='2'/%3E%3Cpath d='M27,20 L29,22 L33,18' stroke='%23e63946' stroke-width='2' fill='none'/%3E%3Ctext x='50%25' y='85%25' text-anchor='middle' fill='%23333' font-family='Arial' font-size='6'%3ECertified%3C/text%3E%3C/svg%3E" alt="McAfee" title="Certificado de seguridad">
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">
                                <i class="fas fa-cog"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="perfil.php">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($user['Nombre_Usuario']); ?>
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

    <!-- Profile Section -->
    <section class="py-5">
        <div class="container">
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- User Info Card -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3" style="font-size: 5rem; color: var(--accent-gold);">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <h3 class="card-title" style="color: var(--moon-glow);">
                                <?php echo htmlspecialchars($userData['Nombre_Usuario']); ?>
                            </h3>
                            <p style="color: var(--accent-warm); font-size: 0.95rem;">
                                <i class="fas fa-envelope"></i> 
                                <?php echo htmlspecialchars($userData['Correo_Electronico']); ?>
                            </p>
                            
                            <hr style="border-color: var(--wood-accent); margin: 1.5rem 0;">
                            
                            <div class="text-start">
                                <?php if ($userData['Fecha_Nacimiento']): ?>
                                    <p class="mb-2" style="color: var(--accent-cream);">
                                        <i class="fas fa-birthday-cake" style="color: var(--accent-copper);"></i> 
                                        <strong>Nacimiento:</strong><br>
                                        <span style="margin-left: 1.5rem;">
                                            <?php echo date('d/m/Y', strtotime($userData['Fecha_Nacimiento'])); ?>
                                        </span>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($userData['Direccion_Postal']): ?>
                                    <p class="mb-2" style="color: var(--accent-cream);">
                                        <i class="fas fa-map-marker-alt" style="color: var(--accent-copper);"></i> 
                                        <strong>Dirección:</strong><br>
                                        <span style="margin-left: 1.5rem;">
                                            <?php echo nl2br(htmlspecialchars($userData['Direccion_Postal'])); ?>
                                        </span>
                                    </p>
                                <?php endif; ?>
                                
                                <p class="mb-0" style="color: var(--accent-cream);">
                                    <i class="fas fa-calendar-plus" style="color: var(--accent-copper);"></i> 
                                    <strong>Miembro desde:</strong><br>
                                    <span style="margin-left: 1.5rem;">
                                        <?php echo date('d/m/Y', strtotime($userData['Fecha_Registro'])); ?>
                                    </span>
                                </p>
                            </div>
                            
                            <hr style="border-color: var(--wood-accent); margin: 1.5rem 0;">
                            
                            <a href="carrito.php" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-shopping-cart"></i> Ver Mi Carrito
                                <?php if ($cart_count > 0): ?>
                                    <span class="badge bg-light text-dark"><?php echo $cart_count; ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="productos.php" class="btn btn-secondary w-100">
                                <i class="fas fa-bed"></i> Explorar Productos
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Order History -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4" style="color: var(--moon-glow);">
                                <i class="fas fa-history"></i> Historial de Compras
                            </h4>
                            
                            <?php if (empty($orders)): ?>
                                <div class="text-center py-5">
                                    <div style="font-size: 4rem; color: var(--wood-lighter); opacity: 0.5;">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <h5 style="color: var(--accent-warm); margin-top: 1rem;">
                                        Aún no has realizado compras
                                    </h5>
                                    <p style="color: var(--wood-lighter);">
                                        Explora nuestros productos y encuentra lo que necesitas para tu mejor descanso
                                    </p>
                                    <a href="productos.php" class="btn btn-primary mt-3">
                                        <i class="fas fa-bed"></i> Ver Productos
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead style="background-color: var(--wood-dark);">
                                            <tr>
                                                <th style="color: var(--moon-glow);">
                                                    <i class="fas fa-hashtag"></i> Orden
                                                </th>
                                                <th style="color: var(--moon-glow);">
                                                    <i class="fas fa-calendar"></i> Fecha
                                                </th>
                                                <th style="color: var(--moon-glow);">
                                                    <i class="fas fa-box"></i> Items
                                                </th>
                                                <th style="color: var(--moon-glow);">
                                                    <i class="fas fa-dollar-sign"></i> Total
                                                </th>
                                                <th style="color: var(--moon-glow);">
                                                    <i class="fas fa-info-circle"></i> Estado
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                                <tr style="background-color: rgba(42, 24, 16, 0.3);">
                                                    <td style="color: var(--accent-gold); font-weight: bold;">
                                                        #<?php echo $order['ID_Orden']; ?>
                                                    </td>
                                                    <td style="color: var(--accent-cream);">
                                                        <?php echo date('d/m/Y H:i', strtotime($order['Fecha_Orden'])); ?>
                                                    </td>
                                                    <td style="color: var(--accent-cream);">
                                                        <?php echo $order['Total_Items']; ?> productos
                                                    </td>
                                                    <td style="color: var(--accent-copper); font-weight: bold;">
                                                        <?php echo formatPrice($order['Total_Orden']); ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $badge_class = 'bg-warning';
                                                        $icon = 'fa-clock';
                                                        
                                                        switch ($order['Estado_Orden']) {
                                                            case 'Completada':
                                                                $badge_class = 'bg-success';
                                                                $icon = 'fa-check-circle';
                                                                break;
                                                            case 'Enviada':
                                                                $badge_class = 'bg-info';
                                                                $icon = 'fa-truck';
                                                                break;
                                                            case 'Cancelada':
                                                                $badge_class = 'bg-danger';
                                                                $icon = 'fa-times-circle';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge <?php echo $badge_class; ?>">
                                                            <i class="fas <?php echo $icon; ?>"></i> 
                                                            <?php echo $order['Estado_Orden']; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <p style="color: var(--wood-lighter); font-size: 0.9rem;">
                                        <i class="fas fa-info-circle"></i> 
                                        Mostrando las últimas 10 órdenes
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5 style="color: var(--moon-glow);">
                        <i class="fas fa-moon"></i> WigNight
                    </h5>
                    <p>Tu mejor descanso comienza aquí</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 style="color: var(--moon-glow);">Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" style="color: var(--accent-cream); text-decoration: none;">Inicio</a></li>
                        <li><a href="productos.php" style="color: var(--accent-cream); text-decoration: none;">Productos</a></li>
                        <li><a href="carrito.php" style="color: var(--accent-cream); text-decoration: none;">Carrito</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 style="color: var(--moon-glow);">Síguenos</h5>
                    <div>
                        <a href="#" class="text-decoration-none me-3" style="color: var(--accent-gold); font-size: 1.5rem;">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="text-decoration-none me-3" style="color: var(--accent-gold); font-size: 1.5rem;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-decoration-none" style="color: var(--accent-gold); font-size: 1.5rem;">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
            <hr style="border-color: var(--wood-accent); margin: 2rem 0;">
            <p class="mb-0">
                &copy; 2025 WigNight. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>

    <!-- Cart Preview Component - Must be direct child of body for fixed positioning -->
    <?php include 'components/cart_preview.php'; ?>
</body>
</html>
