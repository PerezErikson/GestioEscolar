<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// =========================
// GUARDAR AÑO ESCOLAR
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar_anio') {
    $nombre       = $conn->real_escape_string(trim($_POST['nombre']));
    $fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);
    $fecha_fin    = $conn->real_escape_string($_POST['fecha_fin']);
    $estado       = $conn->real_escape_string($_POST['estado']);

    if ($nombre !== '') {
        $sql = "INSERT INTO anio_escolar (nombre, fecha_inicio, fecha_fin, estado)
                VALUES ('$nombre', '$fecha_inicio', '$fecha_fin', '$estado')";

        if ($conn->query($sql)) {
            $mensaje = "✅ Año escolar agregado correctamente.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "❌ Error al guardar: " . $conn->error;
            $tipo_mensaje = "danger";
        }
    }
}

// =========================
// EDITAR AÑO ESCOLAR
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar_anio') {
    $id           = intval($_POST['id']);
    $nombre       = $conn->real_escape_string(trim($_POST['nombre']));
    $fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);
    $fecha_fin    = $conn->real_escape_string($_POST['fecha_fin']);
    $estado       = $conn->real_escape_string($_POST['estado']);

    $sql = "UPDATE anio_escolar SET nombre = '$nombre', fecha_inicio = '$fecha_inicio', fecha_fin = '$fecha_fin', estado = '$estado' WHERE id = $id";

    if ($conn->query($sql)) {
        $mensaje = "✅ Año escolar actualizado correctamente.";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "❌ Error al actualizar: " . $conn->error;
        $tipo_mensaje = "danger";
    }
}

// =========================
// ELIMINAR AÑO ESCOLAR
// =========================
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    $sql = "DELETE FROM anio_escolar WHERE id = $id";

    if ($conn->query($sql)) {
        $mensaje = "🗑️ Año escolar eliminado correctamente.";
        $tipo_mensaje = "danger";
    } else {
        $mensaje = "❌ Error al eliminar: " . $conn->error;
        $tipo_mensaje = "danger";
    }
}

// =========================
// LISTAR AÑOS ESCOLARES
// =========================
$sql = "SELECT * FROM anio_escolar ORDER BY id ASC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-calendar-check"></i> Gestión de Año Escolar
    </h3>

    <?php if (!empty($mensaje)) { ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-3 mb-4"
             role="alert">
            <i class="bi bi-info-circle-fill fs-3"></i>
            <div class="flex-grow-1 fw-semibold"><?php echo $mensaje; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <div class="card border-0 shadow-lg rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3">Registrar nuevo año escolar</h5>
        <form method="POST">
            <input type="hidden" name="accion" value="guardar_anio">

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre del año escolar</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ejemplo: 2025-2026" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Fecha de fin</label>
                    <input type="date" name="fecha_fin" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Estado</label>
                <select name="estado" class="form-select" required>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-save"></i> Guardar Año Escolar
                </button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-lg rounded-4 p-4">
        <h6 class="fw-bold mb-3">Lista de años escolares registrados</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Año Escolar</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_fin']); ?></td>
                        <td>
                            <span class="badge rounded-pill <?php echo ($row['estado'] === 'Activo') ? 'bg-success' : 'bg-secondary'; ?>">
                                <?php echo htmlspecialchars($row['estado']); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-primary rounded-3 p-2 lh-1 me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editarModal<?php echo $row['id']; ?>">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger rounded-3 p-2 lh-1"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#eliminarModal<?php echo $row['id']; ?>">
                                <i class="bi bi-trash3 fs-5"></i>
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="eliminarModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
                        <div class="modal-content border-0 shadow rounded-4 p-4 text-center">
                          <div class="modal-body p-0">
                            <div class="text-danger mb-4">
                              <i class="bi bi-trash3-fill" style="font-size: 5rem; color: #dc3545; opacity: 0.9;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-3">Eliminar Año Escolar</h4>
                            <p class="text-secondary px-3 mb-4">
                              ¿Seguro que deseas eliminar el año escolar <span class="fw-bold text-dark">"<?php echo htmlspecialchars($row['nombre']); ?>"</span>?
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                              <button type="button" class="btn btn-light border rounded-3 px-4 py-2 text-dark fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                              <a href="principal.php?seccion=ano_escolar&eliminar=<?php echo $row['id']; ?>"
   class="btn btn-danger rounded-3 px-4 py-2 fw-semibold d-flex align-items-center gap-2">
    <i class="bi bi-trash3-fill"></i> Eliminar
</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="modal fade" id="editarModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form method="POST">
                            <div class="modal-header">
                              <h5 class="modal-title fw-bold">Editar Año Escolar</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-start">
                              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                              <input type="hidden" name="accion" value="editar_anio">
                              
                              <div class="mb-3">
                                  <label class="form-label fw-semibold">Nombre del año escolar</label>
                                  <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                              </div>
                              <div class="mb-3">
                                  <label class="form-label fw-semibold">Fecha de inicio</label>
                                  <input type="date" name="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($row['fecha_inicio']); ?>" required>
                              </div>
                              <div class="mb-3">
                                  <label class="form-label fw-semibold">Fecha de fin</label>
                                  <input type="date" name="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($row['fecha_fin']); ?>" required>
                              </div>
                              <div class="mb-3">
                                  <label class="form-label fw-semibold">Estado</label>
                                  <select name="estado" class="form-select" required>
                                      <option value="Activo" <?php echo ($row['estado'] === 'Activo') ? 'selected' : ''; ?>>Activo</option>
                                      <option value="Inactivo" <?php echo ($row['estado'] === 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                  </select>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" class="btn btn-success rounded-3">Actualizar</button>
                              <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>