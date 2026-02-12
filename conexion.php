<?php
$serverName = "WIN-APJ9F969N57"; // o IP:PUERTO
$database = "Colegio";
$username = "sa";
$password = "S1st3m4s";

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa a SQL Server."; // Comentado para evitar salida en archivos incluidos
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    // Opcional: registrar el error para depuración
    // error_log("Error de conexión a la base de datos: " . $e->getMessage());
}
?>