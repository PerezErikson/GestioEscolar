<?php
session_start();
include("conexion/conexion.php");

$correo = $_POST['correo'];
$contraseña = $_POST['contraseña'];

$sql = "SELECT * FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    // Comparación directa (texto plano)
    if ($contraseña === $usuario['contraseña']) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['rol_id'] = $usuario['rol_id'];
        $_SESSION['nombre'] = $usuario['nombre'];

        header("Location: principal.php");
    } else {
        echo "<script>alert('Contraseña incorrecta'); window.location='login.php';</script>";
    }
} else {
    echo "<script>alert('Usuario no encontrado'); window.location='login.php';</script>";
}
?>
