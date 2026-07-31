<?php
// ==========================================
// CONEXIÓN A LA BASE DE DATOS
// ==========================================
include(__DIR__ . "/../conexion/conexion.php");

// ==========================================
// OBTENER RESPONSABLES
// ==========================================
$responsables = $conn->query("
    SELECT * 
    FROM responsables
    ORDER BY nombre ASC
");
?>

<div class="container mt-4">

    <!-- ========================================== -->
    <!-- TÍTULO -->
    <!-- ========================================== -->
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-people-fill"></i>
        Gestión de Responsables
    </h3>

    <!-- ========================================== -->
    <!-- CARD PRINCIPAL -->
    <!-- ========================================== -->
    <div class="card border-0 shadow-lg rounded-4 p-4">

        <!-- ========================================== -->
        <!-- BUSCADOR -->
        <!-- ========================================== -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-primary text-white border-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text"
                           id="buscadorResponsable"
                           class="form-control border-0 shadow-sm"
                           placeholder="Buscar responsable por nombre, cédula o teléfono...">
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TABLA -->
        <!-- ========================================== -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <!-- CABECERA -->
                <thead class="table-dark">
                    <tr>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Hijos</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <!-- CUERPO -->
                <tbody id="tablaResponsables">
                    <?php while($r = $responsables->fetch_assoc()) { ?>
                        <?php
                        // ==========================================
                        // ID DEL RESPONSABLE
                        // ==========================================
                        $id_responsable = $r['id'];

                        // ==========================================
                        // CONSULTAR HIJOS
                        // ==========================================
                        $hijos = $conn->query("
                            SELECT 
                                e.nombre,
                                e.apellido,
                                e.ID,
                                CONCAT(g.nombre, ' ', s.nombre) AS grado,
                                n.nombre AS nivel
                            FROM estudiantes e
                            INNER JOIN grados1 g 
                                ON e.grado_id = g.id
                            INNER JOIN secciones s 
                                ON g.id_seccion = s.id
                            INNER JOIN niveles n 
                                ON e.nivel_id = n.id
                            WHERE e.responsable_id = $id_responsable
                        ");
                        
                        // ==========================================
                        // CONTAR HIJOS
                        // ==========================================
                        $cantidad_hijos = $hijos->num_rows;
                        ?>

                        <!-- ========================================== -->
                        <!-- FILA RESPONSABLE -->
                        <!-- ========================================== -->
                        <tr>
                            <!-- TIPO -->
                            <td>
                                <?php
                                if($r['tipo'] == 'Padre'){
                                    echo '
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">
                                        Padre
                                    </span>';
                                } elseif($r['tipo'] == 'Madre'){
                                    echo '
                                    <span class="badge bg-danger px-3 py-2 rounded-pill">
                                        Madre
                                    </span>';
                                } else {
                                    echo '
                                    <span class="badge bg-success px-3 py-2 rounded-pill">
                                        Tutor
                                    </span>';
                                }
                                ?>
                            </td>
                            <!-- NOMBRE -->
                            <td class="fw-semibold">
                                <?php echo htmlspecialchars($r['nombre']); ?>
                            </td>
                            <!-- CÉDULA -->
                            <td>
                                <?php echo htmlspecialchars($r['id_responsable']); ?>
                            </td>
                            <!-- TELÉFONO -->
                            <td>
                                <?php echo htmlspecialchars($r['telefono']); ?>
                            </td>
                            <!-- DIRECCIÓN -->
                            <td>
                                <?php echo htmlspecialchars($r['direccion']); ?>
                            </td>
                            <!-- CANTIDAD HIJOS -->
                            <td>
                                <span class="badge bg-dark rounded-pill px-3 py-2">
                                    <?php echo $cantidad_hijos; ?>
                                </span>
                            </td>
                            <!-- BOTÓN -->
                            <td>
                                <button class="btn btn-outline-primary btn-sm rounded-pill"
                                        type="button"
                                        onclick="toggleHijos(<?php echo $r['id']; ?>, this)">
                                    <i class="bi bi-eye"></i>
                                    Ver Hijos
                                </button>
                            </td>
                        </tr>

                        <!-- ========================================== -->
                        <!-- FILA OCULTA -->
                        <!-- ========================================== -->
                        <tr class="collapse" id="hijos<?php echo $r['id']; ?>">
                            <td colspan="7">
                                <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
                                    <h6 class="fw-bold mb-3 text-secondary">
                                        Hijos registrados
                                    </h6>
                                    <!-- SI TIENE HIJOS -->
                                    <?php if($cantidad_hijos > 0) { ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>ID</th>
                                                        <th>Grado</th>
                                                        <th>Nivel</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while($h = $hijos->fetch_assoc()) { ?>
                                                        <tr>
                                                            <!-- NOMBRE -->
                                                            <td class="fw-semibold">
                                                                <?php
                                                                echo htmlspecialchars(
                                                                    $h['nombre'] . " " . $h['apellido']
                                                                );
                                                                ?>
                                                            </td>
                                                            <!-- ID -->
                                                            <td>
                                                                <?php
                                                                echo htmlspecialchars($h['ID']);
                                                                ?>
                                                            </td>
                                                            <!-- GRADO -->
                                                            <td>
                                                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                                                    <?php
                                                                    echo htmlspecialchars($h['grado']);
                                                                    ?>
                                                                </span>
                                                            </td>
                                                            <!-- NIVEL -->
                                                            <td>
                                                                <span class="badge bg-dark rounded-pill px-3 py-2">
                                                                    <?php
                                                                    echo htmlspecialchars($h['nivel']);
                                                                    ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } else { ?>
                                        <!-- MENSAJE SIN HIJOS -->
                                        <div class="alert alert-warning mb-0">
                                            Este responsable no tiene hijos registrados.
                                        </div>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- BOOTSTRAP -->
<!-- ========================================== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ========================================== -->
<!-- FILTRO DE BÚSQUEDA Y TOGGLE -->
<!-- ========================================== -->
<script>
// ==========================================
// BUSCADOR RESPONSABLES
// ==========================================
document.getElementById('buscadorResponsable').addEventListener('keyup', function () {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll('#tablaResponsables tr');

    filas.forEach(function (fila) {
        // Ignorar filas de hijos
        if (fila.id && fila.id.startsWith('hijos')) {
            return;
        }

        let texto = fila.innerText.toLowerCase();

        if (texto.includes(filtro)) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';

            // Ocultar también la fila de hijos asociada
            let siguiente = fila.nextElementSibling;
            if (siguiente && siguiente.id && siguiente.id.startsWith('hijos')) {
                siguiente.style.display = 'none';
            }
        }
    });
});

// ==========================================
// ABRIR / CERRAR HIJOS
// ==========================================
function toggleHijos(id, boton) {
    let fila = document.getElementById('hijos' + id);
    if (!fila) return;

    if (fila.style.display === 'none' || fila.style.display === '') {
        fila.style.display = 'table-row';
        boton.innerHTML = '<i class="bi bi-eye-slash"></i> Ocultar Hijos';
    } else {
        fila.style.display = 'none';
        boton.innerHTML = '<i class="bi bi-eye"></i> Ver Hijos';
    }
}
</script>