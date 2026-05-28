<?php
include("conexion/conexion.php");

// Guardar comportamiento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_comportamiento'])) {
    $grado_id = intval($_POST['grado_id']);
    $estudiante_id = intval($_POST['estudiante_id']);
    $nota = trim($_POST['nota']);
    $observacion = trim($_POST['observacion']);
    $fecha = date("Y-m-d");

    // Verificar si ya existe comportamiento para el estudiante en la fecha actual
    $check = $conn->prepare("SELECT COUNT(*) AS total FROM comportamiento WHERE estudiante_id=? AND fecha=?");
    $check->bind_param("is", $estudiante_id, $fecha);
    $check->execute();
    $exists = $check->get_result()->fetch_assoc();

    if ($exists['total'] > 0) {
        echo "<script>alert('⚠️ Ya se registró comportamiento para este estudiante hoy'); 
              window.location.href='principal.php?seccion=comportamiento';</script>";
        exit();
    }

    // Insertar nuevo registro
    $stmt = $conn->prepare("INSERT INTO comportamiento (estudiante_id, grado_id, fecha, nota, observacion)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $estudiante_id, $grado_id, $fecha, $nota, $observacion);
    $stmt->execute();

    echo "<script>alert('✅ Comportamiento registrado correctamente'); 
          window.location.href='principal.php?seccion=comportamiento';</script>";
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
    <h3 class="mb-4 text-primary"><i class="bi bi-person-badge"></i>Comportamiento</h3>

    <!-- Selección de grado -->
    <div class="card shadow-sm p-4 mb-4">
        <form method="GET" action="principal.php" class="row g-3">
            <input type="hidden" name="seccion" value="comportamiento">
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
        <h6 class="mb-3">Estudiantes del grado seleccionado</h6>
        <form method="POST" action="principal.php?seccion=comportamiento&grado_id=<?php echo $grado_id; ?>">
            <input type="hidden" name="grado_id" value="<?php echo $grado_id; ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Seleccionar estudiante</label>
                    <select name="estudiante_id" class="form-select" required>
                        <option value="">-- Seleccione estudiante --</option>
                        <?php while($row = $estudiantes->fetch_assoc()) { ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nota de comportamiento</label>
                    <select name="nota" class="form-select" required>
                        <option value="">-- Seleccione --</option>
                        <option value="Excelente">Excelente</option>
                        <option value="Bueno">Bueno</option>
                        <option value="Regular">Regular</option>
                        <option value="Deficiente">Deficiente</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Observación</label>
                    <input type="text" name="observacion" class="form-control" placeholder="Comentario opcional">
                </div>
            </div>
            <div class="text-end">
                <button type="submit" name="guardar_comportamiento" class="btn btn-success">
                    <i class="bi bi-save"></i> Guardar comportamiento
                </button>
            </div>
        </form>
    </div>
    <?php } elseif (isset($grado_id)) { ?>
        <div class="alert alert-warning">⚠️ No hay estudiantes registrados en este grado.</div>
    <?php } ?>
</div>
