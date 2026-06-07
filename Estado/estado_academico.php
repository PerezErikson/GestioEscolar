<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . "/../conexion/conexion.php");

$mensaje_exito = null;

// =========================
// GUARDAR ESTADOS MASIVOS
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar_todos') {
    if (isset($_POST['estado']) && is_array($_POST['estado'])) {
        foreach ($_POST['estado'] as $numero => $estado) {
            $numero = intval($numero);

            // Obtener grado actual
            $res = $conn->query("SELECT grado_id FROM estudiantes WHERE numero = $numero");
            $row = $res->fetch_assoc();
            $grado_actual = $row['grado_id'];

            // Buscar siguiente grado
            $grado_siguiente = $conn->query("SELECT id FROM grados1 WHERE id > $grado_actual ORDER BY id ASC LIMIT 1");
            $nuevo_grado = ($grado_siguiente && $grado_siguiente->num_rows > 0)
                ? $grado_siguiente->fetch_assoc()['id']
                : $grado_actual;

            // Aplicar estado
            if ($estado === 'Promovido') {
                $conn->query("UPDATE estudiantes SET grado_id = $nuevo_grado, estado = 'Activo' WHERE numero = $numero");
            } elseif ($estado === 'Reprobado') {
                $conn->query("UPDATE estudiantes SET estado = 'Activo' WHERE numero = $numero");
            } elseif ($estado === 'Aplazado') {
                $conn->query("UPDATE estudiantes SET grado_id = $nuevo_grado, estado = 'Aplazado' WHERE numero = $numero");
            } elseif ($estado === 'Abandono') {
                $conn->query("UPDATE estudiantes SET estado = 'Inactivo' WHERE numero = $numero");
            }

            // Guardar historial en la tabla correcta
            $conn->query("INSERT INTO estado_estudiante (estudiante_numero, estado, fecha) VALUES ($numero, '$estado', CURDATE())");
        }

        // ✅ Mensaje de éxito en vez de header()
        $curso_id = intval($_POST['curso_id']);
        $mensaje_exito = "Los estados académicos se guardaron correctamente.";
    }
}

// =========================
// OBTENER CURSOS
// =========================
$cursos = $conn->query("
    SELECT g.id, CONCAT(g.nombre,' ',s.nombre) AS curso, n.nombre AS nivel
    FROM grados1 g
    INNER JOIN secciones s ON g.id_seccion = s.id
    INNER JOIN niveles n ON g.id_nivel = n.id
    ORDER BY n.id,g.id
");

// =========================
// CURSO SELECCIONADO
// =========================
$curso_id = isset($_GET['curso_id']) ? intval($_GET['curso_id']) : ($curso_id ?? 0);

// =========================
// ESTUDIANTES DEL CURSO
// =========================
$estudiantes = null;
if ($curso_id > 0) {
    $estudiantes = $conn->query("
        SELECT e.numero, e.nombre, e.apellido, e.ID, e.correo, e.estado,
               CONCAT(g.nombre,' ',s.nombre) AS grado, n.nombre AS nivel
        FROM estudiantes e
        INNER JOIN grados1 g ON e.grado_id = g.id
        INNER JOIN secciones s ON g.id_seccion = s.id
        INNER JOIN niveles n ON e.nivel_id = n.id
        WHERE e.grado_id = $curso_id
        ORDER BY e.numero ASC
    ");
}
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-award-fill"></i> Estado Académico de Estudiantes
    </h3>

    <!-- ALERTA DE ÉXITO -->
    <?php if ($mensaje_exito) { ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-4">
            <i class="bi bi-check-circle-fill fs-2"></i>
            <div>
                <h6 class="fw-bold mb-1">¡Éxito!</h6>
                <span><?php echo $mensaje_exito; ?></span>
            </div>
        </div>
    <?php } ?>

    <!-- FILTROS -->
    <div class="card border-0 shadow-lg rounded-4 p-4 mb-4">
        <form method="GET" action="principal.php" class="row g-3">
            <input type="hidden" name="seccion" value="estado_academico">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Seleccionar curso</label>
                <select name="curso_id" class="form-select rounded-3" required>
                    <option value="">-- Seleccione --</option>
                    <?php while($c = $cursos->fetch_assoc()) { ?>
                        <option value="<?php echo $c['id']; ?>"
                            <?php if(isset($curso_id) && $curso_id == $c['id']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($c['curso'].' - '.$c['nivel']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold">
                    Ver reporte
                </button>
            </div>
        </form>
    </div>

    <!-- RESULTADOS -->
    <?php if ($curso_id > 0 && $estudiantes && $estudiantes->num_rows > 0) { ?>
    <form method="POST">
        <input type="hidden" name="accion" value="guardar_todos">
        <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">

        <div class="card border-0 shadow-lg rounded-4 p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>N°</th>
                            <th>Nombre</th>
                            <th>ID</th>
                            <th>Correo</th>
                            <th>Grado</th>
                            <th>Nivel</th>
                            <th>Estado Académico</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $estudiantes->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['numero']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre'].' '.$row['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($row['ID']); ?></td>
                            <td><?php echo htmlspecialchars($row['correo']); ?></td>
                            <td><?php echo htmlspecialchars($row['grado']); ?></td>
                            <td><?php echo htmlspecialchars($row['nivel']); ?></td>
                            <td>
                                <select class="form-select" name="estado[<?php echo $row['numero']; ?>]">
                                    <option value="Promovido">Promovido</option>
                                    <option value="Reprobado">Reprobado</option>
                                    <option value="Aplazado">Aplazado</option>
                                    <option value="Abandono">Abandono</option>
                                </select>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-success rounded-3 px-4">
                    <i class="bi bi-save"></i> Guardar Estados
                </button>
            </div>
        </div>
    </form>
    <?php } elseif ($curso_id > 0) { ?>
        <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-4">
            <i class="bi bi-exclamation-triangle-fill fs-2"></i>
            <div>
                <h6 class="fw-bold mb-1">No hay registros</h6>
                <span>⚠️ No existen estudiantes registrados en este curso.</span>
            </div>
        </div>
    <?php } ?>
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
