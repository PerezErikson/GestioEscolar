<?php

// ==========================================
// INICIAR SESIÓN
// ==========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// CONEXIÓN A LA BASE DE DATOS
// ==========================================
include("conexion/conexion.php");

// ==========================================
// VERIFICAR SI EL USUARIO ES ADMINISTRADOR
// ==========================================
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    echo "
    <div class='container mt-4'>
        <div class='alert alert-danger shadow-sm border-0 rounded-4 text-center p-4'>
            <i class='bi bi-shield-lock-fill fs-1 d-block mb-3'></i>
            <strong>Acceso denegado.</strong>
            <br>
            No tienes permisos para gestionar administradores.
        </div>
    </div>
    ";
    exit();
}

// ==========================================
// VARIABLES PARA MENSAJES
// ==========================================
$mensaje = "";
$tipoMensaje = "";

// ==========================================
// PROCESAR FORMULARIOS (REGISTRAR, EDITAR, ELIMINAR)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    
    $accion = $_POST['accion'];

    // ------------------------------------------
    // ACCIÓN: REGISTRAR
    // ------------------------------------------
    if ($accion === 'registrar') {
        $nombre           = trim($_POST['nombre']);
        $apellido         = trim($_POST['apellido']);
        $correo           = trim($_POST['correo']);
        $fecha_nacimiento = $_POST['fecha_nacimiento'];

        $fecha_nac = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nac)->y;

        if ($edad < 18) {
            $mensaje = "El administrador debe tener al menos 18 años para registrarse.";
            $tipoMensaje = "warning";
        } else {
            $correoCheck = $conn->prepare("SELECT id FROM administrador WHERE correo = ?");
            $correoCheck->bind_param("s", $correo);
            $correoCheck->execute();
            $correoCheck->store_result();

            if ($correoCheck->num_rows > 0) {
                $mensaje = "Ya existe un administrador registrado con ese correo.";
                $tipoMensaje = "warning";
            } else {
                $sql = "INSERT INTO administrador (nombre, apellido, correo, fecha_nacimiento) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $nombre, $apellido, $correo, $fecha_nacimiento);

                if ($stmt->execute()) {
                    $mensaje = "Administrador registrado correctamente.";
                    $tipoMensaje = "success";
                } else {
                    $mensaje = "Error al registrar el administrador.";
                    $tipoMensaje = "danger";
                }
            }
        }
    }

    // ------------------------------------------
    // ACCIÓN: EDITAR
    // ------------------------------------------
    elseif ($accion === 'editar') {
        $id_editar        = intval($_POST['id']);
        $nombre           = trim($_POST['nombre']);
        $apellido         = trim($_POST['apellido']);
        $correo           = trim($_POST['correo']);
        $fecha_nacimiento = $_POST['fecha_nacimiento'];

        $sql_edit = "UPDATE administrador SET nombre = ?, apellido = ?, correo = ?, fecha_nacimiento = ? WHERE id = ?";
        $stmt_edit = $conn->prepare($sql_edit);
        $stmt_edit->bind_param("ssssi", $nombre, $apellido, $correo, $fecha_nacimiento, $id_editar);

        if ($stmt_edit->execute()) {
            $mensaje = "Administrador actualizado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "Error al actualizar el administrador.";
            $tipoMensaje = "danger";
        }
    }

    // ------------------------------------------
    // ACCIÓN: ELIMINAR
    // ------------------------------------------
    elseif ($accion === 'eliminar') {
        $id_eliminar = intval($_POST['id']);
        
        $sql_delete = "DELETE FROM administrador WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id_eliminar);

        if ($stmt_delete->execute()) {
            $mensaje = "Administrador eliminado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "Error al eliminar el administrador.";
            $tipoMensaje = "danger";
        }
    }
}

// ==========================================
// OBTENER LISTA DE ADMINISTRADORES
// ==========================================
$query_admins = "SELECT * FROM administrador ORDER BY id DESC";
$resultado_admins = $conn->query($query_admins);

?>

