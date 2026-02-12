<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

$profesores = array();

try {
    $stmt = $conn->query("SELECT p.ProfesorID, p.Nombre, p.Especialidad, 
                                 g.NombreGrado, pg.GradoID
                           FROM Profesores p
                           LEFT JOIN ProfesorGrado pg ON p.ProfesorID = pg.ProfesorID
                           LEFT JOIN Grados g ON pg.GradoID = g.GradoID
                           ORDER BY p.ProfesorID");

    $currentProfesorId = null;
    $profesor = null;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['ProfesorID'] !== $currentProfesorId) {
            // Nuevo profesor, guardar el anterior si existe
            if ($profesor !== null) {
                $profesores[] = $profesor;
            }
            // Iniciar un nuevo profesor
            $currentProfesorId = $row['ProfesorID'];
            $profesor = [
                'ProfesorID' => $row['ProfesorID'],
                'Nombre' => $row['Nombre'],
                'Especialidad' => $row['Especialidad'],
                'Grados' => []
            ];
        }

        // Añadir el grado si existe
        if ($row['GradoID'] !== null) {
            $profesor['Grados'][] = [
                'GradoID' => $row['GradoID'],
                'NombreGrado' => $row['NombreGrado']
            ];
        }
    }

    // Añadir el último profesor al array
    if ($profesor !== null) {
        $profesores[] = $profesor;
    }

} catch (PDOException $e) {
    echo json_encode(["error" => "Error al obtener los profesores: " . $e->getMessage()]);
    exit();
}

echo json_encode($profesores);
?>
