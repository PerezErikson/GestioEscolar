<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario_chat = $_GET['usuario'] ?? 0;
?>

<div class="container-fluid">

    <div class="row">

        <!-- CONTACTOS -->
        <div class="col-md-3">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">
                    <i class="bi bi-people-fill"></i>
                    Contactos
                </div>

                <div class="list-group list-group-flush">

                    <?php include(__DIR__ . "/contactos.php"); ?>

                </div>

            </div>

        </div>

        <!-- CHAT -->
        <div class="col-md-9">

            <?php if($usuario_chat): ?>

                <input
                    type="hidden"
                    id="receptor"
                    value="<?= $usuario_chat ?>"
                >

                <!-- BOTÓN VACIAR CHAT -->
                <div class="d-flex justify-content-end mb-2">

                    <button
                        type="button"
                        class="btn btn-outline-danger btn-sm"
                        onclick="vaciarChat()">

                        <i class="bi bi-trash"></i>
                        Vaciar Chat

                    </button>

                </div>

                <!-- MENSAJES -->
                <div
                    id="mensajes"
                    class="card shadow-sm border-0 p-3"
                    style="height:500px;overflow-y:auto;">

                </div>

                <!-- FORMULARIO -->
                <form
                    class="mt-3"
                    onsubmit="return enviarMensaje();">

                    <div class="input-group">

                        <input
                            type="text"
                            id="mensaje"
                            class="form-control"
                            placeholder="Escribe un mensaje..."
                            required
                        >

                        <button
                            class="btn btn-primary"
                            type="submit">

                            <i class="bi bi-send-fill"></i>
                            Enviar

                        </button>

                    </div>

                </form>

            <?php else: ?>

                <div class="alert alert-info shadow-sm">

                    <i class="bi bi-chat-left-text"></i>

                    Seleccione un contacto para comenzar a chatear.

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<!-- TOAST BOOTSTRAP -->

<div
    class="position-fixed bottom-0 end-0 p-3"
    style="z-index:9999;">

    <div
        id="toastChat"
        class="toast align-items-center text-bg-success border-0"
        role="alert">

        <div class="d-flex">

            <div
                class="toast-body"
                id="mensajeToast">

                Operación realizada correctamente

            </div>

            <button
                type="button"
                class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast">
            </button>

        </div>

    </div>

</div>

<!-- MODAL VACIAR CHAT -->

<div
    class="modal fade"
    id="modalVaciarChat"
    tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header bg-danger text-white">

                <h5 class="modal-title">

                    <i class="bi bi-trash"></i>

                    Vaciar conversación

                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                ¿Está seguro de que desea vaciar esta conversación?

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancelar

                </button>

                <button
                    type="button"
                    class="btn btn-danger"
                    onclick="confirmarVaciarChat()">

                    <i class="bi bi-trash"></i>

                    Vaciar Chat

                </button>

            </div>

        </div>

    </div>

</div>

<!-- BOOTSTRAP JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- CHAT JS -->

<script src="chat/chat.js"></script>

<?php if($usuario_chat): ?>
<script>
    cargarMensajes();
</script>
<?php endif; ?>