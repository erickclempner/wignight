<?php
require_once 'config/database.php';

$error = '';

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $direccion = trim($_POST['direccion'] ?? '');
    
    if (empty($nombre) || empty($email) || empty($password) || empty($password_confirm)) {
        $error = 'Por favor, completa todos los campos requeridos';
    } elseif ($password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } else {
        $conn = getConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT ID_Usuario FROM Usuarios WHERE Correo_Electronico = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Este correo ya está registrado. <a href="login.php" style="color: var(--accent-amber);">Inicia sesión aquí</a>';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO Usuarios (Nombre_Usuario, Correo_Electronico, Contrasena, Fecha_Nacimiento, Direccion_Postal, id_rol) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("sssss", $nombre, $email, $hashed_password, $fecha_nacimiento, $direccion);
            
            if ($stmt->execute()) {
                // Get the new user ID
                $new_user_id = $conn->insert_id;
                
                // Auto-login the user
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_name'] = $nombre;
                $_SESSION['user_email'] = $email;
                $_SESSION['id_rol'] = 1; // Cliente
                
                // Merge guest cart if exists
                mergeGuestCartToUser($new_user_id);
                
                $_SESSION['register_success'] = 'Cuenta creada exitosamente. ¡Bienvenido a WigNight!';
                header("Location: perfil.php");
                exit();
            } else {
                $error = 'Error al crear la cuenta. Intenta nuevamente.';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - WigNight</title>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="login.php">
                            <i class="fas fa-user-circle"></i> Mi Cuenta
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Register Section -->
    <section class="py-5">
        <div class="container">
            <div class="auth-container">
                <div class="text-center mb-4">
                    <div style="font-size: 4rem; color: var(--moon-glow); text-shadow: 0 0 30px rgba(244, 228, 193, 0.5);">
                        <i class="fas fa-moon"></i>
                    </div>
                    <h2 class="auth-title mb-2">
                        Únete a WigNight
                    </h2>
                    <p style="color: var(--accent-warm); font-size: 1rem;">
                        Crea tu cuenta y comienza tu viaje hacia el mejor descanso de tu vida
                    </p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-user"></i> Nombre Completo *
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               placeholder="Tu nombre completo" 
                               value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" 
                               required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Correo Electrónico *
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="tu@email.com" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Contraseña *
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Mínimo 6 caracteres" required>
                        <small style="color: var(--wood-lighter); font-size: 0.85rem;">
                            <i class="fas fa-info-circle"></i> Usa al menos 6 caracteres para mayor seguridad
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">
                            <i class="fas fa-lock"></i> Confirmar Contraseña *
                        </label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                               placeholder="Confirma tu contraseña" required>
                    </div>
                    
                    <hr style="border-color: var(--wood-accent); margin: 1.5rem 0;">
                    
                    <p style="color: var(--accent-cream); font-size: 0.9rem; margin-bottom: 1rem;">
                        <i class="fas fa-star" style="color: var(--accent-gold);"></i> 
                        <strong>Opcional:</strong> Completa estos datos para una mejor experiencia
                    </p>
                    
                    <div class="mb-3">
                        <label for="fecha_nacimiento" class="form-label">
                            <i class="fas fa-calendar"></i> Fecha de Nacimiento
                        </label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                               value="<?php echo isset($_POST['fecha_nacimiento']) ? htmlspecialchars($_POST['fecha_nacimiento']) : ''; ?>">
                    </div>
                    <div class="mb-4">
                        <label for="direccion" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Dirección de Envío
                        </label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2" 
                                  placeholder="Tu dirección completa para envíos"><?php echo isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-3">
                        <i class="fas fa-user-plus"></i> Crear Mi Cuenta
                    </button>
                </form>

                <hr style="border-color: var(--wood-accent); margin: 2rem 0;">

                <div class="text-center">
                    <p style="color: var(--accent-cream); margin-bottom: 1rem;">
                        ¿Ya tienes una cuenta?
                    </p>
                    <a href="login.php" class="btn btn-secondary w-100 py-3">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p class="mb-0">
                &copy; 2025 WigNight. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
