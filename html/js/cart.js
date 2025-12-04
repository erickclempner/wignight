// Funcionalidad del carrito
// aqui van funciones que me ayudaron mucho a manejar el carrito sin repetir tanto código

function formatPrice(price) {
    return '$' + parseFloat(price).toFixed(2);
}

function updateCartBadge(count) {
    // actualizar todas las badges del carrito en el navbar
    const badges = document.querySelectorAll('.navbar .cart-badge');
    badges.forEach(badge => {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline' : 'none';
    });
}

function showNotification(type, message, productData = null) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    
    let productHTML = '';
    if (productData) {
        const imgHTML = productData.foto ? 
            `<img src="data:image/jpeg;base64,${productData.foto}" alt="${productData.nombre}" class="toast-img">` :
            `<div class="toast-img-placeholder"><i class="fas fa-box"></i></div>`;
        
        productHTML = `
            <div class="toast-product-info">
                ${imgHTML}
                <div class="toast-product-details">
                    <div class="toast-product-name">${productData.nombre}</div>
                    <div class="toast-product-price">${formatPrice(productData.precio)}</div>
                </div>
            </div>
        `;
    }
    
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'times-circle'}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${type === 'success' ? '¡Agregado al carrito!' : 'Error'}</div>
            <div class="toast-message">${message}</div>
            ${productHTML}
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 4000);
}


let cartPreviewLoaded = false;
let hidePreviewTimeout = null;
let isOverCart = false;
let isOverPreview = false;

function initCartPreview() {
    // encontrar links del carrito
    const cartLinks = document.querySelectorAll('a[href="carrito.php"], a.nav-link[href*="carrito"]');
    const previewOverlay = document.getElementById('cartPreviewOverlay');
    
    console.log('Cart Preview Init:', {
        cartLinksFound: cartLinks.length,
        overlayFound: !!previewOverlay
    });
    
    if (!previewOverlay) {
        console.error('Overlay de vista del carrito no encontrado');
        return;
    }
    
    if (cartLinks.length === 0) {
        console.error('No se encontraron links al carrito');
        return;
    }
    
    cartLinks.forEach((cartLink, index) => {
        // mostrar vista previa al pasar el mouse sobre el link del carrito
        cartLink.addEventListener('mouseenter', function(e) {
            isOverCart = true;
            clearTimeout(hidePreviewTimeout);
            showCartPreview();
        });
        
        // ocultar la vista previa
        cartLink.addEventListener('mouseleave', function(e) {
            isOverCart = false;
            hidePreviewTimeout = setTimeout(() => {
                if (!isOverPreview) {
                    hideCartPreview();
                }
            }, 1000);
        });
    });
    
    // mantener la vista previa visible al tener el mouse adentro
    const previewContainer = previewOverlay.querySelector('.cart-preview-container');
    if (previewContainer) {
        previewContainer.addEventListener('mouseenter', function() {
            isOverPreview = true;
            clearTimeout(hidePreviewTimeout);
        });
        
        // quitar la vista previa
        previewContainer.addEventListener('mouseleave', function() {
            isOverPreview = false;
            hidePreviewTimeout = setTimeout(() => {
                if (!isOverCart) {
                    hideCartPreview();
                }
            }, 300);
        });
    }
    
    // quitar el preview cuando se haga click en el fondo del overlay
    previewOverlay.addEventListener('click', function(e) {
        if (e.target === previewOverlay) {
            hideCartPreview();
        }
    });
}

function showCartPreview() {
    const previewOverlay = document.getElementById('cartPreviewOverlay');
    if (previewOverlay) {
        previewOverlay.classList.add('active');
        
        if (!cartPreviewLoaded) {
            loadCartPreview();
            cartPreviewLoaded = true;
        }
    } else {
        console.error('Overlay del preview no encontrado en showCartPreview');
    }
}
// ocultar el preview
function hideCartPreview() {
    const previewOverlay = document.getElementById('cartPreviewOverlay');
    if (previewOverlay) {
        previewOverlay.classList.remove('active');
    }
}

function loadCartPreview() {
    const formData = new FormData();
    formData.append('action', 'preview');
    
    fetch('api/carrito.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderCartPreview(data.data);
        }
    })
    .catch(error => {
        console.error('Error cargando preview del carrito:', error);
        const body = document.getElementById('cartDropdownBody');
        if (body) {
            body.innerHTML = `
                <div class="cart-dropdown-empty">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Error al cargar el carrito</p>
                </div>
            `;
        }
    });
}

function renderCartPreview(cartData) {
    const body = document.getElementById('cartPreviewBody');
    const footer = document.getElementById('cartPreviewFooter');
    const totalElement = document.getElementById('cartPreviewTotal');
    const countElement = document.getElementById('cartPreviewCount');
    const verBolsaText = document.getElementById('verBolsaText');
    
    if (!body) return;
    
    // Actualizar el contandor en el header
    if (countElement) {
        countElement.textContent = cartData.count;
    }
    
    if (cartData.count === 0) {
        body.innerHTML = `
            <div class="cart-preview-empty">
                <i class="fas fa-shopping-cart"></i>
                <p>Tu carrito está vacío</p>
            </div>
        `;
        if (footer) footer.style.display = 'none';
        return;
    }
    
    // Solo mostrar los primeros 10 artículos en el preview, son suficientes la mayoria de los casos
    const displayItems = cartData.items.slice(0, 10);
    let itemsHTML = '';
    
    displayItems.forEach(item => {
        const imgHTML = item.Fotos ? 
            `<img src="data:image/jpeg;base64,${item.Fotos}" alt="${item.Nombre}" class="cart-preview-img">` :
            `<div class="cart-preview-img-placeholder"><i class="fas fa-bed"></i></div>`;
        
        const subtotal = item.Precio * item.Cantidad;
        
        itemsHTML += `
            <div class="cart-preview-item" data-cart-id="${item.ID_Carrito}">
                ${imgHTML}
                <div class="cart-preview-details">
                    <div class="cart-preview-name">${escapeHtml(item.Nombre)}</div>
                    <div class="cart-preview-meta">Color: ${item.Color || 'BLANCO CON OTRO'}</div>
                    <div class="cart-preview-quantity">Cantidad: ${item.Cantidad}</div>
                    <div class="cart-preview-price">${formatPrice(subtotal)}</div>
                </div>
                <button class="cart-preview-remove" onclick="removeFromCartPreview(${item.ID_Carrito})" title="Eliminar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    });
    
    body.innerHTML = itemsHTML;
    
    // Actualizar total
    if (totalElement) {
        totalElement.textContent = formatPrice(cartData.total);
    }
    
    // Actualizar el boton "Ver Bolsa"
    if (verBolsaText) {
        verBolsaText.textContent = `Ver Bolsa (${cartData.count})`;
    }
    
    // footer
    if (footer) {
        footer.style.display = 'block';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Eliminar artículo del preview del carrito
function removeFromCartPreview(idCarrito) {
    if (!confirm('¿Deseas eliminar este producto del carrito?')) {
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
            reloadCartPreview();
            updateCartBadge(data.data.cart_count || 0);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar producto');
    });
}

// volver a cargar el preview del carrito después de agregar un artículo
function reloadCartPreview() {
    cartPreviewLoaded = false;
    loadCartPreview();
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    initCartPreview();
});
