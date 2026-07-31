<?php
// ==========================================
// OBTENER DATOS DEL CENTRO EDUCATIVO
// ==========================================
$config = $conn->query("
    SELECT *
    FROM configuracion
    LIMIT 1
")->fetch_assoc();

$nombre_centro = $config['nombre_centro'] ?? 'Centro Educativo';
$director       = $config['director'] ?? 'Director no registrado';

// ==========================================
// CONTADORES DOCENTE
// ==========================================
$total_estudiantes = $conn->query("
    SELECT COUNT(*) AS total
    FROM estudiantes
")->fetch_assoc()['total'];

$total_materias = $conn->query("
    SELECT COUNT(*) AS total
    FROM materias
")->fetch_assoc()['total'];

$total_asistencia = $conn->query("
    SELECT COUNT(*) AS total
    FROM asistencia
")->fetch_assoc()['total'];

$total_comportamiento = $conn->query("
    SELECT COUNT(*) AS total
    FROM comportamiento
")->fetch_assoc()['total'];
?>

<div class="container-fluid">

    <!-- ========================================== -->
    <!-- BIENVENIDA DOCENTE -->
    <!-- ========================================== -->

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">

        <div class="p-5 text-white"
             style="background: linear-gradient(135deg,#0d6efd,#003b80);">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h1 class="fw-bold mb-3">

                        👨‍🏫 Panel del Docente

                    </h1>

                    <p class="fs-5 mb-4">

                        Bienvenido al sistema académico de
                        <?php echo htmlspecialchars($nombre_centro); ?>.
                        Desde aquí podrás gestionar asistencia,
                        comportamiento y calificaciones de los estudiantes.

                    </p>

                    <div class="d-flex flex-wrap gap-3">

                        <span class="badge bg-light text-dark px-4 py-3 rounded-pill fs-6">

                            🏫 Centro Educativo:
                            <?php echo htmlspecialchars($nombre_centro); ?>

                        </span>

                        <span class="badge bg-warning text-dark px-4 py-3 rounded-pill fs-6">

                            👨‍💼 Director:
                            <?php echo htmlspecialchars($director); ?>

                        </span>

                    </div>

                </div>

                <div class="col-md-4 text-center">

                    <i class="bi bi-person-workspace"
                       style="
                            font-size:150px;
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

        <!-- CALIFICACIONES -->
        <div class="col-md-4">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <i class="bi bi-journal-check text-primary mb-3"
                       style="font-size:60px;">
                    </i>

                    <h4 class="fw-bold">

                        Calificaciones

                    </h4>

                    <p class="text-muted">

                        Gestiona las notas y evaluaciones
                        de los estudiantes.

                    </p>

                    <a href="principal.php?seccion=calificaciones"
                       class="btn btn-primary rounded-pill px-4">

                        Entrar

                    </a>

                </div>

            </div>

        </div>

        <!-- ASISTENCIA -->
        <div class="col-md-4">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <i class="bi bi-calendar-check text-success mb-3"
                       style="font-size:60px;">
                    </i>

                    <h4 class="fw-bold">

                        Asistencia

                    </h4>

                    <p class="text-muted">

                        Registra y administra la asistencia
                        diaria de los estudiantes.

                    </p>

                    <a href="principal.php?seccion=asistencia"
                       class="btn btn-success rounded-pill px-4">

                        Entrar

                    </a>

                </div>

            </div>

        </div>

        <!-- COMPORTAMIENTO -->
        <div class="col-md-4">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <i class="bi bi-clipboard-check text-warning mb-3"
                       style="font-size:60px;">
                    </i>

                    <h4 class="fw-bold">

                        Comportamiento

                    </h4>

                    <p class="text-muted">

                        Lleva el control disciplinario
                        y reportes estudiantiles.

                    </p>

                    <a href="principal.php?seccion=comportamiento"
                       class="btn btn-warning rounded-pill px-4">

                        Entrar

                    </a>

                </div>

            </div>

        </div>

    </div>

    <!-- ========================================== -->
    <!-- ESTADISTICAS DOCENTE -->
    <!-- ========================================== -->

    <div class="row g-4 mb-4">

        <!-- ESTUDIANTES -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center p-4 h-100">

                <i class="bi bi-people-fill text-primary mb-3"
                   style="font-size:50px;">
                </i>

                <h2 class="fw-bold">

                    <?php echo $total_estudiantes; ?>

                </h2>

                <h6 class="text-muted">

                    Estudiantes

                </h6>

            </div>

        </div>

        <!-- MATERIAS -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center p-4 h-100">

                <i class="bi bi-book-half text-danger mb-3"
                   style="font-size:50px;">
                </i>

                <h2 class="fw-bold">

                    <?php echo $total_materias; ?>

                </h2>

                <h6 class="text-muted">

                    Materias

                </h6>

            </div>

        </div>

        <!-- ASISTENCIA -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center p-4 h-100">

                <i class="bi bi-calendar2-week text-success mb-3"
                   style="font-size:50px;">
                </i>

                <h2 class="fw-bold">

                    <?php echo $total_asistencia; ?>

                </h2>

                <h6 class="text-muted">

                    Asistencias

                </h6>

            </div>

        </div>

        <!-- COMPORTAMIENTO -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center p-4 h-100">

                <i class="bi bi-exclamation-triangle text-warning mb-3"
                   style="font-size:50px;">
                </i>

                <h2 class="fw-bold">

                    <?php echo $total_comportamiento; ?>

                </h2>

                <h6 class="text-muted">

                    Reportes

                </h6>

            </div>

        </div>

    </div>

    <!-- ========================================== -->
    <!-- MENSAJE MOTIVACIONAL -->
    <!-- ========================================== -->

    <div class="card border-0 shadow rounded-4">

        <div class="card-body text-center p-5">

            <i class="bi bi-lightbulb-fill text-warning mb-4"
               style="font-size:70px;">
            </i>

            <h3 class="fw-bold mb-3">

                Inspirando el Futuro

            </h3>

            <p class="fs-5 text-muted">

                “Un buen docente puede crear esperanza,
                encender la imaginación e inspirar amor
                por el aprendizaje.”

            </p>

            <span class="fw-bold text-primary">

                — Brad Henry

            </span>

        </div>

    </div>

</div>