<?php
session_start();
if (!isset($_SESSION['rol_id'])) {
    header("Location: login.php");
    exit();
}

include("conexion/conexion.php");

$rol = $_SESSION['rol_id'];
$nombre = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f5f7fa; }
        .sidebar {
            width: 240px;
            background-color: #002b5c;
            color: white;
            position: fixed;
            height: 100%;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 4px 10px;
        }
        .sidebar a:hover { background-color: #004080; }
        .content { margin-left: 260px; padding: 25px; }
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">GESTIÓN ESCOLAR</h4>
        <p class="text-center mb-2">👤 <?php echo $nombre; ?></p>
        <hr>
        <a href="principal.php">🏠 Inicio</a>
        <?php if ($rol == 1): ?>
            <a href="principal.php?seccion=configuracion">⚙️ Configuración</a>
            <a href="principal.php?seccion=usuarios">👥 Usuarios</a>
            <a href="principal.php?seccion=niveles">📚 Niveles</a>
            <a href="principal.php?seccion=secciones">🗂️ Secciones</a>
            <a href="principal.php?seccion=grados">🎓 Grados</a>
            <a href="principal.php?seccion=inscripcion_estudiante">📝 Inscripción Estudiante</a>
            <a href="principal.php?seccion=registrar_docente">📝 Registrar Docente</a>
            <a href="principal.php?seccion=materias">📖 Materias</a>
            <a href="principal.php?seccion=docentes">👨‍🏫 Docentes</a>
            <a href="principal.php?seccion=estudiantes">👩‍🎓 Estudiantes</a>
            <a href="principal.php?seccion=calificaciones">📝 Calificaciones</a>
            <a href="principal.php?seccion=comportamiento">📋 Comportamiento</a>
            <a href="principal.php?seccion=reporte_comportamiento">📊 Reporte de Comportamiento</a>
            <a href="principal.php?seccion=asistencia">📅 Asistencia</a>
            <a href="principal.php?seccion=reporte_asistencia">📊 Reporte de Asistencia</a>
            <a href="principal.php?seccion=chat">💬 Chat</a>
        <?php elseif ($rol == 2): ?>
            <a href="principal.php?seccion=calificaciones">📝 Calificaciones</a>
            <a href="principal.php?seccion=comportamiento">📋 Comportamiento</a>
            <a href="principal.php?seccion=reporte_comportamiento">📊 Reporte de Comportamiento</a>
            <a href="principal.php?seccion=asistencia">📅 Asistencia</a>
            <a href="principal.php?seccion=reporte_asistencia">📊 Reporte de Asistencia</a>
            <a href="principal.php?seccion=chat">💬 Chat</a>
        <?php elseif ($rol == 3): ?>
            <a href="principal.php?seccion=calificaciones">📝 Calificaciones</a>
            <a href="principal.php?seccion=reporte_comportamiento">📊 Reporte de Comportamiento</a>
            <a href="principal.php?seccion=chat">💬 Chat</a>
        <?php endif; ?>
        <hr>
        <a href="logout.php" style="color:#ff4d4d;">🚪 Cerrar sesión</a>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <?php
        if (!isset($_GET['seccion'])) {
            // Panel principal según rol
            if ($rol == 1) {
                include("panel_admin.php"); // Panel con tarjetas y contadores
            } elseif ($rol == 2) {
                echo "<h3>Bienvenido, Docente 👋</h3>";
                echo "<p>Este es tu panel principal.</p>";
                echo '<div class="row g-3">
                        <div class="col-md-4"><div class="card p-3 bg-info text-white">Calificaciones</div></div>
                        <div class="col-md-4"><div class="card p-3 bg-success text-white">Comportamiento</div></div>
                        <div class="col-md-4"><div class="card p-3 bg-warning text-dark">Asistencia</div></div>
                      </div>';
            } elseif ($rol == 3) {
                echo "<h3>Bienvenido, Estudiante 👋</h3>";
                echo "<p>Este es tu panel principal.</p>";
                echo '<div class="row g-3">
                        <div class="col-md-6"><div class="card p-3 bg-info text-white">Ver Calificaciones</div></div>
                        <div class="col-md-6"><div class="card p-3 bg-success text-white">Ver Comportamiento</div></div>
                        <div class="col-md-6"><div class="card p-3 bg-warning text-dark">Ver Asistencia</div></div>
                        <div class="col-md-6"><div class="card p-3 bg-warning text-dark">Ver Reporte de Comportamiento</div></div>
                     </div>';
            }
        } else {
            $seccion = $_GET['seccion'];
            if ($seccion === 'configuracion' && $rol == 1) include("Configuracion/configuracion.php");
            elseif ($seccion === 'niveles' && $rol == 1) include("Niveles/niveles.php");
            elseif ($seccion === 'grados' && $rol == 1) include("Grados/grados.php");
            elseif ($seccion === 'materias' && $rol == 1) include("Materias/materias.php");
            elseif ($seccion === 'secciones' && $rol == 1) include("Secciones/secciones.php");
            elseif ($seccion === 'roles' && $rol == 1) include("Roles/roles.php");
            elseif ($seccion === 'usuarios' && $rol == 1) include("Usuarios/usuarios.php");
           elseif ($seccion === 'docentes' && $rol == 1) include("Docentes/docentes.php");
            elseif ($seccion === 'estudiantes' && $rol == 1) include("Estudiantes/estudiantes.php");
            elseif ($seccion === 'calificaciones') include("Calificaciones/calificaciones.php");
            elseif ($seccion === 'comportamiento') include("Comportamiento/comportamiento.php");
            elseif ($seccion === 'reporte_comportamiento') include("Comportamiento/reporte_comportamiento.php");
            elseif ($seccion === 'asistencia') include("Asistencia/asistencia.php");
            elseif ($seccion === 'reporte_asistencia') include("Reporte/reporte_asistencia.php");
            elseif ($seccion === 'chat') include("Chat/chat.php");
            elseif ($seccion === 'inscripcion_estudiante' && $rol == 1) include("Inscripcion/inscripcion_estudiante.php");
            elseif ($seccion === 'registrar_docente' && $rol == 1) include("Docentes/registrar_docente.php");
        }
        ?>
    </div>
</body>
</html>
