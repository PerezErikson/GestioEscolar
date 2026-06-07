<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conexion/conexion.php");

// ==========================================
// VALIDAR ACCESO
// ==========================================
if ($_SESSION['rol_id'] != 1) {

    echo "
    <div class='container mt-4'>

        <div class='alert alert-danger shadow-sm border-0 rounded-4 text-center p-4'>

            <i class='bi bi-shield-lock-fill fs-1 d-block mb-3'></i>

            <strong>
                Acceso denegado.
            </strong>

            <br>

            Solo el administrador puede gestionar docentes.

        </div>

    </div>
    ";

    exit();
}

$mensaje = "";
$tipoMensaje = "";

// ==========================================
// ACTUALIZAR ESTADO DEL DOCENTE
// ==========================================
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['docente_id'])
    && isset($_POST['estado'])
) {

    $docente_id = intval($_POST['docente_id']);

    $estado = $_POST['estado'];

    $update = $conn->prepare("
        UPDATE docente
        SET estado = ?
        WHERE id = ?
    ");

    $update->bind_param(
        "si",
        $estado,
        $docente_id
    );

    if ($update->execute()) {

        $mensaje =
            "Estado del docente actualizado correctamente.";

        $tipoMensaje =
            "success";

    } else {

        $mensaje =
            "Error al actualizar el estado del docente.";

        $tipoMensaje =
            "danger";
    }
}

// ==========================================
// OBTENER DOCENTES
// ==========================================
$result = $conn->query("
    SELECT
        id,
        nombre,
        apellido,
        correo,
        cedula,
        telefono,
        estado
    FROM docente
    ORDER BY nombre ASC
");
?>

<div class="container mt-4">

    <h3 class="mb-4 text-primary">

        <i class="bi bi-people-fill"></i>

        Gestión de Docentes

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

    <!-- TABLA -->
    <div class="card shadow-sm border-0 rounded-4 p-4">

        <h5 class="mb-4">

            Lista de docentes registrados

        </h5>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Estado laboral</th>
                        <th class="text-center">Acción</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = $result->fetch_assoc()) { ?>

                    <tr>

                        <td>

                            <?php echo htmlspecialchars($row['nombre']); ?>

                        </td>

                        <td>

                            <?php echo htmlspecialchars($row['apellido']); ?>

                        </td>

                        <td>

                            <?php echo htmlspecialchars($row['correo']); ?>

                        </td>

                        <td>

                            <?php echo htmlspecialchars($row['cedula']); ?>

                        </td>

                        <td>

                            <?php echo htmlspecialchars($row['telefono']); ?>

                        </td>

                        <td>

                            <span class="badge rounded-pill px-3 py-2
                                <?php
                                    if ($row['estado'] == 'Activo') {
                                        echo 'bg-success';
                                    }
                                    elseif ($row['estado'] == 'Inactivo') {
                                        echo 'bg-secondary';
                                    }
                                    elseif ($row['estado'] == 'Vacaciones') {
                                        echo 'bg-warning text-dark';
                                    }
                                    else {
                                        echo 'bg-danger';
                                    }
                                ?>">

                                <?php echo htmlspecialchars($row['estado']); ?>

                            </span>

                        </td>

                        <td class="text-center">

                            <!-- BOTÓN MODAL -->
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm rounded-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#estadoModal<?php echo $row['id']; ?>">

                                <i class="bi bi-pencil-square"></i>

                            </button>

                        </td>

                    </tr>

                    <!-- MODAL -->
                    <div class="modal fade"
                         id="estadoModal<?php echo $row['id']; ?>"
                         tabindex="-1"
                         aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content border-0 shadow rounded-4">

                                <form method="POST">

                                    <div class="modal-header bg-primary text-white rounded-top-4">

                                        <h5 class="modal-title">

                                            <i class="bi bi-pencil-square"></i>

                                            Actualizar Estado

                                        </h5>

                                        <button type="button"
                                                class="btn-close btn-close-white"
                                                data-bs-dismiss="modal">
                                        </button>

                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden"
                                               name="docente_id"
                                               value="<?php echo $row['id']; ?>">

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">

                                                Docente

                                            </label>

                                            <input type="text"
                                                   class="form-control rounded-3"
                                                   value="<?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?>"
                                                   disabled>

                                        </div>

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">

                                                Estado laboral

                                            </label>

                                            <select name="estado"
                                                    class="form-select rounded-3"
                                                    required>

                                                <option value="Activo"
                                                    <?php if($row['estado']=='Activo') echo 'selected'; ?>>

                                                    Activo

                                                </option>

                                                <option value="Inactivo"
                                                    <?php if($row['estado']=='Inactivo') echo 'selected'; ?>>

                                                    Inactivo

                                                </option>

                                                <option value="Vacaciones"
                                                    <?php if($row['estado']=='Vacaciones') echo 'selected'; ?>>

                                                    Vacaciones

                                                </option>

                                                <option value="Despedido"
                                                    <?php if($row['estado']=='Despedido') echo 'selected'; ?>>

                                                    Despedido

                                                </option>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="button"
                                                class="btn btn-light border rounded-3"
                                                data-bs-dismiss="modal">

                                            Cancelar

                                        </button>

                                        <button type="submit"
                                                class="btn btn-primary rounded-3">

                                            <i class="bi bi-check-circle"></i>

                                            Actualizar

                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>