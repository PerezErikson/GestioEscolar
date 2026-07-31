<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../conexion/conexion.php');

$mi_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol_id'];

// Consulta SQL ajustada para tu estructura de base de datos
if ($rol == 1) {
    $sql = "SELECT u.*, g.nombre AS nombre_curso FROM usuarios u LEFT JOIN estudiantes e ON TRIM(LOWER(u.correo)) = TRIM(LOWER(e.correo)) LEFT JOIN grados1 g ON e.grado_id = g.id WHERE u.id != '$mi_id' ORDER BY g.nombre ASC, u.nombre ASC";
} elseif ($rol == 2) {
    $sql = "SELECT u.*, g.nombre AS nombre_curso FROM usuarios u LEFT JOIN estudiantes e ON TRIM(LOWER(u.correo)) = TRIM(LOWER(e.correo)) LEFT JOIN grados1 g ON e.grado_id = g.id WHERE u.rol_id IN (1,3) AND u.id != '$mi_id' ORDER BY g.nombre ASC, u.nombre ASC";
} else {
    $sql = "SELECT u.*, g.nombre AS nombre_curso FROM usuarios u LEFT JOIN estudiantes e ON TRIM(LOWER(u.correo)) = TRIM(LOWER(e.correo)) LEFT JOIN grados1 g ON e.grado_id = g.id WHERE u.rol_id IN (1,2) AND u.id != '$mi_id' ORDER BY g.nombre ASC, u.nombre ASC";
}

$resultado = $conn->query($sql);
$usuarios_agrupados = [];

while ($usuario = $resultado->fetch_assoc()) {
    if (!empty($usuario['nombre_curso'])) {
        $nombre_grupo = $usuario['nombre_curso'];
    } elseif ($usuario['rol_id'] == 1 || $usuario['rol_id'] == 2) {
        $nombre_grupo = "Personal Administrativo / Docentes";
    } else {
        $nombre_grupo = "Estudiantes (Sin Curso Asignado)";
    }
    $usuarios_agrupados[$nombre_grupo][] = $usuario;
}
ksort($usuarios_agrupados);
?>

<div class="accordion" id="accordionCursos">
<?php 
$index = 0;
foreach ($usuarios_agrupados as $grupo => $lista_usuarios):
    $collapseId = "collapseCurso_" . $index;
    $headingId = "headingCurso_" . $index;
    
    // Verificamos si este grupo tiene algún mensaje nuevo para marcarlo
    $tiene_mensajes = false;
?>
    <div class="accordion-item border-0 border-bottom">
        <h2 class="accordion-header" id="<?= $headingId ?>">
            <button class="accordion-button collapsed py-2.5 fw-medium text-secondary bg-transparent shadow-none" 
                    type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" 
                    aria-expanded="false" aria-controls="<?= $collapseId ?>">
                <i class="bi bi-folder2-open me-2 text-warning fs-5"></i> 
                <span class="text-truncate" style="max-width: 75%;"><?= htmlspecialchars($grupo) ?></span>
                <span id="badge_<?= $index ?>" class="badge bg-light text-dark rounded-pill ms-auto me-2 small border">0</span>
            </button>
        </h2>

        <div id="<?= $collapseId ?>" class="accordion-collapse collapse" aria-labelledby="<?= $headingId ?>" data-bs-parent="#accordionCursos">
            <div class="accordion-body p-0">
                <div class="list-group list-group-flush">
                    <?php 
                    $total_grupo = 0;
                    foreach ($lista_usuarios as $usuario):
                        $no_leidos = $conn->query("SELECT COUNT(*) total FROM mensajes WHERE receptor_id = '$mi_id' AND emisor_id = '".$usuario['id']."' AND leido = 0")->fetch_assoc()['total'];
                        $total_grupo += $no_leidos;
                        if($no_leidos > 0) $tiene_mensajes = true;

                        $activo = (isset($_GET['usuario']) && $_GET['usuario'] == $usuario['id']) ? "active" : "";
                        $estado = (!empty($usuario['ultima_actividad']) && (time() - strtotime($usuario['ultima_actividad'])) <= 300) ? "🟢 En línea" : "⚫ Desconectado";
                    ?>
                        <a href="principal.php?seccion=chat&usuario=<?= $usuario['id'] ?>" class="list-group-item list-group-item-action <?= $activo ?> ps-4 py-2 border-0">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div class="fw-semibold small text-truncate" style="max-width: 80%;">
                                    <i class="bi bi-person me-1 text-secondary"></i> <?= htmlspecialchars($usuario['nombre']) ?>
                                </div>
                                <?php if ($no_leidos > 0): ?>
                                    <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.7rem;"><?= $no_leidos ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 0.75rem;" class="<?= $no_leidos > 0 ? 'text-success' : 'text-muted' ?> ms-3 mt-0.5"><?= $estado ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <script>document.getElementById('badge_<?= $index ?>').innerText = '<?= $total_grupo ?>';</script>
    <?php if($tiene_mensajes): ?>
        <script>
            // Marcar este grupo como "tiene mensajes" para el script de apertura
            document.getElementById('<?= $collapseId ?>').dataset.hasMessages = "true";
        </script>
    <?php endif; ?>
<?php 
    $index++;
endforeach; 
?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Abrir carpetas que tienen mensajes nuevos automáticamente
    document.querySelectorAll('[data-has-messages="true"]').forEach(el => {
        el.classList.add('show');
        const btn = document.querySelector('[data-bs-target="#' + el.id + '"]');
        if (btn) { btn.classList.remove('collapsed'); btn.setAttribute('aria-expanded', 'true'); }
    });

    // 2. Si el usuario hace clic en alguien, asegurar que esa carpeta se abra
    const urlParams = new URLSearchParams(window.location.search);
    const usuarioSeleccionado = urlParams.get('usuario');
    if (usuarioSeleccionado) {
        const linkActivo = document.querySelector('a[href*="usuario=' + usuarioSeleccionado + '"]');
        if (linkActivo) {
            const acordeonPadre = linkActivo.closest('.accordion-collapse');
            if (acordeonPadre) {
                acordeonPadre.classList.add('show');
                const btn = document.querySelector('[data-bs-target="#' + acordeonPadre.id + '"]');
                if (btn) { btn.classList.remove('collapsed'); btn.setAttribute('aria-expanded', 'true'); }
            }
        }
    }
});
</script>