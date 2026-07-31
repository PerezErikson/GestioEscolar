<?php
include("conexion/conexion.php");

// Datos del centro educativo
$centro = $conn->query("
    SELECT nombre_centro,
           codigo_centro,
           direccion,
           distrito,
           telefono,
           correo,
           director,
           logo
    FROM configuracion
    LIMIT 1
")->fetch_assoc();

// ================= RASTREO DE LOGO EN LA CARPETA UPLOADS CON BASE64 =================
$logo_base64 = "";
if (!empty($centro['logo'])) {
    
    // Obtiene de forma segura la raíz física del proyecto en XAMPP (ej. C:/xampp/htdocs/GestioEscolar)
    $raiz_proyecto = dirname(__DIR__); 
    
    // Lista de rutas posibles donde se aloja la carpeta uploads
    $rutas_fisicas = [
        $raiz_proyecto . "/uploads/" . $centro['logo'],
        __DIR__ . "/uploads/" . $centro['logo'],
        "uploads/" . $centro['logo'],
        "../uploads/" . $centro['logo']
    ];

    foreach ($rutas_fisicas as $ruta) {
        if (file_exists($ruta)) {
            $tipo_contenido = pathinfo($ruta, PATHINFO_EXTENSION);
            $datos_imagen = file_get_contents($ruta);
            $logo_base64 = 'data:image/' . $tipo_contenido . ';base64,' . base64_encode($datos_imagen);
            break; 
        }
    }
}

// Configuración de la fecha actual en español
$meses = [
    "January" => "enero", "February" => "febrero", "March" => "marzo", "April" => "abril",
    "May" => "mayo", "June" => "junio", "July" => "julio", "August" => "agosto",
    "September" => "septiembre", "October" => "octubre", "November" => "noviembre", "December" => "diciembre"
];
$fecha_hoy = date("d") . " días del mes de " . $meses[date("F")] . " del año " . date("Y");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificación del Estudiante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tipografía limpia y moderna para el formulario y controles generales (similar a la imagen de referencia) */
        body { 
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
            margin: 40px; 
            background-color: #f8f9fa; 
        }

        /* Estilo específico y formal tipo "Times New Roman" exclusivamente para el contenido del certificado impreso */
        .certificado-texto { 
            font-family: 'Times New Roman', serif; 
        }

        .titulo { text-align: center; font-weight: bold; font-size: 21px; margin-top: 30px; letter-spacing: 1px; }
        .firma { text-align: center; margin-top: 60px; font-style: italic; }
        .subrayado { text-decoration: underline; font-weight: bold; }
        .card { border: none; box-shadow: 0 0 10px rgba(0,0,0,0.08); }
        
        /* ================= REGLAS DE IMPRESIÓN REFORZADAS ================= */
        @media print {
            /* Ocultamos visualmente todo el entorno web y los menús del sistema de forma estricta */
            body * { 
                visibility: hidden !important; 
            }
            
            /* Encendemos exclusivamente la visibilidad de la hoja del certificado */
            #contenedor_certificado, #contenedor_certificado * { 
                visibility: visible !important; 
            }
            
            /* Movemos la hoja al extremo superior izquierdo para que no deje áreas en blanco */
            #contenedor_certificado { 
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Desaparecemos el botón verde de imprimir y el formulario de la hoja final */
            .no-print {
                display: none !important;
            }
            
            body { 
                background: white !important; 
            }
            
            .card { 
                box-shadow: none !important; 
                border: none !important; 
                background: transparent !important; 
            }

            @page {
                size: portrait;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-4 mb-4 no-print">
        <h4 class="fw-bold text-dark mb-3">
            <i class="bi bi-file-earmark-text"></i> Generar Certificación
        </h4>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Seleccionar Estudiante</label>
                <select id="select_estudiante" class="form-select" required>
                    <option value="">-- Seleccione un estudiante --</option>
                    <?php
                    $estudiantes = $conn->query("SELECT numero, nombre, apellido FROM estudiantes ORDER BY apellido ASC");
                    while ($row = $estudiantes->fetch_assoc()) {
                        echo "<option value='{$row['numero']}'>{$row['apellido']}, {$row['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Año Escolar</label>
                <select id="select_anio" class="form-select" required>
                    <option value="">-- Seleccione el año --</option>
                    <?php
                    $anios = $conn->query("SELECT id, nombre FROM anio_escolar ORDER BY nombre DESC");
                    while ($row = $anios->fetch_assoc()) {
                        echo "<option value='{$row['nombre']}'>{$row['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Conducta / Comportamiento</label>
                <select id="select_conducta" class="form-select">
                    <option value="EXCELENTE">EXCELENTE</option>
                    <option value="MUY BUENA">MUY BUENA</option>
                    <option value="BUENA">BUENA</option>
                    <option value="REGULAR">REGULAR</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" onclick="generarCertificado(event)" class="btn btn-primary w-100 fw-bold">Generar</button>
            </div>
        </div>
    </div>

    <div id="contenedor_certificado" style="display: none;">
        
        <div class="text-end mb-3 no-print">
            <button type="button" onclick="window.print();" class="btn btn-success fw-bold">
                Imprimir Certificación
            </button>
        </div>

        <div class="card p-5 mt-2 certificado-texto" style="background: #fff; min-height: 24cm;">
            <div class="text-center mb-4">
                <?php if (!empty($logo_base64)): ?>
                    <img id="img_logo_centro" src="<?php echo $logo_base64; ?>" alt="Logo Centro" style="width:110px; height:auto; margin-bottom:10px; display:inline-block !important;">
                <?php endif; ?>
                
                <h5 class="fw-bold m-0" style="font-size: 18px;">MINISTERIO DE EDUCACIÓN DE REPÚBLICA DOMINICANA</h5>
                <h5 class="fw-bold my-1" style="font-size: 16px;">Centro Educativo <?php echo strtoupper($centro['nombre_centro'] ?? ''); ?></h5>
                <p class="m-0 fw-bold" style="font-size:14px;">
                 Código del Centro: <?php echo htmlspecialchars($centro['codigo_centro'] ?? ''); ?>
                 </p>
                 <p class="m-0 fw-bold" style="font-size:14px;">
    Distrito Educativo: <?php echo htmlspecialchars($centro['distrito'] ?? ''); ?>
</p>
                <p class="m-0" style="font-size: 14px; color: #333;">
                    <?php echo $centro['direccion'] ?? ''; ?>, <?php echo $centro['distrito'] ?? ''; ?><br>
                    Tel: <?php echo $centro['telefono'] ?? ''; ?> | Correo: <?php echo $centro['correo'] ?? ''; ?><br>
                    <strong>"EDUCAR ES UNA TAREA DE PADRES Y DOCENTES"</strong>
                </p>
            </div>

            <div class="titulo">A QUIEN PUEDA INTERESAR</div>

            <p style="text-align: justify; margin-top: 35px; font-size: 16px; line-height: 1.6;">
                Quien suscribe es la Licda. <span class="subrayado"><?php echo strtoupper($centro['director'] ?? ''); ?></span>,
                directora del Centro Educativo <span class="subrayado"><?php echo strtoupper($centro['nombre_centro'] ?? ''); ?></span>,
                perteneciente al Distrito Educativo <?php echo $centro['distrito'] ?? ''; ?>.
            </p>

            <p style="text-align: justify; font-size: 16px; line-height: 1.6;">
                Certifico que el alumno <span class="subrayado" id="cert_nombre"></span>,
                <strong>ID/Cédula:</strong> <span id="cert_id"></span>,
                estuvo matriculado en este Centro Educativo cursando el <strong><span id="cert_grado"></span></strong>
                en el presente año escolar <strong><span id="cert_anio"></span></strong>.
            </p>

            <p style="text-align: justify; font-size: 16px; line-height: 1.6;">
                Durante su estadía en el centro el alumno presentó una conducta <strong id="cert_conducta"></strong>.
            </p>

            <p style="text-align: justify; font-size: 16px; line-height: 1.6;">
                La presente certificación se expide a solicitud de la parte interesada en la comunidad de
                <?php echo $centro['direccion'] ?? ''; ?>, a los <?php echo $fecha_hoy; ?>.
            </p>
            

           <div class="firma">
    <p>Atentamente,</p>
    
    <div style="width: 250px; margin: 40px auto 10px auto; border-top: 1px solid #000;"></div>
    
    <p><strong>Licda/o. <?php echo $centro['director'] ?? ''; ?> (MGE)</strong><br>Directora</p>
</div>
        </div>
    </div>
</div>

<script>
function generarCertificado(e) {
    if(e) { e.preventDefault(); }
    
    const estudianteId = document.getElementById('select_estudiante').value;
    const anioSeleccionado = document.getElementById('select_anio').value;
    const conductaSeleccionada = document.getElementById('select_conducta').value;
    
    if (!estudianteId) {
        alert('Por favor, seleccione un estudiante.');
        return;
    }
    if (!anioSeleccionado) {
        alert('Por favor, seleccione el año escolar.');
        return;
    }

    // Petición asíncrona limpia para rellenar los campos
    fetch('Calificaciones/obtener_estudiante.php?estudiante_id=' + estudianteId)
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                document.getElementById('cert_nombre').innerText = `${res.data.nombre} ${res.data.apellido}`.toUpperCase();
                document.getElementById('cert_id').innerText = res.data.ID;
                document.getElementById('cert_grado').innerText = res.data.grado;
                document.getElementById('cert_anio').innerText = anioSeleccionado;
                document.getElementById('cert_conducta').innerText = conductaSeleccionada;

                // Mostramos el bloque imprimible
                document.getElementById('contenedor_certificado').style.display = 'block';
                document.getElementById('contenedor_certificado').scrollIntoView({ behavior: 'smooth' });
            } else {
                alert('Error: ' + res.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error al conectar con el servidor.');
        });
}
</script>

</body>
</html>