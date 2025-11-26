<?php
session_start();

// Guardamos datos del reclamo (paso 3)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['denuncia']['direccion']   = $_POST['direccion'] ?? '';
    $_SESSION['denuncia']['fecha_hecho'] = $_POST['fecha_hecho'] ?? '';
    $_SESSION['denuncia']['mensaje']     = $_POST['mensaje'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Denuncia - Paso 4</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="page-wrapper">

    <!-- mismo <aside> -->

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
            <div class="step">
                <div class="step-circle">3</div>
                <div class="step-label">Reclamo</div>
            </div>
            <div class="stepper-line"></div>
            <div class="step step-active">
                <div class="step-circle">4</div>
                <div class="step-label">Adjuntos</div>
            </div>
        </div>

        <h2 class="form-section-title">4. Adjuntos</h2>

        <form action="guardar_denuncia.php" method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Archivo 1</label>
                    <input type="file">
                </div>
                <div class="form-group">
                    <label>Archivo 2</label>
                    <input type="file" name="archivo2">
                </div>
            </div>

            <div class="actions">
                <a href="paso3.php" class="btn btn-red">Volver</a>
                <button type="submit" class="btn btn-red">Enviar</button>
            </div>
        </form>
    </main>
</div>
</body>
</html>
