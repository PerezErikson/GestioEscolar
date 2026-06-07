<?php
include("conexion/conexion.php");

$mensaje = "";
$tipo_mensaje = "";

/* GUARDAR */

if(isset($_POST['guardar'])){

    $grado_id = intval($_POST['grado_id']);

    if(isset($_POST['materias'])){

        $conn->query("
            DELETE FROM asignacion_materias
            WHERE grado_id = $grado_id
        ");

        foreach($_POST['materias'] as $materia_id){

            $materia_id = intval($materia_id);

            $stmt = $conn->prepare("
                INSERT INTO asignacion_materias
                (grado_id,materia_id)
                VALUES (?,?)
            ");

            $stmt->bind_param(
                "ii",
                $grado_id,
                $materia_id
            );

            $stmt->execute();
        }

        $mensaje = "✅ Materias asignadas correctamente.";
        $tipo_mensaje = "success";
    }
}

/* GRADOS */

$grados = $conn->query("
    SELECT
        g.id,
        CONCAT(g.nombre,' ',s.nombre) AS grado
    FROM grados1 g
    INNER JOIN secciones s
        ON g.id_seccion=s.id
");

/* MATERIAS */

$materias = $conn->query("
    SELECT *
    FROM materias
    ORDER BY nombre
");

/* MATERIAS YA ASIGNADAS */

$asignadas = [];

if(isset($_GET['grado_id'])){

    $grado_id = intval($_GET['grado_id']);

    $consulta = $conn->query("
        SELECT materia_id
        FROM asignacion_materias
        WHERE grado_id = $grado_id
    ");

    while($fila = $consulta->fetch_assoc()){

        $asignadas[] = $fila['materia_id'];
    }
}
?>

<div class="container mt-4">

    <h3 class="text-primary fw-bold">
        <i class="bi bi-book-half"></i>
        Asignar Materias
    </h3>

    <?php if($mensaje){ ?>

        <div class="alert alert-<?php echo $tipo_mensaje; ?>">
            <?php echo $mensaje; ?>
        </div>

    <?php } ?>

    <div class="card shadow border-0 rounded-4 p-4">

        <form method="GET"
              action="principal.php">

            <input type="hidden"
                   name="seccion"
                   value="asignar_materias">

            <div class="row">

                <div class="col-md-8">

                    <label class="form-label">
                        Seleccionar Curso
                    </label>

                    <select name="grado_id"
                            class="form-select"
                            required>

                        <option value="">
                            Seleccione
                        </option>

                        <?php while($g=$grados->fetch_assoc()){ ?>

                            <option
                            value="<?php echo $g['id']; ?>"

                            <?php
                            if(
                                isset($grado_id)
                                &&
                                $grado_id==$g['id']
                            ){
                                echo "selected";
                            }
                            ?>>

                            <?php echo $g['grado']; ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="col-md-4 d-flex align-items-end">

                    <button class="btn btn-primary w-100">

                        Ver Materias

                    </button>

                </div>

            </div>

        </form>

    </div>

<?php if(isset($grado_id)){ ?>

<div class="card shadow border-0 rounded-4 mt-4 p-4">

<form method="POST">

    <input type="hidden"
           name="grado_id"
           value="<?php echo $grado_id; ?>">

    <h5 class="mb-4">

        Materias del curso

    </h5>

    <div class="row">

    <?php

    $materias->data_seek(0);

    while($m = $materias->fetch_assoc()){

    ?>

        <div class="col-md-4 mb-3">

            <div class="form-check">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="materias[]"
                    value="<?php echo $m['id']; ?>"

                    <?php
                    if(
                        in_array(
                            $m['id'],
                            $asignadas
                        )
                    ){
                        echo "checked";
                    }
                    ?>

                >

                <label class="form-check-label">

                    <?php echo $m['nombre']; ?>

                </label>

            </div>

        </div>

    <?php } ?>

    </div>

    <button
        class="btn btn-success mt-3"
        name="guardar">

        <i class="bi bi-save"></i>

        Guardar Asignación

    </button>

</form>

</div>

<?php } ?>

</div>