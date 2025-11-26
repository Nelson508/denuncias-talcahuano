<?php
require __DIR__ . '/lib/fpdf/fpdf.php';

// ------------------------
// 1. Obtener ID y denuncia
// ------------------------
if (!isset($_GET['id'])) {
    die("ID de denuncia no especificado.");
}

function texto_pdf($s) {
    // Convierte de UTF-8 a ISO-8859-1 que es lo que entiende FPDF por defecto
    // TRANSLIT intenta aproximar caracteres que no existen en ISO
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $s);
}

$idBuscado = (int) $_GET['id'];

$archivo = __DIR__ . '/data/denuncias.json';
if (!file_exists($archivo)) {
    die("No se encontró el archivo de denuncias.");
}

$denuncias = json_decode(file_get_contents($archivo), true);
if (!is_array($denuncias)) {
    die("Error al leer denuncias.json");
}

$denuncia = null;
foreach ($denuncias as $d) {
    if ((int)$d['id'] === $idBuscado) {
        $denuncia = $d;
        break;
    }
}

if (!$denuncia) {
    die("No se encontró la denuncia con ID $idBuscado");
}

// Aseguramos claves para no tener notices
function val($arr, $key, $default = '')
{
    return isset($arr[$key]) && $arr[$key] !== '' ? $arr[$key] : $default;
}

// ------------------------
// 2. Configurar PDF
// ------------------------
class PDFDenuncia extends FPDF
{
    public $logoPath;

    function Header()
    {
        // Margen superior
        $this->SetY(15);

        // Texto arriba izquierda: "FORMULARIO DE DENUNCIA"
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 4, texto_pdf('FORMULARIO DE DENUNCIA'), 0, 1, 'L');

        // Título principal centrado
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 8, texto_pdf('COMPROBANTE DE DENUNCIA DIGITAL'), 0, 1, 'C');

        // Logo en la esquina superior derecha
        if ($this->logoPath && file_exists($this->logoPath)) {
            // x, y, width
            $this->Image($this->logoPath, 160, 12, 30);
        }

        // Línea separadora
        $this->Ln(4);
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.4);
        $this->Line(20, $this->GetY(), 190, $this->GetY());
        $this->Ln(5);
    }

    function Footer()
    {
        // Pie de página simple (opcional)
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, texto_pdf('Comprobante generado digitalmente - Municipalidad de Talcahuano'), 0, 0, 'C');
    }
}

$pdf = new PDFDenuncia('P', 'mm', 'A4');
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 20);
$pdf->logoPath ='assets/img/logo-talcahuano-blue.jpeg'; // <- AJUSTA AQUÍ SI CAMBIA EL NOMBRE/RUTA
$pdf->AddPage();

// Borde exterior tipo hoja
$pdf->SetDrawColor(220, 220, 220);
$pdf->SetLineWidth(0.3);
$pdf->Rect(15, 10, 180, 277); // x, y, w, h

// Función helper para títulos de sección
function tituloSeccion($pdf, $texto)
{
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor(40, 40, 40);
    $pdf->Ln(3);
    $pdf->Cell(0, 6, texto_pdf($texto), 0, 1, 'L');

    // Línea debajo del título
    $pdf->SetDrawColor(0, 102, 204); // azul suave
    $pdf->SetLineWidth(0.5);
    $x1 = 20;
    $x2 = 80;
    $y = $pdf->GetY();
    $pdf->Line($x1, $y, $x2, $y);
    $pdf->Ln(3);

    // Volver a color normal para contenido
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->SetLineWidth(0.3);
}

// Helper para imprimir campo etiqueta: valor en columnas
function campoDoble($pdf, $etqIzq, $valIzq, $etqDer = '', $valDer = '')
{
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(60, 60, 60);
    $pdf->Cell(40, 5, texto_pdf($etqIzq), 0, 0, 'L');

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(50, 5, texto_pdf($valIzq), 0, 0, 'L');

    if ($etqDer !== '') {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->Cell(30, 5, texto_pdf($etqDer), 0, 0, 'L');

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 5, texto_pdf($valDer), 0, 1, 'L');
    } else {
        $pdf->Ln(5);
    }
}

// Helper campo simple (una línea)
function campoSimple($pdf, $etq, $val)
{
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(60, 60, 60);
    $pdf->Cell(35, 5, texto_pdf($etq), 0, 0, 'L');

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(0, 5, texto_pdf($val), 0, 'L');
    $pdf->Ln(1);
}

// ------------------------
// 3. DATOS DE LA DENUNCIA
// ------------------------
tituloSeccion($pdf, 'DATOS DE LA DENUNCIA');

campoDoble(
    $pdf,
    'N° Denuncia:',
    val($denuncia, 'id', ''),
    'Área de denuncia:',
    val($denuncia, 'area', '')
);

campoDoble(
    $pdf,
    'Fecha de ingreso:',
    val($denuncia, 'fecha_ingreso', ''),
    'Estado:',
    val($denuncia, 'estado', '')
);

campoDoble(
    $pdf,
    'Fecha de cierre:',
    val($denuncia, 'fecha_cierre', '-'),
    '',
    ''
);

// Línea suave separadora
$pdf->Ln(2);
$pdf->SetDrawColor(230, 230, 230);
$y = $pdf->GetY();
$pdf->Line(20, $y, 190, $y);
$pdf->Ln(4);

// ------------------------
// 4. DESCRIPCIÓN / COMENTARIO
// ------------------------
tituloSeccion($pdf, 'DESCRIPCIÓN DE LA DENUNCIA');

$descripcion = val($denuncia, 'mensaje', '(Sin descripción registrada)');
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(0, 5, texto_pdf($descripcion), 0, 'J'); // Justificado
$pdf->Ln(3);

// Otra línea suave
$pdf->SetDrawColor(230, 230, 230);
$y = $pdf->GetY();
$pdf->Line(20, $y, 190, $y);
$pdf->Ln(4);

// ------------------------
// 5. DATOS DEL DENUNCIANTE
// ------------------------
tituloSeccion($pdf, 'INFORMACIÓN DEL DENUNCIANTE');

campoSimple($pdf, 'Nombre denunciante:', val($denuncia, 'nombre', ''));
campoSimple($pdf, 'RUT:', val($denuncia, 'rut', ''));
campoSimple($pdf, 'Domicilio:', val($denuncia, 'domicilio', ''));
campoSimple($pdf, 'Ubicación geográfica:', val($denuncia, 'direccion', ''));
campoSimple($pdf, 'Contacto teléfono:', val($denuncia, 'telefono', ''));
campoSimple($pdf, 'Correo:', val($denuncia, 'email', ''));
campoSimple($pdf, 'Organización:', val($denuncia, 'organizacion', ''));

// Puedes agregar una nota final si quieres
$pdf->Ln(4);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(120, 120, 120);
$pdf->MultiCell(
    0,
    4,
    texto_pdf(
        "Este comprobante corresponde a una denuncia ingresada a través del formulario web de denuncias ambientales de la Municipalidad de Talcahuano."
    ),
    0,
    'L'
);

// ------------------------
// 6. Salida del PDF
// ------------------------
$nombreArchivo = 'Denuncia_' . $denuncia['id'] . '.pdf';
// Modo "I" = inline en el navegador (como en las fotos)
$pdf->Output('I', $nombreArchivo);
exit;
