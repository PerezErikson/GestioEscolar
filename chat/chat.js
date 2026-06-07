function cargarMensajes() {

    let receptor = document.getElementById("receptor");

    if (!receptor) return;

    fetch("chat/obtener_mensajes.php?usuario=" + receptor.value)
    .then(response => response.text())
    .then(data => {

        let chat = document.getElementById("mensajes");

        if (chat) {

            chat.innerHTML = data;
            chat.scrollTop = chat.scrollHeight;

        }

    })
    .catch(error => {

        console.error(error);

    });

}

function enviarMensaje() {

    let mensaje = document.getElementById("mensaje").value.trim();
    let receptor = document.getElementById("receptor").value;

    if (mensaje === "") {
        return false;
    }

    let datos = new FormData();

    datos.append("mensaje", mensaje);
    datos.append("receptor", receptor);

    fetch("chat/enviar_mensaje.php", {
        method: "POST",
        body: datos
    })
    .then(response => response.text())
    .then(data => {

        if (data.trim() === "ok") {

            document.getElementById("mensaje").value = "";

            cargarMensajes();

        } else {

            mostrarToast(
                "Error al enviar el mensaje",
                "danger"
            );

            console.log(data);

        }

    })
    .catch(error => {

        mostrarToast(
            "Error de conexión",
            "danger"
        );

        console.error(error);

    });

    return false;
}

setInterval(() => {

    cargarMensajes();

}, 2000);

function vaciarChat() {

    let modal = new bootstrap.Modal(
        document.getElementById("modalVaciarChat")
    );

    modal.show();

}

function confirmarVaciarChat() {

    let receptor = document.getElementById("receptor").value;

    let datos = new FormData();

    datos.append("receptor", receptor);

    fetch("chat/vaciar_chat.php", {
        method: "POST",
        body: datos
    })
    .then(response => response.text())
    .then(data => {

        let modal = bootstrap.Modal.getInstance(
            document.getElementById("modalVaciarChat")
        );

        if (modal) {
            modal.hide();
        }

        if (data.trim() === "ok") {

            mostrarToast(
                "🗑️ Conversación vaciada correctamente",
                "success"
            );

            cargarMensajes();

        } else {

            mostrarToast(
                "❌ Error al vaciar la conversación",
                "danger"
            );

            console.log(data);

        }

    })
    .catch(error => {

        mostrarToast(
            "❌ Error de conexión",
            "danger"
        );

        console.error(error);

    });

}

function mostrarToast(mensaje, tipo = "success") {

    let toast = document.getElementById("toastChat");
    let texto = document.getElementById("mensajeToast");

    if (!toast || !texto) {
        alert(mensaje);
        return;
    }

    texto.innerHTML = mensaje;

    toast.classList.remove(
        "text-bg-success",
        "text-bg-danger",
        "text-bg-warning",
        "text-bg-info"
    );

    toast.classList.add("text-bg-" + tipo);

    let bsToast = new bootstrap.Toast(toast);

    bsToast.show();

}