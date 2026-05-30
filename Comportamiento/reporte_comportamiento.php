<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conexion/conexion.php");

$rol = $_SESSION['rol_id'];
$nombre_estudiante = $_SESSION['nombre'];

$reporte = [];

// =========================
// ESTUDIANTE
// =========================
if ($rol == 3) {

    $sql = "SELECT
                e.nombre,
                e.apellido,
                c.nota,
                c.observacion,
                c.fecha
            FROM comportamiento c
            INNER JOIN estudiantes e
                ON c.estudiante_id = e.numero
            WHERE e.nombre = ?
            ORDER BY c.fecha DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre_estudiante);
    $stmt->execute();

    $reporte = $stmt->get_result();

} else {

    // =========================
    // ADMIN Y DOCENTE
    // =========================
    $grados = $conn->query("
        SELECT
            g.id,
            CONCAT(g.nombre,' ',s.nombre) AS grado
        FROM grados1 g
        INNER JOIN secciones s
            ON g.id_seccion = s.id
        ORDER BY g.nombre ASC, s.nombre ASC
    ");

    if (isset($_GET['grado_id']) && isset($_GET['fecha'])) {

        $grado_id = intval($_GET['grado_id']);
        $fecha = $_GET['fecha'];

        $sql = "SELECT
                    e.nombre,
                    e.apellido,
                    c.nota,
                    c.observacion
                FROM comportamiento c
                INNER JOIN estudiantes e
                    ON c.estudiante_id = e.numero
                WHERE c.grado_id = ?
                AND c.fecha = ?
                ORDER BY e.apellido ASC, e.nombre ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $grado_id, $fecha);
        $stmt->execute();

        $reporte = $stmt->get_result();
    }
}
?>

<div class="container mt-4">

    <!-- TITULO -->
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-clipboard-check"></i>
        Reporte de Comportamiento
    </h3>

    <?php if ($rol != 3) { ?>

    <!-- FILTROS -->
    <div class="card border-0 shadow-lg rounded-4 p-4 mb-4">

        <form method="GET"
              action="principal.php"
              class="row g-3">

            <input type="hidden"
                   name="seccion"
                   value="reporte_comportamiento">

            <!-- GRADO -->
            <div class="col-md-5">

                <label class="form-label fw-semibold">
                    Seleccionar grado
                </label>

                <select name="grado_id"
                        class="form-select rounded-3"
                        required>

                    <option value="">-- Seleccione --</option>

                    <?php while($g = $grados->fetch_assoc()) { ?>

                        <option value="<?php echo $g['id']; ?>"
                            <?php if(isset($grado_id) && $grado_id == $g['id']) echo "selected"; ?>>

                            <?php echo htmlspecialchars($g['grado']); ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <!-- FECHA -->
            <div class="col-md-4">

                <label class="form-label fw-semibold">
                    Fecha
                </label>

                <input type="date"
                       name="fecha"
                       class="form-control rounded-3"
                       value="<?php echo isset($fecha) ? $fecha : ''; ?>"
                       required>

            </div>

            <!-- BOTON -->
            <div class="col-md-3 d-flex align-items-end">

                <button type="submit"
                        class="btn btn-primary w-100 rounded-3 fw-semibold">

                    Ver reporte

                </button>

            </div>

        </form>

    </div>

    <?php } ?>

    <!-- RESULTADOS -->
    <?php if (!empty($reporte) && $reporte->num_rows > 0) { ?>

    <div class="card border-0 shadow-lg rounded-4 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h5 class="fw-semibold mb-0">

                <?php
                    echo ($rol == 3)
                    ? "<i class='bi bi-person'></i> Tu historial de comportamiento"
                    : "<i class='bi bi-people'></i> Comportamiento del grado seleccionado";
                ?>

            </h5>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead style="background: #1f2937; color: white;">

                    <tr>

                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Nota</th>
                        <th>Observación</th>

                        <?php if ($rol == 3) { ?>

                            <th>Fecha</th>

                        <?php } ?>

                    </tr>

                </thead>

                <tbody>

                    <?php while($row = $reporte->fetch_assoc()) { ?>

                    <tr>

                        <td class="fw-semibold">
                            <?php echo htmlspecialchars($row['nombre']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['apellido']); ?>
                        </td>

                        <td>

                            <?php

                            if ($row['nota'] == 'Excelente') {

                                echo "<span class='badge bg-success rounded-pill px-3 py-2'>🟢 Excelente</span>";

                            } elseif ($row['nota'] == 'Bueno') {

                                echo "<span class='badge bg-primary rounded-pill px-3 py-2'>🔵 Bueno</span>";

                            } elseif ($row['nota'] == 'Regular') {

                                echo "<span class='badge bg-warning text-dark rounded-pill px-3 py-2'>🟡 Regular</span>";

                            } else {

                                echo "<span class='badge bg-danger rounded-pill px-3 py-2'>🔴 Deficiente</span>";
                            }

                            ?>

                        </td>

                        <td>

                            <?php
                                echo !empty($row['observacion'])
                                ? htmlspecialchars($row['observacion'])
                                : "<span class='text-muted'>Sin observación</span>";
                            ?>

                        </td>

                        <?php if ($rol == 3) { ?>

                        <td>

                            <span class="badge bg-light text-dark border rounded-pill px-3 py-2">

                                <?php echo htmlspecialchars($row['fecha']); ?>

                            </span>

                        </td>

                        <?php } ?>

                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

    <?php } elseif (($rol != 3 && isset($grado_id) && isset($fecha)) || $rol == 3) { ?>

        <!-- ALERTA BONITA -->
        <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-4">

            <i class="bi bi-exclamation-triangle-fill fs-2"></i>

            <div>

                <h6 class="fw-bold mb-1">
                    No hay registros
                </h6>

                <span>
                    ⚠️ No existen registros de comportamiento para la búsqueda realizada.
                </span>

            </div>

        </div>

    <?php } ?>

</div>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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