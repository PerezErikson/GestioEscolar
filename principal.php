<?php
ob_start();
session_start();
// resto de tu código...


if (!isset($_SESSION['rol_id'])) {
    header("Location: login.php");
    exit();
}

include("conexion/conexion.php");

$rol = $_SESSION['rol_id'];
$nombre = $_SESSION['nombre'];

// ==========================================
// OBTENER NOMBRE DEL CENTRO EDUCATIVO
// ==========================================
$consulta_config = $conn->query("
    SELECT nombre_centro
    FROM configuracion
    LIMIT 1
");

$datos_config = $consulta_config->fetch_assoc();

$nombre_centro = $datos_config['nombre_centro'] ?? 'GESTIÓN ESCOLAR';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($nombre_centro); ?></title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ICONOS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>

        body{
            background:#f4f6f9;
            overflow-x:hidden;
        }

        /* SIDEBAR */
        .sidebar{
            width:270px;
            height:100vh;
            position:fixed;
            top:0;
            left:0;
            background:linear-gradient(180deg,#002b5c,#001933);
            color:white;
            overflow-y:auto;
            box-shadow:4px 0 15px rgba(0,0,0,0.1);
            z-index:1000;
        }

        .sidebar-header{
            padding:25px 20px;
            text-align:center;
            border-bottom:1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h4{
            font-weight:bold;
            margin-bottom:10px;
        }

        .user-box{
            background:rgba(255,255,255,0.08);
            padding:12px;
            border-radius:12px;
            margin-top:15px;
        }

        .menu-title{
            font-size:13px;
            color:#bfc9d4;
            text-transform:uppercase;
            letter-spacing:1px;
            padding:20px 20px 10px;
        }

        .sidebar a{
            display:flex;
            align-items:center;
            gap:10px;
            color:white;
            text-decoration:none;
            padding:12px 18px;
            margin:5px 12px;
            border-radius:12px;
            transition:0.3s;
            font-size:15px;
        }

        .sidebar a:hover{
            background:#0d6efd;
            transform:translateX(3px);
        }

        .submenu{
            display:none;
            margin-left:10px;
        }

        .submenu a{
            background:rgba(255,255,255,0.05);
            font-size:14px;
        }

        .submenu-btn{
            cursor:pointer;
        }

        .logout{
            background:#dc3545 !important;
            margin-top:20px;
        }

        .logout:hover{
            background:#bb2d3b !important;
        }

        /* CONTENIDO */
        .content{
            margin-left:270px;
            padding:30px;
        }

        .topbar{
            background:white;
            padding:18px 25px;
            border-radius:16px;
            box-shadow:0 2px 10px rgba(0,0,0,0.08);
            margin-bottom:25px;
        }

        .topbar h3{
            margin:0;
            font-weight:bold;
            color:#002b5c;
        }

    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <!-- HEADER -->
    <div class="sidebar-header">

        <h4>
            <?php echo htmlspecialchars($nombre_centro); ?>
        </h4>

        <div class="user-box">
            👤 <?php echo htmlspecialchars($nombre); ?>
        </div>

    </div>

    <!-- MENU -->
    <div class="menu-title">
        Menú Principal
    </div>

    <!-- INICIO -->
    <a href="principal.php">
        <i class="bi bi-house-door-fill"></i>
        Inicio
    </a>

    <?php if ($rol == 1): ?>

        <!-- CONFIGURACION -->
        <a href="principal.php?seccion=configuracion">
            <i class="bi bi-gear-fill"></i>
            Configuración
        </a>

        <!-- USUARIOS -->
        <a href="principal.php?seccion=usuarios">
            <i class="bi bi-people-fill"></i>
            Usuarios
        </a>

        <!-- ACADEMICO -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('academicoMenu')">

                <i class="bi bi-book-fill"></i>
                Académico

                <i class="bi bi-caret-down-fill ms-auto"></i>

            </a>

            <div class="submenu" id="academicoMenu">

                <a href="principal.php?seccion=niveles">
                    📘 Niveles
                </a>

                <a href="principal.php?seccion=secciones">
                    🗂️ Secciones
                </a>

                <a href="principal.php?seccion=grados">
                    🎓 Grados
                </a>

                <a href="principal.php?seccion=materias">
                    📖 Materias
                </a>

            </div>

        </div>

        <!-- PADRES -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('tutoresMenu')">

                <i class="bi bi-person-hearts"></i>
                Padres / Tutores

                <i class="bi bi-caret-down-fill ms-auto"></i>

            </a>

            <div class="submenu" id="tutoresMenu">

                <a href="principal.php?seccion=inscripcion_tutor">
                    📝 Inscribir Tutor
                </a>

                <a href="principal.php?seccion=tutores">
                    👨‍👩‍👧 Ver Tutores
                </a>

            </div>

        </div>

        <!-- ESTUDIANTES -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('estudiantesMenu')">

                <i class="bi bi-mortarboard-fill"></i>
                Estudiantes

                <i class="bi bi-caret-down-fill ms-auto"></i>

            </a>

            <div class="submenu" id="estudiantesMenu">

                <a href="principal.php?seccion=inscripcion_estudiante">
                    📝 Inscribir Estudiante
                </a>

                <a href="principal.php?seccion=estudiantes">
                    👩‍🎓 Ver Estudiantes
                </a>

                <a href="principal.php?seccion=estado_academico">
                    📚 Estado Académico
                </a>

            </div>

        </div>

        <!-- DOCENTES -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('docentesMenu')">

                <i class="bi bi-person-workspace"></i>
                Docentes

                <i class="bi bi-caret-down-fill ms-auto"></i>

            </a>

            <div class="submenu" id="docentesMenu">

                <a href="principal.php?seccion=registrar_docente">
                    📝 Registrar Docente
                </a>

                <a href="principal.php?seccion=docentes">
                    👨‍🏫 Ver Docentes
                </a>

            </div>

        </div>

        <!-- CALIFICACIONES -->
        <a href="principal.php?seccion=calificaciones">
            <i class="bi bi-journal-check"></i>
            Calificaciones
        </a>

        <!-- COMPORTAMIENTO -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('comportamientoMenu')">

                <i class="bi bi-clipboard-data-fill"></i>
                Comportamiento

                <i class="bi bi-caret-down-fill ms-auto"></i>

            </a>

            <div class="submenu" id="comportamientoMenu">

                <a href="principal.php?seccion=comportamiento">
                    📋 Registrar
                </a>

                <a href="principal.php?seccion=reporte_comportamiento">
                    📊 Reportes
                </a>

            </div>

        </div>

        <!-- ASISTENCIA -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('asistenciaMenu')">

                <i class="bi bi-calendar-check-fill"></i>
                Asistencia

                <i class="bi bi-caret-down-fill ms-auto"></i>

            </a>

            <div class="submenu" id="asistenciaMenu">

                <a href="principal.php?seccion=asistencia">
                    📅 Registrar
                </a>

                <a href="principal.php?seccion=reporte_asistencia">
                    📊 Reportes
                </a>

            </div>

        </div>

    <?php elseif ($rol == 2): ?>

        <!-- DOCENTE -->

        <a href="principal.php?seccion=comportamiento">
            <i class="bi bi-clipboard-check-fill"></i>
            Comportamiento
        </a>

        <a href="principal.php?seccion=reporte_comportamiento">
            <i class="bi bi-bar-chart-fill"></i>
            Reportes
        </a>

        <a href="principal.php?seccion=asistencia">
            <i class="bi bi-calendar2-check-fill"></i>
            Asistencia
        </a>

        <a href="principal.php?seccion=reporte_asistencia">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            Reporte Asistencia
        </a>

    <?php elseif ($rol == 3): ?>

        <!-- ESTUDIANTE -->

        <a href="principal.php?seccion=estado_academico">
            <i class="bi bi-journal-text"></i>
            Estado Académico
        </a>

        <a href="principal.php?seccion=reporte_comportamiento">
            <i class="bi bi-clipboard-data"></i>
            Mi Comportamiento
        </a>

    <?php endif; ?>

    <!-- CERRAR SESION -->
    <a href="logout.php" class="logout">
        <i class="bi bi-box-arrow-right"></i>
        Cerrar sesión
    </a>

</div>

<!-- CONTENIDO -->
<div class="content">

    <!-- TOPBAR -->
    <div class="topbar">

        <h3>
            Bienvenido,
            <?php echo htmlspecialchars($nombre); ?>
        </h3>

    </div>

<?php

if (!isset($_GET['seccion'])) {

    // PANEL ADMIN
    if ($rol == 1) {

        include("panel_admin.php");

    }

    // PANEL DOCENTE
    elseif ($rol == 2) {

        include("panel_docente.php");

    }

    // PANEL ESTUDIANTE
    elseif ($rol == 3) {

        include("panel_estudiante.php");

    }

} else {

    $seccion = $_GET['seccion'];

    // ADMIN
    if ($seccion === 'configuracion' && $rol == 1)
        include("Configuracion/configuracion.php");

    elseif ($seccion === 'niveles' && $rol == 1)
        include("Niveles/niveles.php");

    elseif ($seccion === 'grados' && $rol == 1)
        include("Grados/grados.php");

    elseif ($seccion === 'materias' && $rol == 1)
        include("Materias/materias.php");

    elseif ($seccion === 'secciones' && $rol == 1)
        include("Secciones/secciones.php");

    elseif ($seccion === 'usuarios' && $rol == 1)
        include("Usuarios/usuarios.php");

    elseif ($seccion === 'docentes' && $rol == 1)
        include("Docentes/docentes.php");

    elseif ($seccion === 'registrar_docente' && $rol == 1)
        include("Docentes/registrar_docente.php");

    elseif ($seccion === 'tutores' && $rol == 1)
        include("Tutores/tutores.php");

    elseif ($seccion === 'inscripcion_tutor' && $rol == 1)
        include("Inscripcion/inscripcion_tutores.php");

    elseif ($seccion === 'estudiantes' && $rol == 1)
        include("Estudiantes/estudiantes.php");

    elseif ($seccion === 'inscripcion_estudiante' && $rol == 1)
        include("Inscripcion/inscripcion_estudiante.php");

elseif ($seccion === 'estado_academico')
    include("Estado/estado_academico.php");

    elseif ($seccion === 'calificaciones')
        include("Calificaciones/calificaciones.php");

    elseif ($seccion === 'comportamiento')
        include("Comportamiento/comportamiento.php");

    elseif ($seccion === 'reporte_comportamiento')
        include("Comportamiento/reporte_comportamiento.php");

    elseif ($seccion === 'asistencia')
        include("Asistencia/asistencia.php");

    elseif ($seccion === 'reporte_asistencia')
        include("Reporte/reporte_asistencia.php");

    elseif ($seccion === 'chat')
        include("Chat/chat.php");

    else
        echo "<div class='alert alert-danger'>Sección no encontrada.</div>";
}

?>

</div>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SUBMENUS -->
<script>

function toggleMenu(id){

    let menu = document.getElementById(id);

    if(menu.style.display === "block"){

        menu.style.display = "none";

    } else {

        menu.style.display = "block";
    }
}

</script>

</body>
</html>
<?php
ob_end_flush();
