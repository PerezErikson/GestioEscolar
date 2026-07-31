<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . "/../conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

// ==========================================
// GUARDAR RESPONSABLE
// ==========================================
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['accion'])
    && $_POST['accion'] === 'guardar'
) {

    $tipo = trim($_POST['tipo']);
    $nombre = trim($_POST['nombre']);
    $id_responsable = trim($_POST['id_responsable']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $nacionalidad = trim($_POST['nacionalidad']);
    $ocupacion = trim($_POST['ocupacion']); // <-- Nueva variable
    $estado_civil = trim($_POST['estado_civil']);
    $nivel_academico = trim($_POST['nivel_academico']);

    // ==========================================
    // VALIDAR DUPLICADO
    // ==========================================
    $check = $conn->prepare("
        SELECT id
        FROM responsables
        WHERE id_responsable = ?
    ");

    $check->bind_param("s", $id_responsable);
    $check->execute();

    $resultado = $check->get_result();

    if ($resultado->num_rows > 0) {

        $mensaje = "⚠️ Ya existe un responsable con esa cédula.";
        $tipo_mensaje = "warning";

    } else {

        // ==========================================
        // INSERTAR RESPONSABLE
        // ==========================================
        $stmt = $conn->prepare("
            INSERT INTO responsables
            (
                tipo,
                nombre,
                id_responsable,
                telefono,
                direccion,
                tipo_responsable,
                nacionalidad,
                ocupacion, -- <-- Nuevo campo
                estado_civil,
                nivel_academico
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $tipo_responsable = $tipo;

        // Se ajustó a 10 "s" y se agregó $ocupacion
        $stmt->bind_param(
            "ssssssssss",
            $tipo,
            $nombre,
            $id_responsable,
            $telefono,
            $direccion,
            $tipo_responsable,
            $nacionalidad,
            $ocupacion,
            $estado_civil,
            $nivel_academico
        );

        if ($stmt->execute()) {

            $mensaje = "✅ Responsable registrado correctamente.";
            $tipo_mensaje = "success";

        } else {

            $mensaje = "❌ Error al registrar: " . $stmt->error;
            $tipo_mensaje = "danger";
        }

        $stmt->close();
    }

    $check->close();
}
?>

<div class="container mt-4">

    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-people-fill"></i>
        Registro de Padres / Madres / Tutores
    </h3>

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

    <div class="card border-0 shadow-lg rounded-4 p-4 mb-4">

        <h5 class="mb-4 fw-semibold">
            <i class="bi bi-person-vcard-fill"></i>
            Registrar Responsable
        </h5>

        <form method="POST" class="row g-3">

            <input type="hidden"
                   name="accion"
                   value="guardar">

            <div class="col-12">

                <label class="form-label fw-bold">
                    Tipo de Responsable
                </label>

                <div class="row g-3 mt-1">

                    <div class="col-md-4">

                        <input type="radio"
                               class="btn-check"
                               name="tipo"
                               id="padre"
                               value="Padre"
                               autocomplete="off"
                               required>

                        <label class="btn btn-outline-primary w-100 rounded-4 p-3 shadow-sm"
                               for="padre">

                            <i class="bi bi-person-fill fs-3 d-block mb-2"></i>

                            <span class="fw-semibold">
                                Padre
                            </span>

                        </label>

                    </div>

                    <div class="col-md-4">

                        <input type="radio"
                               class="btn-check"
                               name="tipo"
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

                    <div class="col-md-4">

                        <input type="radio"
                               class="btn-check"
                               name="tipo"
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

            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Nombre Completo
                </label>

                <input type="text"
                       name="nombre"
                       class="form-control rounded-3"
                       required>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Cédula
                </label>

                <input type="text"
                       name="id_responsable"
                       id="cedula"
                       class="form-control rounded-3"
                       maxlength="13"
                       placeholder="000-0000000-0"
                       inputmode="numeric"
                       required>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Teléfono
                </label>

                <input type="text"
                       name="telefono"
                       class="form-control rounded-3"
                       required>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Dirección
                </label>

                <input type="text"
                       name="direccion"
                       class="form-control rounded-3"
                       required>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Nacionalidad
                </label>

                <input type="text"
                       name="nacionalidad"
                       class="form-control rounded-3"
                       required>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Ocupación
                </label>

                <input type="text"
                       name="ocupacion"
                       class="form-control rounded-3"
                       required>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Estado Civil
                </label>

                <select name="estado_civil"
                        class="form-select rounded-3"
                        required>

                    <option value="">Seleccione</option>
                    <option value="Soltero">Soltero</option>
                    <option value="Casado">Casado</option>
                    <option value="Divorciado">Divorciado</option>
                    <option value="Viudo">Viudo</option>

                </select>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Nivel Académico
                </label>

                <select name="nivel_academico"
                        class="form-select rounded-3"
                        required>

                    <option value="">Seleccione</option>
                    <option value="Primaria">Primaria</option>
                    <option value="Secundaria">Secundaria</option>
                    <option value="Universitario">Universitario</option>

                </select>

            </div>

            <div class="col-12 text-end mt-4">

                <button type="submit"
                        class="btn btn-success rounded-3 px-4 py-2">

                    <i class="bi bi-save"></i>
                    Guardar Responsable

                </button>

            </div>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('cedula').addEventListener('input', function (e) {

    let valor = e.target.value.replace(/\D/g, '');

    // MAXIMO 11 NUMEROS
    valor = valor.substring(0, 11);

    // FORMATO 000-0000000-0
    if (valor.length > 3 && valor.length <= 10) {

        valor = valor.replace(/^(\d{3})(\d+)/, '$1-$2');

    } else if (valor.length > 10) {

        valor = valor.replace(/^(\d{3})(\d{7})(\d+)/, '$1-$2-$3');

    }

    e.target.value = valor;
});
</script>

<script>
setTimeout(() => {
    let alerta = document.querySelector('.alert');
    if(alerta){
        let bsAlert = new bootstrap.Alert(alerta);
        bsAlert.close();
    }
}, 5000);
</script>