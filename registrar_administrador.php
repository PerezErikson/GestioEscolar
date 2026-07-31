<?php

// ==========================================
// INICIAR SESIÓN
// ==========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// CONEXIÓN A LA BASE DE DATOS
// ==========================================
include("conexion/conexion.php");

// ==========================================
// VERIFICAR SI EL USUARIO ES ADMINISTRADOR
// ==========================================
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {

    echo "
    <div class='container mt-4'>
        <div class='alert alert-danger shadow-sm border-0 rounded-4 text-center p-4'>
            <i class='bi bi-shield-lock-fill fs-1 d-block mb-3'></i>
            <strong>Acceso denegado.</strong>
            <br>
            No tienes permisos para registrar administradores.
        </div>
    </div>
    ";

    exit();
}

// ==========================================
// VARIABLES PARA MENSAJES
// ==========================================
$mensaje = "";
$tipoMensaje = "";

// ==========================================
// GUARDAR NUEVO ADMINISTRADOR
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ==========================================
    // OBTENER DATOS DEL FORMULARIO
    // ==========================================
    $nombre           = trim($_POST['nombre']);
    $apellido         = trim($_POST['apellido']);
    $correo           = trim($_POST['correo']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    // ==========================================
    // VALIDAR EDAD MÍNIMA (18 AÑOS)
    // ==========================================
    $fecha_nac = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;

    if ($edad < 18) {

        $mensaje = "El administrador debe tener al menos 18 años para registrarse.";
        $tipoMensaje = "warning";

    } else {

        // ==========================================
        // VERIFICAR SI EL CORREO YA EXISTE
        // ==========================================
        $correoCheck = $conn->prepare("
            SELECT id
            FROM administrador
            WHERE correo = ?
        ");

        $correoCheck->bind_param("s", $correo);
        $correoCheck->execute();
        $correoCheck->store_result();

        if ($correoCheck->num_rows > 0) {

            $mensaje = "Ya existe un administrador registrado con ese correo.";
            $tipoMensaje = "warning";

        } else {

            // ==========================================
            // INSERTAR NUEVO ADMINISTRADOR
            // (Ajustado exactamente a los campos de tu tabla)
            // ==========================================
            $sql = "
                INSERT INTO administrador
                (
                    nombre,
                    apellido,
                    correo,
                    fecha_nacimiento
                )
                VALUES
                (
                    ?, ?, ?, ?
                )
            ";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssss",
                $nombre,
                $apellido,
                $correo,
                $fecha_nacimiento
            );

            // ==========================================
            // EJECUTAR INSERT
            // ==========================================
            if ($stmt->execute()) {

                $mensaje = "Administrador registrado correctamente.";
                $tipoMensaje = "success";

            } else {

                $mensaje = "Error al registrar el administrador.";
                $tipoMensaje = "danger";
            }
        }
    }
}
?>

<!-- ========================================== -->
<!-- CONTENEDOR PRINCIPAL -->
<!-- ========================================== -->
<div class="container mt-4">

    <!-- TÍTULO -->
    <h3 class="mb-4 text-primary">
        <i class="bi bi-shield-shaded"></i>
        Registrar Administrador
    </h3>

    <!-- ========================================== -->
    <!-- ALERTAS -->
    <!-- ========================================== -->
    <?php if (!empty($mensaje)) { ?>

        <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4"
             role="alert">

            <div class="d-flex align-items-center">

                <i class="bi 
                    <?php
                        if($tipoMensaje == 'success') echo 'bi-check-circle-fill';
                        elseif($tipoMensaje == 'warning') echo 'bi-exclamation-triangle-fill';
                        elseif($tipoMensaje == 'danger') echo 'bi-trash-fill';
                        else echo 'bi-info-circle-fill';
                    ?>
                    me-2 fs-5">
                </i>

                <strong>
                    <?php echo $mensaje; ?>
                </strong>

            </div>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php } ?>

    <!-- ========================================== -->
    <!-- FORMULARIO -->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0 rounded-4 p-4">

        <h5 class="mb-4">
            Datos del administrador
        </h5>

        <form method="POST" class="row g-3">

            <!-- ========================================== -->
            <!-- NOMBRE -->
            <!-- ========================================== -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Nombre
                </label>

                <input type="text"
                       name="nombre"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- ========================================== -->
            <!-- APELLIDO -->
            <!-- ========================================== -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Apellidos
                </label>

                <input type="text"
                       name="apellido"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- ========================================== -->
            <!-- CORREO -->
            <!-- ========================================== -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Correo electrónico
                </label>

                <input type="email"
                       name="correo"
                       class="form-control rounded-3"
                       placeholder="ejemplo@correo.com"
                       required>

            </div>

            <!-- ========================================== -->
            <!-- FECHA NACIMIENTO -->
            <!-- ========================================== -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Fecha de nacimiento
                </label>

                <input type="date"
                       name="fecha_nacimiento"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- ========================================== -->
            <!-- BOTÓN -->
            <!-- ========================================== -->
            <div class="col-12 text-end mt-4">

                <button type="submit"
                        class="btn btn-primary rounded-3 px-4">

                    <i class="bi bi-save"></i>
                    Guardar administrador

                </button>

            </div>

        </form>

    </div>

</div>

<!-- ========================================== -->
<!-- BOOTSTRAP -->
<!-- ========================================== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>