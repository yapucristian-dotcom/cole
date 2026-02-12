<?php
header('Content-Type: application/json');

require_once 'conexion.php';

$grados = array();

try {
    $stmt = $conn->query("SELECT GradoID, NombreGrado FROM Grados");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $grados[] = $row;
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al obtener los grados: " . $e->getMessage()]);
    exit();
}

echo json_encode($grados);
?>

