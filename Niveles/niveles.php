<?php
include("conexion/conexion.php");

// Guardar nuevo nivel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'guardar') {
    $nombre = trim($_POST['nombre']);
    if ($nombre !== '') {
        // Verificar si ya existe (case-insensitive)
        $sql = "SELECT COUNT(*) AS total FROM niveles WHERE LOWER(nombre) = LOWER(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['total'] > 0) {
            echo "<script>alert('⚠️ El nivel \"$nombre\" ya está registrado');</script>";
        } else {
            $sql = "INSERT INTO niveles (nombre) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $nombre);
            $stmt->execute();
        }
    }
}

// Actualizar nivel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);

    // Verificar duplicado al editar
    $sql = "SELECT COUNT(*) AS total FROM niveles WHERE LOWER(nombre) = LOWER(?) AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nombre, $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['total'] > 0) {
        echo "<script>alert('⚠️ El nivel \"$nombre\" ya está registrado');</script>";
    } else {
        $sql = "UPDATE niveles SET nombre = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombre, $id);
        $stmt->execute();
    }
}

// Eliminar nivel
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM niveles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Redirigir para limpiar la URL
    header("Location: principal.php?seccion=niveles");
    exit();
}

// Obtener niveles
$sql = "SELECT * FROM niveles ORDER BY id ASC";
$result = $conn->query($sql);
?>
<div class="container mt-4">
    <h3 class="mb-4 text-primary"><i class="bi bi-layers"></i> Gestión de Niveles</h3>

    <!-- Registrar nuevo nivel -->
    <div class="card shadow-sm p-4 mb-4">
        <h5 class="mb-3">Registrar nuevo nivel</h5>
        <form method="POST" class="row g-3">
            <input type="hidden" name="accion" value="guardar">
            <div class="col-md-9">
                <input type="text" name="nombre" class="form-control" placeholder="Ejemplo: Primaria" required>
            </div>
            <div class="col-md-3 text-end">
                <button type="submit" class="btn btn-primary w-100">Guardar nivel</button>
            </div>
        </form>
    </div>

    <!-- Lista de niveles -->
    <div class="card shadow-sm p-4">
        <h6 class="mb-3">Lista de niveles registrados</h6>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <!-- Quitamos la columna ID -->
                    <th>Nombre</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <!-- Quitamos la celda ID -->
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal<?php echo $row['id']; ?>">
                            <i class="bi bi-pencil-square"></i> Editar
                        </button>
                        <a href="principal.php?seccion=niveles&eliminar=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este nivel?');">
                            <i class="bi bi-trash"></i> Eliminar
                        </a>
                    </td>
                </tr>

                <!-- Modal edición -->
                <div class="modal fade" id="editarModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title">Editar Nivel</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                          <input type="hidden" name="accion" value="editar">
                          <div class="mb-3">
                              <label class="form-label">Nombre del nivel</label>
                              <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
