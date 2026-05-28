<?php
include(__DIR__ . "/../conexion/conexion.php");

// =========================
// GUARDAR ESTUDIANTE
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar') {

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

    // Verificar duplicado
    $sql = "SELECT COUNT(*) AS total FROM estudiantes WHERE cedula = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['total'] > 0) {

        echo "<script>alert('⚠️ Ya existe un estudiante con esa cédula');</script>";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO estudiantes
            (
                nombre,
                apellido,
                cedula,
                correo,
                fecha_nacimiento,
                direccion,
                telefono,
                padre,
                madre,
                grado_id,
                nivel_id
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssssssii",
            $nombre,
            $apellido,
            $cedula,
            $correo,
            $fecha_nacimiento,
            $direccion,
            $telefono,
            $padre,
            $madre,
            $grado,
            $nivel
        );

        if ($stmt->execute()) {

            echo "<script>alert('✅ Estudiante inscrito correctamente');</script>";

        } else {

            echo "<script>alert('❌ Error: ".$stmt->error."');</script>";
        }
    }
}

// =========================
// EDITAR ESTUDIANTE
// =========================
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

    // Verificar duplicado
    $sql = "SELECT COUNT(*) AS total FROM estudiantes WHERE cedula = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cedula, $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['total'] > 0) {

        echo "<script>alert('⚠️ Ya existe otro estudiante con esa cédula');</script>";

    } else {

        $stmt = $conn->prepare("
            UPDATE estudiantes SET
                nombre=?,
                apellido=?,
                cedula=?,
                correo=?,
                fecha_nacimiento=?,
                direccion=?,
                telefono=?,
                padre=?,
                madre=?,
                grado_id=?,
                nivel_id=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "sssssssssiii",
            $nombre,
            $apellido,
            $cedula,
            $correo,
            $fecha_nacimiento,
            $direccion,
            $telefono,
            $padre,
            $madre,
            $grado,
            $nivel,
            $id
        );

        if ($stmt->execute()) {

            echo "<script>alert('✅ Estudiante actualizado correctamente');</script>";

        } else {

            echo "<script>alert('❌ Error al actualizar: ".$stmt->error."');</script>";
        }
    }
}

// =========================
// ELIMINAR
// =========================
if (isset($_GET['eliminar'])) {

    $id = intval($_GET['eliminar']);

    $stmt = $conn->prepare("DELETE FROM estudiantes WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: ../principal.php?seccion=inscripcion_estudiante");
    exit();
}

