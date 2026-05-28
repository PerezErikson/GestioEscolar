<?php
// Contadores de registros existentes
$niveles = $conn->query("SELECT COUNT(*) AS total FROM niveles")->fetch_assoc()['total'];
$roles = $conn->query("SELECT COUNT(*) AS total FROM roles")->fetch_assoc()['total'];
$secciones = $conn->query("SELECT COUNT(*) AS total FROM secciones")->fetch_assoc()['total'];
$usuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$grados = $conn->query("SELECT COUNT(*) AS total FROM grados1")->fetch_assoc()['total'];
$materias = $conn->query("SELECT COUNT(*) AS total FROM materias")->fetch_assoc()['total'];
$estudiantes = $conn->query("SELECT COUNT(*) AS total FROM estudiantes")->fetch_assoc()['total'];
$comportamiento = $conn->query("SELECT COUNT(*) AS total FROM comportamiento")->fetch_assoc()['total'];
$asistencia = $conn->query("SELECT COUNT(*) AS total FROM asistencia")->fetch_assoc()['total'];

// Datos del centro educativo
$config = $conn->query("SELECT * FROM configuracion LIMIT 1")->fetch_assoc();
?>
<!-- Información del centro educativo -->
<?php if (!empty($config)) { ?>
<div class="card shadow-lg mb-4 border-0">
    <div class="card-header bg-dark text-white text-center">
        <h4 class="mb-0"><i class="bi bi-bank"></i> Información Institucional</h4>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="text-secondary"><i class="bi bi-building"></i> Nombre del Centro</h6>
                <p class="fs-5 fw-bold"><?php echo htmlspecialchars($config['nombre_centro']); ?></p>
            </div>
            <div class="col-md-6">
                <h6 class="text-secondary"><i class="bi bi-person-badge"></i> Director</h6>
                <p class="fs-5 fw-bold"><?php echo htmlspecialchars($config['director']); ?></p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="text-secondary"><i class="bi bi-geo-alt"></i> Dirección</h6>
                <p class="fs-5"><?php echo htmlspecialchars($config['direccion']); ?></p>
            </div>
            <div class="col-md-6">
                <h6 class="text-secondary"><i class="bi bi-diagram-3"></i> Distrito Escolar</h6>
                <p class="fs-5"><?php echo htmlspecialchars($config['distrito']); ?></p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="text-secondary"><i class="bi bi-telephone"></i> Teléfono</h6>
                <p class="fs-5"><?php echo htmlspecialchars($config['telefono']); ?></p>
            </div>
            <div class="col-md-6">
                <h6 class="text-secondary"><i class="bi bi-envelope"></i> Correo Institucional</h6>
                <p class="fs-5"><?php echo htmlspecialchars($config['correo']); ?></p>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<!-- Tarjetas de módulos -->
<div class="row g-4">
    <!-- Niveles -->
    <div class="col-md-3">
        <div class="card bg-success p-3 text-center text-white">
            <i class="bi bi-mortarboard-fill" style="font-size:40px;"></i>
            <h5>Niveles registrados</h5>
            <h4><?php echo $niveles; ?></h4>
            <a href="principal.php?seccion=niveles" class="btn btn-light mt-2 w-100">Entrar</a>
        </div>
    </div>

    <!-- Roles -->
    <div class="col-md-3">
        <div class="card bg-primary p-3 text-center text-white">
            <i class="bi bi-gear-fill" style="font-size:40px;"></i>
            <h5>Roles registrados</h5>
            <h4><?php echo $roles; ?></h4>
            <a href="principal.php?seccion=roles" class="btn btn-light mt-2 w-100">Entrar</a>
        </div>
    </div>

    <!-- Secciones -->
    <div class="col-md-3">
        <div class="card bg-warning p-3 text-center text-dark">
            <i class="bi bi-folder-fill" style="font-size:40px;"></i>
            <h5>Secciones registradas</h5>
            <h4><?php echo $secciones; ?></h4>
            <a href="principal.php?seccion=secciones" class="btn btn-light mt-2 w-100">Entrar</a>
        </div>
    </div>

    <!-- Usuarios -->
    <div class="col-md-3">
        <div class="card bg-info p-3 text-center text-white">
            <i class="bi bi-people-fill" style="font-size:40px;"></i>
            <h5>Usuarios registrados</h5>
            <h4><?php echo $usuarios; ?></h4>
            <a href="principal.php?seccion=usuarios" class="btn btn-light mt-2 w-100">Entrar</a>
        </div>
    </div>

    <!-- Grados -->
    <div class="col-md-3">
        <div class="card bg-secondary p-3 text-center text-white">
            <i class="bi bi-grid-fill" style="font-size:40px;"></i>
            <h5>Grados registrados</h5>
            <h4><?php echo $grados; ?></h4>
            <a href="principal.php?seccion=grados" class="btn btn-light mt-2 w-100">Entrar</a>
        </div>
    </div>

    <!-- Materias -->
    <div class="col-md-3">
        <div class="card bg-danger p-3 text-center text-white">
            <i class="bi bi-book-half" style="font-size:40px;"></i>
            <h5>Materias registradas</h5>
            <h4><?php echo $materias; ?></h4>
            <a href="principal.php?seccion=materias" class="btn btn-light mt-2 w-100">Entrar</a>
        </div>
    </div>

    <!-- Estudiantes -->
    <div class="col-md-3">
        <div class="card bg-light p-3 text-center text-dark border">
            <i class="bi bi-person-vcard-fill" style="font-size:40px;"></i>
            <h5>Estudiantes registrados</h5>
            <h4><?php echo $estudiantes; ?></h4>
            <a href="principal.php?seccion=estudiantes" class="btn btn-dark mt-2 w-100">Entrar</a>
        </div>
    </div>

    <!-- Comportamiento -->
    <div class="col-md-3">
        <div class="card bg-warning p-3 text-center text-dark">
            <i class="bi bi-clipboard-check" style="font-size:40px;"></i>
            <h5>Reportes de comportamiento</h5>
            <h4><?php echo $comportamiento; ?></h4>
            <a href="principal.php?seccion=comportamiento" class="btn btn-light mt-2 w-100">Entrar</a>
        </div>
    </div>

    <!-- Asistencia -->
    <div class="col-md-3">
        <div class="card bg-success p-3 text-center text-white">
            <i class="bi bi-calendar-check" style="font-size:40px;"></i>
            <h5>Registros de asistencia</h5>
            <h4><?php echo $asistencia; ?></h4>
            <a href="principal.php?seccion=asistencia" class="btn btn-light mt-2 w-100">Entrar</a>
        </div>
    </div>
</div>
