<?php
include("conexion/conexion.php");

// Guardar nueva materia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar') {
    $nombre = trim($_POST['nombre']);
    $id_nivel = intval($_POST['id_nivel']);
    if ($nombre !== '' && $id_nivel > 0) {
        // Verificar duplicado (case-insensitive y mismo nivel)
        $sql = "SELECT COUNT(*) AS total FROM materias WHERE LOWER(nombre) = LOWER(?) AND id_nivel = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombre, $id_nivel);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['total'] > 0) {
            echo "<script>alert('⚠️ La materia \"$nombre\" ya está registrada en ese nivel');</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO materias (nombre, id_nivel) VALUES (?, ?)");
            $stmt->bind_param("si", $nombre, $id_nivel);
            $stmt->execute();
        }
    }
}

// Actualizar materia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $id_nivel = intval($_POST['id_nivel']);

    $sql = "SELECT COUNT(*) AS total FROM materias WHERE LOWER(nombre) = LOWER(?) AND id_nivel = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $nombre, $id_nivel, $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['total'] > 0) {
        echo "<script>alert('⚠️ La materia \"$nombre\" ya está registrada en ese nivel');</script>";
    } else {
        $stmt = $conn->prepare("UPDATE materias SET nombre=?, id_nivel=? WHERE id=?");
        $stmt->bind_param("sii", $nombre, $id_nivel, $id);
        $stmt->execute();
    }
}

// Eliminar materia
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM materias WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: principal.php?seccion=materias");
    exit();
}

// Obtener niveles
$niveles = $conn->query("SELECT * FROM niveles ORDER BY nombre ASC");

// Obtener materias registradas
$materias = $conn->query("SELECT m.id, m.nombre, m.id_nivel, n.nombre AS nivel 
                          FROM materias m 
                          INNER JOIN niveles n ON m.id_nivel = n.id 
                          ORDER BY m.id ASC");
?>
<div class="container mt-4">
    <h3 class="mb-4 text-primary"><i class="bi bi-book"></i> Gestión de Materias</h3>

    <!-- Registrar nueva materia -->
    <div class="card shadow-sm p-4 mb-4">
        <h5 class="mb-3">Registrar nueva materia</h5>
        <form method="POST" class="row g-3">
            <input type="hidden" name="accion" value="guardar">
            <div class="col-md-6">
                <input type="text" name="nombre" class="form-control" placeholder="Ejemplo: Matemáticas" required>
            </div>
            <div class="col-md-5">
                <select name="id_nivel" class="form-select" required>
                    <option value="">Seleccione un nivel</option>
                    <?php while($row = $niveles->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-1 text-end">
                <button type="submit" class="btn btn-primary w-100">Guardar</button>
            </div>
        </form>
    </div>

    <!-- Lista de materias -->
    <div class="card shadow-sm p-4">
        <h6 class="mb-3">Lista de materias registradas</h6>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <!-- Quitamos la columna ID -->
                    <th>Nombre</th>
                    <th>Nivel</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $materias->fetch_assoc()) { ?>
                <tr>
                    <!-- Quitamos la celda ID -->
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['nivel']); ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-warning btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editarModal<?php echo $row['id']; ?>">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <a href="principal.php?seccion=materias&eliminar=<?php echo $row['id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('¿Seguro que deseas eliminar esta materia?');">
                           <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>

                <!-- Modal de edición -->
                <div class="modal fade" id="editarModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title">Editar Materia</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                          <input type="hidden" name="accion" value="editar">
                          <div class="mb-3">
                              <label class="form-label">Nombre de la materia</label>
                              <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
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
