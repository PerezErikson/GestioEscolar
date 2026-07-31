<?php
include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// =========================
// ACTUALIZAR ESTUDIANTE
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    $numero = intval($_POST['numero']);

    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $cedula = trim($_POST['ID']);
    $correo = trim($_POST['correo']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);

    $grado = intval($_POST['grado']);
    $nivel = intval($_POST['nivel']);
    $responsable_id = intval($_POST['responsable_id']);

    // VALIDAR RESPONSABLE
    if ($responsable_id <= 0) {
        $mensaje = "⚠️ Debe seleccionar un Padre, Madre o Tutor.";
        $tipo_mensaje = "warning";
    } else {
        $stmt = $conn->prepare("
            UPDATE estudiantes 
            SET 
                nombre=?,
                apellido=?,
                ID=?,
                correo=?,
                fecha_nacimiento=?,
                direccion=?,
                telefono=?,
                responsable_id=?,
                grado_id=?,
                nivel_id=?
            WHERE numero=?
        ");

        $stmt->bind_param(
            "sssssssiiii",
            $nombre,
            $apellido,
            $cedula,
            $correo,
            $fecha_nacimiento,
            $direccion,
            $telefono,
            $responsable_id,
            $grado,
            $nivel,
            $numero
        );

        if ($stmt->execute()) {
            $mensaje = "✅ Estudiante actualizado correctamente.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "❌ Error al actualizar: " . $stmt->error;
            $tipo_mensaje = "danger";
        }

        $stmt->close();
    }
}

// =========================
// CONSULTA PRINCIPAL (TRAE TODOS LOS REGISTROS PARA EL FILTRADO EN VIVO)
// =========================
$sql = "
    SELECT 
        e.*,
        r.nombre AS responsable_nombre,
        r.tipo_responsable,
        CONCAT(g.nombre, ' ', s.nombre) AS grado,
        n.nombre AS nivel
    FROM estudiantes e
    LEFT JOIN responsables r 
        ON e.responsable_id = r.id
    INNER JOIN grados1 g 
        ON e.grado_id = g.id
    INNER JOIN secciones s 
        ON g.id_seccion = s.id
    INNER JOIN niveles n 
        ON e.nivel_id = n.id
    ORDER BY e.numero ASC
";
$estudiantes = $conn->query($sql);
?>

