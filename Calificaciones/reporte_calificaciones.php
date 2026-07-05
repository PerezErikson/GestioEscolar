<?php
include("conexion/conexion.php");

// Capturamos las variables por GET de forma segura
$anio_id = isset($_GET['anio_id']) ? intval($_GET['anio_id']) : 0;
$grado_id = isset($_GET['grado_id']) ? intval($_GET['grado_id']) : 0;
$estudiante_id = isset($_GET['estudiante_id']) ? intval($_GET['estudiante_id']) : 0;

/* =====================================
   OBTENER GRADOS
===================================== */
$grados = $conn->query("
    SELECT
        g.id,
        CONCAT(g.nombre,' ',s.nombre) AS grado
    FROM grados1 g
    INNER JOIN secciones s
        ON g.id_seccion = s.id
    ORDER BY g.nombre, s.nombre
");

/* =====================================
   OBTENER AÑOS ESCOLARES
===================================== */
$anos_escolares = $conn->query("SELECT id, nombre FROM anio_escolar ORDER BY nombre DESC");
?>

<div class="container-fluid mt-4 px-4">

    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-bar-chart-fill"></i>
        Reporte de Calificaciones por Competencias
    </h3>

    <div class="card border-0 shadow-lg rounded-4 p-4 mb-4">
        <form method="GET" action="principal.php" class="row g-3">
            <input type="hidden" name="seccion" value="reporte_calificaciones">

            <div class="col-md-3">
                <label class="form-label fw-semibold">Año Escolar</label>
                <select name="anio_id" class="form-select rounded-3" required>
                    <option value="">-- Seleccione Año --</option>
                    <?php while($a = $anos_escolares->fetch_assoc()){ ?>
                        <option value="<?php echo $a['id']; ?>" <?php echo ($anio_id == $a['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($a['nombre']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Seleccionar Grado</label>
                <select name="grado_id" class="form-select rounded-3" required>
                    <option value="">-- Seleccione --</option>
                    <?php while($g = $grados->fetch_assoc()){ ?>
                        <option value="<?php echo $g['id']; ?>" <?php echo ($grado_id == $g['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($g['grado']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold">
                    <i class="bi bi-search"></i> Buscar Grado
                </button>
            </div>
        </form>
    </div>

<?php
if($grado_id > 0 && $anio_id > 0){

    // OBTENER EL NOMBRE DEL GRADO SELECCIONADO
    $info_grado = $conn->query("SELECT nombre FROM grados1 WHERE id = $grado_id LIMIT 1")->fetch_assoc();
    $nombre_grado_sel = $info_grado ? $info_grado['nombre'] : "";

    // OBTENER LAS COMPETENCIAS GENERALES
    $competencias_db = $conn->query("SELECT id, nombre FROM competencias ORDER BY id ASC");
    $lista_competencias = [];
    while($c = $competencias_db->fetch_assoc()) {
        $lista_competencias[] = $c;
    }

    // 1. LISTADO DE ESTUDIANTES (PANEL WEB)
    $estudiantes = $conn->query("
        SELECT numero, nombre, apellido 
        FROM estudiantes 
        WHERE grado_id = $grado_id 
        ORDER BY apellido, nombre
    ");

    if($estudiantes && $estudiantes->num_rows > 0){
        $array_estudiantes = [];
?>
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <h5 class="fw-bold text-secondary m-0"><i class="bi bi-people-fill"></i> Lista de Estudiantes</h5>
                
                <button onclick="imprimirTodosLosReportes();" class="btn btn-dark rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-printer-fill"></i> Imprimir Todos los Estudiantes
                </button>
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text bg-light border-end-0 rounded-start-pill px-3">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" id="buscadorEstudiantes" class="form-control border-start-0 rounded-end-pill ps-2" placeholder="Escribe el nombre o apellido del estudiante para filtrar...">
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Estudiante</th>
                            <th class="text-center" style="width: 200px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEstudiantes">
                        <?php while($est = $estudiantes->fetch_assoc()){ 
                            $array_estudiantes[] = $est; 
                            if ($estudiante_id == $est['numero']) {
                                $url_accion = "principal.php?seccion=reporte_calificaciones&anio_id=$anio_id&grado_id=$grado_id";
                                $btn_clase = "btn-primary";
                                $texto_btn = "Ocultar Sábana";
                                $icono_btn = "bi-eye-slash";
                            } else {
                                $url_accion = "principal.php?seccion=reporte_calificaciones&anio_id=$anio_id&grado_id=$grado_id&estudiante_id=" . $est['numero'];
                                $btn_clase = "btn-outline-primary";
                                $texto_btn = "Ver Matriz";
                                $icono_btn = "bi-eye";
                            }
                        ?>
                            <tr class="<?php echo ($estudiante_id == $est['numero']) ? 'table-primary fw-bold' : ''; ?>">
                                <td class="nombre-estudiante"><?php echo htmlspecialchars($est['apellido'] . ", " . $est['nombre']); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo $url_accion; ?>" class="btn <?php echo $btn_clase; ?> btn-sm rounded-pill px-3">
                                        <i class="bi <?php echo $icono_btn; ?>"></i> <?php echo $texto_btn; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php 
    } else {
        echo '<div class="alert alert-warning border-0 shadow-sm rounded-4">⚠️ No hay estudiantes registrados en este grado.</div>';
    }

    // 2. MATRIZ INDIVIDUAL EN PANTALLA
    if($estudiante_id > 0){
        $info_estudiante = $conn->query("SELECT nombre, apellido FROM estudiantes WHERE numero = $estudiante_id LIMIT 1")->fetch_assoc();
        $nombre_completo_est = $info_estudiante ? $info_estudiante['apellido'] . ", " . $info_estudiante['nombre'] : "";

        $notes_query = $conn->query("
            SELECT m.id AS materia_id, m.nombre AS materia_nombre, c.competencia_id, c.p1, c.p2, c.p3, c.p4, c.nota_final, c.rec_final, c.rec_especial
            FROM calificaciones c
            INNER JOIN materias m ON c.materia_id = m.id
            WHERE c.grado_id = $grado_id AND c.anio_id = $anio_id AND c.estudiante_id = $estudiante_id
            ORDER BY m.nombre ASC
        ");

        if($notes_query && $notes_query->num_rows > 0){
            $matriz = [];
            while($n = $notes_query->fetch_assoc()){
                $m_id = $n['materia_id'];
                $comp_id = $n['competencia_id'];
                if(!isset($matriz[$m_id])) {
                    $matriz[$m_id] = [
                        'nombre' => $n['materia_nombre'], 
                        'competencias' => [],
                        'rec_final' => null, 
                        'rec_especial' => null 
                    ];
                }
                
                // CORRECCIÓN: Si la fila actual trae nota de recuperación, la guardamos
                if (!style_null_or_empty($n['rec_final'])) {
                    $matriz[$m_id]['rec_final'] = $n['rec_final'];
                }
                if (!style_null_or_empty($n['rec_especial'])) {
                    $matriz[$m_id]['rec_especial'] = $n['rec_especial'];
                }

                $matriz[$m_id]['competencias'][$comp_id] = ['p1' => $n['p1'], 'p2' => $n['p2'], 'p3' => $n['p3'], 'p4' => $n['p4'], 'final' => $n['nota_final']];
            }
?>
            <div class="d-flex justify-content-end align-items-center mb-3">
                <button onclick="imprimirReporteHorizontal();" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-printer"></i> Imprimir Reporte Seleccionado
                </button>
            </div>

            <div class="card border-0 shadow-lg rounded-4 mb-5" id="area-reporte-sabana">
                <div class="card-body p-4">
                    <div class="print-header text-center mb-4">
                        <h3 class="fw-bold mb-1 text-uppercase" style="font-size: 20px; color:#111;">Centro Educativo Pozo De Bejuco</h3>
                        <h4 class="fw-semibold mb-3" style="font-size: 14px; color: #333;">MATRIZ DE EVALUACIÓN DE CALIFICACIONES CURRICULARES</h4>
                        <div class="container-fluid px-5">
                            <div class="row text-start" style="font-size: 13px;">
                                <div class="col-7"><strong>ESTUDIANTE:</strong> <span class="text-uppercase" style="border-bottom: 1px solid #777; padding-right: 20px;"><?php echo htmlspecialchars($nombre_completo_est); ?></span></div>
                                <div class="col-5 text-end"><strong>GRADO:</strong> <span class="text-uppercase" style="border-bottom: 1px solid #777; padding-right: 10px;"><?php echo htmlspecialchars($nombre_grado_sel); ?></span></div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center s-tabla-reporte">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center fw-bold text-uppercase cell-materia-title">ÁREAS CURRICULARES</th>
                                    <?php foreach($lista_competencias as $comp){ ?>
                                        <th colspan="4" class="fw-bold header-competencia"><?php echo htmlspecialchars($comp['nombre']); ?></th>
                                    <?php } ?>
                                    <th colspan="<?php echo count($lista_competencias); ?>" class="fw-bold bg-dark text-white header-final-title">Calificación final por competencia</th>
                                    <th rowspan="2" class="fw-bold cell-nueva-columna">Calificación final del área</th>
                                    <th rowspan="2" class="fw-bold cell-nueva-columna">Calificación recuperación final</th>
                                    <th rowspan="2" class="fw-bold cell-nueva-columna">Calificación recuperación especial</th>
                                </tr>
                                <tr>
                                    <?php foreach($lista_competencias as $comp){ ?>
                                        <th class="cell-periodo">P1</th><th class="cell-periodo">P2</th><th class="cell-periodo">P3</th><th class="cell-periodo">P4</th>
                                    <?php } ?>
                                    <?php foreach($lista_competencias as $index => $comp){ ?>
                                        <th class="bg-secondary text-white cell-c-final">C<?php echo ($index + 1); ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($matriz as $materia_id => $datos_materia){ 
                                    $suma_finales = 0;
                                    $conteo_competencias = 0;
                                    foreach($lista_competencias as $comp){
                                        $c_id = $comp['id'];
                                        if(isset($datos_materia['competencias'][$c_id])) {
                                            $suma_finales += $datos_materia['competencias'][$c_id]['final'];
                                            $conteo_competencias++;
                                        }
                                    }
                                    $calificacion_final_area = ($conteo_competencias > 0) ? round($suma_finales / $conteo_competencias) : 0;
                                ?>
                                    <tr>
                                        <td class="text-start fw-bold text-dark cell-materia-name"><?php echo htmlspecialchars($datos_materia['nombre']); ?></td>
                                        <?php foreach($lista_competencias as $comp){ 
                                            $c_id = $comp['id'];
                                            $tiene_nota = isset($datos_materia['competencias'][$c_id]);
                                        ?>
                                            <td class="cell-nota-valor"><?php echo $tiene_nota ? number_format($datos_materia['competencias'][$c_id]['p1'], 0) : '-'; ?></td>
                                            <td class="cell-nota-valor"><?php echo $tiene_nota ? number_format($datos_materia['competencias'][$c_id]['p2'], 0) : '-'; ?></td>
                                            <td class="cell-nota-valor"><?php echo $tiene_nota ? number_format($datos_materia['competencias'][$c_id]['p3'], 0) : '-'; ?></td>
                                            <td class="cell-nota-valor"><?php echo $tiene_nota ? number_format($datos_materia['competencias'][$c_id]['p4'], 0) : '-'; ?></td>
                                        <?php } ?>
                                        
                                        <?php foreach($lista_competencias as $comp){ 
                                            $c_id = $comp['id'];
                                            $tiene_nota = isset($datos_materia['competencias'][$c_id]);
                                        ?>
                                            <td class="fw-bold text-dark cell-final-bg"><?php echo $tiene_nota ? number_format($datos_materia['competencias'][$c_id]['final'], 0) : '-'; ?></td>
                                        <?php } ?>
                                        
                                        <td class="fw-bold cell-nueva-valor"><?php echo ($calificacion_final_area > 0) ? $calificacion_final_area : '-'; ?></td>
                                        <td class="cell-nueva-valor fw-bold"><?php echo (!style_null_or_empty($datos_materia['rec_final'])) ? number_format($datos_materia['rec_final'], 0) : '-'; ?></td>
                                        <td class="cell-nueva-valor fw-bold"><?php echo (!style_null_or_empty($datos_materia['rec_especial'])) ? number_format($datos_materia['rec_especial'], 0) : '-'; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
<?php
        }
    }

    // BLOQUE OCULTO GENERADOR DE DATOS DE TODOS LOS ALUMNOS (PARA MASIVO)
    if(!empty($array_estudiantes)){
        echo '<div id="contenedor-impresion-masiva" style="display:none;">';
        foreach($array_estudiantes as $alumno){
            $id_est = $alumno['numero'];
            $nombre_est_completo = $alumno['apellido'] . ", " . $alumno['nombre'];

            $notas_masivas = $conn->query("
                SELECT m.id AS materia_id, m.nombre AS materia_nombre, c.competencia_id, c.p1, c.p2, c.p3, c.p4, c.nota_final, c.rec_final, c.rec_especial
                FROM calificaciones c
                INNER JOIN materias m ON c.materia_id = m.id
                WHERE c.grado_id = $grado_id AND c.anio_id = $anio_id AND c.estudiante_id = $id_est
                ORDER BY m.nombre ASC
            ");

            if($notas_masivas && $notas_masivas->num_rows > 0){
                $matriz_m = [];
                while($nm = $notas_masivas->fetch_assoc()){
                    $m_id = $nm['materia_id'];
                    $comp_id = $nm['competencia_id'];
                    if(!isset($matriz_m[$m_id])) {
                        $matriz_m[$m_id] = [
                            'nombre' => $nm['materia_nombre'], 
                            'competencias' => [],
                            'rec_final' => null,
                            'rec_especial' => null
                        ];
                    }

                    // CORRECCIÓN TAMBIÉN AQUÍ: Capturar notas de recuperación en el bucle masivo
                    if (!style_null_or_empty($nm['rec_final'])) {
                        $matriz_m[$m_id]['rec_final'] = $nm['rec_final'];
                    }
                    if (!style_null_or_empty($nm['rec_especial'])) {
                        $matriz_m[$m_id]['rec_especial'] = $nm['rec_especial'];
                    }

                    $matriz_m[$m_id]['competencias'][$comp_id] = ['p1' => $nm['p1'], 'p2' => $nm['p2'], 'p3' => $nm['p3'], 'p4' => $nm['p4'], 'final' => $nm['nota_final']];
                }
?>
                <div class="bloque-boleta-individual" style="page-break-after: always; break-after: page;">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold mb-1 text-uppercase" style="font-size: 20px; color:#111;">Centro Educativo Pozo De Bejuco</h3>
                        <h4 class="fw-semibold mb-3" style="font-size: 14px; color: #333;">MATRIZ DE EVALUACIÓN DE CALIFICACIONES CURRICULARES</h4>
                        <table style="width:100%; font-size:13px; margin-bottom:15px; border:none !important; text-align:left;">
                            <tr style="border:none !important;"><td style="border:none !important; width:65%;"><strong>ESTUDIANTE:</strong> <span class="text-uppercase" style="border-bottom:1px solid #666; display:inline-block; width:80%;"><?php echo htmlspecialchars($nombre_est_completo); ?></span></td>
                            <td style="border:none !important; width:35%; text-align:right;"><strong>GRADO:</strong> <span class="text-uppercase" style="border-bottom:1px solid #666; display:inline-block; width:60%; text-align:center;"><?php echo htmlspecialchars($nombre_grado_sel); ?></span></td></tr>
                        </table>
                    </div>

                    <table class="table table-bordered align-middle text-center s-tabla-reporte">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center fw-bold text-uppercase cell-materia-title">ÁREAS CURRICULARES</th>
                                <?php foreach($lista_competencias as $comp){ ?>
                                    <th colspan="4" class="fw-bold header-competencia"><?php echo htmlspecialchars($comp['nombre']); ?></th>
                                <?php } ?>
                                <th colspan="<?php echo count($lista_competencias); ?>" class="fw-bold bg-dark text-white header-final-title">Calificación final por competencia</th>
                                <th rowspan="2" class="fw-bold cell-nueva-columna">Calificación final del área</th>
                                <th rowspan="2" class="fw-bold cell-nueva-columna">Calificación recuperación final</th>
                                <th rowspan="2" class="fw-bold cell-nueva-columna">Calificación recuperación especial</th>
                            </tr>
                            <tr>
                                <?php foreach($lista_competencias as $comp){ ?>
                                    <th class="cell-periodo">P1</th><th class="cell-periodo">P2</th><th class="cell-periodo">P3</th><th class="cell-periodo">P4</th>
                                <?php } ?>
                                <?php foreach($lista_competencias as $index => $comp){ ?>
                                    <th class="bg-secondary text-white cell-c-final">C<?php echo ($index + 1); ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($matriz_m as $materia_id => $datos_materia){ 
                                $suma_finales_m = 0;
                                $conteo_competencias_m = 0;
                                foreach($lista_competencias as $comp){
                                    $c_id = $comp['id'];
                                    if(isset($datos_materia['competencias'][$c_id])) {
                                        $suma_finales_m += $datos_materia['competencias'][$c_id]['final'];
                                        $conteo_competencias_m++;
                                    }
                                }
                                $calificacion_final_area_m = ($conteo_competencias_m > 0) ? round($suma_finales_m / $conteo_competencias_m) : 0;
                            ?>
                                <tr>
                                    <td class="text-start fw-bold text-dark cell-materia-name"><?php echo htmlspecialchars($datos_materia['nombre']); ?></td>
                                    <?php foreach($lista_competencias as $comp){ 
                                        $c_id = $comp['id'];
                                        $tiene_nota = isset($datos_materia['competencias'][$c_id]);
                                    ?>
                                        <td class="cell-nota-valor"><?php echo $tiene_nota ? number_format($datos_materia['competencias'][$c_id]['p1'], 0) : '-'; ?></td>
                                        <td class="cell-nota-valor"><?php echo $tiene_nota ? number_format($datos_materia['competencias'][$c_id]['p2'], 0) : '-'; ?></td>
                                        <td class="cell-nota-valor"><?php echo $tiene_nota ? number_format($datos_materia['competencias'][$c_id]['p3'], 0) : '-'; ?></td>
                                        <td class="cell-nota-valor"><?php echo $tiene_nota ? number_format($datos_materia['competencias'][$c_id]['p4'], 0) : '-'; ?></td>
                                    <?php } ?>
                                    
                                    <?php foreach($lista_competencias as $comp){ 
                                        $c_id = $comp['id'];
                                        $tiene_nota = isset($datos_materia['competencias'][$c_id]);
                                    ?>
                                        <td class="fw-bold text-dark cell-final-bg"><?php echo $tiene_nota ? number_format($datos_materia['competencias'][$c_id]['final'], 0) : '-'; ?></td>
                                    <?php } ?>
                                    
                                    <td class="fw-bold cell-nueva-valor"><?php echo ($calificacion_final_area_m > 0) ? $calificacion_final_area_m : '-'; ?></td>
                                    <td class="cell-nueva-valor fw-bold"><?php echo (!style_null_or_empty($datos_materia['rec_final'])) ? number_format($datos_materia['rec_final'], 0) : '-'; ?></td>
                                    <td class="cell-nueva-valor fw-bold"><?php echo (!style_null_or_empty($datos_materia['rec_especial'])) ? number_format($datos_materia['rec_especial'], 0) : '-'; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
<?php
            }
        }
        echo '</div>';
    }
}

// Función auxiliar para comprobar si un registro de nota es nulo o vacío
function style_null_or_empty($value) {
    return ($value === null || $value === '' || $value === 'NULL');
}
?>
</div>

<style>
.s-tabla-reporte th, .s-tabla-reporte td { vertical-align: middle !important; border: 1px solid #cbd5e1 !important; }
.header-competencia { background-color: #fdebd0 !important; font-size: 0.8rem; }
.cell-materia-title { background-color: #f5eeeb !important; font-size: 0.8rem; }
.cell-nueva-columna { background-color: #f9d5e5 !important; color: #000 !important; font-size: 0.75rem; max-width: 90px; }
.cell-nueva-valor { background-color: #fdf2f7 !important; font-size: 0.8rem; }
</style>

<script>
document.getElementById('buscadorEstudiantes').addEventListener('keyup', function() {
    var valorBusqueda = this.value.toLowerCase().trim();
    var filas = document.querySelectorAll('#tablaEstudiantes tr');

    filas.forEach(function(filas) {
        var celdaNombre = filas.querySelector('.nombre-estudiante');
        if (celdaNombre) {
            var textoNombre = celdaNombre.textContent.toLowerCase();
            if (textoNombre.indexOf(valorBusqueda) > -1) {
                filas.style.display = '';
            } else {
                filas.style.display = 'none';
            }
        }
    });
});

function imprimirReporteHorizontal() {
    var contenido = document.getElementById('area-reporte-sabana').innerHTML;
    ejecutarImpresionLimpia(contenido);
}

function imprimirTodosLosReportes() {
    var contenedorMasivo = document.getElementById('contenedor-impresion-masiva');
    if (!contenedorMasivo || contenedorMasivo.innerHTML.trim() == "") {
        alert("No hay reportes de calificaciones disponibles para procesar en este grupo.");
        return;
    }
    var contenido = contenedorMasivo.innerHTML;
    ejecutarImpresionLimpia(contenido);
}

function ejecutarImpresionLimpia(htmlContent) {
    var ventanaImpresion = window.open('', '_blank', 'height=800,width=1250');
    ventanaImpresion.document.write('<html><head><title>Impresión de Registro de Calificaciones</title>');
    ventanaImpresion.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
    
    ventanaImpresion.document.write('<style>');
    ventanaImpresion.document.write('@page { size: letter landscape; margin: 0.4cm 0.5cm; }');
    ventanaImpresion.document.write('body { background: #fff !important; color: #000 !important; font-family: Arial, sans-serif; padding: 5px; }');
    ventanaImpresion.document.write('.s-tabla-reporte { width: 100% !important; font-size: 9px !important; border-collapse: collapse !important; margin-top: 10px !important; margin-bottom: 25px !important; page-break-inside: avoid; }');
    ventanaImpresion.document.write('.s-tabla-reporte th, .s-tabla-reporte td { padding: 4px 2px !important; border: 1.5px solid #222 !important; text-align: center !important; vertical-align: middle !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }');
    ventanaImpresion.document.write('.cell-materia-title { background-color: #f5eeeb !important; font-size: 9px !important; font-weight: bold !important; width: 140px !important; }');
    ventanaImpresion.document.write('.header-competencia { background-color: #fdebd0 !important; font-size: 9px !important; font-weight: bold !important; color: #111 !important; }');
    ventanaImpresion.document.write('.cell-materia-name { font-size: 9.5px !important; text-align: left !important; padding-left: 6px !important; font-weight: bold !important; white-space: nowrap !important; }');
    ventanaImpresion.document.write('.cell-periodo { font-size: 8px !important; width: 22px !important; font-weight: bold !important; background-color: #fff !important; }');
    ventanaImpresion.document.write('.cell-nota-valor { font-size: 9px !important; width: 22px !important; }');
    ventanaImpresion.document.write('.header-final-title { background-color: #e5e7eb !important; color: #000 !important; font-size: 9px !important; font-weight: bold !important; }');
    ventanaImpresion.document.write('.cell-c-final { background-color: #f3f4f6 !important; color: #000 !important; font-size: 8px !important; width: 26px !important; font-weight: bold !important; }');
    ventanaImpresion.document.write('.cell-final-bg { background-color: #f9fafb !important; width: 26px !important; font-size: 9px !important; font-weight: bold !important; }');
    
    ventanaImpresion.document.write('.cell-nueva-columna { background-color: #f5b7b1 !important; color: #000 !important; font-size: 8.5px !important; font-weight: bold !important; width: 65px !important; word-wrap: break-word !important; }');
    ventanaImpresion.document.write('.cell-nueva-valor { background-color: #fff !important; font-size: 9.5px !important; width: 65px !important; }');
    
    ventanaImpresion.document.write('.bloque-boleta-individual { page-break-after: always !important; break-after: page !important; }');
    ventanaImpresion.document.write('<' + '/style>'); // Corregido el cierre de etiqueta style para evitar conflictos
    
    ventanaImpresion.document.write('</head><body>');
    ventanaImpresion.document.write(htmlContent);
    ventanaImpresion.document.write('</body></html>');
    
    ventanaImpresion.document.close();
    ventanaImpresion.focus();
    
    setTimeout(function() {
        ventanaImpresion.print();
        ventanaImpresion.close();
    }, 500);
}
</script>