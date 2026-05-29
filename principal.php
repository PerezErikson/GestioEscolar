<?php
session_start();

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
            background-color:#f5f7fa;
        }

        /* SIDEBAR */
        .sidebar{
            width:260px;
            background:#002b5c;
            color:white;
            position:fixed;
            height:100%;
            overflow-y:auto;
            padding-top:20px;
        }

        .sidebar a{
            display:block;
            color:white;
            padding:12px 20px;
            text-decoration:none;
            border-radius:8px;
            margin:4px 10px;
            transition:0.3s;
        }

        .sidebar a:hover{
            background:#004080;
        }

        /* SUBMENU */
        .submenu-btn{
            cursor:pointer;
        }

        .submenu{
            display:none;
            margin-left:10px;
        }

        .submenu a{
            font-size:14px;
            background:rgba(255,255,255,0.05);
        }

        .submenu a:hover{
            background:#0059b3;
        }

        /* CONTENIDO */
        .content{
            margin-left:280px;
            padding:25px;
        }

        .card{
            border-radius:12px;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
            text-align:center;
            font-weight:bold;
        }

    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <!-- NOMBRE DEL CENTRO -->
    <h4 class="text-center px-2">

        <?php echo htmlspecialchars($nombre_centro); ?>

    </h4>

    <!-- USUARIO -->
    <p class="text-center mb-2">
        👤 <?php echo $nombre; ?>
    </p>

    <hr>

    <!-- INICIO -->
    <a href="principal.php">
        🏠 Inicio
    </a>

    <?php if ($rol == 1): ?>

        <!-- CONFIGURACION -->
        <a href="principal.php?seccion=configuracion">
            ⚙️ Configuración
        </a>

        <!-- USUARIOS -->
        <a href="principal.php?seccion=usuarios">
            👥 Usuarios
        </a>

        <!-- ACADEMICO -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('academicoMenu')">

                📚 Académico
                <i class="bi bi-caret-down-fill float-end"></i>

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

        <!-- PADRES / TUTORES -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('tutoresMenu')">

                👨‍👩‍👦 Padres / Tutores
                <i class="bi bi-caret-down-fill float-end"></i>

            </a>

            <div class="submenu" id="tutoresMenu">

                <a href="principal.php?seccion=inscripcion_tutor">
                    📝 Inscribir Tutor
                </a>

                <a href="principal.php?seccion=tutores">
                    👨‍👩‍🏫 Ver Tutores
                </a>

            </div>

        </div>

        <!-- ESTUDIANTES -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('estudiantesMenu')">

                👩‍🎓 Estudiantes
                <i class="bi bi-caret-down-fill float-end"></i>

            </a>

            <div class="submenu" id="estudiantesMenu">

                <a href="principal.php?seccion=inscripcion_estudiante">
                    📝 Inscribir Estudiante
                </a>

                <a href="principal.php?seccion=estudiantes">
                    👩‍🎓 Ver Estudiantes
                </a>

            </div>

        </div>

        <!-- DOCENTES -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('docentesMenu')">

                👨‍🏫 Docentes
                <i class="bi bi-caret-down-fill float-end"></i>

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
            📝 Calificaciones
        </a>

        <!-- COMPORTAMIENTO -->
        <div>

            <a class="submenu-btn"
               onclick="toggleMenu('comportamientoMenu')">

                📋 Comportamiento
                <i class="bi bi-caret-down-fill float-end"></i>

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

                📅 Asistencia
                <i class="bi bi-caret-down-fill float-end"></i>

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

        <!-- CHAT -->
        <a href="principal.php?seccion=chat">
            💬 Chat
        </a>

    <?php elseif ($rol == 2): ?>

        <!-- DOCENTE -->

        <a href="principal.php?seccion=calificaciones">
            📝 Calificaciones
        </a>

        <a href="principal.php?seccion=comportamiento">
            📋 Comportamiento
        </a>

        <a href="principal.php?seccion=reporte_comportamiento">
            📊 Reporte de Comportamiento
        </a>

        <a href="principal.php?seccion=asistencia">
            📅 Asistencia
        </a>

        <a href="principal.php?seccion=reporte_asistencia">
            📊 Reporte de Asistencia
        </a>

        <a href="principal.php?seccion=chat">
            💬 Chat
        </a>

    <?php elseif ($rol == 3): ?>

        <!-- ESTUDIANTE -->

        <a href="principal.php?seccion=calificaciones">
            📝 Calificaciones
        </a>

        <a href="principal.php?seccion=reporte_comportamiento">
            📊 Reporte de Comportamiento
        </a>

        <a href="principal.php?seccion=chat">
            💬 Chat
        </a>

    <?php endif; ?>

    <hr>

    <!-- CERRAR SESION -->
    <a href="logout.php" style="color:#ff4d4d;">

        🚪 Cerrar sesión

    </a>

</div>

<!-- CONTENIDO -->
<div class="content">

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