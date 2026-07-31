<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../conexion/conexion.php');

$usuario1 = $_GET['u1'] ?? '';
$usuario2 = $_GET['u2'] ?? '';
$buscar = $_GET['buscar'] ?? '';
?>

<div class="container-fluid py-4">

    <h1 class="fw-bold text-primary mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-clock-history"></i>
        Historial de Chats
    </h1>

    <div class="bg-white rounded-4 shadow-sm p-4">

        <form method="GET" class="mb-4">
            <input type="hidden" name="seccion" value="historial_chat">
            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                <span class="input-group-text bg-primary text-white border-0">
                    <i class="bi bi-search"></i>
                </span>
                <input
                    type="text"
                    class="form-control border-0"
                    name="buscar"
                    placeholder="Buscar usuario por nombre..."
                    value="<?= htmlspecialchars($buscar) ?>"
                >
                <button class="btn btn-primary px-4">
                    Buscar
                </button>
            </div>
        </form>

        <h5 class="text-muted mb-3">Conversaciones recientes</h5>
        <?php
        $where = '';
        if ($buscar != '') {
            $buscar_esc = $conn->real_escape_string($buscar);
            $where = "
            HAVING
            usuario_a LIKE '%$buscar_esc%'
            OR
            usuario_b LIKE '%$buscar_esc%'
            ";
        }

        $sql = "
        SELECT
            LEAST(m.emisor_id, m.receptor_id) usuario1,
            GREATEST(m.emisor_id, m.receptor_id) usuario2,
            u1.nombre usuario_a,
            u2.nombre usuario_b,
            COUNT(*) total_mensajes,
            MAX(m.fecha) ultimo_mensaje
        FROM mensajes m
        INNER JOIN usuarios u1 ON u1.id = LEAST(m.emisor_id, m.receptor_id)
        INNER JOIN usuarios u2 ON u2.id = GREATEST(m.emisor_id, m.receptor_id)
        GROUP BY LEAST(m.emisor_id, m.receptor_id), GREATEST(m.emisor_id, m.receptor_id)
        $where
        ORDER BY ultimo_mensaje DESC
        ";

        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0):
            while ($chat = $resultado->fetch_assoc()):
                $es_chat_activo = ($usuario1 == $chat['usuario1'] && $usuario2 == $chat['usuario2']);
                
                $url_boton = $es_chat_activo 
                    ? "principal.php?seccion=historial_chat" . ($buscar ? "&buscar=".urlencode($buscar) : "")
                    : "principal.php?seccion=historial_chat&u1={$chat['usuario1']}&u2={$chat['usuario2']}" . ($buscar ? "&buscar=".urlencode($buscar) : "");
        ?>
        
        <div class="card border-0 shadow-sm mb-2 <?= $es_chat_activo ? 'bg-light border-start border-primary border-4 shadow' : '' ?>">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-1 d-flex align-items-center gap-2">
                            <i class="bi bi-chat-left-text text-primary"></i>
                            <span><?= htmlspecialchars($chat['usuario_a']) ?></span>
                            <span class="text-muted fw-normal">↔</span>
                            <span><?= htmlspecialchars($chat['usuario_b']) ?></span>
                        </h6>
                        <small class="text-muted d-block">
                            <i class="bi bi-calendar3 me-1"></i>
                            Último: <?= date('d/m/Y h:i A', strtotime($chat['ultimo_mensaje'])) ?>
                        </small>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="badge bg-white text-dark border px-3 py-2 rounded-pill">
                            <strong><?= $chat['total_mensajes'] ?></strong> mensajes
                        </span>
                    </div>
                    <div class="col-6 col-md-3 text-end">
                        <a href="<?= $url_boton ?>" class="btn <?= $es_chat_activo ? 'btn-danger' : 'btn-primary' ?> btn-sm px-3 rounded-pill">
                            <?php if ($es_chat_activo): ?>
                                <i class="bi bi-eye-slash-fill me-1"></i> Ocultar
                            <?php else: ?>
                                <i class="bi bi-eye-fill me-1"></i> Ver Registro
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($es_chat_activo): 
            $u1_esc = $conn->real_escape_string($usuario1);
            $u2_esc = $conn->real_escape_string($usuario2);

            $sqlMensajes = "
            SELECT m.*, e.nombre emisor, r.nombre receptor
            FROM mensajes m
            INNER JOIN usuarios e ON e.id = m.emisor_id
            INNER JOIN usuarios r ON r.id = m.receptor_id
            WHERE (m.emisor_id = '$u1_esc' AND m.receptor_id = '$u2_esc')
               OR (m.emisor_id = '$u2_esc' AND m.receptor_id = '$u1_esc')
            ORDER BY m.fecha ASC
            ";

            $mensajes = $conn->query($sqlMensajes);
        ?>
        <div class="mb-4 p-3 bg-white border rounded-4 shadow-sm chat-collapse-box mx-2">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <span class="text-secondary fw-bold text-uppercase tracking-wider" style="font-size: 0.85rem;">
                    <i class="bi bi-list-task text-primary me-1"></i> transcripción de mensajes
                </span>
                <a href="principal.php?seccion=historial_chat<?= $buscar ? '&buscar='.urlencode($buscar) : '' ?>" class="btn-close" aria-label="Close"></a>
            </div>

            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="width: 25%;">Remitente</th>
                            <th style="width: 55%;">Mensaje</th>
                            <th style="width: 20%;" class="text-end">Fecha / Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($msg = $mensajes->fetch_assoc()): 
                            // Determinamos un color sutil según quién envíe para dar orden visual
                            $es_u1 = ($msg['emisor_id'] == $usuario1);
                            $badge_color = $es_u1 ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success';
                        ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge <?= $badge_color ?> rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <?= mb_substr($msg['emisor'], 0, 1) ?>
                                        </span>
                                        <span class="fw-semibold text-dark small">
                                            <?= htmlspecialchars($msg['emisor']) ?>
                                        </span>
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="text-secondary p-1" style="white-space: pre-line; word-break: break-word; font-size: 0.9rem;">
                                        <?= htmlspecialchars($msg['mensaje']) ?>
                                    </div>
                                </td>
                                
                                <td class="text-end">
                                    <small class="text-muted text-nowrap" style="font-size: 0.8rem;">
                                        <?= date('d/m/Y g:i A', strtotime($msg['fecha'])) ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <?php 
            endwhile;
        else:
        ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-chat-square-x fs-2"></i>
                <p class="mt-2">No se encontraron conversaciones.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
    /* Efecto de despliegue sutil */
    .chat-collapse-box {
        animation: fadeIn 0.2s ease-in-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Sticky para la cabecera de la tabla interna del chat */
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 2;
        background-color: #f8f9fa !important;
    }
    
    /* Clases de Bootstrap modernas para fondos suaves por si usas versiones anteriores */
    .bg-primary-subtle { background-color: #e3f2fd !important; }
    .bg-success-subtle { background-color: #e8f5e9 !important; }
</style>