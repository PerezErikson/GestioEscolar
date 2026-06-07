<?php
ob_start();
session_start();

if (!isset($_SESSION['rol_id'])) {
    header("Location: login.php");
    exit();
}

include("conexion/conexion.php");

$rol = $_SESSION['rol_id'];
$nombre = $_SESSION['nombre'];

// ==========================================
// OBTENER CONFIGURACIÓN DEL CENTRO EDUCATIVO
// ==========================================
$consulta_config = $conn->query("
    SELECT nombre_centro, logo
    FROM configuracion
    LIMIT 1
");

$datos_config = $consulta_config->fetch_assoc();

$nombre_centro = $datos_config['nombre_centro'] ?? 'GESTIÓN ESCOLAR';
$logo_centro = $datos_config['logo'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($nombre_centro); ?></title>

    <?php if (!empty($logo_centro) && file_exists("uploads/" . $logo_centro)): ?>
        <link rel="shortcut icon" href="uploads/<?php echo htmlspecialchars($logo_centro); ?>" type="image/x-icon">
    <?php else: ?>
        <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/2997/2997300.png" type="image/x-icon">
    <?php endif; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>


<body class="bg-light" style="overflow-x: hidden;">

    <div class="bg-white text-dark border-end border-dark shadow position-fixed top-0 start-0 vh-100" 
         style="width: 270px; overflow-y: auto; z-index: 1000;">

        <div class="p-4 text-center border-bottom border-dark">
            <div class="mb-3">
                <?php if (!empty($logo_centro) && file_exists("uploads/" . $logo_centro)): ?>
                    <img src="uploads/<?php echo $logo_centro; ?>" alt="Logo Centro" class="rounded-circle shadow-sm border border-2 border-dark" style="width: 70px; height: 70px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-dark text-white d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px;">
                        <i class="bi bi-building fs-2"></i>
                    </div>
                <?php endif; ?>
            </div>

            <h4 class="fw-bold text-dark m-0">
                <?php echo htmlspecialchars($nombre_centro); ?>
            </h4>

            <div class="bg-light text-dark p-2 rounded-3 mt-3 border border-secondary-subtle small fw-semibold">
                👤 <?php echo htmlspecialchars($nombre); ?>
            </div>
        </div>

        <div class="p-2">
            <div class="text-secondary small fw-bold text-uppercase px-3 pt-3 pb-2" style="letter-spacing: 1px; font-size: 12px;">
                Menú Principal
            </div>

            <a href="principal.php" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                <i class="bi bi-house-door-fill text-primary"></i>
                <span>Inicio</span>
            </a>

            <?php if ($rol == 1): ?>

                <a href="principal.php?seccion=configuracion" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                    <i class="bi bi-gear-fill text-secondary"></i>
                    <span>Configuración</span>
                </a>

                <a href="principal.php?seccion=usuarios" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                    <i class="bi bi-people-fill text-secondary"></i>
                    <span>Usuarios</span>
                </a>
                <a href="principal.php?seccion=chat"
   class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
    <i class="bi bi-chat-dots-fill text-secondary"></i>
    <span>Chat</span>
</a>
<a href="principal.php?seccion=historial_chat"
   class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
    <i class="bi bi-clock-history text-secondary"></i>
    <span>Historial Chat</span>
</a>

                <div>
                    <div class="d-flex align-items-center gap-2 text-dark p-3 my-1 rounded-3 btn btn-light text-start border-0" onclick="toggleMenu('academicoMenu')" style="cursor: pointer;">
                        <i class="bi bi-book-fill text-secondary"></i>
                        <span>Académico</span>
                        <i class="bi bi-caret-down-fill ms-auto small"></i>
                    </div>

                    <div id="academicoMenu" class="ps-3 d-none bg-light rounded-3 mx-1 border border-light-subtle">
                        <a href="principal.php?seccion=ano_escolar" class="d-block text-dark text-decoration-none p-2 small">📅 Año Escolar</a>
                        <a href="principal.php?seccion=competencias" class="d-block text-dark text-decoration-none p-2 small">🎯 Competencias</a>
                        <a href="principal.php?seccion=niveles" class="d-block text-dark text-decoration-none p-2 small">📘 Niveles</a>
                        <a href="principal.php?seccion=secciones" class="d-block text-dark text-decoration-none p-2 small">🗂️ Secciones</a>
                        <a href="principal.php?seccion=grados" class="d-block text-dark text-decoration-none p-2 small">🎓 Grados</a>
                        <a href="principal.php?seccion=materias" class="d-block text-dark text-decoration-none p-2 small">📖 Materias</a>
                        <a href="principal.php?seccion=asignar_materias" class="d-block text-dark text-decoration-none p-2 small">📚 Asignar Materias</a>
                    </div>
                </div>

                <div>
                    <div class="d-flex align-items-center gap-2 text-dark p-3 my-1 rounded-3 btn btn-light text-start border-0" onclick="toggleMenu('tutoresMenu')" style="cursor: pointer;">
                        <i class="bi bi-person-hearts text-secondary"></i>
                        <span>Padres / Tutores</span>
                        <i class="bi bi-caret-down-fill ms-auto small"></i>
                    </div>

                    <div id="tutoresMenu" class="ps-3 d-none bg-light rounded-3 mx-1 border border-light-subtle">
                        <a href="principal.php?seccion=inscripcion_tutor" class="d-block text-dark text-decoration-none p-2 small">📝 Inscribir Tutor</a>
                        <a href="principal.php?seccion=tutores" class="d-block text-dark text-decoration-none p-2 small">👨‍👩‍👧 Ver Tutores</a>
                    </div>
                </div>

                <div>
                    <div class="d-flex align-items-center gap-2 text-dark p-3 my-1 rounded-3 btn btn-light text-start border-0" onclick="toggleMenu('estudiantesMenu')" style="cursor: pointer;">
                        <i class="bi bi-mortarboard-fill text-secondary"></i>
                        <span>Estudiantes</span>
                        <i class="bi bi-caret-down-fill ms-auto small"></i>
                    </div>

                    <div id="estudiantesMenu" class="ps-3 d-none bg-light rounded-3 mx-1 border border-light-subtle">
                        <a href="principal.php?seccion=inscripcion_estudiante" class="d-block text-dark text-decoration-none p-2 small">📝 Inscribir Estudiante</a>
                        <a href="principal.php?seccion=estudiantes" class="d-block text-dark text-decoration-none p-2 small">👩‍🎓 Ver Estudiantes</a>
                        <a href="principal.php?seccion=estado_academico" class="d-block text-dark text-decoration-none p-2 small">📚 Estado Académico</a>
                    </div>
                </div>

                <div>
                    <div class="d-flex align-items-center gap-2 text-dark p-3 my-1 rounded-3 btn btn-light text-start border-0" onclick="toggleMenu('docentesMenu')" style="cursor: pointer;">
                        <i class="bi bi-person-workspace text-secondary"></i>
                        <span>Docentes</span>
                        <i class="bi bi-caret-down-fill ms-auto small"></i>
                    </div>

                    <div id="docentesMenu" class="ps-3 d-none bg-light rounded-3 mx-1 border border-light-subtle">
                        <a href="principal.php?seccion=registrar_docente" class="d-block text-dark text-decoration-none p-2 small">📝 Registrar Docente</a>
                        <a href="principal.php?seccion=docentes" class="d-block text-dark text-decoration-none p-2 small">👨‍🏫 Ver Docentes</a>
                    </div>
                </div>

                <div>
                    <div class="d-flex align-items-center gap-2 text-dark p-3 my-1 rounded-3 btn btn-light text-start border-0" onclick="toggleMenu('calificacionesMenu')" style="cursor: pointer;">
                        <i class="bi bi-journal-check text-secondary"></i>
                        <span>Calificaciones</span>
                        <i class="bi bi-caret-down-fill ms-auto small"></i>
                    </div>

                    <div id="calificacionesMenu" class="ps-3 d-none bg-light rounded-3 mx-1 border border-light-subtle">
                        <a href="principal.php?seccion=calificaciones" class="d-block text-dark text-decoration-none p-2 small">📝 Registrar Calificaciones</a>
                        <a href="principal.php?seccion=reporte_calificaciones" class="d-block text-dark text-decoration-none p-2 small">📊 Reporte de Calificaciones</a>
                        <a href="principal.php?seccion=record_notas" class="d-block text-dark text-decoration-none p-2 small">🗂️ Récord de Notas</a>
                        <a href="principal.php?seccion=certificacion" class="d-block text-dark text-decoration-none p-2 small">📜 Certificación</a>
                    </div>
                </div>

                <div>
                    <div class="d-flex align-items-center gap-2 text-dark p-3 my-1 rounded-3 btn btn-light text-start border-0" onclick="toggleMenu('comportamientoMenu')" style="cursor: pointer;">
                        <i class="bi bi-clipboard-data-fill text-secondary"></i>
                        <span>Comportamiento</span>
                        <i class="bi bi-caret-down-fill ms-auto small"></i>
                    </div>

                    <div id="comportamientoMenu" class="ps-3 d-none bg-light rounded-3 mx-1 border border-light-subtle">
                        <a href="principal.php?seccion=comportamiento" class="d-block text-dark text-decoration-none p-2 small">📋 Registrar</a>
                        <a href="principal.php?seccion=reporte_comportamiento" class="d-block text-dark text-decoration-none p-2 small">📊 Reportes</a>
                    </div>
                </div>

                <div>
                    <div class="d-flex align-items-center gap-2 text-dark p-3 my-1 rounded-3 btn btn-light text-start border-0" onclick="toggleMenu('asistenciaMenu')" style="cursor: pointer;">
                        <i class="bi bi-calendar-check-fill text-secondary"></i>
                        <span>Asistencia</span>
                        <i class="bi bi-caret-down-fill ms-auto small"></i>
                    </div>

                    <div id="asistenciaMenu" class="ps-3 d-none bg-light rounded-3 mx-1 border border-light-subtle">
                        <a href="principal.php?seccion=asistencia" class="d-block text-dark text-decoration-none p-2 small">📅 Registrar</a>
                        <a href="principal.php?seccion=reporte_asistencia" class="d-block text-dark text-decoration-none p-2 small">📊 Reportes</a>
                    </div>
                </div>

            <?php elseif ($rol == 2): ?>

                <a href="principal.php?seccion=comportamiento" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                    <i class="bi bi-clipboard-check-fill text-secondary"></i> Comportamiento
                </a>
                <a href="principal.php?seccion=reporte_comportamiento" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                    <i class="bi bi-bar-chart-fill text-secondary"></i> Reportes
                </a>
                <a href="principal.php?seccion=asistencia" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                    <i class="bi bi-calendar2-check-fill text-secondary"></i> Asistencia
                </a>
                <a href="principal.php?seccion=reporte_asistencia" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                    <i class="bi bi-file-earmark-bar-graph-fill text-secondary"></i> Reporte Asistencia
                </a>
                 <a href="principal.php?seccion=calificaciones" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                    <i class="bi bi-journal-check text-secondary"></i> Calificaciones
                </a>
                <a href="principal.php?seccion=chat"
   class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
    <i class="bi bi-chat-dots-fill text-secondary"></i>
    <span>Chat</span>
</a>

            <?php elseif ($rol == 3): ?>

                <a href="principal.php?seccion=estado_academico" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                    <i class="bi bi-journal-text text-secondary"></i> Estado Académico
                </a>
                <a href="principal.php?seccion=reporte_comportamiento" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                    <i class="bi bi-clipboard-data text-secondary"></i> Mi Comportamiento
                </a>
                <a href="principal.php?seccion=chat"
   class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
    <i class="bi bi-chat-dots-fill text-secondary"></i>
    <span>Chat</span>
</a>

            <?php endif; ?>

            <a href="logout.php" class="d-flex align-items-center gap-2 text-white text-decoration-none p-3 mt-4 mx-2 rounded-3 btn btn-danger text-start">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
        </div>
    </div>

    <div class="p-4" style="margin-left: 270px;">

        <div class="bg-white p-3 px-4 rounded-4 shadow-sm mb-4 border border-light-subtle">
            <h3 class="m-0 fw-bold text-dark text-opacity-75 fs-4">
                Bienvenido, <?php echo htmlspecialchars($nombre); ?>
            </h3>
        </div>

        <?php
        if (!isset($_GET['seccion'])) {
            if ($rol == 1) include("panel_admin.php");
            elseif ($rol == 2) include("panel_docente.php");
            elseif ($rol == 3) include("panel_estudiante.php");
        } else {
            $seccion = $_GET['seccion'];

            if ($seccion === 'configuracion' && $rol == 1) include("Configuracion/configuracion.php");
            elseif ($seccion === 'ano_escolar' && $rol == 1) include("AñoEscolar/agregar.php");
            elseif ($seccion === 'competencias' && $rol == 1) include("AñoEscolar/agregar_competencia.php");
            elseif ($seccion === 'niveles' && $rol == 1) include("Niveles/niveles.php");
            elseif ($seccion === 'grados' && $rol == 1) include("Grados/grados.php");
            elseif ($seccion === 'materias' && $rol == 1) include("Materias/materias.php");
            elseif ($seccion === 'asignar_materias' && $rol == 1) include("Materias/asignar_materias.php");
            elseif ($seccion === 'secciones' && $rol == 1) include("Secciones/secciones.php");
            elseif ($seccion === 'usuarios' && $rol == 1) include("Usuarios/usuarios.php");
            elseif ($seccion === 'docentes' && $rol == 1) include("Docentes/docentes.php");
            elseif ($seccion === 'registrar_docente' && $rol == 1) include("Docentes/registrar_docente.php");
            elseif ($seccion === 'tutores' && $rol == 1) include("Tutores/tutores.php");
            elseif ($seccion === 'inscripcion_tutor' && $rol == 1) include("Inscripcion/inscripcion_tutores.php");
            elseif ($seccion === 'estudiantes' && $rol == 1) include("Estudiantes/estudiantes.php");
            elseif ($seccion === 'inscripcion_estudiante' && $rol == 1) include("Inscripcion/inscripcion_estudiante.php");
            elseif ($seccion === 'estado_academico') include("Estado/estado_academico.php");
            elseif ($seccion === 'calificaciones') include("Calificaciones/calificaciones.php");
            elseif ($seccion === 'reporte_calificaciones') include("Calificaciones/reporte_calificaciones.php");
            elseif ($seccion === 'comportamiento') include("Comportamiento/comportamiento.php");
            elseif ($seccion === 'reporte_comportamiento') include("Comportamiento/reporte_comportamiento.php");
            elseif ($seccion === 'asistencia') include("Asistencia/asistencia.php");
            elseif ($seccion === 'reporte_asistencia') include("Reporte/reporte_asistencia.php");
            elseif ($seccion === 'chat') include("Chat/chat.php");
            elseif ($seccion === 'historial_chat' && $rol == 1)
    include("Chat/historial_chat.php");
            elseif ($seccion === 'record_notas') include("Calificaciones/record_notas.php");
            elseif ($seccion === 'certificacion') include("Calificaciones/certificacion.php");
            else echo "<div class='alert alert-danger rounded-4'>Sección no encontrada.</div>";
            
        }
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function toggleMenu(id){
        let menu = document.getElementById(id);
        if(menu.classList.contains('d-none')){
            menu.classList.remove('d-none');
            menu.classList.add('d-block');
        } else {
            menu.classList.remove('d-block');
            menu.classList.add('d-none');
        }
    }
    </script>

</body>
</html>
<?php
ob_end_flush();
?>