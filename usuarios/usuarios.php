<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipoMensaje = "";

// ==========================================
// AJAX - CARGAR USUARIOS SEGÚN EL ROL
// ==========================================
if (isset($_GET['rol'])) {

    $rol = $_GET['rol'];

    $usuarios = [];

    switch ($rol) {

        case 'estudiante':

            $result = $conn->query("
                SELECT id, nombre, apellido
                FROM estudiantes
                ORDER BY nombre ASC
            ");

        break;

        case 'docente':

            $result = $conn->query("
                SELECT id, nombre, apellido
                FROM docente
                ORDER BY nombre ASC
            ");

        break;

        case 'administrador':

            $result = $conn->query("
                SELECT id, nombre, apellido
                FROM administrador
                ORDER BY nombre ASC
            ");

        break;

        default:

            $result = false;
    }

    if ($result) {

        while ($row = $result->fetch_assoc()) {

            $usuarios[] = $row;
        }
    }

    header('Content-Type: application/json');

    echo json_encode($usuarios);

    exit();
}

// ==========================================
// GUARDAR USUARIO
// ==========================================
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['accion'])
    && $_POST['accion'] === 'guardar'
) {

    $rol = $_POST['rol'];

    $usuario_id = intval($_POST['usuario_id']);

    switch ($rol) {

        case 'estudiante':

            $query = $conn->query("
                SELECT nombre, apellido, correo, fecha_nacimiento
                FROM estudiantes
                WHERE id = $usuario_id
            ");

        break;

        case 'docente':

            $query = $conn->query("
                SELECT nombre, apellido, correo, fecha_nacimiento
                FROM docente
                WHERE id = $usuario_id
            ");

        break;

        case 'administrador':

            $query = $conn->query("
                SELECT nombre, apellido, correo, fecha_nacimiento
                FROM administrador
                WHERE id = $usuario_id
            ");

        break;

        default:

            $query = false;
    }

    if ($query && $query->num_rows > 0) {

        $data = $query->fetch_assoc();

        $nombre =
            $data['nombre'] .
            " " .
            $data['apellido'];

        $correo =
            $data['correo'];

        $fecha_nacimiento =
            $data['fecha_nacimiento'];

        // ==========================================
        // GENERAR CONTRASEÑA
        // ==========================================

        $partes = explode("-", $fecha_nacimiento);

        $contraseña =
            $partes[2] .
            $partes[1] .
            $partes[0];

        $contraseña_guardar = $contraseña;

        // ==========================================
        // CONVERTIR ROL A ID
        // ==========================================

        switch ($rol) {

            case 'administrador':
                $rol_id = 1;
            break;

            case 'docente':
                $rol_id = 2;
            break;

            case 'estudiante':
                $rol_id = 3;
            break;

            default:
                $rol_id = 0;
        }

        // ==========================================
        // VERIFICAR SI YA EXISTE
        // ==========================================

        $verificar = $conn->prepare("
            SELECT id
            FROM usuarios
            WHERE correo = ?
        ");

        $verificar->bind_param(
            "s",
            $correo
        );

        $verificar->execute();

        $resultado =
            $verificar->get_result();

        if ($resultado->num_rows > 0) {

            $mensaje =
                "Ya existe un usuario con ese correo.";

            $tipoMensaje =
                "warning";

        } else {

            // ==========================================
            // INSERTAR USUARIO
            // ==========================================

            $stmt = $conn->prepare("
                INSERT INTO usuarios
                (
                    nombre,
                    correo,
                    contraseña,
                    rol_id
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "sssi",
                $nombre,
                $correo,
                $contraseña_guardar,
                $rol_id
            );

            if ($stmt->execute()) {

                $mensaje =
                    "Usuario registrado correctamente. Contraseña generada: $contraseña";

                $tipoMensaje =
                    "success";

            } else {

                $mensaje =
                    "Error al guardar usuario.";

                $tipoMensaje =
                    "danger";
            }
        }

    } else {

        $mensaje =
            "Usuario no encontrado.";

        $tipoMensaje =
            "warning";
    }
}

// ==========================================
// OBTENER USUARIOS REGISTRADOS
// ==========================================

$usuarios = $conn->query("
    SELECT 
        usuarios.nombre,
        usuarios.correo,
        roles.nombre AS rol
    FROM usuarios
    INNER JOIN roles
        ON usuarios.rol_id = roles.id
    ORDER BY usuarios.nombre ASC
");
?>

<div class="container mt-4">

    <h3 class="mb-4 text-primary">
        <i class="bi bi-people-fill"></i>
        Gestión de Usuarios
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

                <strong><?php echo $mensaje; ?></strong>

            </div>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php } ?>

    <!-- FORMULARIO -->
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">

        <h5 class="mb-4">
            Registrar nuevo usuario
        </h5>

        <form method="POST" class="row g-3">

            <input type="hidden"
                   name="accion"
                   value="guardar">

            <!-- ROL -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Rol
                </label>

                <select name="rol"
                        id="rol"
                        class="form-select rounded-3"
                        required
                        onchange="cargarUsuarios(this.value)">

                    <option value="">
                        Seleccione un rol
                    </option>

                    <option value="administrador">
                        Administrador
                    </option>

                    <option value="docente">
                        Docente
                    </option>

                    <option value="estudiante">
                        Estudiante
                    </option>

                </select>

            </div>

            <!-- USUARIO -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Nombre y Apellido
                </label>

                <select name="usuario_id"
                        id="usuario_id"
                        class="form-select rounded-3"
                        required>

                    <option value="">
                        Seleccione usuario
                    </option>

                </select>

            </div>

            <!-- BOTÓN -->
            <div class="col-12 text-end">

                <button type="submit"
                        class="btn btn-primary rounded-3 px-4">

                    <i class="bi bi-save"></i>
                    Guardar usuario

                </button>

            </div>

        </form>

    </div>

    <!-- TABLA -->
    <div class="card shadow-sm border-0 rounded-4 p-4">

        <h5 class="mb-4">
            Usuarios registrados
        </h5>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = $usuarios->fetch_assoc()) { ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($row['nombre']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['correo']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['rol']); ?>
                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- SCRIPT AJAX -->
<script>

function cargarUsuarios(rol) {

    const selectUsuario =
        document.getElementById('usuario_id');

    selectUsuario.innerHTML =
        '<option value="">Cargando...</option>';

    fetch('usuarios/usuarios.php?rol=' + rol)

    .then(response => response.json())

    .then(data => {

        selectUsuario.innerHTML =
            '<option value="">Seleccione usuario</option>';

        data.forEach(item => {

            selectUsuario.innerHTML += `
                <option value="${item.id}">
                    ${item.nombre} ${item.apellido}
                </option>
            `;
        });

    })

    .catch(error => {

        console.log(error);

        selectUsuario.innerHTML =
            '<option value="">Error cargando usuarios</option>';
    });

}

</script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>