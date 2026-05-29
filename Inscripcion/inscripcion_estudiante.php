```php
<?php
include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// =========================
// GUARDAR ESTUDIANTE
// =========================
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['accion'])
    && $_POST['accion'] === 'guardar'
) {

    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $id_estudiante = trim($_POST['id_estudiante']);
    $correo = trim($_POST['correo']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);

    $grado = intval($_POST['grado']);
    $nivel = intval($_POST['nivel']);

    $responsable_id = intval($_POST['responsable_id']);

    // VALIDAR RESPONSABLE
    if (empty($responsable_id)) {

        $mensaje = "⚠️ Debe seleccionar un Padre, Madre o Tutor.";
        $tipo_mensaje = "warning";

    } else {

        // VALIDAR ID DUPLICADO
        $sql = "
            SELECT COUNT(*) AS total
            FROM estudiantes
            WHERE ID = ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "s",
            $id_estudiante
        );

        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        if ($result['total'] > 0) {

            $mensaje = "⚠️ Ya existe un estudiante con ese ID.";
            $tipo_mensaje = "warning";

        } else {

            // INSERTAR ESTUDIANTE
            $stmt = $conn->prepare("
                INSERT INTO estudiantes
                (
                    nombre,
                    apellido,
                    ID,
                    correo,
                    fecha_nacimiento,
                    direccion,
                    telefono,
                    grado_id,
                    nivel_id,
                    responsable_id
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "sssssssiii",
                $nombre,
                $apellido,
                $id_estudiante,
                $correo,
                $fecha_nacimiento,
                $direccion,
                $telefono,
                $grado,
                $nivel,
                $responsable_id
            );

            if ($stmt->execute()) {

                $mensaje = "✅ Estudiante inscrito correctamente.";
                $tipo_mensaje = "success";

            } else {

                $mensaje = "❌ Error: " . $stmt->error;
                $tipo_mensaje = "danger";
            }
        }
    }
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
// OBTENER RESPONSABLES
// =========================
$responsables = $conn->query("
    SELECT *
    FROM responsables
    ORDER BY nombre ASC
");
?>

<div class="container mt-4">

    <!-- TITULO -->
    <h3 class="mb-4 text-primary fw-bold">

        <i class="bi bi-person-plus-fill"></i>
        Inscripción de Estudiantes

    </h3>

    <!-- ALERTAS -->
    <?php if (!empty($mensaje)) { ?>

        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show border-0 shadow rounded-4 d-flex align-items-center gap-3 p-3 mb-4">

            <i class="bi bi-bell-fill fs-4"></i>

            <div class="fw-semibold">
                <?php echo $mensaje; ?>
            </div>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php } ?>

    <!-- FORMULARIO -->
    <div class="card border-0 shadow-lg rounded-4 p-4">

        <h5 class="mb-4 fw-semibold">

            <i class="bi bi-person-vcard-fill"></i>
            Registrar Nuevo Estudiante

        </h5>

        <form method="POST" class="row g-3">

            <input type="hidden"
                   name="accion"
                   value="guardar">

            <!-- NOMBRE -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Nombre
                </label>

                <input type="text"
                       name="nombre"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- APELLIDO -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Apellido
                </label>

                <input type="text"
                       name="apellido"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- ID -->
            <div class="col-md-4">

                <label class="form-label fw-semibold">
                    ID
                </label>

                <input type="text"
                       name="id_estudiante"
                       class="form-control rounded-3"
                       placeholder="Ingrese el ID"
                       required>

            </div>

            <!-- CORREO -->
            <div class="col-md-4">

                <label class="form-label fw-semibold">
                    Correo
                </label>

                <input type="email"
                       name="correo"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- FECHA -->
            <div class="col-md-4">

                <label class="form-label fw-semibold">
                    Fecha nacimiento
                </label>

                <input type="date"
                       name="fecha_nacimiento"
                       class="form-control rounded-3"
                       required>

            </div>

            <!-- DIRECCION -->
            <div class="col-12">

                <label class="form-label fw-semibold">
                    Dirección
                </label>

                <input type="text"
                       name="direccion"
                       class="form-control rounded-3">

            </div>

            <!-- TELEFONO -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Teléfono
                </label>

                <input type="text"
                       name="telefono"
                       class="form-control rounded-3">

            </div>

            <!-- GRADO -->
            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Grado
                </label>

                <select name="grado"
                        class="form-select rounded-3"
                        required>

                    <option value="">
                        Seleccione
                    </option>

                    <?php while($g = $grados->fetch_assoc()) { ?>

                        <option value="<?php echo $g['id']; ?>">

                            <?php
                            echo htmlspecialchars(
                                $g['grado'] . ' ' . $g['seccion']
                            );
                            ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <!-- NIVEL -->
            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Nivel
                </label>

                <select name="nivel"
                        class="form-select rounded-3"
                        required>

                    <option value="">
                        Seleccione
                    </option>

                    <?php while($n = $niveles->fetch_assoc()) { ?>

                        <option value="<?php echo $n['id']; ?>">

                            <?php echo htmlspecialchars($n['nombre']); ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <!-- RESPONSABLE -->
            <div class="col-12 mt-4">

                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-header bg-dark text-white rounded-top-4 py-3">

                        <h5 class="mb-0 fw-semibold">

                            <i class="bi bi-people-fill"></i>
                            Padre / Madre / Tutor

                        </h5>

                    </div>

                    <div class="card-body p-4">

                        <!-- OPCIONES -->
                        <div class="mb-4">

                            <label class="form-label fw-bold">

                                Seleccione el responsable

                            </label>

                            <div class="row g-3 mt-1">

                                <!-- PADRE -->
                                <div class="col-md-4">

                                    <input type="radio"
                                           class="btn-check"
                                           name="tipo_responsable"
                                           id="padre"
                                           value="Padre"
                                           autocomplete="off">

                                    <label class="btn btn-outline-primary w-100 rounded-4 p-3 shadow-sm"
                                           for="padre">

                                        <i class="bi bi-person-fill fs-3 d-block mb-2"></i>

                                        <span class="fw-semibold">
                                            Padre
                                        </span>

                                    </label>

                                </div>

                                <!-- MADRE -->
                                <div class="col-md-4">

                                    <input type="radio"
                                           class="btn-check"
                                           name="tipo_responsable"
                                           id="madre"
                                           value="Madre"
                                           autocomplete="off">

                                    <label class="btn btn-outline-danger w-100 rounded-4 p-3 shadow-sm"
                                           for="madre">

                                        <i class="bi bi-person-heart fs-3 d-block mb-2"></i>

                                        <span class="fw-semibold">
                                            Madre
                                        </span>

                                    </label>

                                </div>

                                <!-- TUTOR -->
                                <div class="col-md-4">

                                    <input type="radio"
                                           class="btn-check"
                                           name="tipo_responsable"
                                           id="tutor"
                                           value="Tutor"
                                           autocomplete="off">

                                    <label class="btn btn-outline-success w-100 rounded-4 p-3 shadow-sm"
                                           for="tutor">

                                        <i class="bi bi-person-workspace fs-3 d-block mb-2"></i>

                                        <span class="fw-semibold">
                                            Tutor
                                        </span>

                                    </label>

                                </div>

                            </div>

                        </div>

                        <!-- SELECT RESPONSABLE -->
                        <div id="contenedorResponsable"
                             style="display:none;">

                            <label class="form-label fw-semibold">

                                Seleccione el responsable registrado

                            </label>

                            <select name="responsable_id"
                                    id="responsable_id"
                                    class="form-select rounded-3">

                                <option value="">
                                    Seleccione un responsable
                                </option>

                                <?php while($r = $responsables->fetch_assoc()) { ?>

                                    <option value="<?php echo $r['id']; ?>">

                                        <?php
                                        echo $r['tipo']
                                           . " - "
                                           . $r['nombre']
                                           . " | ID: "
                                           . $r['id_responsable'];
                                        ?>

                                    </option>

                                <?php } ?>

                            </select>

                            <div class="form-text mt-2">

                                Si el responsable no existe,
                                primero debe registrarlo
                                en el módulo de responsables.

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- BOTON -->
            <div class="col-12 text-end mt-4">

                <button type="submit"
                        class="btn btn-success rounded-3 px-4 py-2">

                    <i class="bi bi-save"></i>
                    Guardar inscripción

                </button>

            </div>

        </form>

    </div>

</div>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

const radios =
    document.querySelectorAll('input[name="tipo_responsable"]');

const contenedor =
    document.getElementById('contenedorResponsable');

radios.forEach(radio => {

    radio.addEventListener('change', function(){

        contenedor.style.display = 'block';

        let tipoSeleccionado = this.value;

        let select =
            document.getElementById('responsable_id');

        for(let i = 0; i < select.options.length; i++) {

            let texto =
                select.options[i].text;

            if(texto.startsWith(tipoSeleccionado)) {

                select.options[i].style.display = '';

            } else if(i !== 0) {

                select.options[i].style.display = 'none';
            }

        }

        select.value = "";

    });

});

// VALIDAR
document.querySelector('form').addEventListener('submit', function(e){

    const tipo =
        document.querySelector('input[name="tipo_responsable"]:checked');

    const responsable =
        document.getElementById('responsable_id').value;

    if(!tipo){

        e.preventDefault();

        alert(
            'Debe seleccionar Padre, Madre o Tutor.'
        );

        return;
    }

    if(responsable === ""){

        e.preventDefault();

        alert(
            'Debe seleccionar un responsable.'
        );

    }

});

</script>
```
