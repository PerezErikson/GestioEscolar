<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../conexion/conexion.php');

$mi_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol_id'];

// ADMINISTRADOR
if ($rol == 1) {

    $sql = "
        SELECT *
        FROM usuarios
        WHERE id != '$mi_id'
        ORDER BY nombre
    ";

}
// DOCENTE
elseif ($rol == 2) {

    $sql = "
        SELECT *
        FROM usuarios
        WHERE rol_id IN (1,3)
        AND id != '$mi_id'
        ORDER BY nombre
    ";

}
// ESTUDIANTE
else {

    $sql = "
        SELECT *
        FROM usuarios
        WHERE rol_id IN (1,2)
        AND id != '$mi_id'
        ORDER BY nombre
    ";
}
$resultado = $conn->query($sql);

while ($usuario = $resultado->fetch_assoc()) {
$no_leidos = $conn->query("
    SELECT COUNT(*) total
    FROM mensajes
    WHERE receptor_id = '$mi_id'
    AND emisor_id = '".$usuario['id']."'
    AND leido = 0
")->fetch_assoc()['total'];
    $activo = "";

    if (
        isset($_GET['usuario']) &&
        $_GET['usuario'] == $usuario['id']
    ) {
        $activo = "active";
    }
$estado = "⚫ Desconectado";

if (!empty($usuario['ultima_actividad'])) {

    $ultima = strtotime($usuario['ultima_actividad']);

    if ((time() - $ultima) <= 300) {
        $estado = "🟢 En línea";
    }

}
echo '
<a href="principal.php?seccion=chat&usuario='.$usuario['id'].'"
   class="list-group-item list-group-item-action '.$activo.'">

    <div class="fw-bold">
        '.$usuario['nombre'].'
    </div>

    <small class="text-success">
        '.$estado.'
    </small>

</a>';
}
?>