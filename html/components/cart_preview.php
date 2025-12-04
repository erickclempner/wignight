<!-- componente del preview del carrito cuando pones el mouse arriba del logo en el header -->
<div class="cart-preview-overlay" id="cartPreviewOverlay">
    <div class="cart-preview-container">
        <div class="cart-preview-header">
            <h6>Cantidad: <span id="cartPreviewCount">0</span></h6>
        </div>
        
        <div class="cart-preview-body" id="cartPreviewBody">
            <div class="cart-preview-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Cargando...</p>
            </div>
        </div>
        
        <div class="cart-preview-footer" id="cartPreviewFooter" style="display:none;">
            <div class="cart-preview-total-section">
                <div class="cart-preview-total">
                    <span>Total:</span>
                    <span class="cart-preview-total-amount" id="cartPreviewTotal">$0.00</span>
                </div>
            </div>
            
            <div class="cart-preview-actions">
                <a href="carrito.php" class="cart-preview-btn cart-preview-btn-primary">
                    <i class="fas fa-shopping-bag"></i> <span id="verBolsaText">Ver Bolsa (0)</span>
                </a>
                <a href="carrito.php" class="cart-preview-btn cart-preview-btn-secondary">
                    <i class="fas fa-lock"></i> Iniciar Compra Segura
                </a>
            </div>
            
            <div class="cart-preview-payment-icons">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 32'%3E%3Crect fill='%23f90' width='48' height='32' rx='4'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='white' font-family='Arial' font-weight='bold' font-size='10'%3ECard%3C/text%3E%3C/svg%3E" alt="Credit Card" title="Tarjetas de crédito">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 32'%3E%3Crect fill='%230070ba' width='48' height='32' rx='4'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='white' font-family='Arial' font-weight='bold' font-size='8'%3EPayPal%3C/text%3E%3C/svg%3E" alt="PayPal" title="PayPal">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 32'%3E%3Crect fill='%2300a3e0' width='48' height='32' rx='4'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='white' font-family='Arial' font-weight='bold' font-size='8'%3EMaestro%3C/text%3E%3C/svg%3E" alt="Maestro" title="Maestro">
            </div>
            
            <div class="cart-preview-security-badges">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 40'%3E%3Crect fill='white' width='60' height='40' rx='4' stroke='%23ccc'/%3E%3Cpath d='M20,15 L20,25 M40,15 L40,25' stroke='%23333' stroke-width='2'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%23333' font-family='Arial' font-size='8'%3ESecure%3C/text%3E%3C/svg%3E" alt="Verificado" title="Compra Segura">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 40'%3E%3Crect fill='white' width='60' height='40' rx='4' stroke='%23ccc'/%3E%3Ccircle cx='30' cy='20' r='8' fill='none' stroke='%23e63946' stroke-width='2'/%3E%3Cpath d='M27,20 L29,22 L33,18' stroke='%23e63946' stroke-width='2' fill='none'/%3E%3Ctext x='50%25' y='85%25' text-anchor='middle' fill='%23333' font-family='Arial' font-size='6'%3ECertified%3C/text%3E%3C/svg%3E" alt="McAfee" title="Certificado de seguridad">
            </div>
        </div>
    </div>
</div>
