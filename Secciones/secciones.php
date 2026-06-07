<?php
include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// =========================
// GUARDAR NUEVA SECCIÓN
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    if ($nombre !== '' && $descripcion !== '') {
        // Validar duplicados (case-insensitive)
        $check = $conn->prepare("SELECT id FROM secciones WHERE LOWER(nombre) = LOWER(?)");
        $check->bind_param("s", $nombre);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $mensaje = "⚠️ La sección ya existe.";
            $tipo_mensaje = "warning";
        } else {
            $sql = "INSERT INTO secciones (nombre, descripcion) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $nombre, $descripcion);
            $stmt->execute();

            $mensaje = "✅ Sección registrada correctamente.";
            $tipo_mensaje = "success";
        }
    }
}

// =========================
// EDITAR SECCIÓN
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    $sql = "UPDATE secciones SET nombre = ?, descripcion = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $descripcion, $id);
    $stmt->execute();

    $mensaje = "✅ Sección actualizada correctamente.";
    $tipo_mensaje = "success";
}

// =========================
// ELIMINAR SECCIÓN
// =========================
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM secciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $mensaje = "🗑️ Sección eliminada correctamente.";
    $tipo_mensaje = "danger";
}

// =========================
// LISTAR SECCIONES
// =========================
$sql = "SELECT * FROM secciones ORDER BY id ASC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-grid"></i> Gestión de Secciones
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
        <h5 class="fw-bold mb-3">Registrar nueva sección</h5>
        <form method="POST" class="row g-3">
            <input type="hidden" name="accion" value="guardar">
            <div class="col-md-5">
                <input type="text" name="nombre" class="form-control" placeholder="Ejemplo: A, B, C" required>
            </div>
            <div class="col-md-5">
                <input type="text" name="descripcion" class="form-control" placeholder="Ejemplo: Sección matutina" required>
            </div>
            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-lg rounded-4 p-4">
        <h6 class="fw-bold mb-3">Lista de secciones registradas</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
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
                            <h4 class="fw-bold text-dark mb-3">Eliminar Sección</h4>
                            <p class="text-secondary px-3 mb-4">
                              ¿Seguro que deseas eliminar la sección <span class="fw-bold text-dark">"<?php echo htmlspecialchars($row['nombre']); ?>"</span>?
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                              <button type="button" class="btn btn-light border rounded-3 px-4 py-2 text-dark fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                              <a href="principal.php?seccion=secciones&eliminar=<?php echo $row['id']; ?>" class="btn btn-danger rounded-3 px-4 py-2 fw-semibold d-flex align-items-center gap-2">
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
                              <h5 class="modal-title">Editar Sección</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                              <input type="hidden" name="accion" value="editar">
                              <div class="mb-3">
                                  <label>Nombre de la sección</label>
                                  <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                              </div>
                              <div class="mb-3">
                                  <label>Descripción</label>
                                  <input type="text" name="descripcion" class="form-control" value="<?php echo htmlspecialchars($row['descripcion']); ?>" required>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" class="btn btn-success">Actualizar</button>
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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