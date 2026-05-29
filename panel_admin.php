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
$telefono       = $config['telefono'] ?? 'No disponible';
$correo         = $config['correo'] ?? 'No disponible';
$direccion      = $config['direccion'] ?? 'No disponible';
$distrito       = $config['distrito'] ?? 'No disponible';

// ==========================================
// CONTADORES
// ==========================================
$total_estudiantes = $conn->query("
    SELECT COUNT(*) AS total
    FROM estudiantes
")->fetch_assoc()['total'];

$total_docentes = $conn->query("
    SELECT COUNT(*) AS total
    FROM docente
")->fetch_assoc()['total'];

$total_tutores = $conn->query("
    SELECT COUNT(*) AS total
    FROM responsables
")->fetch_assoc()['total'];

$total_materias = $conn->query("
    SELECT COUNT(*) AS total
    FROM materias
")->fetch_assoc()['total'];

$total_grados = $conn->query("
    SELECT COUNT(*) AS total
    FROM grados1
")->fetch_assoc()['total'];

$total_asistencia = $conn->query("
    SELECT COUNT(*) AS total
    FROM asistencia
")->fetch_assoc()['total'];
?>

<!-- ========================================== -->
<!-- BIENVENIDA -->
<!-- ========================================== -->

<div class="container-fluid">

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">

        <div class="p-5 text-white"
             style="
                background: linear-gradient(135deg,#003366,#0059b3);
             ">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h1 class="fw-bold mb-3">

                        🎓 <?php echo htmlspecialchars($nombre_centro); ?>

                    </h1>

                    <p class="fs-5 mb-4">

                        Bienvenido al sistema de gestión académica del centro educativo.
                        Administra estudiantes, docentes, asistencia, calificaciones y mucho más
                        desde un solo lugar.

                    </p>

                    <div class="d-flex flex-wrap gap-3">

                        <span class="badge bg-light text-dark px-4 py-3 rounded-pill fs-6">

                            👨‍🏫 Director:
                            <?php echo htmlspecialchars($director); ?>

                        </span>

                        <span class="badge bg-warning text-dark px-4 py-3 rounded-pill fs-6">

                            📚 Distrito:
                            <?php echo htmlspecialchars($distrito); ?>

                        </span>

                    </div>

                </div>

                <div class="col-md-4 text-center">

                    <i class="bi bi-mortarboard-fill"
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
    <!-- TARJETAS PRINCIPALES -->
    <!-- ========================================== -->

    <div class="row g-4 mb-4">

        <!-- ESTUDIANTES -->
        <div class="col-md-4">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <div class="mb-3">

                        <i class="bi bi-people-fill text-primary"
                           style="font-size:60px;">
                        </i>

                    </div>

                    <h2 class="fw-bold text-primary">

                        <?php echo $total_estudiantes; ?>

                    </h2>

                    <h5 class="fw-semibold">

                        Estudiantes Registrados

                    </h5>

                    <p class="text-muted">

                        Total de estudiantes activos en el sistema.

                    </p>

                </div>

            </div>

        </div>

        <!-- DOCENTES -->
        <div class="col-md-4">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <div class="mb-3">

                        <i class="bi bi-person-workspace text-success"
                           style="font-size:60px;">
                        </i>

                    </div>

                    <h2 class="fw-bold text-success">

                        <?php echo $total_docentes; ?>

                    </h2>

                    <h5 class="fw-semibold">

                        Docentes Registrados

                    </h5>

                    <p class="text-muted">

                        Personal docente disponible en el centro.

                    </p>

                </div>

            </div>

        </div>

        <!-- TUTORES -->
        <div class="col-md-4">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <div class="mb-3">

                        <i class="bi bi-person-hearts text-danger"
                           style="font-size:60px;">
                        </i>

                    </div>

                    <h2 class="fw-bold text-danger">

                        <?php echo $total_tutores; ?>

                    </h2>

                    <h5 class="fw-semibold">

                        Padres / Tutores

                    </h5>

                    <p class="text-muted">

                        Responsables registrados en la institución.

                    </p>

                </div>

            </div>

        </div>

    </div>

    <!-- ========================================== -->
    <!-- SEGUNDA FILA -->
    <!-- ========================================== -->

    <div class="row g-4 mb-4">

        <!-- MATERIAS -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center p-4 h-100">

                <i class="bi bi-book-half text-warning mb-3"
                   style="font-size:50px;">
                </i>

                <h3 class="fw-bold">

                    <?php echo $total_materias; ?>

                </h3>

                <h6 class="text-muted">
                    Materias
                </h6>

            </div>

        </div>

        <!-- GRADOS -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center p-4 h-100">

                <i class="bi bi-grid-fill text-info mb-3"
                   style="font-size:50px;">
                </i>

                <h3 class="fw-bold">

                    <?php echo $total_grados; ?>

                </h3>

                <h6 class="text-muted">
                    Grados
                </h6>

            </div>

        </div>

        <!-- ASISTENCIA -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center p-4 h-100">

                <i class="bi bi-calendar-check text-success mb-3"
                   style="font-size:50px;">
                </i>

                <h3 class="fw-bold">

                    <?php echo $total_asistencia; ?>

                </h3>

                <h6 class="text-muted">
                    Registros de Asistencia
                </h6>

            </div>

        </div>

        <!-- DIRECTOR -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center p-4 h-100">

                <i class="bi bi-person-badge-fill text-primary mb-3"
                   style="font-size:50px;">
                </i>

                <h6 class="fw-bold">

                    Director

                </h6>

                <p class="mb-0 text-muted">

                    <?php echo htmlspecialchars($director); ?>

                </p>

            </div>

        </div>

    </div>

    <!-- ========================================== -->
    <!-- INFORMACION DEL CENTRO -->
    <!-- ========================================== -->

    <div class="row g-4">

        <!-- INFORMACION -->
        <div class="col-lg-8">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-header bg-primary text-white rounded-top-4 p-3">

                    <h5 class="mb-0">

                        <i class="bi bi-building"></i>
                        Información Institucional

                    </h5>

                </div>

                <div class="card-body p-4">

                    <div class="row mb-4">

                        <div class="col-md-6">

                            <h6 class="text-secondary fw-bold">

                                📍 Dirección

                            </h6>

                            <p>

                                <?php echo htmlspecialchars($direccion); ?>

                            </p>

                        </div>

                        <div class="col-md-6">

                            <h6 class="text-secondary fw-bold">

                                ☎️ Teléfono

                            </h6>

                            <p>

                                <?php echo htmlspecialchars($telefono); ?>

                            </p>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <h6 class="text-secondary fw-bold">

                                📧 Correo

                            </h6>

                            <p>

                                <?php echo htmlspecialchars($correo); ?>

                            </p>

                        </div>

                        <div class="col-md-6">

                            <h6 class="text-secondary fw-bold">

                                🏫 Distrito Escolar

                            </h6>

                            <p>

                                <?php echo htmlspecialchars($distrito); ?>

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- FRASE -->
        <div class="col-lg-4">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body d-flex flex-column justify-content-center text-center p-4">

                    <i class="bi bi-stars text-warning mb-4"
                       style="font-size:70px;">
                    </i>

                    <h4 class="fw-bold mb-3">

                        Educación de Calidad

                    </h4>

                    <p class="text-muted fs-5">

                        “La educación es el arma más poderosa
                        para cambiar el mundo.”

                    </p>

                    <span class="fw-semibold text-primary">

                        — Nelson Mandela

                    </span>

                </div>

            </div>

        </div>

    </div>

</div>