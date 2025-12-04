<?php
require_once 'config/database.php';

// se permiten invitados tambien
$currentUser = isLoggedIn() ? getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - WigNight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
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
                        <a class="nav-link" href="productos.php">
                            <i class="fas fa-bed"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="carrito.php">
                            <i class="fas fa-shopping-cart"></i> Carrito
                            <span class="badge bg-danger ms-1" id="cart-badge-nav">0</span>
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

    <section class="py-4" style="background: linear-gradient(135deg, rgba(44, 24, 16, 0.5), rgba(45, 36, 22, 0.5)); min-height: 20vh; display: flex; align-items: center;">
        <div class="container">
            <h1 class="hero-title" style="font-size: 2.5rem;">
                <i class="fas fa-shopping-cart"></i> Mi Carrito de Compras
            </h1>
            <p style="color: var(--accent-cream); font-size: 1.1rem;">
                Revisa tus productos y finaliza tu compra
            </p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div id="loading" class="text-center py-5">
                <div class="spinner-border" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3" style="color: var(--accent-cream);">Cargando tu carrito...</p>
            </div>

            <div id="cart-empty" class="text-center py-5" style="display: none;">
                <i class="fas fa-shopping-cart fa-5x mb-4" style="color: var(--wood-lighter);"></i>
                <h3 style="color: var(--moon-glow);">Tu carrito está vacío</h3>
                <p style="color: var(--accent-cream); font-size: 1.1rem;">
                    Agrega productos para empezar tu viaje hacia el mejor descanso con WigNight
                </p>
                <a href="productos.php" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-bed"></i> Ver Productos
                </a>
            </div>

            <div id="cart-content" style="display: none;">
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 style="color: var(--moon-glow);">
                                <i class="fas fa-list"></i> Productos (<span id="item-count">0</span>)
                            </h4>
                            <button class="btn btn-secondary btn-sm" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Vaciar Carrito
                            </button>
                        </div>

                        <div id="cart-items">
                            <!-- Los artículos del carrito se suben dinámicamente -->
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card sticky-top" style="top: 100px;">
                            <div class="card-body">
                                <h4 class="card-title mb-4">
                                    <i class="fas fa-receipt"></i> Resumen de Compra
                                </h4>
                                
                                <div class="d-flex justify-content-between mb-3">
                                    <span style="color: var(--accent-cream);">Subtotal:</span>
                                    <span class="card-price" id="cart-total" style="font-size: 1.5rem;">$0.00</span>
                                </div>

                                <hr style="border-color: var(--wood-accent);">

                                <div class="d-flex justify-content-between mb-4">
                                    <strong style="color: var(--moon-glow); font-size: 1.2rem;">Total:</strong>
                                    <strong class="card-price" id="cart-total-final" style="font-size: 1.8rem;">$0.00</strong>
                                </div>

                                <button class="btn btn-primary w-100 py-3 mb-3" onclick="checkout()">
                                    <i class="fas fa-check-circle"></i> Finalizar Compra
                                </button>

                                <a href="productos.php" class="btn btn-secondary w-100">
                                    <i class="fas fa-arrow-left"></i> Seguir Comprando
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 2px solid var(--wood-accent);">
                    <h5 class="modal-title" style="color: var(--moon-glow);">
                        <i class="fas fa-check-circle" style="color: var(--accent-gold);"></i> ¡Compra Exitosa!
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-moon fa-4x mb-3" style="color: var(--moon-glow);"></i>
                    <h4 style="color: var(--accent-cream);">¡Gracias por tu compra!</h4>
                    <p style="color: var(--accent-cream);" id="order-message">
                        Tu pedido ha sido procesado exitosamente.
                    </p>
                    <div class="mt-4">
                        <p style="color: var(--wood-lighter); font-size: 0.9rem;">
                            Número de orden: <strong style="color: var(--accent-gold);" id="order-number"></strong>
                        </p>
                        <p style="color: var(--wood-lighter); font-size: 0.9rem;">
                            Total: <strong style="color: var(--accent-gold);" id="order-total"></strong>
                        </p>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 2px solid var(--wood-accent);">
                    <a href="index.php" class="btn btn-primary w-100">
                        <i class="fas fa-home"></i> Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <p class="mb-0">
                &copy; 2025 WigNight. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cartData = null;

        // cargar carrito
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
        });

        function loadCart() {
            fetch('api/carrito.php', {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartData = data.data;
                    displayCart(data.data);
                } else {
                    showError('Error al cargar el carrito');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error de conexión');
            });
        }

        function displayCart(data) {
            const loading = document.getElementById('loading');
            const emptyCart = document.getElementById('cart-empty');
            const cartContent = document.getElementById('cart-content');
            
            loading.style.display = 'none';

            if (data.items.length === 0) {
                emptyCart.style.display = 'block';
                cartContent.style.display = 'none';
                updateCartBadge(0);
                return;
            }

            emptyCart.style.display = 'none';
            cartContent.style.display = 'block';

            const cartItemsContainer = document.getElementById('cart-items');
            cartItemsContainer.innerHTML = '';

            data.items.forEach(item => {
                const itemHtml = `
                    <div class="card mb-3" id="cart-item-${item.ID_Carrito}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-3">
                                    ${item.Fotos ? 
                                        `<img src="data:image/jpeg;base64,${item.Fotos}" class="img-fluid" style="border-radius: 10px; max-height: 100px; object-fit: cover;" alt="${item.Nombre}">` :
                                        `<div class="img-placeholder" style="height: 100px; font-size: 2rem;">
                                            <i class="fas fa-bed"></i>
                                        </div>`
                                    }
                                </div>
                                <div class="col-md-4 col-9">
                                    <h5 class="card-title mb-1">${item.Nombre}</h5>
                                    <p class="mb-1" style="color: var(--wood-lighter); font-size: 0.9rem;">
                                        Stock disponible: ${item.Cantidad_Almacen}
                                    </p>
                                    <p class="card-price mb-0" style="font-size: 1.3rem;">
                                        ${formatPrice(item.Precio)}
                                    </p>
                                </div>
                                <div class="col-md-3 col-6 mt-3 mt-md-0">
                                    <label class="form-label" style="color: var(--accent-cream); font-size: 0.9rem;">Cantidad:</label>
                                    <div class="input-group">
                                        <button class="btn btn-secondary btn-sm" onclick="updateQuantity(${item.ID_Carrito}, ${item.Cantidad - 1}, ${item.Cantidad_Almacen})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" value="${item.Cantidad}" 
                                               min="1" max="${item.Cantidad_Almacen}" 
                                               onchange="updateQuantity(${item.ID_Carrito}, this.value, ${item.Cantidad_Almacen})"
                                               style="max-width: 70px;">
                                        <button class="btn btn-secondary btn-sm" onclick="updateQuantity(${item.ID_Carrito}, ${item.Cantidad + 1}, ${item.Cantidad_Almacen})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 col-4 mt-3 mt-md-0 text-end">
                                    <p class="mb-2" style="color: var(--accent-cream); font-size: 0.9rem;">Subtotal:</p>
                                    <p class="card-price mb-2" style="font-size: 1.4rem;">
                                        ${formatPrice(item.Subtotal)}
                                    </p>
                                    <button class="btn btn-danger btn-sm" onclick="removeItem(${item.ID_Carrito})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                cartItemsContainer.innerHTML += itemHtml;
            });

            document.getElementById('item-count').textContent = data.count;
            document.getElementById('cart-total').textContent = formatPrice(data.total);
            document.getElementById('cart-total-final').textContent = formatPrice(data.total);
            updateCartBadge(data.count);
        }

        function updateQuantity(idCarrito, newQuantity, maxStock) {
            newQuantity = parseInt(newQuantity);
            
            if (newQuantity < 1) {
                if (confirm('¿Deseas eliminar este producto del carrito?')) {
                    removeItem(idCarrito);
                }
                return;
            }

            if (newQuantity > maxStock) {
                alert(`Solo hay ${maxStock} unidades disponibles`);
                loadCart();
                return;
            }

            const formData = new FormData();
            formData.append('action', 'update');
            formData.append('id_carrito', idCarrito);
            formData.append('cantidad', newQuantity);

            fetch('api/carrito.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadCart();
                } else {
                    alert(data.message);
                    loadCart();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar cantidad');
            });
        }

        function removeItem(idCarrito) {
            if (!confirm('¿Estás seguro de eliminar este producto?')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('id_carrito', idCarrito);

            fetch('api/carrito.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadCart();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar producto');
            });
        }

        function clearCart() {
            if (!confirm('¿Estás seguro de vaciar todo el carrito?')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'clear');

            fetch('api/carrito.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadCart();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al vaciar carrito');
            });
        }

        function checkout() {
            if (!confirm('¿Confirmas que deseas finalizar la compra?')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'checkout');

            fetch('api/carrito.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('order-number').textContent = '#' + data.data.id_orden;
                    document.getElementById('order-total').textContent = formatPrice(data.data.total);
                    
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    updateCartBadge(0);
                } else {
                    // Checar si el usuario tiene que volverse a loguear
                    if (data.data && data.data.redirect) {
                        if (confirm(data.message + '. ¿Deseas iniciar sesión ahora?')) {
                            window.location.href = data.data.redirect;
                        }
                    } else {
                        alert(data.message);
                        loadCart(); // volver a cargar para mostrar que se hizo la compra
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la compra');
            });
        }

        function formatPrice(price) {
            return '$' + parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function updateCartBadge(count) {
            const badge = document.getElementById('cart-badge-nav');
            if (badge) {
                badge.textContent = count;
                if (count > 0) {
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            }
        }

        function showError(message) {
            document.getElementById('loading').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> ${message}
                </div>
            `;
        }
    </script>
</body>
</html>
