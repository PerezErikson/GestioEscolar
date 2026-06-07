<?php
include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// =========================
// GUARDAR ASISTENCIA
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar_asistencia') {
    $grado_id = intval($_POST['grado_id']);
    $fecha = $_POST['fecha'];
    $estados = $_POST['estado'];

    foreach ($estados as $estudiante_id => $estado) {
        // Verificar si ya existe registro para ese estudiante y fecha
        $check = $conn->query("SELECT id FROM asistencia WHERE estudiante_id=$estudiante_id AND fecha='$fecha'");
        if ($check->num_rows > 0) {
            // Actualizar
            $conn->query("UPDATE asistencia SET estado='$estado' WHERE estudiante_id=$estudiante_id AND fecha='$fecha'");
        } else {
            // Insertar nuevo
            $conn->query("INSERT INTO asistencia (estudiante_id, grado_id, fecha, estado) VALUES ($estudiante_id, $grado_id, '$fecha', '$estado')");
        }
    }

    $mensaje = "✅ Asistencia guardada correctamente.";
    $tipo_mensaje = "success";
}

// =========================
// OBTENER GRADOS
// =========================
$grados = $conn->query("
    SELECT g.id, CONCAT(g.nombre, ' ', s.nombre) AS grado
    FROM grados1 g
    INNER JOIN secciones s ON g.id_seccion = s.id
    ORDER BY g.id ASC
");

// =========================
// FILTRAR ESTUDIANTES POR GRADO
// =========================
$grado_id = isset($_GET['grado_id']) ? intval($_GET['grado_id']) : 0;
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

$estudiantes = null;
if ($grado_id > 0) {
    $estudiantes = $conn->query("
        SELECT e.numero, e.nombre, e.apellido, e.ID, e.correo
        FROM estudiantes e
        WHERE e.grado_id = $grado_id
        ORDER BY e.numero ASC
    ");
}
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-check2-square"></i> Registro de Asistencia
    </h3>

    <!-- ALERTAS -->
    <?php if (!empty($mensaje)) { ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-3 mb-4"
             role="alert">
            <i class="bi bi-info-circle-fill fs-3"></i>
            <div class="flex-grow-1 fw-semibold"><?php echo $mensaje; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <!-- SELECTOR DE GRADO Y FECHA -->
    <form method="GET" action="principal.php" class="row g-3 align-items-center">
        <input type="hidden" name="seccion" value="asistencia">

        <div class="col-md-5">
            <label class="form-label fw-bold">Seleccionar grado:</label>
            <select name="grado_id" class="form-select" required>
                <option value="">-- Seleccione --</option>
                <?php while($g = $grados->fetch_assoc()) { ?>
                    <option value="<?php echo $g['id']; ?>" <?php if($grado_id == $g['id']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($g['grado']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Fecha:</label>
            <input type="date" name="fecha" class="form-control" value="<?php echo $fecha; ?>">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Buscar
            </button>
        </div>
    </form>
</div>

<!-- TABLA DE ASISTENCIA -->
<?php if ($grado_id > 0 && $estudiantes && $estudiantes->num_rows > 0) { ?>
<form method="POST" action="">
    <input type="hidden" name="accion" value="guardar_asistencia">
    <input type="hidden" name="grado_id" value="<?php echo $grado_id; ?>">
    <input type="hidden" name="fecha" value="<?php echo $fecha; ?>">

    <div class="card border-0 shadow-lg rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>N°</th>
                        <th>Nombre</th>
                        <th>ID</th>
                        <th>Correo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $estudiantes->fetch_assoc()) { ?>
                    <?php
                        $estado_actual = "Presente";
                        $check_estado = $conn->query("SELECT estado FROM asistencia WHERE estudiante_id={$row['numero']} AND fecha='$fecha'");
                        if ($check_estado->num_rows > 0) {
                            $estado_actual = $check_estado->fetch_assoc()['estado'];
                        }
                    ?>
                    <tr>
                        <td><?php echo $row['numero']; ?></td>
                        <td><?php echo htmlspecialchars($row['nombre'].' '.$row['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($row['ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['correo']); ?></td>
                        <td>
                            <select name="estado[<?php echo $row['numero']; ?>]" class="form-select form-select-sm">
                                <option value="Presente" <?php if($estado_actual=='Presente') echo 'selected'; ?>>Presente</option>
                                <option value="Ausente" <?php if($estado_actual=='Ausente') echo 'selected'; ?>>Ausente</option>
                                <option value="Excusa" <?php if($estado_actual=='Excusa') echo 'selected'; ?>>Excusa</option>
                            </select>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- BOTÓN GUARDAR -->
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-success rounded-3 px-4">
                <i class="bi bi-save"></i> Guardar Asistencia
            </button>
        </div>
    </div>
</form>
<?php } elseif ($grado_id > 0) { ?>
    <div class="alert alert-warning">⚠️ No hay estudiantes registrados en este grado.</div>
<?php } ?>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
