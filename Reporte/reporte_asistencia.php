<?php
include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

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
// FILTROS
// =========================
$grado_id = isset($_GET['grado_id']) ? intval($_GET['grado_id']) : 0;
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

$asistencias = null;
if ($grado_id > 0) {
    $asistencias = $conn->query("
        SELECT 
            e.numero,
            e.nombre,
            e.apellido,
            e.ID,
            e.correo,
            a.estado,
            a.fecha
        FROM asistencia a
        INNER JOIN estudiantes e ON a.estudiante_id = e.numero
        WHERE a.grado_id = $grado_id AND a.fecha = '$fecha'
        ORDER BY e.numero ASC
    ");
}
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-clipboard-check"></i> Reporte de Asistencia
    </h3>

    <!-- SELECTOR DE GRADO Y FECHA -->
    <form method="GET" action="principal.php" class="row g-3 align-items-center">
        <input type="hidden" name="seccion" value="reporte_asistencia">

        <div class="col-md-5">
            <label class="form-label fw-bold">Seleccionar grado:</label>
            <select name="grado_id" class="form-select" required>
                <option value="">-- Seleccione --</option>
                <?php while($g = $grados->fetch_assoc()) { ?>
                    <option value="<?php echo $g['id']; ?>"
                        <?php if($grado_id == $g['id']) echo "selected"; ?>>
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

<!-- TABLA DE REPORTE -->
<?php if ($grado_id > 0 && $asistencias && $asistencias->num_rows > 0) { ?>
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
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $asistencias->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['numero']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre'].' '.$row['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($row['ID']); ?></td>
                    <td><?php echo htmlspecialchars($row['correo']); ?></td>
                    <td>
                        <?php
                            $color = 'secondary';
                            if ($row['estado'] === 'Presente') $color = 'success';
                            elseif ($row['estado'] === 'Ausente') $color = 'danger';
                            elseif ($row['estado'] === 'Excusa') $color = 'warning';
                        ?>
                        <span class="badge bg-<?php echo $color; ?>">
                            <?php echo htmlspecialchars($row['estado']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } elseif ($grado_id > 0) { ?>
    <div class="alert alert-warning">⚠️ No hay registros de asistencia para esta fecha.</div>
<?php } ?>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
