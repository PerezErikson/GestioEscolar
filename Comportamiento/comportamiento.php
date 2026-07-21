<?php
include("conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// =========================
// GUARDAR REGISTRO ANECDÓTICO
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_comportamiento'])) {

    $grado_id = intval($_POST['grado_id']);
    $estudiante_id = intval($_POST['estudiante_id']);
    $nota = trim($_POST['nota']);
    $observacion = trim($_POST['observacion']);
    $fecha = date("Y-m-d");

    // Verificar duplicado
    $check = $conn->prepare("SELECT COUNT(*) AS total FROM comportamiento 
                             WHERE estudiante_id=? AND fecha=?");

    $check->bind_param("is", $estudiante_id, $fecha);
    $check->execute();

    $exists = $check->get_result()->fetch_assoc();

    if ($exists['total'] > 0) {

        $mensaje = "⚠️ Ya se registró un registro anecdótico para este estudiante hoy.";
        $tipo_mensaje = "warning";

    } else {

        // Insertar registro
        $stmt = $conn->prepare("INSERT INTO comportamiento 
                                (estudiante_id, grado_id, fecha, nota, observacion)
                                VALUES (?, ?, ?, ?, ?)");

$stmt = $conn->prepare("
    INSERT INTO comportamiento
    (estudiante_id, grado_id, fecha, nota, observacion)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iisss",
    $estudiante_id,
    $grado_id,
    $fecha,
    $nota,
    $observacion
);

if (!$stmt->execute()) {

    die(
        "Error MySQL: " .
        $stmt->error .
        "<br>Código: " .
        $stmt->errno
    );

}

$mensaje = "✅ Registro anecdótico guardado correctamente.";
$tipo_mensaje = "success";
    }
}

// =========================
// OBTENER GRADOS
// =========================
$grados = $conn->query("SELECT g.id, CONCAT(g.nombre, ' ', s.nombre) AS grado 
                        FROM grados1 g 
                        INNER JOIN secciones s ON g.id_seccion = s.id 
                        ORDER BY g.nombre ASC, s.nombre ASC");

// =========================
// OBTENER ESTUDIANTES
// =========================
$estudiantes = [];

if (isset($_GET['grado_id'])) {

    $grado_id = intval($_GET['grado_id']);
$estudiantes = $conn->query("SELECT e.numero, e.nombre, e.apellido
                           FROM estudiantes e
                           WHERE e.grado_id = $grado_id
                           ORDER BY e.apellido ASC, e.nombre ASC");
                           
}
?>

<div class="container mt-4">

    <!-- TITULO -->
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-journal-text"></i> Registro anecdótico
    </h3>

    <!-- ALERTAS -->
    <?php if (!empty($mensaje)) { ?>

        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4"
             role="alert">

            <?php echo $mensaje; ?>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php } ?>

    <!-- SELECCIONAR GRADO -->
    <div class="card border-0 shadow-lg rounded-4 p-4 mb-4">

        <form method="GET"
              action="principal.php"
              class="row g-3">

            <input type="hidden"
                   name="seccion"
                   value="comportamiento">

            <div class="col-md-8">

                <label class="form-label fw-semibold">
                    Seleccionar grado
                </label>

                <select name="grado_id"
                        class="form-select rounded-3"
                        required>

                    <option value="">-- Seleccione --</option>

                    <?php while($g = $grados->fetch_assoc()) { ?>

                        <option value="<?php echo $g['id']; ?>"
                            <?php if(isset($grado_id) && $grado_id == $g['id']) echo "selected"; ?>>

                            <?php echo htmlspecialchars($g['grado']); ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <div class="col-md-4 d-flex align-items-end">

                <button type="submit"
                        class="btn btn-primary w-100 rounded-3 fw-semibold">

                    Ver estudiantes

                </button>

            </div>

        </form>

    </div>

    <!-- FORMULARIO -->
    <?php if (!empty($estudiantes) && $estudiantes->num_rows > 0) { ?>

    <div class="card border-0 shadow-lg rounded-4 p-4">

        <h5 class="mb-4 fw-semibold">
            <i class="bi bi-people"></i> Nuevo registro anecdótico
        </h5>

        <form method="POST"
              action="principal.php?seccion=comportamiento&grado_id=<?php echo $grado_id; ?>">

            <input type="hidden"
                   name="grado_id"
                   value="<?php echo $grado_id; ?>">

            <div class="row g-4">

                <!-- ESTUDIANTE -->
                <div class="col-md-5">

                    <label class="form-label fw-semibold">
                        Seleccionar estudiante
                    </label>

                    <select name="estudiante_id"
        class="form-select rounded-3"
        required>

    <option value="">-- Seleccione estudiante --</option>

    <?php while($row = $estudiantes->fetch_assoc()) { ?>

        <option value="<?php echo $row['numero']; ?>">

            <?php echo htmlspecialchars(
                $row['nombre'] . ' ' . $row['apellido']
            ); ?>

        </option>

    <?php } ?>

</select>

                </div>

                <!-- NOTA -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">
                        Valoración del registro
                    </label>

                    <select name="nota"
                            class="form-select rounded-3"
                            required>

                        <option value="">-- Seleccione --</option>

                        <option value="Excelente">🟢 Excelente</option>
                        <option value="Bueno">🔵 Bueno</option>
                        <option value="Regular">🟡 Regular</option>
                        <option value="Deficiente">🔴 Deficiente</option>

                    </select>

                </div>

                <!-- OBSERVACION -->
                <div class="col-md-4">

                    <label class="form-label fw-semibold">
                        Observación / Descripción
                    </label>

                    <input type="text"
                           name="observacion"
                           class="form-control rounded-3"
                           placeholder="Detalle del acontecimiento">

                </div>

            </div>

            <!-- BOTON -->
            <div class="text-end mt-4">

                <button type="button"
                        class="btn btn-success rounded-3 px-4 fw-semibold"
                        onclick="abrirModalGuardar()">

                    <i class="bi bi-save"></i>
                    Guardar registro anecdótico

                </button>

            </div>

            <!-- MODAL -->
            <div class="modal fade"
                 id="modalGuardar"
                 tabindex="-1"
                 aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">

                    <div class="modal-content border-0 rounded-4 shadow-lg">

                        <div class="modal-header bg-success text-white rounded-top-4">

                            <h5 class="modal-title">
                                <i class="bi bi-check-circle-fill"></i>
                                Confirmar registro
                            </h5>

                            <button type="button"
                                    class="btn-close btn-close-white"
                                    data-bs-dismiss="modal">
                            </button>

                        </div>

                        <div class="modal-body text-center p-4">

                            <div class="mb-3">

                                <i class="bi bi-journal-check text-success"
                                   style="font-size: 65px;">
                                </i>

                            </div>

                            <h4 class="fw-bold mb-3">
                                ¿Deseas guardar este registro anecdótico?
                            </h4>

                            <p class="text-muted">
                                Verifica que los datos ingresados sean correctos.
                            </p>

                        </div>

                        <div class="modal-footer border-0 justify-content-center pb-4">

                            <button type="button"
                                    class="btn btn-light border rounded-3 px-4"
                                    data-bs-dismiss="modal">

                                Cancelar

                            </button>

                            <button type="submit"
                                    name="guardar_comportamiento"
                                    class="btn btn-success rounded-3 px-4 fw-semibold">

                                Sí, guardar

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

    <?php } elseif (isset($grado_id)) { ?>

        <div class="alert alert-warning border-0 shadow-sm rounded-4">

            ⚠️ No hay estudiantes registrados en este grado.

        </div>

    <?php } ?>

</div>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SCRIPTS -->
<script>

// Auto ocultar alertas
setTimeout(() => {

    let alerta = document.querySelector('.alert');

    if(alerta){

        let bsAlert = new bootstrap.Alert(alerta);

        bsAlert.close();
    }

}, 4000);

// Modal guardar
function abrirModalGuardar() {

    let modal = new bootstrap.Modal(document.getElementById('modalGuardar'));

    modal.show();
}

</script>