<div class="container mt-4">

    <!-- TITULO -->
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-person-vcard-fill"></i>
        Gestión de Estudiantes
    </h3>

    <!-- ALERTAS -->
    <?php if (!empty($mensaje)) { ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-3 mb-4" role="alert">
            <?php if ($tipo_mensaje == "success") { ?>
                <i class="bi bi-check-circle-fill fs-3"></i>
            <?php } else { ?>
                <i class="bi bi-exclamation-triangle-fill fs-3"></i>
            <?php } ?>
            <div class="flex-grow-1 fw-semibold">
                <?php echo $mensaje; ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <!-- CARD -->
    <div class="card border-0 shadow-lg rounded-4 p-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-4">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-people"></i>
                Lista de estudiantes registrados
            </h5>

            <!-- PANEL DE BÚSQUEDA EN TIEMPO REAL -->
            <div class="d-flex gap-2 w-100 w-md-auto" style="max-width: 400px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 rounded-start-3">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" 
                           id="inputBusquedaEnVivo" 
                           class="form-control border-start-0 rounded-end-3" 
                           placeholder="Escribe para buscar (nombre, ID, curso)..." 
                           autocomplete="off">
                </div>
                <button class="btn btn-outline-secondary rounded-3 px-3" type="button" id="btnLimpiarBusqueda" title="Limpiar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaEstudiantes">
                <thead style="background: #1f2937; color: white;">
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Responsable</th>
                        <th>Grado</th>
                        <th>Nivel</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($estudiantes->num_rows > 0) { ?>
                        <?php while($row = $estudiantes->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['numero']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['ID']); ?>
                            </td>
                            <td class="fw-semibold">
                                <?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['correo']); ?>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                                    <?php echo htmlspecialchars($row['tipo_responsable']); ?>:
                                    <?php echo htmlspecialchars($row['responsable_nombre']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                    <?php echo htmlspecialchars($row['grado']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-dark rounded-pill px-3 py-2">
                                    <?php echo htmlspecialchars($row['nivel']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <!-- BOTON EDITAR -->
                                <button type="button"
                                        class="btn btn-outline-primary btn-sm rounded-3"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal<?php echo $row['numero']; ?>">
                                    <i class="bi bi-pencil-square"></i>
                                    Editar
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL -->
                        <div class="modal fade"
                             id="editarModal<?php echo $row['numero']; ?>"
                             tabindex="-1"
                             aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content border-0 rounded-4 shadow-lg">
                                    <!-- HEADER -->
                                    <div class="modal-header bg-dark text-white rounded-top-4">
                                        <h5 class="modal-title">
                                            <i class="bi bi-pencil-square"></i>
                                            Editar Estudiante
                                        </h5>
                                        <button type="button"
                                                class="btn-close btn-close-white"
                                                data-bs-dismiss="modal">
                                        </button>
                                    </div>

                                    <form method="POST" action="principal.php<?php echo isset($_GET['modulo']) ? '?modulo=' . htmlspecialchars($_GET['modulo']) : ''; ?>">
                                        <div class="modal-body p-4">
                                            <!-- INPUT OCULTO -->
                                            <input type="hidden" name="numero" value="<?php echo $row['numero']; ?>">
                                            <input type="hidden" name="accion" value="editar">

                                            <div class="row g-3">
                                                <!-- ID -->
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">ID</label>
                                                    <input type="text"
                                                           name="ID"
                                                           value="<?php echo htmlspecialchars($row['ID']); ?>"
                                                           class="form-control rounded-3"
                                                           required>
                                                </div>

                                                <!-- NOMBRE -->
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nombre</label>
                                                    <input type="text"
                                                           name="nombre"
                                                           class="form-control rounded-3"
                                                           value="<?php echo htmlspecialchars($row['nombre']); ?>"
                                                           required>
                                                </div>

                                                <!-- APELLIDO -->
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Apellido</label>
                                                    <input type="text"
                                                           name="apellido"
                                                           class="form-control rounded-3"
                                                           value="<?php echo htmlspecialchars($row['apellido']); ?>"
                                                           required>
                                                </div>

                                                <!-- CORREO -->
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Correo</label>
                                                    <input type="email"
                                                           name="correo"
                                                           class="form-control rounded-3"
                                                           value="<?php echo htmlspecialchars($row['correo']); ?>"
                                                           required>
                                                </div>

                                                <!-- FECHA -->
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Fecha de nacimiento</label>
                                                    <input type="date"
                                                           name="fecha_nacimiento"
                                                           class="form-control rounded-3"
                                                           value="<?php echo $row['fecha_nacimiento']; ?>">
                                                </div>

                                                <!-- TELEFONO -->
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Teléfono</label>
                                                    <input type="text"
                                                           name="telefono"
                                                           class="form-control rounded-3"
                                                           value="<?php echo $row['telefono']; ?>">
                                                </div>

                                                <!-- DIRECCION -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Dirección</label>
                                                    <input type="text"
                                                           name="direccion"
                                                           class="form-control rounded-3"
                                                           value="<?php echo $row['direccion']; ?>">
                                                </div>

                                                <!-- RESPONSABLE -->
                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold">
                                                        <i class="bi bi-people-fill"></i>
                                                        Padre / Madre / Tutor
                                                    </label>
                                                    <select name="responsable_id" class="form-select rounded-3" required>
                                                        <option value="">Seleccione un responsable</option>
                                                        <?php
                                                        $responsables2 = $conn->query("SELECT * FROM responsables ORDER BY nombre ASC");
                                                        while($r = $responsables2->fetch_assoc()) {
                                                            $selected = ($r['id'] == $row['responsable_id']) ? 'selected' : '';
                                                            echo "<option value='{$r['id']}' $selected>{$r['tipo_responsable']} - {$r['nombre']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <!-- GRADO -->
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Grado</label>
                                                    <select name="grado" class="form-select rounded-3" required>
                                                        <?php
                                                        $grados2 = $conn->query("
                                                            SELECT g.id, g.nombre AS grado, s.nombre AS seccion 
                                                            FROM grados1 g 
                                                            INNER JOIN secciones s ON g.id_seccion = s.id 
                                                            ORDER BY g.nombre ASC
                                                        ");
                                                        while($g2 = $grados2->fetch_assoc()) {
                                                            $selected = ($g2['id'] == $row['grado_id']) ? "selected" : "";
                                                            echo "<option value='{$g2['id']}' $selected>{$g2['grado']} {$g2['seccion']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <!-- NIVEL -->
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nivel</label>
                                                    <select name="nivel" class="form-select rounded-3" required>
                                                        <?php
                                                        $niveles2 = $conn->query("SELECT * FROM niveles ORDER BY nombre ASC");
                                                        while($n2 = $niveles2->fetch_assoc()) {
                                                            $selected = ($n2['id'] == $row['nivel_id']) ? "selected" : "";
                                                            echo "<option value='{$n2['id']}' $selected>{$n2['nombre']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- FOOTER -->
                                        <div class="modal-footer border-0 px-4 pb-4">
                                            <button type="button" class="btn btn-light border rounded-3 px-4" data-bs-dismiss="modal">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-dark rounded-3 px-4">
                                                <i class="bi bi-check-circle"></i> Actualizar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted" id="filaVaciaGenerica">
                                <i class="bi bi-search fs-3 d-block mb-2"></i>
                                No hay estudiantes registrados en la base de datos.
                            </td>
                        </tr>
                    <?php } ?>
                    <!-- Fila dinámica si la búsqueda no arroja resultados en vivo -->
                    <tr id="filaSinResultados" style="display: none;">
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-search fs-3 d-block mb-2"></i>
                            No se encontraron resultados que coincidan con la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SCRIPT DE BÚSQUEDA EN TIEMPO REAL -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const inputBusqueda = document.getElementById("inputBusquedaEnVivo");
    const btnLimpiar = document.getElementById("btnLimpiarBusqueda");
    const filas = document.querySelectorAll("#tablaEstudiantes tbody tr:not(#filaSinResultados)");
    const filaSinResultados = document.getElementById("filaSinResultados");

    inputBusqueda.addEventListener("keyup", function() {
        let textoBusqueda = inputBusqueda.value.toLowerCase().trim();
        let contadorVisibles = 0;

        filas.forEach(fila => {
            let contenidoFila = fila.textContent.toLowerCase();
            if (contenidoFila.includes(textoBusqueda)) {
                fila.style.display = "";
                contadorVisibles++;
            } else {
                fila.style.display = "none";
            }
        });

        // Mostrar u ocultar mensaje de "sin resultados"
        if (contadorVisibles === 0 && filas.length > 0) {
            filaSinResultados.style.display = "";
        } else {
            filaSinResultados.style.display = "none";
        }
    });

    btnLimpiar.addEventListener("click", function() {
        inputBusqueda.value = "";
        filas.forEach(fila => fila.style.display = "");
        filaSinResultados.style.display = "none";
        inputBusqueda.focus();
    });
});
</script>

<!-- AUTO CERRAR ALERTAS -->
<script>
setTimeout(() => {
    let alerta = document.querySelector('.alert');
    if(alerta){
        let bsAlert = new bootstrap.Alert(alerta);
        bsAlert.close();
    }
}, 5000);
</script>