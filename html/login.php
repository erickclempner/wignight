<?php
require_once 'config/database.php';

$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos';
    } else {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT ID_Usuario, Nombre_Usuario, Correo_Electronico, Contrasena, id_rol FROM Usuarios WHERE Correo_Electronico = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['Contrasena'])) {
                $_SESSION['user_id'] = $user['ID_Usuario'];
                $_SESSION['nombre_usuario'] = $user['Nombre_Usuario'];
                $_SESSION['id_rol'] = $user['id_rol'];
                
                // Merge guest cart to user account
                mergeGuestCartToUser($user['ID_Usuario']);
                
                // Redirect based on role
                if ($user['id_rol'] == 2) {
                    header("Location: admin.php");
                } else {
                    header("Location: perfil.php");
                }
                exit();
            } else {
                $error = 'Credenciales incorrectas';
            }
        } else {
            $error = 'Credenciales incorrectas';
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
    <title>Iniciar Sesión - WigNight</title>
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

    <!-- Login Section -->
    <section class="py-5">
        <div class="container">
            <div class="auth-container">
                <div class="text-center mb-4">
                    <div style="font-size: 4rem; color: var(--moon-glow); text-shadow: 0 0 30px rgba(244, 228, 193, 0.5);">
                        <i class="fas fa-moon"></i>
                    </div>
                    <h2 class="auth-title mb-2">
                        Bienvenido de Vuelta
                    </h2>
                    <p style="color: var(--accent-warm); font-size: 1rem;">
                        Inicia sesión para continuar tu viaje hacia el mejor descanso
                    </p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['register_success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['register_success']; ?>
                    </div>
                    <?php unset($_SESSION['register_success']); ?>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Correo Electrónico
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="tu@email.com" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Tu contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </button>
                </form>

                <hr style="border-color: var(--wood-accent); margin: 2rem 0;">

                <div class="text-center">
                    <p style="color: var(--accent-cream); margin-bottom: 1rem;">
                        ¿Aún no tienes una cuenta?
                    </p>
                    <a href="register.php" class="btn btn-secondary w-100 py-3">
                        <i class="fas fa-user-plus"></i> Crear Cuenta Nueva
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
