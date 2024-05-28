<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "emocionvital";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Depuración: Verificar los valores recibidos
    error_log("ID: $id, Status: $status");

    $sql = "UPDATE cita SET Status='$status' WHERE Id_Cita=$id";

    if (mysqli_query($conn, $sql)) {
        echo "Estado actualizado con éxito";
    } else {
        // Depuración: Mostrar el error de MySQL
        error_log("Error al actualizar el estado: " . mysqli_error($conn));
        echo "Error al actualizar el estado";
    }

    mysqli_close($conn);
} else {
    echo "Método de solicitud no permitido";
}
?>
