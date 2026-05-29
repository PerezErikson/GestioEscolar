<?php
include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// =========================
// GUARDAR / ACTUALIZAR
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    // =========================
    // GUARDAR
    // =========================
    if ($_POST['accion'] === 'guardar') {

        $nombre_centro = trim($_POST['nombre_centro']);
        $direccion = trim($_POST['direccion']);
        $distrito = trim($_POST['distrito']);
        $telefono = trim($_POST['telefono']);
        $correo = trim($_POST['correo']);
        $director = trim($_POST['director']);

        if (!empty($nombre_centro)) {

            $sql = "INSERT INTO configuracion 
            (nombre_centro, direccion, distrito, telefono, correo, director)
            VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssssss",
                $nombre_centro,
                $direccion,
                $distrito,
                $telefono,
                $correo,
                $director
            );

            if ($stmt->execute()) {

                $mensaje = "✅ Centro educativo registrado correctamente.";
                $tipo_mensaje = "success";

            } else {

                $mensaje = "❌ Error al guardar el centro.";
                $tipo_mensaje = "danger";
            }
        }
    }

    // =========================
    // ACTUALIZAR
    // =========================
    if ($_POST['accion'] === 'actualizar') {

        $id = intval($_POST['id']);

        $nombre_centro = trim($_POST['nombre_centro']);
        $direccion = trim($_POST['direccion']);
        $distrito = trim($_POST['distrito']);
        $telefono = trim($_POST['telefono']);
        $correo = trim($_POST['correo']);
        $director = trim($_POST['director']);

        $sql = "UPDATE configuracion 
                SET nombre_centro = ?, 
                    direccion = ?, 
                    distrito = ?, 
                    telefono = ?, 
                    correo = ?, 
                    director = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssssssi",
            $nombre_centro,
            $direccion,
            $distrito,
            $telefono,
            $correo,
            $director,
            $id
        );

        if ($stmt->execute()) {

            $mensaje = "✅ Centro actualizado correctamente.";
            $tipo_mensaje = "success";

        } else {

            $mensaje = "❌ Error al actualizar.";
            $tipo_mensaje = "danger";
        }
    }
}

// =========================
// ELIMINAR
// =========================
if (isset($_GET['eliminar'])) {

    $id = intval($_GET['eliminar']);

    $sql = "DELETE FROM configuracion WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        $mensaje = "🗑️ Centro eliminado correctamente.";
        $tipo_mensaje = "danger";

    } else {

        $mensaje = "❌ Error al eliminar.";
        $tipo_mensaje = "danger";
    }
}

// =========================
// LISTAR
// =========================
$sql = "SELECT * FROM configuracion ORDER BY id ASC";
$result = $conn->query($sql);
?>

