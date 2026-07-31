<?php
session_start();
include("conexion/conexion.php");

// ==========================================
// PROCESAR LOGIN SI SE ENVÍA EL FORMULARIO
// ==========================================
$error_login = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'login') {
    $correo = $_POST['correo'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';

    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Comparación directa (texto plano)
        if ($contraseña === $usuario['contraseña']) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['rol_id'] = $usuario['rol_id'];
            $_SESSION['nombre'] = $usuario['nombre'];

            header("Location: principal.php");
            exit();
        } else {
            $error_login = true;
        }
    } else {
        $error_login = true;
    }
}

// ==========================================
// OBTENER CONFIGURACIÓN DEL CENTRO EDUCATIVO
// ==========================================
$consulta_config = $conn->query("
    SELECT nombre_centro, logo
    FROM configuracion
    LIMIT 1
");

$datos_config = $consulta_config->fetch_assoc();

$nombre_centro = $datos_config['nombre_centro'] ?? 'SISTEMA DE GESTIÓN ESCOLAR';
$logo_centro = $datos_config['logo'] ?? '';

// Definir la ruta de la imagen
$ruta_logo = "images/login_escuela.png";

if (!empty($logo_centro) && file_exists("uploads/" . $logo_centro)) {
    $ruta_logo = "uploads/" . $logo_centro;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($nombre_centro); ?> - Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">

            <!-- Tarjeta Principal -->
            <div class="card border-0 shadow-lg overflow-hidden rounded-4 bg-white" style="min-height: 650px;">

                <div class="row g-0 h-100">

                    <!-- LOGO -->
                    <div class="col-md-6 bg-white d-flex align-items-center justify-content-center p-3 border-end">
                        <img
                            src="<?php echo $ruta_logo; ?>"
                            alt="Logo Institucional"
                            class="img-fluid"
                            style="max-width: 95%; max-height: 550px;"
                        >
                    </div>

                    <!-- FORMULARIO -->
                    <div class="col-md-6 p-4 p-md-5 d-flex flex-column justify-content-center">

                        <div class="mb-4 text-center text-md-start">
                            <h2 class="fw-bold text-dark text-uppercase mb-2">
                                <?php echo htmlspecialchars($nombre_centro); ?>
                            </h2>

                            <span class="text-muted small fw-semibold">
                                <i class="bi bi-shield-lock-fill text-primary me-1"></i>
                                Panel de Acceso Académico
                            </span>
                        </div>

                        <!-- ALERTA DE BOOTSTRAP PARA CONTRASEÑA INCORRECTA -->
                        <?php if ($error_login): ?>
                            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>¡Acceso denegado!</strong> Correo o contraseña incorrectos.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <input type="hidden" name="accion" value="login">

                            <!-- Correo -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small text-uppercase">
                                    Correo Electrónico
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope-fill"></i>
                                    </span>

                                    <input
                                        type="email"
                                        name="correo"
                                        id="correo"
                                        class="form-control border-start-0 bg-light py-2"
                                        placeholder="admin@admin.com"
                                        value="<?php echo htmlspecialchars($correo ?? ''); ?>"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small text-uppercase">
                                    Contraseña
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-key-fill"></i>
                                    </span>

                                    <input
                                        type="password"
                                        name="contraseña"
                                        id="contraseña"
                                        class="form-control border-start-0 bg-light py-2"
                                        placeholder="********"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Enlace para Cambiar Contraseña -->
                            <div class="mb-4 text-end">
                                <button type="button" class="btn btn-link p-0 text-decoration-none small fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCambiarPassword">
                                    ¿Olvidaste o quieres cambiar tu contraseña?
                                </button>
                            </div>

                            <!-- Botón -->
                            <button
                                type="submit"
                                class="btn btn-primary btn-lg w-100 fw-bold shadow-sm rounded-3"
                            >
                                Ingresar al Sistema
                                <i class="bi bi-box-arrow-in-right ms-1"></i>
                            </button>

                        </form>

                        <!-- Footer -->
                        <div class="mt-5 text-center text-muted small border-top pt-3">
                            © <?php echo date('Y'); ?> Sistema Escolar • La Vega, RD
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL PARA CAMBIAR CONTRASEÑA -->
<!-- ========================================== -->
<div class="modal fade" id="modalCambiarPassword" tabindex="-1" aria-labelledby="modalCambiarPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="modalCambiarPasswordLabel">
                    <i class="bi bi-key-fill me-2"></i> Cambiar Contraseña
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="procesar_cambio_password.php" method="POST">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Ingresa tu correo electrónico registrado y escribe tu nueva contraseña.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Correo Electrónico</label>
                        <input type="email" name="correo_recuperacion" class="form-control bg-light py-2" placeholder="admin@admin.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Nueva Contraseña</label>
                        <input type="password" name="nueva_contraseña" class="form-control bg-light py-2" placeholder="********" required>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Actualizar Contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>