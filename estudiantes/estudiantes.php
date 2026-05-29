<?php
include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// =========================
// ACTUALIZAR ESTUDIANTE
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $cedula = trim($_POST['cedula']);
    $correo = trim($_POST['correo']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $padre = trim($_POST['padre']);
    $madre = trim($_POST['madre']);
    $grado = intval($_POST['grado']);
    $nivel = intval($_POST['nivel']);

    $stmt = $conn->prepare("UPDATE estudiantes 
                            SET nombre=?, apellido=?, cedula=?, correo=?, fecha_nacimiento=?, direccion=?, telefono=?, padre=?, madre=?, grado_id=?, nivel_id=? 
                            WHERE id=?");

    $stmt->bind_param(
        "ssssssssiiii",
        $nombre,
        $apellido,
        $cedula,
        $correo,
        $fecha_nacimiento,
        $direccion,
        $telefono,
        $padre,
        $madre,
        $grado,
        $nivel,
        $id
    );

    if ($stmt->execute()) {

        $mensaje = "✅ Estudiante actualizado correctamente.";
        $tipo_mensaje = "success";

    } else {

        $mensaje = "❌ Error al actualizar el estudiante.";
        $tipo_mensaje = "danger";
    }
}

// =========================
// OBTENER ESTUDIANTES
// =========================
$estudiantes = $conn->query("SELECT e.*, 
                                    CONCAT(g.nombre, ' ', s.nombre) AS grado,
                                    n.nombre AS nivel
                             FROM estudiantes e
                             INNER JOIN grados1 g ON e.grado_id = g.id
                             INNER JOIN secciones s ON g.id_seccion = s.id
                             INNER JOIN niveles n ON e.nivel_id = n.id
                             ORDER BY e.id ASC");
?>

<div class="container mt-4">

    <!-- TITULO -->
    <h3 class="mb-4 text-primary fw-bold">

        <i class="bi bi-person-vcard-fill"></i>
        Gestión de Estudiantes

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

    <!-- TABLA -->
    <div class="card border-0 shadow-lg rounded-4 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h5 class="fw-semibold mb-0">

                <i class="bi bi-people"></i>
                Lista de estudiantes registrados

            </h5>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead style="background: #1f2937; color: white;">

                    <tr>

                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Cédula</th>
                        <th>Correo</th>
                        <th>Grado</th>
                        <th>Nivel</th>
                        <th class="text-center">Acciones</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = $estudiantes->fetch_assoc()) { ?>

                    <tr>

                        <td><?php echo $row['id']; ?></td>

                        <td class="fw-semibold">
                            <?php echo htmlspecialchars($row['nombre']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['apellido']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['cedula']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['correo']); ?>
                        </td>

                        <td>
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                <?php echo htmlspecialchars($row['grado']); ?>
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-dark rounded-pill px-3 py-2">
                                <?php echo htmlspecialchars($row['nivel']); ?>
                            </span>
                        </td>

                        <td class="text-center">

                            <!-- BOTON EDITAR -->
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm rounded-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editarModal<?php echo $row['id']; ?>">

                                <i class="bi bi-pencil-square"></i>
                                Editar

                            </button>

                        </td>

                    </tr>

                    <!-- MODAL -->
                    <div class="modal fade"
                         id="editarModal<?php echo $row['id']; ?>"
                         tabindex="-1"
                         aria-hidden="true">

                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

                            <div class="modal-content border-0 rounded-4 shadow-lg">

                                <!-- HEADER -->
                                <div class="modal-header bg-dark text-white rounded-top-4">

                                    <h5 class="modal-title">

                                        <i class="bi bi-pencil-square"></i>
                                        Editar Estudiante

                                    </h5>

                                    <button type="button"
                                            class="btn-close btn-close-white"
                                            data-bs-dismiss="modal">
                                    </button>

                                </div>

                                <form method="POST">

                                    <div class="modal-body p-4">

                                        <input type="hidden"
                                               name="id"
                                               value="<?php echo $row['id']; ?>">

                                        <input type="hidden"
                                               name="accion"
                                               value="editar">

                                        <div class="row g-3">

                                            <div class="col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Nombre
                                                </label>

                                                <input type="text"
                                                       name="nombre"
                                                       class="form-control rounded-3"
                                                       value="<?php echo htmlspecialchars($row['nombre']); ?>"
                                                       required>

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Apellido
                                                </label>

                                                <input type="text"
                                                       name="apellido"
                                                       class="form-control rounded-3"
                                                       value="<?php echo htmlspecialchars($row['apellido']); ?>"
                                                       required>

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Cédula
                                                </label>

                                                <input type="text"
                                                       name="cedula"
                                                       class="form-control rounded-3"
                                                       value="<?php echo htmlspecialchars($row['cedula']); ?>"
                                                       required>

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Correo
                                                </label>

                                                <input type="email"
                                                       name="correo"
                                                       class="form-control rounded-3"
                                                       value="<?php echo htmlspecialchars($row['correo']); ?>"
                                                       required>

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Fecha de nacimiento
                                                </label>

                                                <input type="date"
                                                       name="fecha_nacimiento"
                                                       class="form-control rounded-3"
                                                       value="<?php echo $row['fecha_nacimiento']; ?>">

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Teléfono
                                                </label>

                                                <input type="text"
                                                       name="telefono"
                                                       class="form-control rounded-3"
                                                       value="<?php echo $row['telefono']; ?>">

                                            </div>

                                            <div class="col-12">

                                                <label class="form-label fw-semibold">
                                                    Dirección
                                                </label>

                                                <input type="text"
                                                       name="direccion"
                                                       class="form-control rounded-3"
                                                       value="<?php echo $row['direccion']; ?>">

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Padre
                                                </label>

                                                <input type="text"
                                                       name="padre"
                                                       class="form-control rounded-3"
                                                       value="<?php echo $row['padre']; ?>">

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Madre
                                                </label>

                                                <input type="text"
                                                       name="madre"
                                                       class="form-control rounded-3"
                                                       value="<?php echo $row['madre']; ?>">

                                            </div>

                                            <!-- GRADO -->
                                            <div class="col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Grado
                                                </label>

                                                <select name="grado"
                                                        class="form-select rounded-3"
                                                        required>

                                                    <?php
                                                    $grados2 = $conn->query("SELECT g.id, g.nombre AS grado, s.nombre AS seccion 
                                                                             FROM grados1 g 
                                                                             INNER JOIN secciones s ON g.id_seccion = s.id 
                                                                             ORDER BY g.nombre ASC, s.nombre ASC");

                                                    while($g2 = $grados2->fetch_assoc()) {

                                                        $selected = ($g2['id'] == $row['grado_id']) ? "selected" : "";

                                                        echo "<option value='{$g2['id']}' $selected>" .
                                                            htmlspecialchars($g2['grado'] . ' ' . $g2['seccion']) .
                                                            "</option>";
                                                    }
                                                    ?>

                                                </select>

                                            </div>

                                            <!-- NIVEL -->
                                            <div class="col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Nivel
                                                </label>

                                                <select name="nivel"
                                                        class="form-select rounded-3"
                                                        required>

                                                    <?php
                                                    $niveles2 = $conn->query("SELECT * FROM niveles ORDER BY nombre ASC");

                                                    while($n2 = $niveles2->fetch_assoc()) {

                                                        $selected = ($n2['id'] == $row['nivel_id']) ? "selected" : "";

                                                        echo "<option value='{$n2['id']}' $selected>" .
                                                            htmlspecialchars($n2['nombre']) .
                                                            "</option>";
                                                    }
                                                    ?>

                                                </select>

                                            </div>

                                        </div>

                                    </div>

                                    <!-- FOOTER -->
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

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AUTO CERRAR ALERTAS -->
<script>

setTimeout(() => {

    let alerta = document.querySelector('.alert');

    if(alerta){

        let bsAlert = new bootstrap.Alert(alerta);

        bsAlert.close();
    }

}, 5000);

</script>