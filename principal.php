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
// CAPTURAR EL ID DEL USUARIO DESDE LA SESIÓN
// ==========================================
$id_usuario = $_SESSION['id_usuario'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? 0; 

// ==========================================
// CONTADOR DE MENSAJES NO LEÍDOS
// ==========================================
$mensajes_no_leidos = 0;

if ($id_usuario > 0) {
    $query_mensajes = "SELECT COUNT(*) AS total FROM mensajes WHERE receptor_id = ? AND leido = 0";
    if ($stmt_msg = $conn->prepare($query_mensajes)) {
        $stmt_msg->bind_param("i", $id_usuario);
        $stmt_msg->execute();
        $res_msg = $stmt_msg->get_result()->fetch_assoc();
        $mensajes_no_leidos = $res_msg['total'] ?? 0;
        $stmt_msg->close();
    }
}

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
        <link class="shortcut icon" href="uploads/<?php echo htmlspecialchars($logo_centro); ?>" type="image/x-icon">
    <?php else: ?>
        <link class="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/2997/2997300.png" type="image/x-icon">
    <?php endif; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        /* Estilos personalizados para la pestaña flotante de notificaciones */
        .contenedor-notificaciones {
            position: relative;
        }
        .pestana-flotante {
            position: absolute;
            top: 50px;
            right: 0;
            width: 290px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.15);
            z-index: 2000;
            display: none; /* Oculto por defecto */
        }
    </style>
</head>

<body class="bg-light" style="overflow-x: hidden;">

    <!-- MENÚ LATERAL -->
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
                
                <a href="principal.php?seccion=chat" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
                    <i class="bi bi-chat-dots-fill text-secondary"></i>
                    <span>Chat</span>
                </a>
                
                <a href="principal.php?seccion=historial_chat" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
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
                <a href="principal.php?seccion=chat" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
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
                <a href="principal.php?seccion=chat" class="d-flex align-items-center gap-2 text-dark text-decoration-none p-3 my-1 rounded-3 btn btn-light text-start border-0">
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

    <!-- CONTENIDO GENERAL DE LA VISTA -->
    <div class="p-4" style="margin-left: 270px;">

        <!-- BARRA SUPERIOR DE BIENVENIDA -->
        <div class="bg-white p-3 px-4 rounded-4 shadow-sm mb-4 border border-light-subtle d-flex justify-content-between align-items-center">
            <h3 class="m-0 fw-bold text-dark text-opacity-75 fs-4">
                Bienvenido, <?php echo htmlspecialchars($nombre); ?>
            </h3>

            <!-- Contenedor relativo de la Campana -->
            <div class="contenedor-notificaciones">
                <button class="position-relative text-secondary p-2 bg-light rounded-circle border d-flex align-items-center justify-content-center" 
                        type="button" 
                        id="btnNoti"
                        onclick="toggleNotificaciones(event)"
                        style="width: 42px; height: 42px; transition: all 0.2s ease; cursor: pointer;">
                    
                    <i class="bi bi-bell-fill fs-5"></i>
                    
                    <!-- Burbuja roja de cantidad -->
                    <?php if ($mensajes_no_leidos > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 11px;">
                            <?php echo $mensajes_no_leidos; ?>
                        </span>
                    <?php endif; ?>
                </button>

                <!-- PESTAÑA FLOTANTE MANUAL -->
                <div class="pestana-flotante p-3" id="menuNoti">
                    <div class="fw-bold text-dark border-bottom pb-2 mb-2 d-flex align-items-center gap-1" style="font-size: 15px;">
                        <i class="bi bi-bell"></i> Notificaciones del Sistema
                    </div>
                    
                    <?php if ($mensajes_no_leidos > 0): ?>
                        <div class="d-flex align-items-start gap-2 py-2" style="font-size: 13.5px; color: #333;">
                            <i class="bi bi-chat-left-text-fill text-primary mt-1" style="font-size: 16px;"></i>
                            <div>
                                Tienes <strong class="text-danger"><?php echo $mensajes_no_leidos; ?></strong> mensajes nuevos sin leer en la plataforma escolar.
                            </div>
                        </div>
                        <div class="border-top pt-2 mt-2">
                            <a class="btn btn-primary btn-sm w-100 text-center text-white fw-semibold py-1-5 border-0" href="principal.php?seccion=chat">
                                Ir a la bandeja de mensajes
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-muted text-center py-3" style="font-size: 13.5px;">
                            <i class="bi bi-check-circle-fill text-success d-block fs-4 mb-1"></i>
                            Todo al día. No tienes avisos pendientes.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
            elseif ($seccion === 'historial_chat' && $rol == 1) include("Chat/historial_chat.php");
            elseif ($seccion === 'record_notas') include("Calificaciones/record_notas.php");
            elseif ($seccion === 'certificacion') include("Calificaciones/certificacion.php");
            else echo "<div class='alert alert-danger rounded-4'>Sección no encontrada.</div>";
        }
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Control manual estricto del estado bloqueado/desplegado de la pestaña flotante
    function toggleNotificaciones(event) {
        event.stopPropagation();
        let menu = document.getElementById('menuNoti');
        if (menu.style.display === 'none' || menu.style.display === '') {
            menu.style.display = 'block';
        } else {
            menu.style.display = 'none';
        }
    }

    // Cerrar automáticamente la pestaña si haces clic en cualquier otra parte fuera de ella
    document.addEventListener('click', function(e) {
        let menu = document.getElementById('menuNoti');
        let btn = document.getElementById('btnNoti');
        if (menu && menu.style.display === 'block') {
            if (!menu.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
                menu.style.display = 'none';
            }
        }
    });

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