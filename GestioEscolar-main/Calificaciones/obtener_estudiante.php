<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

include("../conexion/conexion.php");

$estudiante_id = $_GET['estudiante_id'] ?? '';

if (!empty($estudiante_id)) {
    // Consulta optimizada para traer los datos del estudiante y su grado asignado
    $query = "
        SELECT 
            e.nombre, 
            e.apellido, 
            e.ID, 
            IFNULL(g.nombre, 'No Asignado') AS grado
        FROM estudiantes e
        LEFT JOIN grados1 g ON e.grado_id = g.id
        WHERE e.numero = ?
    ";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $estudiante_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        
        if ($resultado) {
            echo json_encode(['status' => 'success', 'data' => $resultado]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Estudiante no encontrado.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID vacío.']);
}
$conn->close();
exit;