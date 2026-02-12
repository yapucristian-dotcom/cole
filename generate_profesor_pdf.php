<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'conexion.php'; // Tu archivo de conexión a la base de datos

use Dompdf\Dompdf;
use Dompdf\Options;

$profesorId = $_GET['id'] ?? null;

if (!$profesorId) {
    die("ID de profesor no proporcionado.");
}

$profesor = null;

try {
    // Obtener los datos del profesor y sus grados
    $stmt = $conn->prepare("SELECT p.ProfesorID, p.Nombre, p.Especialidad, 
                                 g.GradoID, g.NombreGrado
                           FROM Profesores p
                           LEFT JOIN ProfesorGrado pg ON p.ProfesorID = pg.ProfesorID
                           LEFT JOIN Grados g ON pg.GradoID = g.GradoID
                           WHERE p.ProfesorID = :profesorId
                           ORDER BY p.ProfesorID");
    $stmt->bindParam(':profesorId', $profesorId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($profesor === null) {
            $profesor = [
                'ProfesorID' => $row['ProfesorID'],
                'Nombre' => $row['Nombre'],
                'Especialidad' => $row['Especialidad'],
                'Grados' => []
            ];
        }
        if ($row['GradoID'] !== null) {
            $profesor['Grados'][] = [
                'GradoID' => $row['GradoID'],
                'NombreGrado' => $row['NombreGrado']
            ];
        }
    }

    if ($profesor === null) {
        die("Profesor no encontrado.");
    }

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}

// Generar el HTML para el PDF
$html = '<!DOCTYPE html>';
$html .= '<html lang="es">';
$html .= '<head>';
$html .= '    <meta charset="UTF-8">';
$html .= '    <title>Reporte de Profesor</title>';
$html .= '    <style>';
$html .= '        body { font-family: sans-serif; }';
$html .= '        h1 { color: #333; }';
$html .= '        table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
$html .= '        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
$html .= '        th { background-color: #f2f2f2; }';
$html .= '    </style>';
$html .= '</head>';
$html .= '<body>';
$html .= '    <h1>Reporte de Profesor</h1>';
$html .= '    <p><strong>ID:</strong> ' . htmlspecialchars($profesor['ProfesorID']) . '</p>';
$html .= '    <p><strong>Nombre:</strong> ' . htmlspecialchars($profesor['Nombre']) . '</p>';
$html .= '    <p><strong>Especialidad:</strong> ' . htmlspecialchars($profesor['Especialidad']) . '</p>';

$html .= '    <h2>Grados Asignados</h2>';
if (!empty($profesor['Grados'])) {
    $html .= '    <table>';
    $html .= '        <thead>';
    $html .= '            <tr>';
    $html .= '                <th>ID Grado</th>';
    $html .= '                <th>Nombre del Grado</th>';
    $html .= '            </tr>';
    $html .= '        </thead>';
    $html .= '        <tbody>';
    foreach ($profesor['Grados'] as $grado) {
        $html .= '            <tr>';
        $html .= '                <td>' . htmlspecialchars($grado['GradoID']) . '</td>';
        $html .= '                <td>' . htmlspecialchars($grado['NombreGrado']) . '</td>';
        $html .= '            </tr>';
    }
    $html .= '        </tbody>';
    $html .= '    </table>';
} else {
    $html .= '    <p>Este profesor no tiene grados asignados.</p>';
}

$html .= '</body>';
$html .= '</html>';

// Configurar opciones de Dompdf
$options = new Options();
$options->set('defaultFont', 'Dejavu Sans'); // O cualquier otra fuente compatible con caracteres especiales

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// (Opcional) Configurar el tamaño y orientación del papel
$dompdf->setPaper('A4', 'portrait');

// Renderizar el HTML como PDF
$dompdf->render();

// Enviar el PDF al navegador
$dompdf->stream("reporte_profesor_" . $profesor['ProfesorID'] . ".pdf", ["Attachment" => false]);
?>