<!-- ========================================== -->
<!-- DEPENDENCIAS DE BOOTSTRAP (CSS E ICONOS) -->
<!-- ========================================== -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- ========================================== -->
<!-- CONTENEDOR PRINCIPAL -->
<!-- ========================================== -->
<div class="container mt-4 mb-5">

    <!-- TÍTULO -->
    <h3 class="mb-4 text-primary">
        <i class="bi bi-shield-shaded"></i>
        Gestión de Administradores
    </h3>

    <!-- ========================================== -->
    <!-- ALERTAS DE BOOTSTRAP -->
    <!-- ========================================== -->
    <?php if (!empty($mensaje)) { ?>
        <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi 
                    <?php
                        if($tipoMensaje == 'success') echo 'bi-check-circle-fill';
                        elseif($tipoMensaje == 'warning') echo 'bi-exclamation-triangle-fill';
                        elseif($tipoMensaje == 'danger') echo 'bi-exclamation-octagon-fill';
                        else echo 'bi-info-circle-fill';
                    ?> me-2 fs-5">
                </i>
                <div>
                    <strong><?php echo ($tipoMensaje == 'success') ? '¡Éxito!' : (($tipoMensaje == 'warning') ? 'Atención:' : '¡Error!'); ?></strong>
                    <?php echo $mensaje; ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <!-- ========================================== -->
    <!-- FORMULARIO DE REGISTRO -->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-5">
        <h5 class="mb-4"><i class="bi bi-person-plus"></i> Registrar Nuevo Administrador</h5>
        
        <form method="POST" class="row g-3">
            <input type="hidden" name="accion" value="registrar">

            <div class="col-md-6">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="nombre" class="form-control rounded-3" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Apellidos</label>
                <input type="text" name="apellido" class="form-control rounded-3" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Correo electrónico</label>
                <input type="email" name="correo" class="form-control rounded-3" placeholder="ejemplo@correo.com" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control rounded-3" required>
            </div>

            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-save"></i> Guardar administrador
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================== -->
    <!-- TABLA DE ADMINISTRADORES REGISTRADOS -->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="mb-4"><i class="bi bi-people-fill"></i> Administradores Registrados</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Fecha de Nacimiento</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($resultado_admins && $resultado_admins->num_rows > 0) {
                        while ($row = $resultado_admins->fetch_assoc()) { 
                    ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['nombre'] . ' ' . $row['apellido']; ?></td>
                            <td><?php echo $row['correo']; ?></td>
                            <td><?php echo $row['fecha_nacimiento']; ?></td>
                            <td class="text-center">
                                <!-- Botón Editar -->
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <!-- Botón Eliminar (Abre el Modal de Bootstrap) -->
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $row['id']; ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- ========================================== -->
                        <!-- MODAL EDITAR ADMINISTRADOR -->
                        <!-- ========================================== -->
                        <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $row['id']; ?>">Editar Administrador</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="accion" value="editar">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nombre</label>
                                                <input type="text" name="nombre" class="form-control" value="<?php echo $row['nombre']; ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Apellidos</label>
                                                <input type="text" name="apellido" class="form-control" value="<?php echo $row['apellido']; ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Correo electrónico</label>
                                                <input type="email" name="correo" class="form-control" value="<?php echo $row['correo']; ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Fecha de nacimiento</label>
                                                <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo $row['fecha_nacimiento']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary rounded-3">Actualizar Datos</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- MODAL ELIMINAR ADMINISTRADOR -->
                        <!-- ========================================== -->
                        <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteModalLabel<?php echo $row['id']; ?>">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center py-4">
                                        <i class="bi bi-trash3 fs-1 text-danger d-block mb-3"></i>
                                        <p class="mb-1">¿Estás seguro de que deseas eliminar a:</p>
                                        <strong class="fs-5 text-dark"><?php echo $row['nombre'] . ' ' . $row['apellido']; ?>?</strong>
                                        <p class="text-muted small mt-2">Esta acción no se puede deshacer.</p>
                                    </div>
                                    <div class="modal-footer justify-content-center border-0 pb-4">
                                        <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancelar</button>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger rounded-3 px-4">Sí, eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php 
                        } 
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-muted py-3">No hay administradores registrados aún.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ========================================== -->
<!-- SCRIPT DE BOOTSTRAP -->
<!-- ========================================== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>