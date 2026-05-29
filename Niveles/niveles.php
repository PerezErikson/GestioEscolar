
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conexion/conexion.php");

$mensaje = "";
$tipoMensaje = "";

// =========================
// GUARDAR NIVEL
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar') {

    $nombre = trim($_POST['nombre']);

    if ($nombre !== '') {

        // Verificar duplicado
        $sql = "
            SELECT COUNT(*) AS total
            FROM niveles
            WHERE LOWER(nombre) = LOWER(?)
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        if ($result['total'] > 0) {

            $mensaje = "El nivel \"$nombre\" ya está registrado.";
            $tipoMensaje = "warning";

        } else {

            $stmt = $conn->prepare("
                INSERT INTO niveles (nombre)
                VALUES (?)
            ");

            $stmt->bind_param("s", $nombre);

            if ($stmt->execute()) {

                $mensaje = "Nivel registrado correctamente.";
                $tipoMensaje = "success";

            } else {

                $mensaje = "Error al registrar el nivel.";
                $tipoMensaje = "danger";
            }
        }
    }
}

// =========================
// ACTUALIZAR NIVEL
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);

    // Verificar duplicado
    $sql = "
        SELECT COUNT(*) AS total
        FROM niveles
        WHERE LOWER(nombre) = LOWER(?)
        AND id != ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nombre, $id);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    if ($result['total'] > 0) {

        $mensaje = "El nivel \"$nombre\" ya está registrado.";
        $tipoMensaje = "warning";

    } else {

        $stmt = $conn->prepare("
            UPDATE niveles
            SET nombre=?
            WHERE id=?
        ");

        $stmt->bind_param("si", $nombre, $id);

        if ($stmt->execute()) {

            $mensaje = "Nivel actualizado correctamente.";
            $tipoMensaje = "info";

        } else {

            $mensaje = "Error al actualizar el nivel.";
            $tipoMensaje = "danger";
        }
    }
}

// =========================
// ELIMINAR NIVEL
// =========================
if (isset($_GET['eliminar'])) {

    $id = intval($_GET['eliminar']);

    $stmt = $conn->prepare("
        DELETE FROM niveles
        WHERE id=?
    ");

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        $mensaje = "Nivel eliminado correctamente.";
        $tipoMensaje = "danger";

    } else {

        $mensaje = "Error al eliminar el nivel.";
        $tipoMensaje = "warning";
    }
}

// =========================
// OBTENER NIVELES
// =========================
$niveles = $conn->query("
    SELECT *
    FROM niveles
    ORDER BY id ASC
");
?>

<div class="container mt-4">

    <h3 class="mb-4 text-primary">
        <i class="bi bi-layers"></i>
        Gestión de Niveles
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
            Registrar nuevo nivel
        </h5>

        <form method="POST" class="row g-3">

            <input type="hidden"
                   name="accion"
                   value="guardar">

            <div class="col-md-12">

                <label class="form-label fw-semibold">
                    Nombre del nivel
                </label>

                <input type="text"
                       name="nombre"
                       class="form-control rounded-3"
                       placeholder="Ejemplo: Primaria"
                       required>

            </div>

            <div class="col-12 text-end">

                <button type="submit"
                        class="btn btn-primary rounded-3 px-4">

                    <i class="bi bi-save"></i>
                    Guardar nivel

                </button>

            </div>

        </form>

    </div>

    <!-- TABLA -->
    <div class="card shadow-sm border-0 rounded-4 p-4">

        <h5 class="mb-4">
            Lista de niveles registrados
        </h5>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>Nombre</th>
                        <th class="text-center">Acciones</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = $niveles->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($row['nombre']); ?>
                        </td>

                        <td class="text-center">

                            <!-- EDITAR -->
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm rounded-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editarModal<?php echo $row['id']; ?>">

                                <i class="bi bi-pencil-square"></i>

                            </button>

                            <!-- ELIMINAR -->
                            <button type="button"
                                    class="btn btn-outline-danger btn-sm rounded-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#eliminarModal<?php echo $row['id']; ?>">

                                <i class="bi bi-trash"></i>

                            </button>

                        </td>

                    </tr>

                    <!-- MODAL EDITAR -->
                    <div class="modal fade"
                         id="editarModal<?php echo $row['id']; ?>"
                         tabindex="-1"
                         aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content border-0 shadow rounded-4">

                                <form method="POST">

                                    <div class="modal-header bg-primary text-white rounded-top-4">

                                        <h5 class="modal-title">

                                            <i class="bi bi-pencil-square"></i>
                                            Editar Nivel

                                        </h5>

                                        <button type="button"
                                                class="btn-close btn-close-white"
                                                data-bs-dismiss="modal">
                                        </button>

                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden"
                                               name="id"
                                               value="<?php echo $row['id']; ?>">

                                        <input type="hidden"
                                               name="accion"
                                               value="editar">

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">
                                                Nombre del nivel
                                            </label>

                                            <input type="text"
                                                   name="nombre"
                                                   class="form-control rounded-3"
                                                   value="<?php echo htmlspecialchars($row['nombre']); ?>"
                                                   required>

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

                    <!-- MODAL ELIMINAR -->
                    <div class="modal fade"
                         id="eliminarModal<?php echo $row['id']; ?>"
                         tabindex="-1"
                         aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content border-0 shadow rounded-4">

                                <div class="modal-body text-center p-5">

                                    <div class="mb-3">

                                        <i class="bi bi-trash-fill text-danger"
                                           style="font-size: 70px;">
                                        </i>

                                    </div>

                                    <h4 class="fw-bold text-dark mb-3">
                                        Eliminar Nivel
                                    </h4>

                                    <p class="text-muted mb-4">

                                        ¿Seguro que deseas eliminar el nivel
                                        <strong>
                                            "<?php echo htmlspecialchars($row['nombre']); ?>"
                                        </strong>?

                                    </p>

                                    <div class="d-flex justify-content-center gap-3">

                                        <button type="button"
                                                class="btn btn-light border px-4 rounded-3"
                                                data-bs-dismiss="modal">

                                            Cancelar

                                        </button>

                                        <a href="principal.php?seccion=niveles&eliminar=<?php echo $row['id']; ?>"
                                           class="btn btn-danger px-4 rounded-3">

                                            <i class="bi bi-trash"></i>
                                            Eliminar

                                        </a>

                                    </div>

                                </div>

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
```
