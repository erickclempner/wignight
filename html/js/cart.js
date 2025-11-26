// Cart functionality - shared across pages

function formatPrice(price) {
    return '$' + parseFloat(price).toFixed(2);
}

function updateCartBadge(count) {
    // Update all cart badges in navbar
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

// Cart Preview Management
let cartPreviewLoaded = false;
let hidePreviewTimeout = null;
let isOverCart = false;
let isOverPreview = false;

function initCartPreview() {
    // Find cart links - more flexible selector
    const cartLinks = document.querySelectorAll('a[href="carrito.php"], a.nav-link[href*="carrito"]');
    const previewOverlay = document.getElementById('cartPreviewOverlay');
    
    console.log('Cart Preview Init:', {
        cartLinksFound: cartLinks.length,
        overlayFound: !!previewOverlay
    });
    
    if (!previewOverlay) {
        console.error('Cart preview overlay not found!');
        return;
    }
    
    if (cartLinks.length === 0) {
        console.error('No cart links found!');
        return;
    }
    
    cartLinks.forEach((cartLink, index) => {
        console.log(`Setting up hover for cart link ${index + 1}`);
        
        // Show preview when hovering over cart link
        cartLink.addEventListener('mouseenter', function(e) {
            console.log('Mouse entered cart link');
            isOverCart = true;
            clearTimeout(hidePreviewTimeout);
            showCartPreview();
        });
        
        // Hide preview when leaving cart link (with delay)
        cartLink.addEventListener('mouseleave', function(e) {
            console.log('Mouse left cart link');
            isOverCart = false;
            hidePreviewTimeout = setTimeout(() => {
                if (!isOverPreview) {
                    hideCartPreview();
                }
            }, 300);
        });
    });
    
    // Keep preview open when hovering over it
    previewOverlay.addEventListener('mouseenter', function() {
        console.log('Mouse entered preview overlay');
        isOverPreview = true;
        clearTimeout(hidePreviewTimeout);
    });
    
    // Hide preview when leaving overlay
    previewOverlay.addEventListener('mouseleave', function() {
        console.log('Mouse left preview overlay');
        isOverPreview = false;
        hidePreviewTimeout = setTimeout(() => {
            if (!isOverCart) {
                hideCartPreview();
            }
        }, 300);
    });
    
    // Hide preview when clicking on overlay background (not on the container)
    previewOverlay.addEventListener('click', function(e) {
        if (e.target === previewOverlay) {
            hideCartPreview();
        }
    });
    
    console.log('Cart preview initialized successfully');
}

function showCartPreview() {
    console.log('showCartPreview called');
    const previewOverlay = document.getElementById('cartPreviewOverlay');
    if (previewOverlay) {
        console.log('Adding active class to overlay');
        previewOverlay.classList.add('active');
        
        if (!cartPreviewLoaded) {
            console.log('Loading cart preview data');
            loadCartPreview();
            cartPreviewLoaded = true;
        }
    } else {
        console.error('Preview overlay not found in showCartPreview');
    }
}

function hideCartPreview() {
    console.log('hideCartPreview called');
    const previewOverlay = document.getElementById('cartPreviewOverlay');
    if (previewOverlay) {
        console.log('Removing active class from overlay');
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
        console.error('Error loading cart preview:', error);
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
    
    // Update count in header
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
    
    // Show only first 3 items
    const displayItems = cartData.items.slice(0, 3);
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
    
    // Update total
    if (totalElement) {
        totalElement.textContent = formatPrice(cartData.total);
    }
    
    // Update "Ver Bolsa" button text
    if (verBolsaText) {
        verBolsaText.textContent = `Ver Bolsa (${cartData.count})`;
    }
    
    // Show footer
    if (footer) {
        footer.style.display = 'block';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Remove item from cart preview
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

// Reload cart preview after adding item
function reloadCartPreview() {
    cartPreviewLoaded = false;
    loadCartPreview();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initCartPreview();
});
