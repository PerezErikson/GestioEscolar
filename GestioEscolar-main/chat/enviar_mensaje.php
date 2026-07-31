<?php
session_start();

include(__DIR__ . '/../conexion/conexion.php');

$emisor = $_SESSION['usuario_id'];
$receptor = intval($_POST['receptor']);
$mensaje = trim($_POST['mensaje']);

if ($mensaje != '') {

    $stmt = $conn->prepare("
        INSERT INTO mensajes
        (
            emisor_id,
            receptor_id,
            mensaje
        )
        VALUES
        (
            ?,
            ?,
            ?
        )
    ");

    $stmt->bind_param(
        "iis",
        $emisor,
        $receptor,
        $mensaje
    );

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "error";
    }
}
?>