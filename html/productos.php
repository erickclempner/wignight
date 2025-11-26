<?php
require_once 'config/database.php';

// Get all products from database
$conn = getConnection();
$query = "SELECT * FROM Productos ORDER BY ID_Producto DESC";
$result = $conn->query($query);
$productos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
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
    <title>Productos - WigNight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
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
                        <a class="nav-link active" href="productos.php">
                            <i class="fas fa-bed"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">
                            <i class="fas fa-shopping-cart"></i> Carrito
                            <?php 
                            $cart_count = getCartCount();
                            if ($cart_count > 0): 
                            ?>
                                <span class="badge bg-danger cart-badge"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin.php">
                                    <i class="fas fa-cog"></i> Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($currentUser['Nombre_Usuario']); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Salir
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-user-circle"></i> Mi Cuenta
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="py-5" style="background: linear-gradient(135deg, rgba(61, 39, 35, 0.5), rgba(22, 33, 62, 0.5)); min-height: 30vh; display: flex; align-items: center;">
        <div class="container text-center">
            <h1 class="hero-title mb-3">
                <i class="fas fa-bed"></i> Nuestros Productos
            </h1>
            <p class="hero-subtitle">
                Descubre nuestra colección completa para el mejor descanso
            </p>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-5">
        <div class="container">
            <?php if (empty($productos)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> 
                    No hay productos disponibles en este momento.
                    <?php if (isAdmin()): ?>
                        <br><br>
                        <a href="admin.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Agregar Primer Producto
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <p style="color: var(--accent-cream);">
                                <i class="fas fa-box"></i> 
                                Mostrando <strong><?php echo count($productos); ?></strong> producto(s)
                            </p>
                        </div>
                    </div>
                </div>

                <div class="product-grid">
                    <?php foreach ($productos as $producto): ?>
                        <div class="card">
                            <?php if ($producto['Fotos']): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['Fotos']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($producto['Nombre']); ?>">
                            <?php else: ?>
                                <div class="img-placeholder">
                                    <i class="fas fa-bed"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($producto['Nombre']); ?>
                                </h5>
                                <p class="card-text flex-grow-1">
                                    <?php 
                                    $descripcion = $producto['Descripcion'] ?? 'Sin descripción';
                                    echo htmlspecialchars(substr($descripcion, 0, 120)); 
                                    if (strlen($descripcion) > 120) echo '...'; 
                                    ?>
                                </p>
                                
                                <div class="mb-2">
                                    <?php if ($producto['Fabricante']): ?>
                                        <p class="mb-1" style="color: var(--wood-lighter); font-size: 0.9rem;">
                                            <i class="fas fa-industry"></i> 
                                            <strong>Fabricante:</strong> <?php echo htmlspecialchars($producto['Fabricante']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($producto['Origen']): ?>
                                        <p class="mb-1" style="color: var(--wood-lighter); font-size: 0.9rem;">
                                            <i class="fas fa-globe"></i> 
                                            <strong>Origen:</strong> <?php echo htmlspecialchars($producto['Origen']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="card-price">
                                        <?php echo formatPrice($producto['Precio']); ?>
                                    </span>
                                    <span class="badge">
                                        <i class="fas fa-box"></i> 
                                        <?php echo $producto['Cantidad_Almacen']; ?> disponibles
                                    </span>
                                </div>

                                <?php if ($producto['Cantidad_Almacen'] > 0): ?>
                                    <button id="btn-<?php echo $producto['ID_Producto']; ?>" 
                                            class="btn btn-primary mt-3 w-100" 
                                            onclick="addToCart(<?php echo $producto['ID_Producto']; ?>, '<?php echo htmlspecialchars($producto['Nombre']); ?>')">
                                        <i class="fas fa-shopping-cart"></i> Agregar al Carrito
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary mt-3 w-100" disabled>
                                        <i class="fas fa-times"></i> Sin Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Info Banner -->
    <section class="py-5" style="background: rgba(61, 39, 35, 0.3);">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-3">
                    <i class="fas fa-truck fa-3x mb-2" style="color: var(--accent-gold);"></i>
                    <h5 style="color: var(--moon-glow);">Envío Gratis</h5>
                    <p style="color: var(--accent-cream); font-size: 0.9rem;">En compras mayores a $999</p>
                </div>
                <div class="col-md-3 mb-3">
                    <i class="fas fa-undo fa-3x mb-2" style="color: var(--accent-gold);"></i>
                    <h5 style="color: var(--moon-glow);">Devoluciones</h5>
                    <p style="color: var(--accent-cream); font-size: 0.9rem;">30 días para devolver</p>
                </div>
                <div class="col-md-3 mb-3">
                    <i class="fas fa-lock fa-3x mb-2" style="color: var(--accent-gold);"></i>
                    <h5 style="color: var(--moon-glow);">Pago Seguro</h5>
                    <p style="color: var(--accent-cream); font-size: 0.9rem;">100% protegido</p>
                </div>
                <div class="col-md-3 mb-3">
                    <i class="fas fa-headset fa-3x mb-2" style="color: var(--accent-gold);"></i>
                    <h5 style="color: var(--moon-glow);">Soporte 24/7</h5>
                    <p style="color: var(--accent-cream); font-size: 0.9rem;">Siempre disponibles</p>
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
                        <li><a href="login.php" style="color: var(--accent-cream); text-decoration: none;">Mi Cuenta</a></li>
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
            <hr style="border-color: var(--wood-lighter);">
            <p class="mb-0">
                &copy; 2025 WigNight. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
    <script>
        function addToCart(idProducto, nombreProducto) {
            const btn = document.getElementById('btn-' + idProducto);
            const originalHtml = btn.innerHTML;
            
            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agregando...';

            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('id_producto', idProducto);
            formData.append('cantidad', 1);

            fetch('api/carrito.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success feedback
                    btn.innerHTML = '<i class="fas fa-check"></i> ¡Agregado!';
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-success');
                    
                    // Update cart badge
                    updateCartBadge(data.data.cart_count);
                    
                    // Reload cart preview
                    reloadCartPreview();
                    
                    // Show enhanced notification with product details
                    showNotification('success', 'Producto agregado correctamente', {
                        nombre: nombreProducto,
                        precio: data.data.precio || 0,
                        foto: data.data.foto || null
                    });
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-primary');
                        btn.disabled = false;
                    }, 2000);
                } else {
                    alert(data.message);
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al agregar al carrito');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        }

        // Functions updateCartBadge, showNotification, reloadCartPreview, etc. are in cart.js
    </script>

    <!-- Cart Preview Component - Must be direct child of body for fixed positioning -->
    <?php include 'components/cart_preview.php'; ?>
</body>
</html>
