<?php
header('Content-Type: application/json');

require_once 'conexion.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profesorId = $_POST['id'] ?? null;

    if (!$profesorId) {
        $response['message'] = 'ID de profesor no proporcionado.';
    } else {
        try {
            // La eliminación en ProfesorGrado es automática debido a ON DELETE CASCADE
            $stmt = $conn->prepare("DELETE FROM Profesores WHERE ProfesorID = :profesorId");
            $stmt->bindParam(':profesorId', $profesorId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Profesor eliminado exitosamente.';
                } else {
                    $response['message'] = 'Profesor no encontrado o ya eliminado.';
                }
            } else {
                $response['message'] = 'Error al eliminar el profesor.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Error de base de datos: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Método de solicitud no permitido.';
}

echo json_encode($response);
?>


