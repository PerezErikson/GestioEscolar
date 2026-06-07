<?php
include("conexion/conexion.php");

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

                        <form action="validar_login.php" method="POST">

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
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-4">
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>