<div class="container mt-4">

    <!-- TITULO -->
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-gear-fill"></i>
        Gestión de Configuración
    </h3>

    <!-- ALERTAS -->
    <?php if (!empty($mensaje)) { ?>

        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-3 mb-4"
             role="alert">

            <?php if ($tipo_mensaje == "success") { ?>

                <i class="bi bi-check-circle-fill fs-3"></i>

            <?php } else { ?>

                <i class="bi bi-exclamation-triangle-fill fs-3"></i>

            <?php } ?>

            <div class="flex-grow-1 fw-semibold">

                <?php echo $mensaje; ?>

            </div>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php } ?>

    <!-- FORMULARIO -->
    <div class="card border-0 shadow-lg rounded-4 p-4 mb-4">

        <h5 class="mb-4 fw-semibold">
            <i class="bi bi-building"></i>
            Registrar Nuevo Centro Educativo
        </h5>

        <form method="POST" class="row g-3">

            <input type="hidden" name="accion" value="guardar">

            <div class="col-md-4">

                <input type="text"
                       name="nombre_centro"
                       class="form-control rounded-3"
                       placeholder="Nombre del centro"
                       required>

            </div>

            <div class="col-md-4">

                <input type="text"
                       name="distrito"
                       class="form-control rounded-3"
                       placeholder="Distrito"
                       required>

            </div>

            <div class="col-md-4">

                <input type="text"
                       name="direccion"
                       class="form-control rounded-3"
                       placeholder="Dirección"
                       required>

            </div>

            <div class="col-md-4">

                <input type="text"
                       name="telefono"
                       class="form-control rounded-3"
                       placeholder="Teléfono">

            </div>

            <div class="col-md-4">

                <input type="email"
                       name="correo"
                       class="form-control rounded-3"
                       placeholder="Correo institucional">

            </div>

            <div class="col-md-4">

                <input type="text"
                       name="director"
                       class="form-control rounded-3"
                       placeholder="Director">

            </div>

            <div class="col-12">

                <button type="submit"
                        class="btn btn-primary w-100 rounded-3 fw-semibold">

                    <i class="bi bi-save"></i>
                    Guardar Centro

                </button>

            </div>

        </form>

    </div>

    <!-- TABLA -->
    <div class="card border-0 shadow-lg rounded-4 p-4">

        <h5 class="mb-4 fw-semibold">
            <i class="bi bi-list-ul"></i>
            Centros Registrados
        </h5>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead style="background: #1f2937; color: white;">

                    <tr>

                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Distrito</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Director</th>
                        <th class="text-center">Acciones</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = $result->fetch_assoc()) { ?>

                    <tr>

                        <td><?php echo $row['id']; ?></td>

                        <td class="fw-semibold">
                            <?php echo htmlspecialchars($row['nombre_centro']); ?>
                        </td>

                        <td><?php echo htmlspecialchars($row['direccion']); ?></td>

                        <td><?php echo htmlspecialchars($row['distrito']); ?></td>

                        <td><?php echo htmlspecialchars($row['telefono']); ?></td>

                        <td><?php echo htmlspecialchars($row['correo']); ?></td>

                        <td><?php echo htmlspecialchars($row['director']); ?></td>

                        <td class="text-center">

                            <!-- EDITAR -->
                            <button class="btn btn-outline-primary btn-sm rounded-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editarModal<?php echo $row['id']; ?>">

                                <i class="bi bi-pencil-square"></i>
                                Editar

                            </button>

                            <!-- ELIMINAR -->
                            <a href="principal.php?seccion=configuracion&eliminar=<?php echo $row['id']; ?>"
                               class="btn btn-outline-danger btn-sm rounded-3"
                               onclick="return confirmarEliminacion(event, this.href);">

                                <i class="bi bi-trash"></i>
                                Eliminar

                            </a>

                            <!-- MODAL EDITAR -->
                            <div class="modal fade"
                                 id="editarModal<?php echo $row['id']; ?>"
                                 tabindex="-1">

                                <div class="modal-dialog modal-lg modal-dialog-centered">

                                    <div class="modal-content border-0 rounded-4 shadow-lg">

                                        <div class="modal-header bg-dark text-white rounded-top-4">

                                            <h5 class="modal-title">

                                                <i class="bi bi-pencil-square"></i>
                                                Editar Centro Educativo

                                            </h5>

                                            <button type="button"
                                                    class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal">
                                            </button>

                                        </div>

                                        <form method="POST">

                                            <div class="modal-body p-4">

                                                <input type="hidden"
                                                       name="accion"
                                                       value="actualizar">

                                                <input type="hidden"
                                                       name="id"
                                                       value="<?php echo $row['id']; ?>">

                                                <div class="row g-3">

                                                    <div class="col-md-6">

                                                        <label class="form-label fw-semibold">
                                                            Nombre del Centro
                                                        </label>

                                                        <input type="text"
                                                               name="nombre_centro"
                                                               class="form-control rounded-3"
                                                               required
                                                               value="<?php echo htmlspecialchars($row['nombre_centro']); ?>">

                                                    </div>

                                                    <div class="col-md-6">

                                                        <label class="form-label fw-semibold">
                                                            Distrito
                                                        </label>

                                                        <input type="text"
                                                               name="distrito"
                                                               class="form-control rounded-3"
                                                               required
                                                               value="<?php echo htmlspecialchars($row['distrito']); ?>">

                                                    </div>

                                                    <div class="col-md-6">

                                                        <label class="form-label fw-semibold">
                                                            Dirección
                                                        </label>

                                                        <input type="text"
                                                               name="direccion"
                                                               class="form-control rounded-3"
                                                               required
                                                               value="<?php echo htmlspecialchars($row['direccion']); ?>">

                                                    </div>

                                                    <div class="col-md-6">

                                                        <label class="form-label fw-semibold">
                                                            Teléfono
                                                        </label>

                                                        <input type="text"
                                                               name="telefono"
                                                               class="form-control rounded-3"
                                                               value="<?php echo htmlspecialchars($row['telefono']); ?>">

                                                    </div>

                                                    <div class="col-md-6">

                                                        <label class="form-label fw-semibold">
                                                            Correo
                                                        </label>

                                                        <input type="email"
                                                               name="correo"
                                                               class="form-control rounded-3"
                                                               value="<?php echo htmlspecialchars($row['correo']); ?>">

                                                    </div>

                                                    <div class="col-md-6">

                                                        <label class="form-label fw-semibold">
                                                            Director
                                                        </label>

                                                        <input type="text"
                                                               name="director"
                                                               class="form-control rounded-3"
                                                               value="<?php echo htmlspecialchars($row['director']); ?>">

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="modal-footer border-0 px-4 pb-4">

                                                <button type="button"
                                                        class="btn btn-light border rounded-3 px-4"
                                                        data-bs-dismiss="modal">

                                                    Cancelar

                                                </button>

                                                <button type="submit"
                                                        class="btn btn-dark rounded-3 px-4">

                                                    <i class="bi bi-check-circle"></i>
                                                    Actualizar

                                                </button>
                                            </div>
                                        </form>
                                    </div>
                               </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL ELIMINAR -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title">
                    <i class="bi bi-trash3-fill"></i>
                    Confirmar eliminación
                </h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger"
                       style="font-size: 65px;">
                    </i>
                </div>
                <h4 class="fw-bold mb-3">
                    ¿Deseas eliminar este centro?
                </h4>
                <p class="text-muted">
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button"
                        class="btn btn-light border rounded-3 px-4"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>
                <a href="#"
                   id="btnConfirmarEliminar"
                   class="btn btn-danger rounded-3 px-4 fw-semibold">
                    Sí, eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SCRIPTS -->
<script>

// Auto cerrar alertas
setTimeout(() => {

    let alerta = document.querySelector('.alert');

    if(alerta){

        let bsAlert = new bootstrap.Alert(alerta);

        bsAlert.close();
    }

}, 5000);

// Modal eliminar elegante
function confirmarEliminacion(event, url) {

    event.preventDefault();

    document.getElementById('btnConfirmarEliminar').href = url;

    let modal = new bootstrap.Modal(document.getElementById('modalEliminar'));

    modal.show();
}

</script>