// =========================
// OBTENER GRADOS
// =========================
$grados = $conn->query("
    SELECT
        g.id,
        g.nombre AS grado,
        s.nombre AS seccion
    FROM grados1 g
    INNER JOIN secciones s
    ON g.id_seccion = s.id
    ORDER BY g.nombre ASC
");

// =========================
// OBTENER NIVELES
// =========================
$niveles = $conn->query("
    SELECT *
    FROM niveles
    ORDER BY nombre ASC
");

// =========================
// OBTENER ESTUDIANTES
// =========================
$estudiantes = $conn->query("
    SELECT
        e.*,
        CONCAT(g.nombre, ' ', s.nombre) AS grado,
        n.nombre AS nivel
    FROM estudiantes e
    INNER JOIN grados1 g
    ON e.grado_id = g.id
    INNER JOIN secciones s
    ON g.id_seccion = s.id
    INNER JOIN niveles n
    ON e.nivel_id = n.id
    ORDER BY e.id ASC
");
?>

<div class="container mt-4">

    <h3 class="mb-4 text-primary">
        <i class="bi bi-person-plus"></i>
        Inscripción de Estudiantes
    </h3>

    <!-- FORMULARIO -->
    <div class="card shadow-sm p-4 mb-4">

        <form method="POST" class="row g-3">

            <input type="hidden" name="accion" value="guardar">

            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">ID</label>
                <input type="text" name="cedula" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Fecha nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control" required>
            </div>

            <div class="col-md-12">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Padre</label>
                <input type="text" name="padre" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Madre</label>
                <input type="text" name="madre" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Grado</label>

                <select name="grado" class="form-select" required>

                    <option value="">Seleccione</option>

                    <?php while($g = $grados->fetch_assoc()) { ?>

                        <option value="<?php echo $g['id']; ?>">

                            <?php
                            echo htmlspecialchars($g['grado'] . ' ' . $g['seccion']);
                            ?>

                        </option>

                    <?php } ?>

                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Nivel</label>

                <select name="nivel" class="form-select" required>

                    <option value="">Seleccione</option>

                    <?php while($n = $niveles->fetch_assoc()) { ?>

                        <option value="<?php echo $n['id']; ?>">

                            <?php echo htmlspecialchars($n['nombre']); ?>

                        </option>

                    <?php } ?>

                </select>
            </div>

            <div class="col-12 text-end">

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i>
                    Guardar inscripción
                </button>

            </div>

        </form>

    </div>

    <!-- TABLA -->
    <div class="card shadow-sm p-4">

        <h5 class="mb-3">Estudiantes inscritos</h5>

        <table class="table table-bordered table-hover align-middle">

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

                            <!-- BOTON EDITAR -->
                            <button type="button"
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editarModal<?php echo $row['id']; ?>">

                                <i class="bi bi-pencil-square"></i>
                                Editar

                            </button>

                            <!-- BOTON ELIMINAR -->
                            <a href="Inscripcion/inscripcion_estudiante.php?eliminar=<?php echo $row['id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Seguro que deseas eliminar este estudiante?')">

                                <i class="bi bi-trash"></i>
                                Eliminar

                            </a>

                        </td>

                    </tr>

                    <!-- MODAL EDITAR -->
                    <div class="modal fade"
                         id="editarModal<?php echo $row['id']; ?>"
                         tabindex="-1"
                         aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">

                                <form method="POST">

                                    <div class="modal-header bg-primary text-white">

                                        <h5 class="modal-title">
                                            Editar Estudiante
                                        </h5>

                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"></button>

                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden"
                                               name="accion"
                                               value="editar">

                                        <input type="hidden"
                                               name="id"
                                               value="<?php echo $row['id']; ?>">

                                        <div class="mb-3">
                                            <label class="form-label">Nombre</label>

                                            <input type="text"
                                                   name="nombre"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($row['nombre']); ?>"
                                                   required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Apellido</label>

                                            <input type="text"
                                                   name="apellido"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($row['apellido']); ?>"
                                                   required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Cédula</label>

                                            <input type="text"
                                                   name="cedula"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($row['cedula']); ?>"
                                                   required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Correo</label>

                                            <input type="email"
                                                   name="correo"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($row['correo']); ?>"
                                                   required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Fecha nacimiento</label>

                                            <input type="date"
                                                   name="fecha_nacimiento"
                                                   class="form-control"
                                                   value="<?php echo $row['fecha_nacimiento']; ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Dirección</label>

                                            <input type="text"
                                                   name="direccion"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($row['direccion']); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Teléfono</label>

                                            <input type="text"
                                                   name="telefono"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($row['telefono']); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Padre</label>

                                            <input type="text"
                                                   name="padre"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($row['padre']); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Madre</label>

                                            <input type="text"
                                                   name="madre"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($row['madre']); ?>">
                                        </div>

                                        <!-- GRADO -->
                                        <div class="mb-3">

                                            <label class="form-label">Grado</label>

                                            <select name="grado"
                                                    class="form-select"
                                                    required>

                                                <?php
                                                $gradosEditar = $conn->query("
                                                    SELECT
                                                        g.id,
                                                        g.nombre AS grado,
                                                        s.nombre AS seccion
                                                    FROM grados1 g
                                                    INNER JOIN secciones s
                                                    ON g.id_seccion = s.id
                                                    ORDER BY g.nombre ASC
                                                ");

                                                while($g2 = $gradosEditar->fetch_assoc()) {

                                                    $selected = ($g2['id'] == $row['grado_id'])
                                                        ? 'selected'
                                                        : '';

                                                    echo "
                                                    <option value='{$g2['id']}' $selected>
                                                        {$g2['grado']} {$g2['seccion']}
                                                    </option>";
                                                }
                                                ?>

                                            </select>

                                        </div>

                                        <!-- NIVEL -->
                                        <div class="mb-3">

                                            <label class="form-label">Nivel</label>

                                            <select name="nivel"
                                                    class="form-select"
                                                    required>

                                                <?php
                                                $nivelesEditar = $conn->query("
                                                    SELECT *
                                                    FROM niveles
                                                    ORDER BY nombre ASC
                                                ");

                                                while($n2 = $nivelesEditar->fetch_assoc()) {

                                                    $selected = ($n2['id'] == $row['nivel_id'])
                                                        ? 'selected'
                                                        : '';

                                                    echo "
                                                    <option value='{$n2['id']}' $selected>
                                                        {$n2['nombre']}
                                                    </option>";
                                                }
                                                ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="submit"
                                                class="btn btn-success">

                                            Actualizar

                                        </button>

                                        <button type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal">

                                            Cancelar

                                        </button>

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

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>