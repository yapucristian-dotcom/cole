<?php
header('Content-Type: application/json');

require_once 'conexion.php';

$profesorId = $_GET['id'] ?? null;

if (!$profesorId) {
    echo json_encode(['error' => 'ID de profesor no proporcionado.']);
    exit();
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

    $gradosAsociados = [];
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
        echo json_encode(['error' => 'Profesor no encontrado.']);
    } else {
        echo json_encode($profesor);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>


