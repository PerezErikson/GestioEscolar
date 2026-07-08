<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../conexion/conexion.php');

$usuario_chat = $_GET['usuario'] ?? 0;
$nombre_conversacion = "";

// Consulta segura para obtener el nombre del contacto seleccionado en la cabecera
if ($usuario_chat > 0) {
    $stmt = $conn->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_chat);
    $stmt->execute();
    $resultado_usuario = $stmt->get_result();
    if ($user_data = $resultado_usuario->fetch_assoc()) {
        $nombre_conversacion = $user_data['nombre'];
    }
    $stmt->close();
}
?>

<style>
    /* Scrollbar sutil y moderno para la caja de mensajes y contactos */
    #mensajes::-webkit-scrollbar,
    #accordionCursos::-webkit-scrollbar {
        width: 6px;
    }
    #mensajes::-webkit-scrollbar-track,
    #accordionCursos::-webkit-scrollbar-track {
        background: transparent;
    }
    #mensajes::-webkit-scrollbar-thumb,
    #accordionCursos::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.15);
        border-radius: 10px;
    }
    #mensajes::-webkit-scrollbar-thumb:hover,
    #accordionCursos::-webkit-scrollbar-thumb:hover {
        background-color: rgba(0, 0, 0, 0.25);
    }

    /* Enfoque del input sin destellos toscos */
    .input-institucional:focus {
        background-color: #ffffff !important;
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.12) !important;
    }

    /* Regla para evitar rupturas de texto en mensajes largos */
    .msg-break {
        word-break: break-word;
        white-space: pre-wrap;
    }
</style>

<div class="container-fluid py-3">
    <div class="row g-3">

        <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white py-3 border-0">
                    <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2" style="letter-spacing: 0.5px;">
                        <i class="bi bi-chat-square-text-fill text-primary"></i>
                        Bandeja Escolar
                    </h6>
                </div>
                <div class="list-group list-group-flush overflow-y-auto" id="accordionCursos" style="max-height: 580px; min-height: 580px;">
                    <?php include(__DIR__ . "/contactos.php"); ?>
                </div>
            </div>
        </div>

        <div class="col-md-8 col-lg-9">
            <?php if($usuario_chat): ?>

                <input type="hidden" id="receptor" value="<?= $usuario_chat ?>">

                <div class="card shadow-sm border-0 rounded-4 overflow-hidden d-flex flex-column" style="height: 640px;">
                    
                    <div class="p-3 bg-white border-bottom d-flex align-items-center justify-content-between shadow-sm" style="z-index: 5;">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 42px; height: 42px; font-size: 1.1rem; letter-spacing: 0.5px;">
                                <?= strtoupper(substr($nombre_conversacion, 0, 1)) ?>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;"><?= htmlspecialchars($nombre_conversacion) ?></h6>
                                <small class="text-success d-flex align-items-center gap-1" style="font-size: 0.75rem; font-weight: 500;">
                                    <i class="bi bi-shield-check-fill"></i> Canal Institucional Protegido
                                </small>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-link text-danger text-decoration-none btn-sm fw-medium d-flex align-items-center gap-1 small opacity-75" style="font-size: 0.8rem;" onclick="vaciarChat()">
                                <i class="bi bi-trash3-fill"></i> Vaciar conversación
                            </button>
                        </div>
                    </div>

                    <div id="mensajes" class="flex-grow-1 p-4 overflow-y-auto" style="background-color: #f4f6f9;">
                        </div>

                    <div class="p-3 bg-white border-top">
                        <form onsubmit="return enviarMensaje();">
                            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                <input type="text" 
                                       id="mensaje" 
                                       class="form-control border-0 bg-light text-dark shadow-none input-institucional px-4 py-2.5" 
                                       placeholder="Escriba un mensaje formal aquí..." 
                                       required 
                                       autocomplete="off"
                                       style="font-size: 0.9rem;">
                                <button class="btn btn-primary border-0 px-4 d-flex align-items-center gap-2 fw-medium" type="submit" style="font-size: 0.9rem;">
                                    <span>Enviar</span>
                                    <i class="bi bi-send-fill" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            <?php else: ?>

                <div class="card shadow-sm border-0 rounded-4 d-flex flex-column align-items-center justify-content-center p-5 text-center bg-white" style="height: 640px;">
                    <div class="p-4 bg-light rounded-circle mb-3 shadow-sm text-muted opacity-75">
                        <i class="bi bi-chat-square-dots" style="font-size: 3.5rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.15rem;">Entorno de Comunicación Interna</h5>
                    <p class="text-muted small px-4 mb-0" style="max-width: 400px; line-height: 1.5;">
                        Por favor, seleccione un docente, personal administrativo o alumno desde el panel izquierdo para desplegar el historial de mensajes institucional.
                    </p>
                </div>

            <?php endif; ?>
        </div>

    </div>
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
    <div id="toastChat" class="toast align-items-center text-bg-success border-0 rounded-3 shadow" role="alert">
        <div class="d-flex">
            <div class="toast-body fw-medium" id="mensajeToast">
                Operación realizada correctamente
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVaciarChat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Confirmación de Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-secondary py-4" style="font-size: 0.95rem; line-height: 1.5;">
                ¿Está seguro de que desea vaciar por completo el historial de esta conversación? Esta acción limpiará los registros de su bandeja de entrada de forma definitiva.
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary rounded-pill px-3 btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill px-3 btn-sm fw-medium" onclick="confirmarVaciarChat()">Vaciar Chat</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="chat/chat.js"></script>

<?php if($usuario_chat): ?>
<script>
    // Inicialización automática al cargar el componente con usuario activo
    cargarMensajes();
</script>
<?php endif; ?>