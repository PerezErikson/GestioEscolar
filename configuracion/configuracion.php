<?php
include(__DIR__ . "/../conexion/conexion.php");

// =========================
// GUARDAR NUEVO CENTRO
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    // =========================
    // GUARDAR
    // =========================
    if ($_POST['accion'] === 'guardar') {

        $nombre_centro = trim($_POST['nombre_centro']);
        $direccion = trim($_POST['direccion']);
        $distrito = trim($_POST['distrito']);
        $telefono = trim($_POST['telefono']);
        $correo = trim($_POST['correo']);
        $director = trim($_POST['director']);

        if (!empty($nombre_centro)) {

            $sql = "INSERT INTO configuracion 
            (nombre_centro, direccion, distrito, telefono, correo, director)
            VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssssss",
                $nombre_centro,
                $direccion,
                $distrito,
                $telefono,
                $correo,
                $director
            );

            $stmt->execute();
        }

        header("Location: principal.php?seccion=configuracion");
        exit();
    }

    // =========================
    // ACTUALIZAR
    // =========================
    if ($_POST['accion'] === 'actualizar') {

        $id = intval($_POST['id']);

        $nombre_centro = trim($_POST['nombre_centro']);
        $direccion = trim($_POST['direccion']);
        $distrito = trim($_POST['distrito']);
        $telefono = trim($_POST['telefono']);
        $correo = trim($_POST['correo']);
        $director = trim($_POST['director']);

        $sql = "UPDATE configuracion 
                SET nombre_centro = ?, 
                    direccion = ?, 
                    distrito = ?, 
                    telefono = ?, 
                    correo = ?, 
                    director = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssssssi",
            $nombre_centro,
            $direccion,
            $distrito,
            $telefono,
            $correo,
            $director,
            $id
        );

        $stmt->execute();

        header("Location: principal.php?seccion=configuracion");
        exit();
    }
}

// =========================
// ELIMINAR
// =========================
if (isset($_GET['eliminar'])) {

    $id = intval($_GET['eliminar']);

    $sql = "DELETE FROM configuracion WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    header("Location: principal.php?seccion=configuracion");
    exit();
}

// =========================
// LISTAR CENTROS
// =========================
$sql = "SELECT * FROM configuracion ORDER BY id ASC";
$result = $conn->query($sql);
?>

<div class="container mt-4">

    <h3 class="mb-4 text-primary">
        <i class="bi bi-gear-fill"></i>
        Gestión de Configuración
    </h3>

    <!-- REGISTRAR CENTRO -->
    <div class="card shadow-sm p-4 mb-4">

        <h5 class="mb-3">
            Registrar Nuevo Centro Educativo
        </h5>

        <form method="POST" class="row g-3">

            <input type="hidden" name="accion" value="guardar">

            <div class="col-md-4">

                <input type="text"
                       name="nombre_centro"
                       class="form-control"
                       placeholder="Nombre del centro"
                       required>

            </div>

            <div class="col-md-4">

                <input type="text"
                       name="distrito"
                       class="form-control"
                       placeholder="Distrito"
                       required>

            </div>

            <div class="col-md-4">

                <input type="text"
                       name="direccion"
                       class="form-control"
                       placeholder="Dirección"
                       required>

            </div>

            <div class="col-md-4">

                <input type="text"
                       name="telefono"
                       class="form-control"
                       placeholder="Teléfono">

            </div>

            <div class="col-md-4">

                <input type="email"
                       name="correo"
                       class="form-control"
                       placeholder="Correo institucional">

            </div>

            <div class="col-md-4">

                <input type="text"
                       name="director"
                       class="form-control"
                       placeholder="Director">

            </div>

            <div class="col-12 text-end">

                <button type="submit" class="btn btn-primary w-100">

                    <i class="bi bi-save"></i>
                    Guardar Centro

                </button>

            </div>

        </form>

    </div>

    <!-- TABLA -->
    <div class="card shadow-sm p-4">

        <h5 class="mb-3">
            Centros Registrados
        </h5>

        <div class="table-responsive">

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

                            <!-- BOTON EDITAR -->
                            <button 
                                class="btn btn-secondary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editarModal<?php echo $row['id']; ?>">

                                <i class="bi bi-pencil-square"></i>
                                Editar

                            </button>

                            <!-- BOTON ELIMINAR -->
                            <a href="principal.php?seccion=configuracion&eliminar=<?php echo $row['id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Seguro que deseas eliminar este centro?');">

                                <i class="bi bi-trash"></i>
                                Eliminar

                            </a>

                            <!-- MODAL EDITAR -->
                            <div class="modal fade"
                                 id="editarModal<?php echo $row['id']; ?>"
                                 tabindex="-1">

                                <div class="modal-dialog modal-lg">

                                    <div class="modal-content border-0 shadow">

                                        <!-- HEADER NEUTRO -->
                                        <div class="modal-header"
                                             style="background-color: #495057; color: white;">

                                            <h5 class="modal-title">
                                                <i class="bi bi-pencil-square"></i>
                                                Editar Centro Educativo
                                            </h5>

                                            <button type="button"
                                                    class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal">
                                            </button>

                                        </div>

                                        <form method="POST">

                                            <div class="modal-body">

                                                <input type="hidden"
                                                       name="accion"
                                                       value="actualizar">

                                                <input type="hidden"
                                                       name="id"
                                                       value="<?php echo $row['id']; ?>">

                                                <div class="row g-3">

                                                    <div class="col-md-6">

                                                        <label class="form-label">
                                                            Nombre del Centro
                                                        </label>

                                                        <input type="text"
                                                               name="nombre_centro"
                                                               class="form-control"
                                                               required
                                                               value="<?php echo htmlspecialchars($row['nombre_centro']); ?>">

                                                    </div>

                                                    <div class="col-md-6">

                                                        <label class="form-label">
                                                            Distrito
                                                        </label>

                                                        <input type="text"
                                                               name="distrito"
                                                               class="form-control"
                                                               required
                                                               value="<?php echo htmlspecialchars($row['distrito']); ?>">

                                                    </div>

                                                    <div class="col-md-6">

                                                        <label class="form-label">
                                                            Dirección
                                                        </label>

                                                        <input type="text"
                                                               name="direccion"
                                                               class="form-control"
                                                               required
                                                               value="<?php echo htmlspecialchars($row['direccion']); ?>">

                                                    </div>

                                                    <div class="col-md-6">

                                                        <label class="form-label">
                                                            Teléfono
                                                        </label>

                                                        <input type="text"
                                                               name="telefono"
                                                               class="form-control"
                                                               value="<?php echo htmlspecialchars($row['telefono']); ?>">

                                                    </div>

                                                    <div class="col-md-6">

                                                        <label class="form-label">
                                                            Correo
                                                        </label>

                                                        <input type="email"
                                                               name="correo"
                                                               class="form-control"
                                                               value="<?php echo htmlspecialchars($row['correo']); ?>">

                                                    </div>

                                                    <div class="col-md-6">

                                                        <label class="form-label">
                                                            Director
                                                        </label>

                                                        <input type="text"
                                                               name="director"
                                                               class="form-control"
                                                               value="<?php echo htmlspecialchars($row['director']); ?>">

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="modal-footer">

                                                <button type="button"
                                                        class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">

                                                    Cancelar

                                                </button>

                                                <button type="submit"
                                                        class="btn btn-dark">

                                                    <i class="bi bi-check-circle"></i>
                                                    Actualizar

                                                </button>

                                            </div>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </td>

                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>