<?php
include(__DIR__ . "/../conexion/conexion.php");

$response = ['existe' => false];

if (isset($_POST['id_estudiante'])) {
    $id_estudiante = trim($_POST['id_estudiante']);

    if (!empty($id_estudiante)) {
        // Consultar si ya existe el ID
        $sql = "SELECT COUNT(*) AS total FROM estudiantes WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id_estudiante);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['total'] > 0) {
            $response['existe'] = true;
        }
    }
}

// Retornar la respuesta en formato JSON para JavaScript
header('Content-Type: application/json');
echo json_encode($response);