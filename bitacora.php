<?php
// bitacora.php

// 1. Validar que venga un id
if (!isset($_GET['id'])) {
    echo "ID de denuncia no especificado.";
    exit;
}

$id = (int) $_GET['id'];

// 2. Cargar archivo JSON
$archivo = __DIR__ . '/data/denuncias.json';

if (!file_exists($archivo)) {
    echo "No hay denuncias registradas.";
    exit;
}

$json = file_get_contents($archivo);
$denuncias = json_decode($json, true) ?? [];

// 3. Buscar la denuncia por ID
$denuncia = null;
foreach ($denuncias as $d) {
    if ((int)$d['id'] === $id) {
        $denuncia = $d;
        break;
    }
}

if (!$denuncia) {
    echo "No se encontró la denuncia con ID $id.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora denuncia #<?= htmlspecialchars($denuncia['id']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

</head>
<body class="tracking-body">

<div class="tracking-container bitacora-page">
    <!-- <h1 class="tracking-title">Bitácora denuncia Nº</h1> -->
    <div class="bitacora-grid">
        <div class="bitacora-header-row">
            <h1 class="bitacora-title">
                Bitácora denuncia N° <?= htmlspecialchars($denuncia['id']) ?>
            </h1>

            <div class="bitacora-actions">
                <a href="generar_pdf.php?id=<?= (int)$denuncia['id'] ?>"
                class="bit-btn bit-btn-primary"
                target="_blank">
                    PDF
                </a>

                <button type="button"
                        class="bit-btn bit-btn-outline"
                        id="btn-estado">
                    Cambiar estado
                </button>
            </div>
        </div>
    </div>

    <div class="bitacora-grid">
        <!-- Panel izquierdo: datos -->
        <div class="bitacora-left">
            <div class="bitacora-card">
                <h3>Datos de la denuncia</h3>
                <p><strong>Estado:</strong> <span id="estado-texto"><?= htmlspecialchars($denuncia['estado']) ?></span></p>
                <p><strong>Área:</strong> <?= htmlspecialchars($denuncia['area'] ?? '') ?></p>
                <p><strong>ID denuncia:</strong> <?= htmlspecialchars($denuncia['id']) ?></p>
                <p><strong>Fecha de ingreso:</strong> <?= htmlspecialchars($denuncia['fecha_ingreso'] ?? '') ?></p>
                <p><strong>Fecha del hecho:</strong> <?= htmlspecialchars($denuncia['fecha_hecho'] ?? '') ?></p>
                <p><strong>Dirección / referencia:</strong><br>
                    <?= nl2br(htmlspecialchars($denuncia['direccion'] ?? '')) ?></p>
            </div>

            <div class="bitacora-card">
                <h3>Información del denunciante</h3>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($denuncia['nombre'] ?? '') ?></p>
                <p><strong>RUT:</strong> <?= htmlspecialchars($denuncia['rut'] ?? '') ?></p>
                <p><strong>Domicilio:</strong> <?= htmlspecialchars($denuncia['domicilio'] ?? '') ?></p>
                <p><strong>Teléfono:</strong> <?= htmlspecialchars($denuncia['telefono'] ?? '') ?></p>
                <p><strong>Correo:</strong> <?= htmlspecialchars($denuncia['email'] ?? '') ?></p>
                <p><strong>Organización:</strong> <?= htmlspecialchars($denuncia['organizacion'] ?? '') ?></p>
            </div>

            <div class="bitacora-card">
                <h3>Mensaje</h3>
                <p><?= nl2br(htmlspecialchars($denuncia['mensaje'] ?? '')) ?></p>
            </div>
        </div>

        <!-- Panel derecho: imágenes / placeholders -->
        <div class="bitacora-right">
            <div class="bitacora-map-card">
                <h3>Ubicación de la denuncia</h3>
                <div id="map" class="bitacora-map"></div>
            </div>
            <div class="bitacora-img-placeholder">[Imagen 1]</div>
            <div class="bitacora-img-placeholder">[Imagen 2]</div>
        </div>
    </div>

    <div class="actions">
        <a href="seguimiento.php" class="btn btn-outline">Volver</a>
    </div>
</div>

<!-- Modal cambio de estado -->
<div id="estado-modal-overlay" class="modal-overlay" style="display:none;">
    <div class="modal-estado">
        <div class="modal-header">
            <h2>Cambiar estado de denuncia</h2>
            <button type="button" class="modal-close" id="modal-close">&times;</button>
        </div>

        <div class="modal-body">
            <!-- Select de área -->
            <div class="form-row">
                <label for="select-area">Área de denuncia</label>
                <select id="select-area">
                    <option value="">Seleccione área</option>
                    <option value="Contaminación acuática">Contaminación acuática</option>
                    <option value="Contaminación acústica">Contaminación acústica</option>
                    <option value="Contaminación atmosférica">Contaminación atmosférica</option>
                    <option value="Recursos naturales y biodiversidad">Recursos naturales y biodiversidad</option>
                    <option value="Gestión de residuos">Gestión de residuos</option>
                    <option value="Tenencia responsable">Tenencia responsable</option>
                    <option value="Malos olores">Malos olores</option>
                </select>
            </div>

            <!-- Mensaje predeterminado -->
            <div class="form-row">
                <label for="textarea-mensaje">Mensaje asociado al área</label>
                <textarea id="textarea-mensaje" rows="6"></textarea>
            </div>

            <!-- Dos inputs “de adorno” -->
            <div class="form-row modal-extra-inputs">
                <input type="text" placeholder="Campo adicional 1">
                <input type="text" placeholder="Campo adicional 2">
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-pill btn-red" id="btn-cerrado">
                Cerrado
            </button>
            <button type="button" class="btn-pill btn-yellow" id="btn-revision">
                Revisión
            </button>
        </div>
    </div>
</div>

<script>
    const modalOverlay = document.getElementById('estado-modal-overlay');
    const btnEstado    = document.getElementById('btn-estado');
    const btnClose     = document.getElementById('modal-close');
    const selectArea   = document.getElementById('select-area');
    const textareaMsg  = document.getElementById('textarea-mensaje');
    const btnCerrado   = document.getElementById('btn-cerrado');
    const btnRevision  = document.getElementById('btn-revision');

    // ID actual de la denuncia (desde PHP)
    const denunciaId = <?= (int)$denuncia['id'] ?>;

    // Mensajes predefinidos por área
    const mensajesPorArea = {
        "Contaminación acuática":
            `En respuesta a la denuncia sobre la presencia de malos olores atribuibles a actividades industriales, se informa que la Dirección de Medio Ambiente ha derivado los antecedentes a la Autoridad Sanitaria, entidad competente para fiscalizar y evaluar el cumplimiento de la normativa vigente en materia sanitaria y ambiental. Asimismo, esta Dirección se mantendrá atenta a los resultados de dicha fiscalización y continuará monitoreando la situación en el marco de sus atribuciones.
            Adicionalmente, se informa que las denuncias también pueden ser presentadas directamente ante la Autoridad Sanitaria a través del siguiente enlace: https://oirs.minsal.cl/. De esta manera, se refuerza la visibilización de la problemática y se facilita la acción de fiscalización correspondiente.
            No obstante, en caso de que se presenten nuevas situaciones que vulneren la normativa vigente en materia ambiental, le agradeceremos remitir los antecedentes a la Dirección de Medio Ambiente para su debida evaluación y fiscalización.`,
        "Contaminación acústica":
            `En respuesta a la denuncia sobre la presencia de malos olores atribuibles a actividades industriales, se informa que la Dirección de Medio Ambiente ha derivado los antecedentes a la Autoridad Sanitaria, entidad competente para fiscalizar y evaluar el cumplimiento de la normativa vigente en materia sanitaria y ambiental. Asimismo, esta Dirección se mantendrá atenta a los resultados de dicha fiscalización y continuará monitoreando la situación en el marco de sus atribuciones.
            Adicionalmente, se informa que las denuncias también pueden ser presentadas directamente ante la Autoridad Sanitaria a través del siguiente enlace: https://oirs.minsal.cl/. De esta manera, se refuerza la visibilización de la problemática y se facilita la acción de fiscalización correspondiente.
            No obstante, en caso de que se presenten nuevas situaciones que vulneren la normativa vigente en materia ambiental, le agradeceremos remitir los antecedentes a la Dirección de Medio Ambiente para su debida evaluación y fiscalización.`,
        "Contaminación atmosférica":
            `En respuesta a la denuncia sobre la presencia de malos olores atribuibles a actividades industriales, se informa que la Dirección de Medio Ambiente ha derivado los antecedentes a la Autoridad Sanitaria, entidad competente para fiscalizar y evaluar el cumplimiento de la normativa vigente en materia sanitaria y ambiental. Asimismo, esta Dirección se mantendrá atenta a los resultados de dicha fiscalización y continuará monitoreando la situación en el marco de sus atribuciones.
            Adicionalmente, se informa que las denuncias también pueden ser presentadas directamente ante la Autoridad Sanitaria a través del siguiente enlace: https://oirs.minsal.cl/. De esta manera, se refuerza la visibilización de la problemática y se facilita la acción de fiscalización correspondiente.
            No obstante, en caso de que se presenten nuevas situaciones que vulneren la normativa vigente en materia ambiental, le agradeceremos remitir los antecedentes a la Dirección de Medio Ambiente para su debida evaluación y fiscalización.`,
        "Recursos naturales y biodiversidad":
            `En respuesta a la denuncia sobre la presencia de malos olores atribuibles a actividades industriales, se informa que la Dirección de Medio Ambiente ha derivado los antecedentes a la Autoridad Sanitaria, entidad competente para fiscalizar y evaluar el cumplimiento de la normativa vigente en materia sanitaria y ambiental. Asimismo, esta Dirección se mantendrá atenta a los resultados de dicha fiscalización y continuará monitoreando la situación en el marco de sus atribuciones.
            Adicionalmente, se informa que las denuncias también pueden ser presentadas directamente ante la Autoridad Sanitaria a través del siguiente enlace: https://oirs.minsal.cl/. De esta manera, se refuerza la visibilización de la problemática y se facilita la acción de fiscalización correspondiente.
            No obstante, en caso de que se presenten nuevas situaciones que vulneren la normativa vigente en materia ambiental, le agradeceremos remitir los antecedentes a la Dirección de Medio Ambiente para su debida evaluación y fiscalización.`,
        "Gestión de residuos":
            `En respuesta a la denuncia sobre la presencia de malos olores atribuibles a actividades industriales, se informa que la Dirección de Medio Ambiente ha derivado los antecedentes a la Autoridad Sanitaria, entidad competente para fiscalizar y evaluar el cumplimiento de la normativa vigente en materia sanitaria y ambiental. Asimismo, esta Dirección se mantendrá atenta a los resultados de dicha fiscalización y continuará monitoreando la situación en el marco de sus atribuciones.
            Adicionalmente, se informa que las denuncias también pueden ser presentadas directamente ante la Autoridad Sanitaria a través del siguiente enlace: https://oirs.minsal.cl/. De esta manera, se refuerza la visibilización de la problemática y se facilita la acción de fiscalización correspondiente.
            No obstante, en caso de que se presenten nuevas situaciones que vulneren la normativa vigente en materia ambiental, le agradeceremos remitir los antecedentes a la Dirección de Medio Ambiente para su debida evaluación y fiscalización.`,
        "Tenencia responsable":
            `En respuesta a la denuncia sobre la presencia de malos olores atribuibles a actividades industriales, se informa que la Dirección de Medio Ambiente ha derivado los antecedentes a la Autoridad Sanitaria, entidad competente para fiscalizar y evaluar el cumplimiento de la normativa vigente en materia sanitaria y ambiental. Asimismo, esta Dirección se mantendrá atenta a los resultados de dicha fiscalización y continuará monitoreando la situación en el marco de sus atribuciones.
            Adicionalmente, se informa que las denuncias también pueden ser presentadas directamente ante la Autoridad Sanitaria a través del siguiente enlace: https://oirs.minsal.cl/. De esta manera, se refuerza la visibilización de la problemática y se facilita la acción de fiscalización correspondiente.
            No obstante, en caso de que se presenten nuevas situaciones que vulneren la normativa vigente en materia ambiental, le agradeceremos remitir los antecedentes a la Dirección de Medio Ambiente para su debida evaluación y fiscalización.`,
        "Malos olores":
            `En respuesta a la denuncia sobre la presencia de malos olores atribuibles a actividades industriales, se informa que la Dirección de Medio Ambiente ha derivado los antecedentes a la Autoridad Sanitaria, entidad competente para fiscalizar y evaluar el cumplimiento de la normativa vigente en materia sanitaria y ambiental. Asimismo, esta Dirección se mantendrá atenta a los resultados de dicha fiscalización y continuará monitoreando la situación en el marco de sus atribuciones.
            Adicionalmente, se informa que las denuncias también pueden ser presentadas directamente ante la Autoridad Sanitaria a través del siguiente enlace: https://oirs.minsal.cl/. De esta manera, se refuerza la visibilización de la problemática y se facilita la acción de fiscalización correspondiente.
            No obstante, en caso de que se presenten nuevas situaciones que vulneren la normativa vigente en materia ambiental, le agradeceremos remitir los antecedentes a la Dirección de Medio Ambiente para su debida evaluación y fiscalización.`
    };

    // Abrir / cerrar modal
    btnEstado.addEventListener('click', () => {
        modalOverlay.style.display = 'flex';
    });

    btnClose.addEventListener('click', () => {
        modalOverlay.style.display = 'none';
    });

    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.style.display = 'none';
        }
    });

    // Cargar mensaje al cambiar área
    selectArea.addEventListener('change', () => {
        const area = selectArea.value;
        textareaMsg.value = mensajesPorArea[area] || "";
    });

    // Helper para llamar al backend y cambiar estado
    function actualizarEstado(nuevoEstado) {
        const formData = new FormData();
        formData.append('id', denunciaId);
        formData.append('estado', nuevoEstado);

        fetch('actualizar_estado.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(resp => {
            if (resp.ok) {
                alert('Estado actualizado a: ' + nuevoEstado);
                // Opcional: actualizar texto en la ficha de la bitácora
                const estadoSpan = document.getElementById('estado-texto');
                if (estadoSpan) {
                    estadoSpan.textContent = nuevoEstado;
                }
                modalOverlay.style.display = 'none';
            } else {
                alert('No se pudo actualizar el estado: ' + (resp.error || 'Error desconocido'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error de comunicación al actualizar el estado');
        });
    }

    btnCerrado.addEventListener('click', () => {
        actualizarEstado('Cerrado');
    });

    btnRevision.addEventListener('click', () => {
        actualizarEstado('En revisión');
    });

</script>

<script>
    // Denuncia actual desde PHP → JS
    const denuncia = <?= json_encode($denuncia, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    // Centro por defecto (Talcahuano)
    const defaultLatLng = [-36.7167, -73.1167];

    // Inicializar mapa
    const map = L.map('map').setView(defaultLatLng, 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Si la denuncia tiene coordenadas válidas, ponemos solo ESA marca
    if (denuncia && denuncia.lat !== null && denuncia.lat !== undefined &&
        denuncia.lng !== null && denuncia.lng !== undefined) {

        const lat = parseFloat(denuncia.lat);
        const lon = parseFloat(denuncia.lng);

        if (!isNaN(lat) && !isNaN(lon)) {
            const dir    = (denuncia.direccion || '').trim();
            const area   = denuncia.area   || '';
            const estado = denuncia.estado || 'Creado';
            const id     = denuncia.id     || '';

            const popup = `
                <strong>ID:</strong> ${id}<br>
                <strong>Área:</strong> ${area}<br>
                <strong>Estado:</strong> ${estado}<br>
                <strong>Dirección:</strong> ${dir || '(sin dirección)'}
            `;

            const latlng = [lat, lon];
            L.marker(latlng).addTo(map).bindPopup(popup);

            // Centrar bien sobre esta denuncia
            map.setView(latlng, 15);
        }
    }
</script>



</body>
</html>
