<?php
include("conexion/conexion.php");

// Guardar asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_asistencia'])) {
    $grado_id = intval($_POST['grado_id']);
    $fecha = date("Y-m-d");

    // Verificar si ya existe asistencia para este grado en la fecha actual
    $check = $conn->prepare("SELECT COUNT(*) AS total FROM asistencia WHERE grado_id=? AND fecha=?");
    $check->bind_param("is", $grado_id, $fecha);
    $check->execute();
    $exists = $check->get_result()->fetch_assoc();

    if ($exists['total'] > 0) {
        echo "<script>alert('⚠️ Ya se registró asistencia para este curso hoy'); 
              window.location.href='principal.php?seccion=asistencia';</script>";
        exit();
    }

    // Guardar asistencia
    foreach ($_POST['estado'] as $estudiante_id => $estado) {
        $stmt = $conn->prepare("INSERT INTO asistencia (estudiante_id, grado_id, fecha, estado) 
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $estudiante_id, $grado_id, $fecha, $estado);
        $stmt->execute();
    }

    // Limpiar pantalla y volver al inicio de asistencia
    echo "<script>alert('✅ Asistencia guardada correctamente'); 
          window.location.href='principal.php?seccion=asistencia';</script>";
    exit();
}

// Obtener grados
$grados = $conn->query("SELECT g.id, CONCAT(g.nombre, ' ', s.nombre) AS grado 
                        FROM grados1 g 
                        INNER JOIN secciones s ON g.id_seccion = s.id 
                        ORDER BY g.nombre ASC, s.nombre ASC");

// Si se selecciona un grado
$estudiantes = [];
if (isset($_GET['grado_id'])) {
    $grado_id = intval($_GET['grado_id']);
    $estudiantes = $conn->query("SELECT e.id, e.nombre, e.apellido 
                                 FROM estudiantes e 
                                 WHERE e.grado_id = $grado_id 
                                 ORDER BY e.apellido ASC, e.nombre ASC");
}
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary"><i class="bi bi-calendar-check"></i> Registro de Asistencia</h3>

    <!-- Selección de grado -->
    <div class="card shadow-sm p-4 mb-4">
        <form method="GET" action="principal.php" class="row g-3">
            <input type="hidden" name="seccion" value="asistencia">
            <div class="col-md-8">
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
            <div class="col-md-4 text-end">
                <button type="submit" class="btn btn-primary w-100">Ver estudiantes</button>
            </div>
        </form>
    </div>

    <!-- Lista de estudiantes -->
    <?php if (!empty($estudiantes) && $estudiantes->num_rows > 0) { ?>
    <div class="card shadow-sm p-4">
        <form method="POST" action="principal.php?seccion=asistencia&grado_id=<?php echo $grado_id; ?>">
            <input type="hidden" name="grado_id" value="<?php echo $grado_id; ?>">
            <h6 class="mb-3">Estudiantes del grado seleccionado</h6>
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $estudiantes->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                        <td class="text-center">
                            <select name="estado[<?php echo $row['id']; ?>]" class="form-select" required>
                                <option value="Presente">Presente</option>
                                <option value="Ausente">Ausente</option>
                                <option value="Excusa">Excusa</option>
                            </select>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="text-end">
                <button type="submit" name="guardar_asistencia" class="btn btn-success">
                    <i class="bi bi-save"></i> Guardar asistencia
                </button>
            </div>
        </form>
    </div>
    <?php } elseif (isset($grado_id)) { ?>
        <div class="alert alert-warning">⚠️ No hay estudiantes registrados en este grado.</div>
    <?php } ?>
</div>
