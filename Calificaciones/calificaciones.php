<?php
include("conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// Capturamos las variables por GET o POST de forma segura
$anio_id = isset($_REQUEST['anio_id']) ? intval($_REQUEST['anio_id']) : 0;
$grado_id = isset($_REQUEST['grado_id']) ? intval($_REQUEST['grado_id']) : 0;
$competencia_id = isset($_REQUEST['competencia_id']) ? intval($_REQUEST['competencia_id']) : 0;
$estudiante_id = isset($_GET['estudiante_id']) ? intval($_GET['estudiante_id']) : 0;

/* =====================================
   GUARDAR CALIFICACIONES
===================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_calificaciones'])) {

    $grado_id = intval($_POST['grado_id']);
    $estudiante_id = intval($_POST['estudiante_id']);
    $anio_id = intval($_POST['anio_id']); 
    $competencia_id = intval($_POST['competencia_id']); 

    foreach ($_POST['materia_id'] as $i => $materia_id) {

        $materia_id = intval($materia_id);

        $p1 = floatval($_POST['p1'][$i]);
        $p2 = floatval($_POST['p2'][$i]);
        $p3 = floatval($_POST['p3'][$i]);
        $p4 = floatval($_POST['p4'][$i]);

        $nota_final = ($p1 + $p2 + $p3 + $p4) / 4;

        // Verificar si ya existe registro previo
        $check = $conn->prepare("
            SELECT id
            FROM calificaciones
            WHERE estudiante_id = ?
            AND materia_id = ?
            AND anio_id = ?
            AND competencia_id = ?
        ");

        $check->bind_param("iiii", $estudiante_id, $materia_id, $anio_id, $competencia_id);
        $check->execute();
        $resultado = $check->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();

            $update = $conn->prepare("
                UPDATE calificaciones
                SET p1=?, p2=?, p3=?, p4=?, nota_final=?
                WHERE id=?
            ");
            $update->bind_param("dddddi", $p1, $p2, $p3, $p4, $nota_final, $fila['id']);
            $update->execute();

        } else {
            $insert = $conn->prepare("
                INSERT INTO calificaciones
                (estudiante_id, grado_id, materia_id, anio_id, competencia_id, p1, p2, p3, p4, nota_final)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insert->bind_param("iiiiiddddd", $estudiante_id, $grado_id, $materia_id, $anio_id, $competencia_id, $p1, $p2, $p3, $p4, $nota_final);
            $insert->execute();
        }
    }

    $mensaje = "✅ Calificaciones guardadas correctamente para la competencia seleccionada.";
    $tipo_mensaje = "success";
}

/* =====================================
   OBTENER GRADOS Y AÑOS (Filtros superiores)
===================================== */
$grados = $conn->query("
    SELECT g.id, CONCAT(g.nombre,' ',s.nombre) AS grado
    FROM grados1 g
    INNER JOIN secciones s ON g.id_seccion=s.id
    ORDER BY g.nombre ASC
");
$anos_escolares = $conn->query("SELECT id, nombre FROM anio_escolar ORDER BY nombre DESC");
$competencias = $conn->query("SELECT id, nombre FROM competencias ORDER BY nombre ASC");


/* =====================================
   LÓGICA DE DETECCIÓN Y ALERTAS
===================================== */
$estudiantes = [];
$materias = [];
$total_estudiantes = 0;
$total_materias = 0;

if ($grado_id > 0) {
    // 1. Validar si hay estudiantes
    $estudiantes = $conn->query("SELECT numero, nombre, apellido FROM estudiantes WHERE grado_id = $grado_id ORDER BY apellido, nombre");
    $total_estudiantes = $estudiantes->num_rows;

    // 2. Validar si hay materias asignadas
    $materias = $conn->query("
        SELECT m.id, m.nombre
        FROM asignacion_materias am
        INNER JOIN materias m ON am.materia_id = m.id
        WHERE am.grado_id = $grado_id
        ORDER BY m.nombre ASC
    ");
    $total_materias = $materias->num_rows;
}
?>

<div class="container mt-4">

    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-journal-check"></i>
        Calificaciones por Competencia
    </h3>

    <?php if(!empty($mensaje)){ ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show rounded-4 shadow-sm">
            <?php echo $mensaje; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <div class="card border-0 shadow-lg rounded-4 p-4 mb-4">
        <form method="GET" action="principal.php" class="row g-3">
            <input type="hidden" name="seccion" value="calificaciones">

            <div class="col-md-3">
                <label class="form-label fw-semibold">Año Escolar</label>
                <select name="anio_id" class="form-select" required>
                    <option value="">-- Seleccione Año --</option>
                    <?php while($a = $anos_escolares->fetch_assoc()){ ?>
                        <option value="<?php echo $a['id']; ?>" <?php echo ($anio_id == $a['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($a['nombre']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Seleccionar Grado</label>
                <select name="grado_id" class="form-select" required>
                    <option value="">-- Seleccione Grado --</option>
                    <?php 
                    $grados->data_seek(0);
                    while($g = $grados->fetch_assoc()){ 
                    ?>
                        <option value="<?php echo $g['id']; ?>" <?php echo ($grado_id == $g['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($g['grado']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Competencia</label>
                <select name="competencia_id" class="form-select" required>
                    <option value="">-- Seleccione Competencia --</option>
                    <?php while($c = $competencias->fetch_assoc()){ ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($competencia_id == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['nombre']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    <i class="bi bi-search"></i> Buscar Curso
                </button>
            </div>
        </form>
    </div>

    <?php if ($grado_id > 0 && $competencia_id > 0): ?>
        
        <?php if ($total_estudiantes === 0): ?>
            <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-1"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Curso Vacío</h5>
                    <p class="m-0 text-secondary">Actualmente no hay ningún estudiante inscrito en el grado seleccionado.</p>
                </div>
            </div>

        <?php elseif ($total_materias === 0): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 d-flex align-items-center gap-3">
                <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Sin Materias Asignadas</h5>
                    <p class="m-0 text-secondary">Este grado no cuenta con materias asignadas todavía. Por favor, ve al módulo de administración e instrumenta las asignaciones de asignaturas.</p>
                </div>
            </div>

        <?php else: ?>
            
            <div class="card border-0 shadow-lg rounded-4 p-4">
                <h5 class="mb-4 text-dark fw-bold">
                    <i class="bi bi-people-fill text-secondary"></i> Registro de Calificaciones
                </h5>

                <form method="POST" action="principal.php?seccion=calificaciones&grado_id=<?php echo $grado_id; ?>&anio_id=<?php echo $anio_id; ?>&competencia_id=<?php echo $competencia_id; ?>&estudiante_id=<?php echo $estudiante_id; ?>">
                    
                    <input type="hidden" name="grado_id" value="<?php echo $grado_id; ?>">
                    <input type="hidden" name="anio_id" value="<?php echo $anio_id; ?>">
                    <input type="hidden" name="competencia_id" value="<?php echo $competencia_id; ?>">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estudiante</label>
                            <select name="estudiante_id" class="form-select border-2" onchange="cambiarEstudiante(this.value)" required>
                                <option value="">-- Seleccione Estudiante --</option>
                                <?php 
                                $estudiantes->data_seek(0);
                                while($e = $estudiantes->fetch_assoc()){ 
                                ?>
                                    <option value="<?php echo $e['numero']; ?>" <?php echo ($estudiante_id == $e['numero']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($e['apellido'] . ", " . $e['nombre']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <?php if ($estudiante_id > 0) { ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle border">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th class="text-start" style="width: 40%;">Materia</th>
                                    <th>P1</th>
                                    <th>P2</th>
                                    <th>P3</th>
                                    <th>P4</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $materias->data_seek(0);
                            while($m = $materias->fetch_assoc()){ 
                                $m_id = $m['id'];
                                
                                // PRECARGA DE CALIFICACIONES DE LA BASE DE DATOS
                                $p1_val = 0; $p2_val = 0; $p3_val = 0; $p4_val = 0;
                                $buscar_nota = $conn->query("
                                    SELECT p1, p2, p3, p4 
                                    FROM calificaciones 
                                    WHERE estudiante_id = $estudiante_id 
                                    AND materia_id = $m_id 
                                    AND anio_id = $anio_id 
                                    AND competencia_id = $competencia_id
                                    LIMIT 1
                                ");
                                if($nota_row = $buscar_nota->fetch_assoc()){
                                    $p1_val = $nota_row['p1'];
                                    $p2_val = $nota_row['p2'];
                                    $p3_val = $nota_row['p3'];
                                    $p4_val = $nota_row['p4'];
                                }
                            ?>
                                <tr>
                                    <td class="fw-semibold text-dark">
                                        <?php echo htmlspecialchars($m['nombre']); ?>
                                        <input type="hidden" name="materia_id[]" value="<?php echo $m['id']; ?>">
                                    </td>
                                    <td>
                                        <input type="number" name="p1[]" class="form-control text-center" min="0" max="100" step="0.01" value="<?php echo $p1_val; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="p2[]" class="form-control text-center" min="0" max="100" step="0.01" value="<?php echo $p2_val; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="p3[]" class="form-control text-center" min="0" max="100" step="0.01" value="<?php echo $p3_val; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="p4[]" class="form-control text-center" min="0" max="100" step="0.01" value="<?php echo $p4_val; ?>" required>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-success px-4 shadow-sm" onclick="abrirModalGuardar()">
                            <i class="bi bi-save"></i> Guardar Calificaciones
                        </button>
                    </div>
                    <?php } ?>

                    <div class="modal fade" id="modalGuardar" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title"><i class="bi bi-question-circle"></i> Confirmar Registro</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center py-4">
                                    <h5 class="text-secondary">¿Desea guardar las calificaciones para esta competencia?</h5>
                                    <p class="text-muted small">Los promedios finales se recalcularán automáticamente.</p>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" name="guardar_calificaciones" class="btn btn-success px-4">Sí, guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function cambiarEstudiante(idEstudiante) {
    let baseQuery = "principal.php?seccion=calificaciones&anio_id=<?php echo $anio_id; ?>&grado_id=<?php echo $grado_id; ?>&competencia_id=<?php echo $competencia_id; ?>";
    if(idEstudiante !== "") {
        window.location.href = baseQuery + "&estudiante_id=" + idEstudiante;
    } else {
        window.location.href = baseQuery;
    }
}

setTimeout(() => {
    let alerta = document.querySelector('.alert');
    if(alerta){
        let bsAlert = new bootstrap.Alert(alerta);
        bsAlert.close();
    }
}, 4000);

function abrirModalGuardar(){
    let modal = new bootstrap.Modal(document.getElementById('modalGuardar'));
    modal.show();
}
</script>