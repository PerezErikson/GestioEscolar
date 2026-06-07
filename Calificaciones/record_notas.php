<?php
// Forzar mostrar errores por si acaso
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("conexion/conexion.php");

// Solo capturamos el estudiante_id seleccionado
$estudiante_id = isset($_GET['estudiante_id']) ? intval($_GET['estudiante_id']) : 0;

/* =====================================
   OBTENER LAS COMPETENCIAS GENERALES
===================================== */
$competencias_db = $conn->query("SELECT id, nombre FROM competencias ORDER BY id ASC");
$lista_competencias = [];
if ($competencias_db) {
    while($c = $competencias_db->fetch_assoc()) {
        $lista_competencias[] = $c;
    }
}

/* =====================================
   OBTENER TODOS LOS ESTUDIANTES DIRECTAMENTE
===================================== */
$estudiantes = $conn->query("
    SELECT e.numero, e.nombre, e.apellido, CONCAT(g.nombre, ' ', s.nombre) AS grado_actual
    FROM estudiantes e
    LEFT JOIN grados1 g ON e.grado_id = g.id
    LEFT JOIN secciones s ON g.id_seccion = s.id
    ORDER BY e.apellido, e.nombre
");
?>

<div class="container-fluid mt-4 px-4">

    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-journal-text"></i>
        Record de Notas Histórico
    </h3>

    <!-- BLOQUE: LISTA DIRECTA DE ESTUDIANTES (Como en la imagen image_8d5962.png) -->
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body">
            <h5 class="fw-bold text-secondary mb-3">
                <i class="bi bi-people-fill"></i> Estudiantes Registrados
            </h5>
            
            <!-- Buscador instantáneo global -->
            <div class="mb-3 input-group">
                <span class="input-group-text bg-light border-end-0 rounded-start-pill px-3">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" id="buscadorEstudiantes" class="form-control border-start-0 rounded-end-pill ps-2" placeholder="Filtrar por nombre o apellido...">
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Estudiante</th>
                            <th>Grado Actual</th>
                            <th class="text-center" style="width: 200px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEstudiantes">
                        <?php 
                        if($estudiantes && $estudiantes->num_rows > 0){
                            while($est = $estudiantes->fetch_assoc()){ 
                                if ($estudiante_id == $est['numero']) {
                                    $url_accion = "principal.php?seccion=record_notas";
                                    $btn_clase = "btn-danger";
                                    $texto_btn = "Ocultar Record";
                                    $icono_btn = "bi-eye-slash";
                                } else {
                                    $url_accion = "principal.php?seccion=record_notas&estudiante_id=" . $est['numero'];
                                    $btn_clase = "btn-outline-primary";
                                    $texto_btn = "Ver Record Completo";
                                    $icono_btn = "bi-eye";
                                }
                            ?>
                                <tr class="<?php echo ($estudiante_id == $est['numero']) ? 'table-primary fw-bold' : ''; ?>">
                                    <td class="nombre-estudiante"><?php echo htmlspecialchars($est['apellido'] . ", " . $est['nombre']); ?></td>
                                    <td class="text-secondary" style="font-size: 13px;"><?php echo htmlspecialchars($est['grado_actual'] ?? 'Sin Asignar'); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo $url_accion; ?>" class="btn <?php echo $btn_clase; ?> btn-sm rounded-pill px-3">
                                            <i class="bi <?php echo $icono_btn; ?>"></i> <?php echo $texto_btn; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                            }
                        } else {
                            echo '<tr><td colspan="3" class="text-center text-muted">No hay estudiantes registrados en la base de datos.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
    /* ========================================================
       BLOQUE: RECORD HISTÓRICO MULTI-AÑO DEL ESTUDIANTE SELECCIONADO
       ======================================================== */
    if ($estudiante_id > 0) {
        
        $info_estudiante = $conn->query("SELECT nombre, apellido FROM estudiantes WHERE numero = $estudiante_id LIMIT 1")->fetch_assoc();
        $nombre_completo_est = $info_estudiante ? $info_estudiante['apellido'] . ", " . $info_estudiante['nombre'] : "";

        // Buscamos todos los años y grados históricos donde este alumno tenga calificaciones cursadas
        $anios_cursados = $conn->query("
            SELECT DISTINCT c.anio_id, ae.nombre AS anio_nombre, c.grado_id, g.nombre AS grado_nombre
            FROM calificaciones c
            INNER JOIN anio_escolar ae ON c.anio_id = ae.id
            INNER JOIN grados1 g ON c.grado_id = g.id
            WHERE c.estudiante_id = $estudiante_id
            ORDER BY ae.nombre ASC
        ");

        if ($anios_cursados && $anios_cursados->num_rows > 0) {
?>
            <div class="card border-0 shadow-lg rounded-4 mb-4" id="area-record-completo">
                <div class="card-body p-4">
                    
                    <!-- Encabezado de Impresión -->
                    <div class="text-center mb-4">
                        <h3 class="fw-bold mb-1 text-uppercase text-dark">Centro Educativo Pozo De Bejuco</h3>
                        <h4 class="fw-semibold mb-3 text-secondary" style="font-size: 14px;">RECORD DE NOTAS ACUMULATIVO POR COMPETENCIAS</h4>
                        <div class="row text-start px-md-5 mt-3" style="font-size: 13px;">
                            <div class="col-8"><strong>ESTUDIANTE:</strong> <span class="text-uppercase border-bottom border-secondary pb-1 ps-2 d-inline-block w-75"><?php echo htmlspecialchars($nombre_completo_est); ?></span></div>
                            <div class="col-4 text-end"><strong>ID / No:</strong> <span class="border-bottom border-secondary pb-1 px-2"><?php echo $estudiante_id; ?></span></div>
                        </div>
                    </div>

                    <?php 
                    while ($periodo = $anios_cursados->fetch_assoc()) {
                        $curr_anio_id = $periodo['anio_id'];
                        $curr_grado_id = $periodo['grado_id'];
                        
                        $notas_query = $conn->query("
                            SELECT m.id AS materia_id, m.nombre AS materia_nombre, c.competencia_id, c.p1, c.p2, c.p3, c.p4, c.nota_final
                            FROM calificaciones c
                            INNER JOIN materias m ON c.materia_id = m.id
                            WHERE c.estudiante_id = $estudiante_id AND c.anio_id = $curr_anio_id AND c.grado_id = $curr_grado_id
                            ORDER BY m.nombre ASC
                        ");

                        $matriz = [];
                        while ($n = $notas_query->fetch_assoc()) {
                            $m_id = $n['materia_id'];
                            $comp_id = $n['competencia_id'];
                            if (!isset($matriz[$m_id])) {
                                $matriz[$m_id] = ['nombre' => $n['materia_nombre'], 'competencias' => []];
                            }
                            $matriz[$m_id]['competencias'][$comp_id] = [
                                'p1' => $n['p1'], 'p2' => $n['p2'], 'p3' => $n['p3'], 'p4' => $n['p4'], 'final' => $n['nota_final']
                            ];
                        }
                    ?>
                        
                        <!-- Contenedor Individual de Año -->
                        <div class="bloque-anio-contenedor mb-5" id="bloque-anio-<?php echo $curr_anio_id; ?>">
                            <div class="bg-light border p-2 rounded-3 mb-2 d-flex justify-content-between align-items-center dynamic-year-block">
                                <span class="fw-bold text-dark m-0"><i class="bi bi-calendar-check-fill text-primary"></i> Año Escolar: <?php echo htmlspecialchars($periodo['anio_nombre']); ?> — <?php echo htmlspecialchars($periodo['grado_nombre']); ?></span>
                                <button onclick="imprimirAnioEspecifico('bloque-anio-<?php echo $curr_anio_id; ?>')" class="btn btn-sm btn-dark rounded-pill px-3 no-print"><i class="bi bi-printer"></i> Imprimir Solo Este Año</button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center s-tabla-reporte mb-0">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th rowspan="2" class="text-center fw-bold text-uppercase cell-materia-title">ÁREAS CURRICULARES</th>
                                            <?php foreach($lista_competencias as $comp){ ?>
                                                <th colspan="4" class="fw-bold header-competencia text-wrap" style="max-width: 150px; font-size: 11px;"><?php echo htmlspecialchars($comp['nombre']); ?></th>
                                            <?php } ?>
                                            <th colspan="<?php echo count($lista_competencias); ?>" class="fw-bold bg-dark text-white">Calificación Final</th>
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
                                        <?php foreach($matriz as $materia_id => $datos_materia){ ?>
                                            <tr>
                                                <td class="text-start fw-bold text-dark cell-materia-name"><?php echo htmlspecialchars($datos_materia['nombre']); ?></td>
                                                <?php foreach($lista_competencias as $comp){ 
                                                    $c_id = $comp['id'];
                                                    $tiene_nota = isset($datos_materia['competencias'][$c_id]);
                                                ?>
                                                    <td class="cell-nota-valor"><?php echo $tiene_nota ? $datos_materia['competencias'][$c_id]['p1'] : '-'; ?></td>
                                                    <td class="cell-nota-valor"><?php echo $tiene_nota ? $datos_materia['competencias'][$c_id]['p2'] : '-'; ?></td>
                                                    <td class="cell-nota-valor"><?php echo $tiene_nota ? $datos_materia['competencias'][$c_id]['p3'] : '-'; ?></td>
                                                    <td class="cell-nota-valor"><?php echo $tiene_nota ? $datos_materia['competencias'][$c_id]['p4'] : '-'; ?></td>
                                                <?php } ?>
                                                <?php foreach($lista_competencias as $comp){ 
                                                    $c_id = $comp['id'];
                                                    echo '<td class="fw-bold text-dark cell-final-bg">' . (isset($datos_materia['competencias'][$c_id]) ? number_format($datos_materia['competencias'][$c_id]['final'], 0) : '-') . '</td>';
                                                } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php 
                    } // Fin del While
                    ?>
                </div>
            </div>

            <!-- BOTÓN GENERAL HISTORIAL -->
            <div class="d-flex justify-content-center mb-5">
                <button onclick="imprimirTodoElHistorial();" class="btn btn-success btn-lg rounded-pill px-5 shadow-sm fw-bold">
                    <i class="bi bi-printer-fill"></i> Descargar Historial Completo (Todos los Años)
                </button>
            </div>
<?php
        } else {
            echo '<div class="alert alert-info border-0 shadow-sm rounded-4">ℹ️ Este estudiante no cuenta con asignaciones históricas registradas.</div>';
        }
    }
    ?>
</div>

<!-- ESTILOS ADICIONALES -->
<style>
.s-tabla-reporte th, .s-tabla-reporte td { vertical-align: middle !important; border: 1px solid #cbd5e1 !important; }
.header-competencia { background-color: #fdebd0 !important; font-size: 0.75rem; }
.cell-materia-title { background-color: #f5eeeb !important; font-size: 0.8rem; }
</style>

<!-- MOTOR SCRIPT JAVASCRIPT -->
<script>
// Filtro en tiempo real por entrada de teclado
if(document.getElementById('buscadorEstudiantes')){
    document.getElementById('buscadorEstudiantes').addEventListener('keyup', function() {
        var valorBusqueda = this.value.toLowerCase().trim();
        var filas = document.querySelectorAll('#tablaEstudiantes tr');
        
        filas.forEach(function(fila) {
            var celdaNombre = fila.querySelector('.nombre-estudiante');
            if (celdaNombre) {
                var textoNombre = celdaNombre.textContent.toLowerCase();
                fila.style.display = (textoNombre.indexOf(valorBusqueda) > -1) ? '' : 'none';
            }
        });
    });
}

// IMPRIMIR TODOS LOS AÑOS JUNTOS
function imprimirTodoElHistorial() {
    var contenidoHtml = document.getElementById('area-record-completo').innerHTML;
    var divTemp = document.createElement('div');
    divTemp.innerHTML = contenidoHtml;
    var botones = divTemp.querySelectorAll('.no-print');
    botones.forEach(btn => btn.remove());
    ejecutarImpresionLimpia(divTemp.innerHTML);
}

// IMPRIMIR UN SOLO AÑO SELECCIONADO
function imprimirAnioEspecifico(idBloque) {
    var encabezadoEstudiante = document.querySelector('#area-record-completo .text-center').outerHTML;
    var tablaAnio = document.getElementById(idBloque).outerHTML;
    
    var divTemp = document.createElement('div');
    divTemp.innerHTML = encabezadoEstudiante + tablaAnio;
    var botones = divTemp.querySelectorAll('.no-print');
    botones.forEach(btn => btn.remove());

    ejecutarImpresionLimpia(divTemp.innerHTML);
}

// CONFIGURACIÓN DE IMPRESIÓN IMPRESA HORIZONTAL
function ejecutarImpresionLimpia(htmlContent) {
    var ventanaImpresion = window.open('', '_blank', 'height=800,width=1300');
    ventanaImpresion.document.write('<html><head><title>Record de Notas - Pozo De Bejuco</title>');
    ventanaImpresion.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
    
    ventanaImpresion.document.write('<style>');
    ventanaImpresion.document.write('@page { size: letter landscape; margin: 0.5cm 0.6cm; }');
    ventanaImpresion.document.write('body { background: #fff !important; color: #000 !important; font-family: Arial, sans-serif; padding: 10px; }');
    ventanaImpresion.document.write('.s-tabla-reporte { width: 100% !important; font-size: 10px !important; border-collapse: collapse !important; margin-top: 10px !important; margin-bottom: 20px !important; page-break-inside: avoid; }');
    ventanaImpresion.document.write('.s-tabla-reporte th, .s-tabla-reporte td { padding: 4px 2px !important; border: 1.5px solid #111 !important; text-align: center !important; vertical-align: middle !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }');
    ventanaImpresion.document.write('.cell-materia-title { background-color: #f5eeeb !important; font-size: 9.5px !important; font-weight: bold !important; width: 160px !important; }');
    ventanaImpresion.document.write('.header-competencia { background-color: #fdebd0 !important; font-size: 9px !important; font-weight: bold !important; color: #111 !important; }');
    ventanaImpresion.document.write('.cell-materia-name { font-size: 10px !important; text-align: left !important; padding-left: 8px !important; font-weight: bold !important; }');
    ventanaImpresion.document.write('.cell-periodo { font-size: 8.5px !important; width: 25px !important; font-weight: bold !important; background-color: #eee !important; }');
    ventanaImpresion.document.write('.cell-c-final { background-color: #f3f4f6 !important; color: #000 !important; font-size: 8.5px !important; width: 30px !important; font-weight: bold !important; }');
    ventanaImpresion.document.write('.cell-final-bg { background-color: #f9fafb !important; width: 30px !important; font-size: 10px !important; font-weight: bold !important; }');
    ventanaImpresion.document.write('.bloque-anio-contenedor { page-break-after: always !important; break-after: page !important; }');
    ventanaImpresion.document.write('</style>');
    
    ventanaImpresion.document.write('</head><body>');
    ventanaImpresion.document.write(htmlContent);
    ventanaImpresion.document.write('</body></html>');
    
    ventanaImpresion.document.close();
    ventanaImpresion.focus();
    
    setTimeout(function() {
        ventanaImpresion.print();
        ventanaImpresion.close();
    }, 600);
}
</script>