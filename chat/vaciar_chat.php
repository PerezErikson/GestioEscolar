<?php
session_start();

include(__DIR__ . '/../conexion/conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    exit("Sesión no válida");
}

$yo = intval($_SESSION['usuario_id']);
$otro = intval($_POST['receptor']);

$sql = "
UPDATE mensajes
SET
    oculto_emisor = CASE
        WHEN emisor_id = $yo THEN 1
        ELSE oculto_emisor
    END,

    oculto_receptor = CASE
        WHEN receptor_id = $yo THEN 1
        ELSE oculto_receptor
    END

WHERE
(
    emisor_id = $yo
    AND receptor_id = $otro
)
OR
(
    emisor_id = $otro
    AND receptor_id = $yo
)
";

if ($conn->query($sql)) {
    echo "ok";
} else {
    echo $conn->error;
}
?>