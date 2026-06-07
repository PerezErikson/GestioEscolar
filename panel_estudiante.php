<?php
// ==========================================
// DATOS DEL CENTRO EDUCATIVO
// ==========================================
$config = $conn->query("
    SELECT *
    FROM configuracion
    LIMIT 1
")->fetch_assoc();

$nombre_centro = $config['nombre_centro'] ?? 'Centro Educativo';

// ==========================================
// DATOS DEL ESTUDIANTE
// ==========================================
$nombre_estudiante = $_SESSION['nombre'] ?? 'Estudiante';

// ==========================================
// FRASES MOTIVADORAS
// ==========================================
$frases = [

    "La educación es la llave del éxito.",
    "Nunca dejes de aprender.",
    "Tu esfuerzo de hoy será tu éxito de mañana.",
    "Cada día es una nueva oportunidad para aprender.",
    "El conocimiento cambia vidas."

];

$frase = $frases[array_rand($frases)];
?>

<div class="container-fluid">

    <!-- ========================================== -->
    <!-- BIENVENIDA -->
    <!-- ========================================== -->

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">

        <div class="p-5 text-white"
             style="
                background: linear-gradient(135deg,#0d6efd,#3b82f6);
             ">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h1 class="fw-bold mb-3">

                        👋 Bienvenido/a,
                        <?php echo htmlspecialchars($nombre_estudiante); ?>

                    </h1>

                    <p class="fs-5 mb-4">

                        Nos alegra tenerte nuevamente en
                        <strong>
                            <?php echo htmlspecialchars($nombre_centro); ?>
                        </strong>.

                    </p>

                    <div class="d-flex flex-wrap gap-3">

                        <span class="badge bg-light text-dark px-4 py-3 rounded-pill fs-6">

                            🎓 Panel del Estudiante

                        </span>

                        <span class="badge bg-warning text-dark px-4 py-3 rounded-pill fs-6">

                            📚 Sigue aprendiendo cada día

                        </span>

                    </div>

                </div>

                <div class="col-md-4 text-center">

                    <i class="bi bi-mortarboard-fill"
                       style="
                            font-size:140px;
                            opacity:0.2;
                       ">
                    </i>

                </div>

            </div>

        </div>

    </div>

    <!-- ========================================== -->
    <!-- ACCESOS RAPIDOS -->
    <!-- ========================================== -->

    <div class="row g-4 mb-4">

        <!-- COMPORTAMIENTO -->
        <div class="col-md-6">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body text-center p-5">

                    <div class="mb-4">

                        <i class="bi bi-clipboard-check-fill text-primary"
                           style="font-size:70px;">
                        </i>

                    </div>

                    <h3 class="fw-bold mb-3">

                        Mi Comportamiento

                    </h3>

                    <p class="text-muted fs-5">

                        Consulta tus reportes de comportamiento
                        registrados por tus docentes.

                    </p>

                    <a href="principal.php?seccion=reporte_comportamiento"
                       class="btn btn-primary rounded-pill px-4 py-2 mt-2">

                        Ver Reportes

                    </a>

                </div>

            </div>

        </div>

        <!-- ASISTENCIA -->
        <div class="col-md-6">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body text-center p-5">

                    <div class="mb-4">

                        <i class="bi bi-calendar-check-fill text-success"
                           style="font-size:70px;">
                        </i>

                    </div>

                    <h3 class="fw-bold mb-3">

                        Mi Asistencia

                    </h3>

                    <p class="text-muted fs-5">

                        Consulta tu historial de asistencia
                        en el centro educativo.

                    </p>

                    <a href="principal.php?seccion=reporte_asistencia"
                       class="btn btn-success rounded-pill px-4 py-2 mt-2">

                        Ver Asistencia

                    </a>

                </div>

            </div>

        </div>

    </div>

    <!-- ========================================== -->
    <!-- FRASE MOTIVADORA -->
    <!-- ========================================== -->

    <div class="card border-0 shadow rounded-4 mb-4">

        <div class="card-body text-center p-5">

            <i class="bi bi-stars text-warning mb-4"
               style="font-size:80px;">
            </i>

            <h2 class="fw-bold mb-4">

                Frase Motivadora

            </h2>

            <p class="fs-4 text-muted">

                “<?php echo $frase; ?>”

            </p>

        </div>

    </div>

    <!-- ========================================== -->
    <!-- INFORMACION -->
    <!-- ========================================== -->

    <div class="row g-4">

        <!-- INFORMACION CENTRO -->
        <div class="col-lg-6">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-header bg-primary text-white rounded-top-4 p-3">

                    <h5 class="mb-0">

                        <i class="bi bi-building"></i>
                        Información del Centro

                    </h5>

                </div>

                <div class="card-body p-4">

                    <div class="mb-4">

                        <h6 class="text-secondary fw-bold">

                            🏫 Centro Educativo

                        </h6>

                        <p class="fs-5">

                            <?php echo htmlspecialchars($nombre_centro); ?>

                        </p>

                    </div>

                    <div class="mb-4">

                        <h6 class="text-secondary fw-bold">

                            📍 Dirección

                        </h6>

                        <p class="fs-5">

                            <?php echo htmlspecialchars($config['direccion'] ?? 'No disponible'); ?>

                        </p>

                    </div>

                    <div>

                        <h6 class="text-secondary fw-bold">

                            ☎️ Teléfono

                        </h6>

                        <p class="fs-5">

                            <?php echo htmlspecialchars($config['telefono'] ?? 'No disponible'); ?>

                        </p>

                    </div>

                </div>

            </div>

        </div>

        <!-- CONSEJOS -->
        <div class="col-lg-6">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-header bg-success text-white rounded-top-4 p-3">

                    <h5 class="mb-0">

                        <i class="bi bi-lightbulb-fill"></i>
                        Consejos para el Éxito

                    </h5>

                </div>

                <div class="card-body p-4">

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item py-3">

                            📖 Estudia diariamente.

                        </li>

                        <li class="list-group-item py-3">

                            🕒 Llega puntual a clases.

                        </li>

                        <li class="list-group-item py-3">

                            ✍️ Cumple con tus tareas.

                        </li>

                        <li class="list-group-item py-3">

                            🤝 Respeta a tus compañeros y docentes.

                        </li>

                        <li class="list-group-item py-3">

                            🌟 Mantén una actitud positiva.

                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>