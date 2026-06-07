<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// =========================
// GUARDAR COMPETENCIA
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar_competencia') {
    $nombre       = $conn->real_escape_string(trim($_POST['nombre']));
    $descripcion  = $conn->real_escape_string(trim($_POST['descripcion']));
    $estado       = $conn->real_escape_string($_POST['estado']);

    if ($nombre !== '') {
        $sql = "INSERT INTO competencias (nombre, descripcion, estado) VALUES ('$nombre', '$descripcion', '$estado')";

        if ($conn->query($sql)) {
            $mensaje = "✅ Competencia agregada correctamente.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "❌ Error al guardar: " . $conn->error;
            $tipo_mensaje = "danger";
        }
    }
}

// =========================
// EDITAR COMPETENCIA
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar_competencia') {
    $id           = intval($_POST['id']);
    $nombre       = $conn->real_escape_string(trim($_POST['nombre']));
    $descripcion  = $conn->real_escape_string(trim($_POST['descripcion']));
    $estado       = $conn->real_escape_string($_POST['estado']);

    $sql = "UPDATE competencias SET nombre = '$nombre', descripcion = '$descripcion', estado = '$estado' WHERE id = $id";

    if ($conn->query($sql)) {
        $mensaje = "✅ Competencia actualizada correctamente.";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "❌ Error al actualizar: " . $conn->error;
        $tipo_mensaje = "danger";
    }
}

// =========================
// ELIMINAR COMPETENCIA
// =========================
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM competencias WHERE id = $id";

    if ($conn->query($sql)) {
        $mensaje = "🗑️ Competencia eliminada correctamente.";
        $tipo_mensaje = "danger";
    } else {
        $mensaje = "❌ Error al eliminar: " . $conn->error;
        $tipo_mensaje = "danger";
    }
}

// =========================
// LISTAR COMPETENCIAS
// =========================
$sql = "SELECT * FROM competencias ORDER BY id ASC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-lightbulb"></i> Gestión de Competencias Fundamentales
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
        <h5 class="fw-bold mb-3">Registrar nueva competencia</h5>
        <form method="POST">
            <input type="hidden" name="accion" value="guardar_competencia">

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre de la competencia</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ejemplo: Comunicativa" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" placeholder="Describe brevemente la competencia (opcional)"></textarea>
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
                    <i class="bi bi-save"></i> Guardar Competencia
                </button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-lg rounded-4 p-4">
        <h6 class="fw-bold mb-3">Lista de competencias registradas</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
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
                            <h4 class="fw-bold text-dark mb-3">Eliminar Competencia</h4>
                            <p class="text-secondary px-3 mb-4">
                              ¿Seguro que deseas eliminar la competencia <span class="fw-bold text-dark">"<?php echo htmlspecialchars($row['nombre']); ?>"</span>?
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                              <button type="button" class="btn btn-light border rounded-3 px-4 py-2 text-dark fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                              <a href="principal.php?seccion=competencias&eliminar=<?php echo $row['id']; ?>" class="btn btn-danger rounded-3 px-4 py-2 fw-semibold d-flex align-items-center gap-2">
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
                              <h5 class="modal-title fw-bold">Editar Competencia</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-start">
                              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                              <input type="hidden" name="accion" value="editar_competencia">
                              
                              <div class="mb-3">
                                  <label class="form-label fw-semibold">Nombre de la competencia</label>
                                  <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                              </div>
                              <div class="mb-3">
                                  <label class="form-label fw-semibold">Descripción</label>
                                  <textarea name="descripcion" class="form-control" rows="3" required><?php echo htmlspecialchars($row['descripcion']); ?></textarea>
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