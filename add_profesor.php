<?php
header('Content-Type: application/json');

require_once 'conexion.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $especialidad = $_POST['especialidad'] ?? '';
    $gradosSeleccionadosJson = $_POST['grados_seleccionados'] ?? '[]';
    $gradosSeleccionados = json_decode($gradosSeleccionadosJson, true); // Decodificar a array

    if (empty($nombre) || empty($especialidad)) {
        $response['message'] = 'Nombre y Especialidad son requeridos.';
    } else {
        try {
            // Iniciar una transacción
            $conn->beginTransaction();

            // 1. Insertar el nuevo profesor
            $stmt = $conn->prepare("INSERT INTO Profesores (Nombre, Especialidad) VALUES (:nombre, :especialidad)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':especialidad', $especialidad);
            
            if ($stmt->execute()) {
                $profesorId = $conn->lastInsertId(); // Obtener el ID del profesor recién insertado

                // 2. Insertar los grados seleccionados en ProfesorGrado
                if (!empty($gradosSeleccionados)) {
                    $stmtGrado = $conn->prepare("INSERT INTO ProfesorGrado (ProfesorID, GradoID) VALUES (:profesorId, :gradoId)");
                    foreach ($gradosSeleccionados as $gradoId) {
                        $stmtGrado->bindParam(':profesorId', $profesorId);
                        $stmtGrado->bindParam(':gradoId', $gradoId);
                        $stmtGrado->execute();
                    }
                }

                $conn->commit(); // Confirmar la transacción
                $response['success'] = true;
                $response['message'] = 'Profesor y grados agregados exitosamente.';
            } else {
                $conn->rollBack(); // Revertir la transacción si falla la inserción del profesor
                $response['message'] = 'Error al agregar el profesor.';
            }
        } catch (PDOException $e) {
            $conn->rollBack(); // Revertir la transacción en caso de excepción
            $response['message'] = 'Error de base de datos: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Método de solicitud no permitido.';
}

echo json_encode($response);
?>
