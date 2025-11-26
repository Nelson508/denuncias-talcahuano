<?php
session_start();

// Guardamos lo que viene del paso 1
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['denuncia'] = [
        'nombre'           => $_POST['nombre'] ?? '',
        'rut'              => $_POST['rut'] ?? '',
        'genero'           => $_POST['genero'] ?? '',
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
        'domicilio'        => $_POST['domicilio'] ?? '',
        'telefono'         => $_POST['telefono'] ?? '',
        'organizacion'     => $_POST['organizacion'] ?? '',
        'email'            => $_POST['email'] ?? '',
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Denuncia - Paso 2</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="page-wrapper">

    <?php /* Puedes copiar exactamente el mismo <aside> del index.php */ ?>
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
            <div class="step step-active">
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

        <h2 class="form-section-title">2. Identificación de denuncia</h2>

        <form action="paso3.php" method="post">
            <div class="radio-list">
                <div class="radio-item">
                    <label><input type="radio" name="area" value="Contaminación acuática"> Contaminación acuática</label>
                </div>
                <div class="radio-item">
                    <label><input type="radio" name="area" value="Contaminación acústica"> Contaminación acústica</label>
                </div>
                <div class="radio-item">
                    <label><input type="radio" name="area"  value="Contaminación atmosférica"> Contaminación atmosférica</label>
                </div>
                <div class="radio-item">
                    <label><input type="radio" name="area" value="Recursos naturales y biodiversidad"> Recursos naturales y biodiversidad</label>
                </div>
                <div class="radio-item">
                    <label><input type="radio" name="area" value="Gestión de residuos"> Gestión de residuos</label>
                </div>
                <div class="radio-item">
                    <label><input type="radio" name="area" value="Tenencia responsable"> Tenencia responsable</label>
                </div>
                <div class="radio-item">
                    <label><input type="radio" name="area" value="Malos olores"> Malos olores</label>
                </div>
            </div>

            <div class="actions">
                <a href="index.php" class="btn btn-red">Volver</a>
                <button type="submit" class="btn btn-red">Siguiente</button>
            </div>

        </form>
    </main>
</div>
</body>
</html>
