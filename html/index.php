<?php
require_once 'config/database.php';

// Get featured products (limit to 6)
$conn = getConnection();
$query = "SELECT * FROM Productos ORDER BY ID_Producto DESC LIMIT 6";
$result = $conn->query($query);
$featuredProducts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $featuredProducts[] = $row;
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
    <title>WigNight - Tu Mejor Descanso Comienza Aquí</title>
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
                        <a class="nav-link active" href="index.php">
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
                            <a class="nav-link" href="perfil.php">
                                <i class="fas fa-user"></i> Mi Perfil
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">
                <i class="fas fa-moon"></i> WigNight
            </h1>
            <p class="hero-subtitle">
                La primera marca en descubrir el secreto para dormir efectivamente
            </p>
            <p class="lead mb-4" style="color: var(--accent-cream); max-width: 700px; margin: 0 auto;">
                ¿Alguna vez has escuchado el mito que "debemos dormir 8 horas"?
                <br>Es solo eso, un mito.

            </p>
            <div class="mt-4">
                <a href="productos.php" class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-shopping-bag"></i> Descubre tu cantidad óptima de sueño
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="py-5">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-star"></i> Productos Destacados
            </h2>
            
            <?php if (empty($featuredProducts)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> 
                    No hay productos disponibles en este momento. 
                    <?php if (isAdmin()): ?>
                        <a href="admin.php" class="alert-link">Agregar productos</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($featuredProducts as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if ($product['Fotos']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Fotos']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($product['Nombre']); ?>">
                                <?php else: ?>
                                    <div class="img-placeholder">
                                        <i class="fas fa-bed"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($product['Nombre']); ?>
                                    </h5>
                                    <p class="card-text flex-grow-1">
                                        <?php echo htmlspecialchars(substr($product['Descripcion'], 0, 100)); ?>
                                        <?php if (strlen($product['Descripcion']) > 100) echo '...'; ?>
                                    </p>
                                    <?php if ($product['Fabricante']): ?>
                                        <p class="mb-1" style="color: var(--wood-lighter); font-size: 0.9rem;">
                                            <i class="fas fa-industry"></i> 
                                            <?php echo htmlspecialchars($product['Fabricante']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($product['Origen']): ?>
                                        <p class="mb-2" style="color: var(--wood-lighter); font-size: 0.9rem;">
                                            <i class="fas fa-globe"></i> 
                                            <?php echo htmlspecialchars($product['Origen']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="card-price">
                                            <?php echo formatPrice($product['Precio']); ?>
                                        </span>
                                        <span class="badge">
                                            <i class="fas fa-box"></i> 
                                            <?php echo $product['Cantidad_Almacen']; ?> en stock
                                        </span>
                                    </div>
                                    
                                    <?php if ($product['Cantidad_Almacen'] > 0): ?>
                                        <button id="btn-<?php echo $product['ID_Producto']; ?>" 
                                                class="btn btn-primary mt-3 w-100" 
                                                onclick="addToCart(<?php echo $product['ID_Producto']; ?>, '<?php echo htmlspecialchars($product['Nombre']); ?>')">
                                            <i class="fas fa-shopping-cart"></i> Agregar al Carrito
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary mt-3 w-100" disabled>
                                            <i class="fas fa-ban"></i> Sin Stock
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="productos.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag"></i> Ver Todos los Productos
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" style="background: rgba(61, 39, 35, 0.3);">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-gift"></i> ¿Por Qué Elegir WigNight?
            </h2>
            <div class="row">
                <div class="col-md-4 mb-4 text-center">
                    <div class="p-4">
                        <i class="fas fa-moon fa-4x mb-3" style="color: var(--accent-gold);"></i>
                        <h4 style="color: var(--moon-glow);">Descanso Premium</h4>
                        <p style="color: var(--accent-cream);">
                            Productos diseñados con la más alta tecnología para garantizar 
                            el mejor descanso nocturno
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <div class="p-4">
                        <i class="fas fa-shield-alt fa-4x mb-3" style="color: var(--accent-gold);"></i>
                        <h4 style="color: var(--moon-glow);">Calidad Garantizada</h4>
                        <p style="color: var(--accent-cream);">
                            Todos nuestros productos cuentan con garantía y están certificados 
                            por expertos en descanso
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <div class="p-4">
                        <i class="fas fa-truck fa-4x mb-3" style="color: var(--accent-gold);"></i>
                        <h4 style="color: var(--moon-glow);">Envío Rápido</h4>
                        <p style="color: var(--accent-cream);">
                            Recibe tus productos en la comodidad de tu hogar con envío 
                            express disponible
                        </p>
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
                    
                    // Show enhanced notification
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

        // Functions updateCartBadge, showNotification, etc. are in cart.js
    </script>

    <!-- Cart Preview Component - Must be direct child of body for fixed positioning -->
    <?php include 'components/cart_preview.php'; ?>
</body>
</html>
