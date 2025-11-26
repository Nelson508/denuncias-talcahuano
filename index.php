<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Denuncia - Paso 1</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
</head>
<body>
<div class="page-wrapper">

    <!-- LADO IZQUIERDO -->
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

    <!-- LADO DERECHO -->
    <main class="main">
        <h1 class="form-title">Formulario de Denuncia</h1>

        <!-- STEPPER -->
        <div class="stepper">
            <div class="step step-active">
                <div class="step-circle">1</div>
                <div class="step-label">Datos del<br>denunciante</div>
            </div>
            <div class="stepper-line"></div>
            <div class="step">
                <div class="step-circle">2</div>
                <div class="step-label">Identificación<br>de denuncia</div>
            </div>
            <div class="stepper-line"></div>
            <div class="step">
                <div class="step-circle">3</div>
                <div class="step-label">Reclamo</div>
            </div>
            <div class="stepper-line"></div>
            <div class="step">
                <div class="step-circle">4</div>
                <div class="step-label">Adjuntos</div>
            </div>
        </div>

        <!-- CONTENIDO PASO 1 -->
        <h2 class="form-section-title">1. Datos del denunciante</h2>

        <form action="paso2.php" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" placeholder="Nombre" required>
                </div>
                <div class="form-group">
                    <label>RUT</label>
                    <input type="text" name="rut" placeholder="RUT" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Género</label>
                    <select name="genero">
                        <option>--Género--</option>
                        <option>Masculino</option>
                        <option>Femenino</option>
                        <option>Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha Nacimiento</label>
                    <input type="text" name="fecha_nacimiento" class="datepicker">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Domicilio</label>
                    <input type="text" name="domicilio" placeholder="Domicilio">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" placeholder="Teléfono">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Organización</label>
                    <input type="text" name="organizacion"  placeholder="Organización">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Email">
                </div>
            </div>

            <div class="actions">
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

</body>
</html>
