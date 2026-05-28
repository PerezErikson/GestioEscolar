<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . "/../conexion/conexion.php");

// Guardar nuevo grado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar') {
    $nombre = trim($_POST['nombre']);
    $id_seccion = intval($_POST['id_seccion']);
    $id_nivel = intval($_POST['id_nivel']);
    if ($nombre !== '' && $id_seccion > 0 && $id_nivel > 0) {
        // Verificar duplicado (case-insensitive, mismo nivel y sección)
        $sql = "SELECT COUNT(*) AS total FROM grados1 
                WHERE LOWER(nombre) = LOWER(?) AND id_seccion = ? AND id_nivel = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $nombre, $id_seccion, $id_nivel);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['total'] > 0) {
            echo "<script>alert('⚠️ El grado \"$nombre\" ya está registrado en esa sección y nivel');</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO grados1 (nombre, id_seccion, id_nivel) VALUES (?, ?, ?)");
            $stmt->bind_param("sii", $nombre, $id_seccion, $id_nivel);
            $stmt->execute();
        }
    }
}

// Actualizar grado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $id_seccion = intval($_POST['id_seccion']);
    $id_nivel = intval($_POST['id_nivel']);

    // Verificar duplicado al editar
    $sql = "SELECT COUNT(*) AS total FROM grados1 
            WHERE LOWER(nombre) = LOWER(?) AND id_seccion = ? AND id_nivel = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $nombre, $id_seccion, $id_nivel, $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['total'] > 0) {
        echo "<script>alert('⚠️ El grado \"$nombre\" ya está registrado en esa sección y nivel');</script>";
    } else {
        $stmt = $conn->prepare("UPDATE grados1 SET nombre=?, id_seccion=?, id_nivel=? WHERE id=?");
        $stmt->bind_param("siii", $nombre, $id_seccion, $id_nivel, $id);
        $stmt->execute();
    }
}

// Eliminar grado
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM grados1 WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: ../principal.php?seccion=grados");
    exit();
}

// Obtener secciones y niveles
$secciones = $conn->query("SELECT * FROM secciones ORDER BY nombre ASC");
$niveles = $conn->query("SELECT * FROM niveles ORDER BY nombre ASC");

// Obtener grados registrados
$grados = $conn->query("SELECT g.id, g.nombre, g.id_seccion, g.id_nivel, 
                               s.nombre AS seccion, n.nombre AS nivel 
                        FROM grados1 g 
                        INNER JOIN secciones s ON g.id_seccion = s.id 
                        INNER JOIN niveles n ON g.id_nivel = n.id 
                        ORDER BY g.id ASC");
?>
<div class="container mt-4">
    <h3 class="mb-4 text-primary"><i class="bi bi-grid"></i> Gestión de Grados</h3>

    <!-- Registrar nuevo grado -->
    <div class="card shadow-sm p-4 mb-4">
        <h5 class="mb-3">Registrar nuevo grado</h5>
        <form method="POST" class="row g-3">
            <input type="hidden" name="accion" value="guardar">
            <div class="col-md-4">
                <input type="text" name="nombre" class="form-control" placeholder="Ejemplo: 1ro de Primaria" required>
            </div>
            <div class="col-md-4">
                <select name="id_seccion" class="form-select" required>
                    <option value="">Seleccione sección</option>
                    <?php while($row = $secciones->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="id_nivel" class="form-select" required>
                    <option value="">Seleccione nivel</option>
                    <?php while($row = $niveles->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-1 text-end">
                <button type="submit" class="btn btn-primary w-100">Guardar grado</button>
            </div>
        </form>
    </div>

    <!-- Lista de grados -->
    <div class="card shadow-sm p-4">
        <h6 class="mb-3">Lista de grados registrados</h6>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Sección</th>
                    <th>Nivel</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $grados->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['seccion']); ?></td>
                    <td><?php echo htmlspecialchars($row['nivel']); ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-warning btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editarModal<?php echo $row['id']; ?>">
                            <i class="bi bi-pencil-square"></i> Editar
                        </button>
                        <a href="Grados/grados.php?eliminar=<?php echo $row['id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('¿Seguro que deseas eliminar este grado?');">
                           <i class="bi bi-trash"></i> Eliminar
                        </a>
                    </td>
                </tr>

                <!-- Modal de edición -->
                <div class="modal fade" id="editarModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title">Editar Grado</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                          <input type="hidden" name="accion" value="editar">
                          <div class="mb-3">
                              <label class="form-label">Nombre del grado</label>
                              <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Sección</label>
                              <select name="id_seccion" class="form-select" required>
                                  <?php
                                  $secciones2 = $conn->query("SELECT * FROM secciones ORDER BY nombre ASC");
                                  while($s = $secciones2->fetch_assoc()) {
                                      $selected = ($s['id'] == $row['id_seccion']) ? "selected" : "";
                                      echo "<option value='{$s['id']}' $selected>{$s['nombre']}</option>";
                                  }
                                  ?>
                              </select>
                          </div>
                                                    <div class="mb-3">
                              <label class="form-label">Nivel</label>
                              <select name="id_nivel" class="form-select" required>
                                  <?php
                                  $niveles2 = $conn->query("SELECT * FROM niveles ORDER BY nombre ASC");
                                  while($n = $niveles2->fetch_assoc()) {
                                      $selected = ($n['id'] == $row['id_nivel']) ? "selected" : "";
                                      echo "<option value='{$n['id']}' $selected>{$n['nombre']}</option>";
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

<!-- Bootstrap JS para modales -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
