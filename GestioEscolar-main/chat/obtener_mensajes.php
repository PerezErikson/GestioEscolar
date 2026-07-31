<?php
session_start();

include(__DIR__ . '/../conexion/conexion.php');

$yo = $_SESSION['usuario_id'];
$otro = intval($_GET['usuario']);

// Marcar como leídos
$conn->query("
    UPDATE mensajes
    SET leido = 1
    WHERE receptor_id = '$yo'
    AND emisor_id = '$otro'
");

$sql = "
SELECT *
FROM mensajes
WHERE
(
    emisor_id = '$yo'
    AND receptor_id = '$otro'
    AND oculto_emisor = 0
)
OR
(
    emisor_id = '$otro'
    AND receptor_id = '$yo'
    AND oculto_receptor = 0
)
ORDER BY fecha ASC
";

$resultado = $conn->query($sql);

/* CHAT VACÍO */

if ($resultado->num_rows == 0) {

    echo '
    <div class="h-100 d-flex flex-column justify-content-center align-items-center text-muted">

        <i class="bi bi-chat-square-text fs-1"></i>

        <h5 class="mt-3">
            No hay mensajes
        </h5>

        <small>
            Inicie una conversación enviando un mensaje.
        </small>

    </div>';

    exit;
}

while ($msg = $resultado->fetch_assoc()) {

    $hora = date('h:i A', strtotime($msg['fecha']));

    if ($msg['emisor_id'] == $yo) {

        echo '
        <div class="d-flex justify-content-end mb-3">

            <div>

                <div class="bg-primary text-white p-2 rounded shadow-sm"
                     style="max-width:350px;">

                    '.htmlspecialchars($msg['mensaje']).'

                </div>

                <div class="text-end">

                    <small class="text-muted">
                        '.$hora.'
                    </small>

                </div>

            </div>

        </div>';

    } else {

        echo '
        <div class="d-flex justify-content-start mb-3">

            <div>

                <div class="bg-light border p-2 rounded shadow-sm"
                     style="max-width:350px;">

                    '.htmlspecialchars($msg['mensaje']).'

                </div>

                <small class="text-muted">
                    '.$hora.'
                </small>

            </div>

        </div>';
    }
}
?>