<?php
$archivo = __DIR__ . '/data/denuncias.json';
$denuncias = [];

if (file_exists($archivo)) {
    $json = file_get_contents($archivo);
    $denuncias = json_decode($json, true) ?: [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento de denuncias</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body class="tracking-body">

<div class="tracking-container">

    <h1 class="tracking-title">Seguimiento de denuncias</h1>

    <!-- FILTROS -->
    <form class="tracking-filters">
        <div class="filter-group">
            <label>Área denuncia</label>
            <select>
                <option>Área denuncia</option>
                <option>Malos olores</option>
                <option>Tenencia responsable</option>
                <option>Contaminación acústica</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Fecha de ingreso</label>
            <input type="text" name="fecha_ingreso_desde" class="datepicker">
        </div>

        <div class="filter-group">
            <label>Fecha de término</label>
            <input type="text" name="fecha_ingreso_hasta" class="datepicker">
        </div>

        <div class="filter-group">
            <label>Estado</label>
            <select>
                <option>Estado</option>
                <option>Creado</option>
                <option>En revisión</option>
                <option>Cerrado</option>
            </select>
        </div>

        <div class="filter-group filter-button">
            <button type="submit" class="btn-blue">Buscar</button>
        </div>
    </form>

    <!-- CONTENIDO PRINCIPAL: TABLA + LADO DERECHO -->
    <div class="tracking-main">
        <?php
        function claseEstado($estado) {
            $estado = mb_strtolower(trim($estado), 'UTF-8');

            switch ($estado) {
                case 'cerrado':
                    return 'estado-cerrado';
                case 'en revisión':
                case 'en revision':   // por si viene sin tilde
                    return 'estado-revision';
                default:
                    return 'estado-creado'; // incluye "Creado" y cualquier otro
            }
        }
        ?>

        <!-- TABLA -->
        <div class="tracking-table-wrapper">
            <table class="tracking-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha de Ingreso</th>
                    <th>Fecha de cierre</th>
                    <th>Estado</th>
                    <th>Área Denuncia</th>
                    <th>Bitácora</th>
                    <th>PDF</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($denuncias)): ?>
                    <tr>
                        <td colspan="7">No hay denuncias registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($denuncias as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['id']) ?></td>
                            <td><?= htmlspecialchars($d['fecha_ingreso']) ?></td>
                            <td><?= htmlspecialchars($d['fecha_cierre']) ?></td>
                            <td class="col-estado">
                                <span class="estado-pill <?= claseEstado($d['estado'] ?? '') ?>">
                                    <?= htmlspecialchars($d['estado']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($d['area'] ?? '') ?></td>
                            <td><a href="bitacora.php?id=<?= urlencode($d['id']) ?>" class="btn-pill">Bitácora</a></td>
                            <td><a class="btn-pill" href="generar_pdf.php?id=<?= $d['id'] ?>">PDF</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

        <!-- LADO DERECHO: MAPA + GRÁFICOS (SIMULADO) -->
        <aside class="tracking-side">
            <div class="side-block">
                <h3>Mapa de denuncias</h3>
                <div id="map" class="side-map"></div>
            </div>

            <div class="panel panel-small">
                <h3>Datos históricos</h3>
                <div class="panel-chart-wrapper">
                    <canvas id="chartEstados"></canvas>
                </div>
            </div>

        </aside>
    </div>

</div>

<script>
    // Denuncias desde PHP → JS
    const denuncias = <?= json_encode($denuncias, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    // =======================
    // Gráfico de estados
    // =======================

    // Contar estados
    const conteoEstados = {
        'Creado': 0,
        'En revisión': 0,
        'Cerrado': 0
    };

    denuncias.forEach(d => {
        const estadoRaw = (d.estado || 'Creado').toLowerCase().trim();

        if (estadoRaw === 'cerrado') {
            conteoEstados['Cerrado']++;
        } else if (estadoRaw === 'en revisión' || estadoRaw === 'en revision') {
            conteoEstados['En revisión']++;
        } else {
            conteoEstados['Creado']++;
        }
    });

    const labelsEstados = ['Creado', 'En revisión', 'Cerrado'];
    const datosEstados  = labelsEstados.map(e => conteoEstados[e] || 0);

    // Dibujar gráfico si existe el canvas
    const canvasEstados = document.getElementById('chartEstados');
    if (canvasEstados) {
        const ctx = canvasEstados.getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsEstados,
                datasets: [{
                    data: datosEstados,
                    backgroundColor: ['#28a745', '#f4b000', '#e73333']  // verde, amarillo, rojo
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Recuento de denuncias por estado'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Recuento de denuncias'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Estado'
                        }
                    }
                }
            }
        });
    }

    // Centro por defecto (Talcahuano)
    const defaultLatLng = [-36.7167, -73.1167];

    // Inicializar mapa
    const map = L.map('map').setView(defaultLatLng, 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const markerLatLngs = [];

    function addMarker(lat, lon, popupHtml) {
        const latlng = [lat, lon];
        L.marker(latlng).addTo(map).bindPopup(popupHtml);
        markerLatLngs.push(latlng);
    }

    denuncias.forEach(d => {
        if (d.lat === null || d.lat === undefined || d.lng === null || d.lng === undefined) {
            return; // denuncia sin coordenadas válidas
        }

        const lat = parseFloat(d.lat);
        const lon = parseFloat(d.lng);
        if (isNaN(lat) || isNaN(lon)) {
            return;
        }

        const dir    = (d.direccion || '').trim();
        const area   = d.area   || '';
        const estado = d.estado || 'Creado';
        const id     = d.id     || '';

        const popup = `
            <strong>ID:</strong> ${id}<br>
            <strong>Área:</strong> ${area}<br>
            <strong>Estado:</strong> ${estado}<br>
            <strong>Dirección:</strong> ${dir || '(sin dirección)'}
        `;

        addMarker(lat, lon, popup);
    });

    // Ajustar zoom para mostrar todas las marcas
    if (markerLatLngs.length === 1) {
        map.setView(markerLatLngs[0], 15);
    } else if (markerLatLngs.length > 1) {
        map.fitBounds(markerLatLngs, { padding: [20, 20] });
    }

    flatpickr(".datepicker", {
        locale: "es",
        dateFormat: "d-m-Y",     // lo que se ve y lo que se envía al backend
        altInput: false
    });
</script>


</body>
</html>
