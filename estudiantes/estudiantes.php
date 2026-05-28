<?php
include(__DIR__ . "/../conexion/conexion.php");

// Obtener todos los estudiantes
$estudiantes = $conn->query("SELECT e.id, e.nombre, e.apellido, e.cedula, e.correo, e.fecha_nacimiento,
                                    e.direccion, e.telefono, e.padre, e.madre,
                                    CONCAT(g.nombre, ' ', s.nombre) AS grado,
                                    n.nombre AS nivel
                             FROM estudiantes e
                             INNER JOIN grados1 g ON e.grado_id = g.id
                             INNER JOIN secciones s ON g.id_seccion = s.id
                             INNER JOIN niveles n ON e.nivel_id = n.id
                             ORDER BY e.id ASC");

// Actualizar estudiante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $cedula = trim($_POST['cedula']);
    $correo = trim($_POST['correo']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $padre = trim($_POST['padre']);
    $madre = trim($_POST['madre']);
    $grado = intval($_POST['grado']);
    $nivel = intval($_POST['nivel']);

    $stmt = $conn->prepare("UPDATE estudiantes 
                            SET nombre=?, apellido=?, cedula=?, correo=?, fecha_nacimiento=?, direccion=?, telefono=?, padre=?, madre=?, grado_id=?, nivel_id=? 
                            WHERE id=?");
    $stmt->bind_param("ssssssssiiii", $nombre, $apellido, $cedula, $correo, $fecha_nacimiento, $direccion, $telefono, $padre, $madre, $grado, $nivel, $id);
    $stmt->execute();

    echo "<script>alert('✅ Estudiante actualizado correctamente'); window.location.href='../principal.php?seccion=estudiantes';</script>";
}
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary"><i class="bi bi-person-vcard-fill"></i> Gestión de Estudiantes</h3>

    <!-- Tabla de estudiantes -->
    <div class="card shadow-sm p-4">
        <h6 class="mb-3">Lista de estudiantes registrados</h6>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>ID</th>
                    <th>Correo</th>
                    <th>Grado</th>
                    <th>Nivel</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $estudiantes->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                    <td><?php echo htmlspecialchars($row['correo']); ?></td>
                    <td><?php echo htmlspecialchars($row['grado']); ?></td>
                    <td><?php echo htmlspecialchars($row['nivel']); ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-warning btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editarModal<?php echo $row['id']; ?>">
                            <i class="bi bi-pencil-square"></i> Editar
                        </button>
                    </td>
                </tr>

                <!-- Modal de edición -->
                <div class="modal fade" id="editarModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title">Editar Estudiante</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                          <input type="hidden" name="accion" value="editar">

                          <div class="mb-3">
                              <label class="form-label">Nombre</label>
                              <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Apellido</label>
                              <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($row['apellido']); ?>" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">ID</label>
                              <input type="text" name="cedula" class="form-control" value="<?php echo htmlspecialchars($row['cedula']); ?>" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Correo electrónico</label>
                              <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($row['correo']); ?>" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Fecha de nacimiento</label>
                              <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo $row['fecha_nacimiento']; ?>">
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Dirección</label>
                              <input type="text" name="direccion" class="form-control" value="<?php echo $row['direccion']; ?>">
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Teléfono</label>
                              <input type="text" name="telefono" class="form-control" value="<?php echo $row['telefono']; ?>">
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Padre</label>
                              <input type="text" name="padre" class="form-control" value="<?php echo $row['padre']; ?>">
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Madre</label>
                              <input type="text" name="madre" class="form-control" value="<?php echo $row['madre']; ?>">
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Grado (con sección)</label>
                              <select name="grado" class="form-select" required>
                                  <?php
                                  $grados2 = $conn->query("SELECT g.id, g.nombre AS grado, s.nombre AS seccion 
                                                           FROM grados1 g 
                                                           INNER JOIN secciones s ON g.id_seccion = s.id 
                                                           ORDER BY g.nombre ASC, s.nombre ASC");
                                  while($g2 = $grados2->fetch_assoc()) {
                                      $selected = ($g2['id'] == $row['grado_id']) ? "selected" : "";
                                      echo "<option value='{$g2['id']}' $selected>" . htmlspecialchars($g2['grado'] . ' ' . $g2['seccion']) . "</option>";
                                  }
                                  ?>
                              </select>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Nivel</label>
                              <select name="nivel" class="form-select" required>
                                  <?php
                                  $niveles2 = $conn->query("SELECT * FROM niveles ORDER BY nombre ASC");
                                  while($n2 = $niveles2->fetch_assoc()) {
                                      $selected = ($n2['id'] == $row['nivel_id']) ? "selected" : "";
                                      echo "<option value='{$n2['id']}' $selected>" . htmlspecialchars($n2['nombre']) . "</option>";
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
