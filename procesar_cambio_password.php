<?php
session_start();
include("conexion/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo_recuperacion'] ?? '';
    $nueva_contraseña = $_POST['nueva_contraseña'] ?? '';

    if (!empty($correo) && !empty($nueva_contraseña)) {
        // Verificar si el correo existe en la base de datos
        $sql_verificar = "SELECT id FROM usuarios WHERE correo = ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("s", $correo);
        $stmt_verificar->execute();
        $resultado = $stmt_verificar->get_result();

        if ($resultado->num_rows > 0) {
            // Actualizar la contraseña (en texto plano como lo tienes configurado)
            $sql_actualizar = "UPDATE usuarios SET contraseña = ? WHERE correo = ?";
            $stmt_actualizar = $conn->prepare($sql_actualizar);
            $stmt_actualizar->bind_param("ss", $nueva_contraseña, $correo);

            if ($stmt_actualizar->execute()) {
                // Redirigir al login con un parámetro de éxito (opcional) o mensaje
                header("Location: login.php?exito=password");
                exit();
            } else {
                echo "<script>alert('Error al actualizar la contraseña.'); window.location='login.php';</script>";
            }
        } else {
            echo "<script>alert('El correo electrónico no está registrado.'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Por favor complete todos los campos.'); window.location='login.php';</script>";
    }
} else {
    header("Location: login.php");
    exit();
}
?>