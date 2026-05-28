<?php
include(__DIR__ . "/../conexion/conexion.php");

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
        // 2000-05-19 => 19052000
        // ==========================================

        $partes = explode("-", $fecha_nacimiento);

        $contraseña =
            $partes[2] .
            $partes[1] .
            $partes[0];

        // GUARDAR CONTRASEÑA NORMAL
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

            echo "
            <script>
                alert('⚠️ Ya existe un usuario con ese correo');
            </script>
            ";

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

                echo "
                <script>
                    alert('✅ Usuario registrado correctamente');
                    alert('🔑 Contraseña generada: $contraseña');
                </script>
                ";

            } else {

                echo "
                <script>
                    alert('❌ Error al guardar usuario');
                </script>
                ";
            }
        }

    } else {

        echo "
        <script>
            alert('⚠️ Usuario no encontrado');
        </script>
        ";
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
        Gestión de Usuarios
    </h3>

    <!-- REGISTRAR -->
    <div class="card shadow-sm p-4 mb-4">

        <h4 class="text-center mb-4">
            Registrar nuevo usuario
        </h4>

        <form method="POST" class="row g-3">

            <input type="hidden"
                   name="accion"
                   value="guardar">

            <!-- ROL -->
            <div class="col-md-4">

                <label class="form-label fw-bold">
                    Rol
                </label>

                <select name="rol"
                        id="rol"
                        class="form-select"
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
            <div class="col-md-4">

                <label class="form-label fw-bold">
                    Nombre y Apellido
                </label>

                <select name="usuario_id"
                        id="usuario_id"
                        class="form-select"
                        required>

                    <option value="">
                        Seleccione usuario
                    </option>

                </select>

            </div>

            <!-- BOTÓN -->
            <div class="col-md-4 d-flex align-items-end">

                <button type="submit"
                        class="btn btn-success w-100">

                    Guardar usuario

                </button>

            </div>

        </form>

    </div>

    <!-- TABLA -->
    <div class="card shadow-sm p-4">

        <h4 class="text-center mb-4">
            Usuarios registrados
        </h4>

        <table class="table table-striped table-hover">

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