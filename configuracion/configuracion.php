<?php
include(__DIR__ . "/../conexion/conexion.php");

// Guardar nuevo centro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'guardar') {
    $nombre_centro = trim($_POST['nombre_centro']);
    $direccion = trim($_POST['direccion']);
    $distrito = trim($_POST['distrito']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $director = trim($_POST['director']);

    if ($nombre_centro !== '') {
        $sql = "INSERT INTO configuracion (nombre_centro, direccion, distrito, telefono, correo, director)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $nombre_centro, $direccion, $distrito, $telefono, $correo, $director);
        $stmt->execute();
    }
}

// Actualizar centro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id']);
    $nombre_centro = trim($_POST['nombre_centro']);
    $direccion = trim($_POST['direccion']);
    $distrito = trim($_POST['distrito']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $director = trim($_POST['director']);

    $sql = "UPDATE configuracion SET nombre_centro=?, direccion=?, distrito=?, telefono=?, correo=?, director=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $nombre_centro, $direccion, $distrito, $telefono, $correo, $director, $id);
    $stmt->execute();
}

// Eliminar centro
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM configuracion WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: principal.php?seccion=configuracion");
    exit();
}

// Obtener centros
$sql = "SELECT * FROM configuracion ORDER BY id ASC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h3 class="mb-4 text-primary"><i class="bi bi-gear-fill"></i> Gestión de Configuración</h3>

    <!-- Registrar nuevo centro -->
    <div class="card shadow-sm p-4 mb-4">
        <h5 class="mb-3">Registrar nuevo centro educativo</h5>
        <form method="POST" class="row g-3">
            <input type="hidden" name="accion" value="guardar">
            <div class="col-md-4">
                <input type="text" name="nombre_centro" class="form-control" placeholder="Nombre del centro" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="distrito" class="form-control" placeholder="Distrito" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="direccion" class="form-control" placeholder="Dirección" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="telefono" class="form-control" placeholder="Teléfono">
            </div>
            <div class="col-md-4">
                <input type="email" name="correo" class="form-control" placeholder="Correo institucional">
            </div>
            <div class="col-md-4">
                <input type="text" name="director" class="form-control" placeholder="Director">
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary w-100">Guardar centro</button>
            </div>
        </form>
    </div>

    <!-- Lista de centros -->
    <div class="card shadow-sm p-4">
        <h6 class="mb-3">Lista de centros registrados</h6>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Distrito</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Director</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre_centro']); ?></td>
                    <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                    <td><?php echo htmlspecialchars($row['distrito']); ?></td>
                    <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($row['correo']); ?></td>
                    <td><?php echo htmlspecialchars($row['director']); ?></td>
                    <td class="text-center">
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>
                        </form>
                        <a href="principal.php?seccion=configuracion&eliminar=<?php echo $row['id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('¿Seguro que deseas eliminar este centro?');">
                           <i class="bi bi-trash"></i> Eliminar
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
