<?php
// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conexion/conexion.php");

$rol = $_SESSION['rol_id'];
$nombre_estudiante = $_SESSION['nombre']; // nombre del estudiante logueado

$reporte = [];

if ($rol == 3) {
    // Estudiante: solo sus registros por nombre
    $sql = "SELECT e.nombre, e.apellido, c.nota, c.observacion, c.fecha
            FROM comportamiento c
            INNER JOIN estudiantes e ON c.estudiante_id = e.id
            WHERE e.nombre = ?
            ORDER BY c.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre_estudiante);
    $stmt->execute();
    $reporte = $stmt->get_result();
} else {
    // Admin y Docente: filtros
    $grados = $conn->query("SELECT g.id, CONCAT(g.nombre, ' ', s.nombre) AS grado 
                            FROM grados1 g 
                            INNER JOIN secciones s ON g.id_seccion = s.id 
                            ORDER BY g.nombre ASC, s.nombre ASC");

    if (isset($_GET['grado_id']) && isset($_GET['fecha'])) {
        $grado_id = intval($_GET['grado_id']);
        $fecha = $_GET['fecha'];

        $sql = "SELECT e.nombre, e.apellido, c.nota, c.observacion 
                FROM comportamiento c
                INNER JOIN estudiantes e ON c.estudiante_id = e.id
                WHERE c.grado_id = ? AND c.fecha = ?
                ORDER BY e.apellido ASC, e.nombre ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $grado_id, $fecha);
        $stmt->execute();
        $reporte = $stmt->get_result();
    }
}
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary"><i class="bi bi-clipboard-check"></i> Reporte de Comportamiento</h3>

    <?php if ($rol != 3) { ?>
    <!-- Filtros solo para Admin y Docente -->
    <div class="card shadow-sm p-4 mb-4">
        <form method="GET" action="principal.php" class="row g-3">
            <input type="hidden" name="seccion" value="reporte_comportamiento">
            <div class="col-md-5">
                <label class="form-label">Seleccionar grado</label>
                <select name="grado_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    <?php while($g = $grados->fetch_assoc()) { ?>
                        <option value="<?php echo $g['id']; ?>" 
                            <?php if(isset($grado_id) && $grado_id == $g['id']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($g['grado']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" 
                       value="<?php echo isset($fecha) ? $fecha : ''; ?>" required>
            </div>
            <div class="col-md-3 text-end">
                <button type="submit" class="btn btn-primary w-100">Ver reporte</button>
            </div>
        </form>
    </div>
    <?php } ?>

    <!-- Resultados -->
    <?php if (!empty($reporte) && $reporte->num_rows > 0) { ?>
    <div class="card shadow-sm p-4">
        <h6 class="mb-3">
            <?php echo ($rol == 3) ? "Tu historial de comportamiento" : "Comportamiento del grado en la fecha seleccionada"; ?>
        </h6>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Nota</th>
                    <th>Observación</th>
                    <?php if ($rol == 3) { ?><th>Fecha</th><?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $reporte->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                    <td>
                        <?php 
                            if ($row['nota'] == 'Excelente') echo "<span class='badge bg-success'>Excelente</span>";
                            elseif ($row['nota'] == 'Bueno') echo "<span class='badge bg-primary'>Bueno</span>";
                            elseif ($row['nota'] == 'Regular') echo "<span class='badge bg-warning text-dark'>Regular</span>";
                            else echo "<span class='badge bg-danger'>Deficiente</span>";
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['observacion']); ?></td>
                    <?php if ($rol == 3) { ?>
                        <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } elseif (($rol != 3 && isset($grado_id) && isset($fecha)) || $rol == 3) { ?>
        <div class="alert alert-warning">⚠️ No hay registros de comportamiento.</div>
    <?php } ?>
</div>
