<?php
header('Content-Type: application/json');

require_once 'conexion.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profesorId = $_POST['profesor_id'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $especialidad = $_POST['especialidad'] ?? '';
    $gradosSeleccionadosJson = $_POST['edit_grados_seleccionados'] ?? '[]';
    $gradosSeleccionados = json_decode($gradosSeleccionadosJson, true); // Decodificar a array

    if (!$profesorId || empty($nombre) || empty($especialidad)) {
        $response['message'] = 'Todos los campos son requeridos para la actualización.';
    } else {
        try {
            $conn->beginTransaction();

            // 1. Actualizar los datos del profesor
            $stmt = $conn->prepare("UPDATE Profesores SET Nombre = :nombre, Especialidad = :especialidad WHERE ProfesorID = :profesorId");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':especialidad', $especialidad);
            $stmt->bindParam(':profesorId', $profesorId, PDO::PARAM_INT);
            $stmt->execute();

            // 2. Eliminar los grados existentes para este profesor
            $stmtDeleteGrados = $conn->prepare("DELETE FROM ProfesorGrado WHERE ProfesorID = :profesorId");
            $stmtDeleteGrados->bindParam(':profesorId', $profesorId, PDO::PARAM_INT);
            $stmtDeleteGrados->execute();

            // 3. Insertar los nuevos grados seleccionados
            if (!empty($gradosSeleccionados)) {
                $stmtInsertGrados = $conn->prepare("INSERT INTO ProfesorGrado (ProfesorID, GradoID) VALUES (:profesorId, :gradoId)");
                foreach ($gradosSeleccionados as $gradoId) {
                    $stmtInsertGrados->bindParam(':profesorId', $profesorId);
                    $stmtInsertGrados->bindParam(':gradoId', $gradoId);
                    $stmtInsertGrados->execute();
                }
            }

            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Profesor y grados actualizados exitosamente.';

        } catch (PDOException $e) {
            $conn->rollBack();
            $response['message'] = 'Error de base de datos: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Método de solicitud no permitido.';
}

echo json_encode($response);
?>


