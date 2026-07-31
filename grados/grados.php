<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Conexión corregida
include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// =========================
// GUARDAR NUEVO GRADO
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar') {
    $nombre = trim($_POST['nombre']);
    $id_seccion = intval($_POST['id_seccion']);
    $id_nivel = intval($_POST['id_nivel']);

    if ($nombre !== '' && $id_seccion > 0 && $id_nivel > 0) {
        $sql = "INSERT INTO grados1 (nombre, id_seccion, id_nivel) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $nombre, $id_seccion, $id_nivel);
        $stmt->execute();

        $mensaje = "✅ Grado registrado correctamente.";
        $tipo_mensaje = "success";
    }
}

// =========================
// EDITAR GRADO
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $id_seccion = intval($_POST['id_seccion']);
    $id_nivel = intval($_POST['id_nivel']);

    $sql = "UPDATE grados1 SET nombre=?, id_seccion=?, id_nivel=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $nombre, $id_seccion, $id_nivel, $id);
    $stmt->execute();

    $mensaje = "✅ Grado actualizado correctamente.";
    $tipo_mensaje = "success";
}

// =========================
// ELIMINAR GRADO
// =========================
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM grados1 WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $mensaje = "🗑️ Grado eliminado correctamente.";
    $tipo_mensaje = "danger";
}

// =========================
// LISTAR GRADOS
// =========================
$sql = "
    SELECT g.id, g.nombre, s.nombre AS seccion, n.nombre AS nivel
    FROM grados1 g
    INNER JOIN secciones s ON g.id_seccion = s.id
    INNER JOIN niveles n ON g.id_nivel = n.id
    ORDER BY n.id, g.id
";
$result = $conn->query($sql);

// =========================
// OBTENER SECCIONES Y NIVELES
// =========================
$secciones = $conn->query("SELECT id, nombre FROM secciones ORDER BY id ASC");
$niveles = $conn->query("SELECT id, nombre FROM niveles ORDER BY id ASC");
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-mortarboard-fill"></i> Gestión de Grados
    </h3>

    <!-- ALERTAS -->
    <?php if (!empty($mensaje)) { ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-3 mb-4"
             role="alert">
            <i class="bi bi-info-circle-fill fs-3"></i>
            <div class="flex-grow-1 fw-semibold"><?php echo $mensaje; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <!-- FORMULARIO NUEVO GRADO -->
    <div class="card border-0 shadow-lg rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3">Registrar nuevo grado</h5>
        <form method="POST" class="row g-3">
            <input type="hidden" name="accion" value="guardar">
            <div class="col-md-4">
                <input type="text" name="nombre" class="form-control" placeholder="Ejemplo: 1ro, 2do, 3ro" required>
            </div>
            <div class="col-md-4">
                <select name="id_seccion" class="form-select" required>
                    <option value="">-- Sección --</option>
                    <?php while($s = $secciones->fetch_assoc()) { ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="id_nivel" class="form-select" required>
                    <option value="">-- Nivel --</option>
                    <?php while($n = $niveles->fetch_assoc()) { ?>
                        <option value="<?php echo $n['id']; ?>"><?php echo htmlspecialchars($n['nombre']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>

    <!-- LISTA DE GRADOS -->
    <div class="card border-0 shadow-lg rounded-4 p-4">
        <h6 class="fw-bold mb-3">Lista de grados registrados</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Sección</th>
                        <th>Nivel</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['seccion']); ?></td>
                        <td><?php echo htmlspecialchars($row['nivel']); ?></td>
                        <td class="text-center">
                            <!-- Botón editar minimalista (image_8be9db.png) -->
                            <button type="button" class="btn btn-outline-primary rounded-3 p-2 lh-1 me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editarModal<?php echo $row['id']; ?>">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </button>
                            <!-- Botón eliminar minimalista (image_8be9db.png) -->
                            <button type="button" class="btn btn-outline-danger rounded-3 p-2 lh-1"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#eliminarModal<?php echo $row['id']; ?>">
                                <i class="bi bi-trash3 fs-5"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal de Confirmación de Eliminación Personalizado (image_8be99f.png) -->
                    <div class="modal fade" id="eliminarModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
                        <div class="modal-content border-0 shadow rounded-4 p-4 text-center">
                          <div class="modal-body p-0">
                            <!-- Icono grande de basurero -->
                            <div class="text-danger mb-4">
                              <i class="bi bi-trash3-fill" style="font-size: 5rem; color: #dc3545; opacity: 0.9;"></i>
                            </div>
                            <!-- Título -->
                            <h4 class="fw-bold text-dark mb-3">Eliminar Grado</h4>
                            <!-- Mensaje descriptivo con el nombre dinámico -->
                            <p class="text-secondary px-3 mb-4">
                              ¿Seguro que deseas eliminar el grado <span class="fw-bold text-dark">"<?php echo htmlspecialchars($row['nombre'] . ' - ' . $row['seccion']); ?>"</span>?
                            </p>
                            <!-- Botones de Acción -->
                            <div class="d-flex justify-content-center gap-2">
                              <button type="button" class="btn btn-light border rounded-3 px-4 py-2 text-dark fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                              <a href="principal.php?seccion=grados&eliminar=<?php echo $row['id']; ?>" class="btn btn-danger rounded-3 px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-trash3-fill"></i> Eliminar
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Modal edición -->
                    <div class="modal fade" id="editarModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form method="POST">
                            <div class="modal-header">
                              <h5 class="modal-title">Editar Grado</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                              <input type="hidden" name="accion" value="editar">
                              <div class="mb-3">
                                  <label>Nombre del grado</label>
                                  <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                              </div>
                              <div class="mb-3">
                                  <label>Sección</label>
                                  <select name="id_seccion" class="form-select" required>
                                      <?php
                                      $secciones2 = $conn->query("SELECT id, nombre FROM secciones ORDER BY id ASC");
                                      while($s2 = $secciones2->fetch_assoc()) {
                                          $selected = ($s2['nombre'] == $row['seccion']) ? "selected" : "";
                                          echo "<option value='{$s2['id']}' $selected>{$s2['nombre']}</option>";
                                      }
                                      ?>
                                  </select>
                              </div>
                              <div class="mb-3">
                                  <label>Nivel</label>
                                  <select name="id_nivel" class="form-select" required>
                                      <?php
                                      $niveles2 = $conn->query("SELECT id, nombre FROM niveles ORDER BY id ASC");
                                      while($n2 = $niveles2->fetch_assoc()) {
                                          $selected = ($n2['nombre'] == $row['nivel']) ? "selected" : "";
                                          echo "<option value='{$n2['id']}' $selected>{$n2['nombre']}</option>";
                                      }
                                      ?>
                                  </select>
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

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>