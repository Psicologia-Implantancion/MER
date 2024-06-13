<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "emocionvital";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['cita_seleccionada']) && isset($_POST['accion'])) {
    $id_cita = $_POST['cita_seleccionada'];
    $accion = $_POST['accion'];

    if ($accion == 'cancelar') {
        $sql = "UPDATE cita SET Status = 'Suspendida' WHERE Id_Cita = '$id_cita'";
        if (mysqli_query($conn, $sql)) {
            echo "Cita cancelada exitosamente.";
        } else {
            echo "Error al cancelar la cita: " . mysqli_error($conn);
        }
    }
    // Redirigir de nuevo a la página de citas agendadas
    header('Location: index.php#citas-agendadas');
} else {
    echo "Error: No se seleccionó ninguna cita o acción.";
}

mysqli_close($conn);
?>
