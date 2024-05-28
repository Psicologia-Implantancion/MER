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

if (isset($_GET['id_cita']) && isset($_GET['id_fecha'])) {
    $id_cita = $_GET['id_cita'];
    $id_fecha = $_GET['id_fecha'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nueva_fecha = $_POST['fecha'];
        $nueva_hora = $_POST['hora'];

        $sql_fecha = "UPDATE fecha SET Dia = '$nueva_fecha' WHERE Id_Fecha = '$id_fecha'";
        if (mysqli_query($conn, $sql_fecha)) {
            
            $sql_cita = "UPDATE cita SET Hora = '$nueva_hora', Status = 'Reagendada' WHERE Id_Cita = '$id_cita'";
            if (mysqli_query($conn, $sql_cita)) {
                echo "Cita reagendada exitosamente.";
                header('Location: index.php#agendar');
                exit();
            } else {
                echo "Error al reagendar la cita: " . mysqli_error($conn);
            }
        } else {
            echo "Error al actualizar la fecha: " . mysqli_error($conn);
        }
    }
} else {
    echo "Error: No se especificó la cita o la fecha a reagendar.";
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reagendar Cita</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="post" action="reagendar.php?id_cita=<?php echo $id_cita; ?>&id_fecha=<?php echo $id_fecha; ?>">
        <label for="fecha">Nueva Fecha:</label>
        <input type="date" id="fecha" name="fecha" required>
        <label for="hora">Nueva Hora:</label>
        <input type="time" id="hora" name="hora" required>
        <button type="submit">Reagendar</button>
    </form>
</body>
</html>
