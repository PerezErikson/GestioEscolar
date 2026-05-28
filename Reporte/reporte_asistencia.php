<?php
include("conexion/conexion.php");

// Obtener grados
$grados = $conn->query("SELECT g.id, CONCAT(g.nombre, ' ', s.nombre) AS grado 
                        FROM grados1 g 
                        INNER JOIN secciones s ON g.id_seccion = s.id 
                        ORDER BY g.nombre ASC, s.nombre ASC");

// Variables
$reporte = [];
if (isset($_GET['grado_id']) && isset($_GET['fecha'])) {
    $grado_id = intval($_GET['grado_id']);
    $fecha = $_GET['fecha'];

    $sql = "SELECT e.nombre, e.apellido, a.estado 
            FROM asistencia a
            INNER JOIN estudiantes e ON a.estudiante_id = e.id
            WHERE a.grado_id = ? AND a.fecha = ?
            ORDER BY e.apellido ASC, e.nombre ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $grado_id, $fecha);
    $stmt->execute();
    $reporte = $stmt->get_result();
}
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary"><i class="bi bi-journal-check"></i> Reporte de Asistencia</h3>

    <div class="card shadow-sm p-4 mb-4">
        <form method="GET" action="principal.php" class="row g-3">
            <input type="hidden" name="seccion" value="reporte_asistencia">
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

    <?php if (!empty($reporte) && $reporte->num_rows > 0) { ?>
    <div class="card shadow-sm p-4">
        <h6 class="mb-3">Asistencia del grado en la fecha seleccionada</h6>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $reporte->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                    <td class="text-center">
                        <?php 
                            if ($row['estado'] == 'Presente') echo "<span class='badge bg-success'>Presente</span>";
                            elseif ($row['estado'] == 'Ausente') echo "<span class='badge bg-danger'>Ausente</span>";
                            else echo "<span class='badge bg-warning text-dark'>Excusa</span>";
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } elseif (isset($grado_id) && isset($fecha)) { ?>
        <div class="alert alert-warning">⚠️ No hay registros de asistencia para este curso en esa fecha.</div>
    <?php } ?>
</div>
