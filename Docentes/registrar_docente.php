<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conexion/conexion.php");

// Verificar si el usuario es administrador
if ($_SESSION['rol_id'] != 1) {

    echo "
    <div class='container mt-4'>

        <div class='alert alert-danger shadow-sm border-0 rounded-4 text-center p-4'>

            <i class='bi bi-shield-lock-fill fs-1 d-block mb-3'></i>

            <strong>
                Acceso denegado.
            </strong>

            <br>

            Solo el administrador puede registrar docentes.

        </div>

    </div>
    ";

    exit();
}

$mensaje = "";
$tipoMensaje = "";

// ==========================================
// GUARDAR NUEVO DOCENTE
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre             = trim($_POST['nombre']);
    $apellido           = trim($_POST['apellido']);
    $correo             = trim($_POST['correo']);
    $cedula             = trim($_POST['cedula']);
    $fecha_nacimiento   = $_POST['fecha_nacimiento'];
    $titulo             = trim($_POST['titulo']);
    $direccion          = trim($_POST['direccion']);
    $telefono           = trim($_POST['telefono']);
    $estado_civil       = trim($_POST['estado_civil']);
    $anos_servicio      = intval($_POST['anos_servicio']);

    // ==========================================
    // VALIDAR EDAD MÍNIMA
    // ==========================================
    $fecha_nac = new DateTime($fecha_nacimiento);

    $hoy = new DateTime();

    $edad = $hoy->diff($fecha_nac)->y;

    if ($edad < 18) {

        $mensaje =
            "El docente debe tener al menos 18 años para registrarse.";

        $tipoMensaje =
            "warning";

    } else {

        // ==========================================
        // VERIFICAR CÉDULA
        // ==========================================
        $check = $conn->prepare("
            SELECT id
            FROM docente
            WHERE cedula = ?
        ");

        $check->bind_param(
            "s",
            $cedula
        );

        $check->execute();

        $check->store_result();

        if ($check->num_rows > 0) {

            $mensaje =
                "Ya existe un docente registrado con esa cédula.";

            $tipoMensaje =
                "warning";

        } else {

            // ==========================================
            // VERIFICAR CORREO
            // ==========================================
            $correoCheck = $conn->prepare("
                SELECT id
                FROM docente
                WHERE correo = ?
            ");

            $correoCheck->bind_param(
                "s",
                $correo
            );

            $correoCheck->execute();

            $correoCheck->store_result();

            if ($correoCheck->num_rows > 0) {

                $mensaje =
                    "Ya existe un docente registrado con ese correo.";

                $tipoMensaje =
                    "warning";

            } else {

                // ==========================================
                // INSERTAR DOCENTE
                // ==========================================
                $sql = "
                    INSERT INTO docente
                    (
                        nombre,
                        apellido,
                        correo,
                        cedula,
                        fecha_nacimiento,
                        titulo,
                        direccion,
                        telefono,
                        estado_civil,
                        anos_servicio
                    )
                    VALUES
                    (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )
                ";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "sssssssssi",
                    $nombre,
                    $apellido,
                    $correo,
                    $cedula,
                    $fecha_nacimiento,
                    $titulo,
                    $direccion,
                    $telefono,
                    $estado_civil,
                    $anos_servicio
                );

                if ($stmt->execute()) {

                    $mensaje =
                        "Docente registrado correctamente.";

                    $tipoMensaje =
                        "success";

                } else {

                    $mensaje =
                        "Error al registrar el docente.";

                    $tipoMensaje =
                        "danger";
                }
            }
        }
    }
}
?>

<div class="container mt-4">

    <h3 class="mb-4 text-primary">

        <i class="bi bi-person-badge-fill"></i>

        Registrar Docente

    </h3>

    <!-- ALERTAS -->
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

    <!-- FORMULARIO -->
    <div class="card shadow-sm border-0 rounded-4 p-4">

        <h5 class="mb-4">

            Datos del docente

        </h5>

        <form method="POST" class="row g-3">

            <!-- NOMBRE -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">

                    Nombre

                </label>

                <input type="text"
                       name="nombre"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- APELLIDO -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">

                    Apellidos

                </label>

                <input type="text"
                       name="apellido"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- CORREO -->
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

            <!-- CÉDULA -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">

                    Cédula

                </label>

                <input type="text"
                       name="cedula"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- FECHA NACIMIENTO -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">

                    Fecha de nacimiento

                </label>

                <input type="date"
                       name="fecha_nacimiento"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- TÍTULO -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">

                    Último título alcanzado

                </label>

                <input type="text"
                       name="titulo"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- DIRECCIÓN -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">

                    Dirección

                </label>

                <input type="text"
                       name="direccion"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- TELÉFONO -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">

                    Teléfono

                </label>

                <input type="text"
                       name="telefono"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- ESTADO CIVIL -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">

                    Estado civil

                </label>

                <select name="estado_civil"
                        class="form-select rounded-3"
                        required>

                    <option value="">
                        Seleccione...
                    </option>

                    <option value="Soltero">
                        Soltero
                    </option>

                    <option value="Casado">
                        Casado
                    </option>

                    <option value="Divorciado">
                        Divorciado
                    </option>

                    <option value="Viudo">
                        Viudo
                    </option>

                </select>

            </div>

            <!-- AÑOS SERVICIO -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">

                    Años en servicio

                </label>

                <input type="number"
                       name="anos_servicio"
                       class="form-control rounded-3"
                       min="0"
                       required>

            </div>

            <!-- BOTÓN -->
            <div class="col-12 text-end">

                <button type="submit"
                        class="btn btn-primary rounded-3 px-4">

                    <i class="bi bi-save"></i>

                    Guardar docente

                </button>

            </div>

        </form>

    </div>

</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>