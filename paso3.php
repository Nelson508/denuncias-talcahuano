<?php
session_start();

// Guardamos el Área elegida en el paso 2
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['denuncia']['area'] = $_POST['area'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Denuncia - Paso 3</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
</head>
<body>
<div class="page-wrapper">

    <!-- mismo <aside> que en index.php -->

    <aside class="sidebar">
        <div class="sidebar-inner">
            <div class="sidebar-logo">
                <img src="assets/img/logo-talcahuano.png" alt="Talcahuano" class="logo-img">
            </div>
            <p class="sidebar-text">
                Estimado/a vecina/a este formulario web de denuncias ambientales esta diseñado para informar responsablemente a la Municipalidad cualquier hecho, 
                acto u omisión que represente una infracción a las normativas ambientales y por lo tanto, afectación al medio ambiente, con el objetivo que este 
                municipio actúe en el ámbito de sus competencias con la mayor celeridad posible. En los casos en que el municipio carezca de competencias, 
                los antecedentes serán derivados a la autoridad y/o servicio competente. Recordarles que la información proporcionada a través de esta línea de 
                denuncias es tratada con la máxima confidencialidad. Valoramos y respetamos su privacidad, asegurándoles que la información entregada será manejada 
                con discreción y de manera rigurosa.
            </p>

            <ul>
                <li>Tenencia irresponsable de mascotas y animales de compañía.</li>
                <li>Emisiones sonoras generadas por industrias, construcciones, entre otros.</li>
                <li>Mala gestión de residuos sólidos (transporte, disposición, entre otros).</li>
                <li>Intervención de humedales o cualquier cuerpo o curso de agua protegido o prioritario para su conservación.</li>
                <li>Emisiones atmosféricas generadas calefacción a leña o quemas ilegales.</li>
                <li>Emisión de malos olores provenientes de viviendas, rellenos sanitarios.</li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="seguimiento.php" class="btn-seguimiento">SEGUIMIENTO DE DENUNCIA</a>
        </div>
    </aside>

    <main class="main">
        <h1 class="form-title">Formulario de Denuncia</h1>

        <div class="stepper">
            <div class="step">
                <div class="step-circle">1</div>
                <div class="step-label">Datos del<br>denunciante</div>
            </div>
            <div class="stepper-line"></div>
            <div class="step">
                <div class="step-circle">2</div>
                <div class="step-label">Identificación<br>de denuncia</div>
            </div>
            <div class="stepper-line"></div>
            <div class="step step-active">
                <div class="step-circle">3</div>
                <div class="step-label">Reclamo</div>
            </div>
            <div class="stepper-line"></div>
            <div class="step">
                <div class="step-circle">4</div>
                <div class="step-label">Adjuntos</div>
            </div>
        </div>

        <h2 class="form-section-title">3. Reclamo</h2>

        <form action="paso4.php" method="post">
            <div class="form-row">
                <div class="form-group autocomplete">
                    <label>Dirección o referencia del lugar</label>
                    <input type="text" name="direccion" id="direccion_denuncia"
                        placeholder="Dirección o referencia del lugar">
                    <ul id="direccion_suggestions" class="autocomplete-list"></ul>
                </div>

                <div class="form-group">
                    <label>Fecha</label>
                    <input type="text" name="fecha_hecho" class="datepicker">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label>Mensaje</label>
                    <textarea name="mensaje" placeholder="Describa el reclamo"></textarea>
                </div>
            </div>

            <div class="actions">
                <a href="paso2.php" class="btn btn-red">Volver</a>
                <button type="submit" class="btn btn-red">Siguiente</button>
            </div>
        </form>
    </main>
</div>
<script>
    flatpickr(".datepicker", {
        locale: "es",
        dateFormat: "d-m-Y",     // lo que se ve y lo que se envía al backend
        altInput: false
    });
</script>
<script>
    // ===== Autocomplete de dirección con Nominatim vía autocomplete.php =====

    const dirInput = document.getElementById('direccion_denuncia');
    const dirList  = document.getElementById('direccion_suggestions');

    // Debounce para no disparar la búsqueda en cada tecla
    function debounce(fn, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    // Construir dirección "bonita" usando item.address de Nominatim
    function construirDireccionBonita(item) {
    if (!item || !item.address) return item.display_name || '';

    const a = item.address;
    const partes = [];

    // 1. Calle + número (si existe)
    if (a.road || a.street) {
        let calle = a.road || a.street;
        if (a.house_number) {
            // Formato tipo "Claudio Gay 1457"
            calle = calle + ' ' + a.house_number;
        }
        partes.push(calle);
    }

    // 2. Población / barrio / sector
    if (a.neighbourhood) {
        partes.push(a.neighbourhood);
    } else if (a.suburb) {
        partes.push(a.suburb);
    }

    // 3. Ciudad o localidad
    if (a.city) {
        partes.push(a.city);
    } else if (a.town) {
        partes.push(a.town);
    } else if (a.village) {
        partes.push(a.village);
    }

    // 4. Región (state)
    if (a.state) {
        partes.push(a.state);
    }

    // Sin provincia, sin código postal, sin país
    return partes.join(', ');
}


    function clearSuggestions() {
        dirList.innerHTML = '';
        dirList.style.display = 'none';
    }

    function showSuggestions(results) {
        dirList.innerHTML = '';
        if (!results || results.length === 0) {
            clearSuggestions();
            return;
        }

        results.forEach(item => {
            const texto = construirDireccionBonita(item);
            if (!texto) return;

            const li = document.createElement('li');
            li.textContent = texto;

            li.addEventListener('click', () => {
                dirInput.value = texto;
                clearSuggestions();
            });

            dirList.appendChild(li);
        });

        if (dirList.children.length === 0) {
            clearSuggestions();
        } else {
            dirList.style.display = 'block';
        }
    }

    const searchAddress = debounce(() => {
        const q = dirInput.value.trim();
        if (q.length < 3) {
            clearSuggestions();
            return;
        }

        // Llamamos a nuestro PHP, que a su vez llama a Nominatim
        const url = 'autocomplete.php?q=' + encodeURIComponent(q);

        fetch(url)
            .then(resp => resp.json())
            .then(data => {
                showSuggestions(data);
            })
            .catch(err => {
                console.error('Error autocomplete:', err);
                clearSuggestions();
            });
    }, 400);

    // Eventos
    dirInput.addEventListener('input', searchAddress);

    // Ocultar la lista al hacer click fuera
    document.addEventListener('click', (e) => {
        if (!dirInput.contains(e.target) && !dirList.contains(e.target)) {
            clearSuggestions();
        }
    });
</script>




</body>

</html>
