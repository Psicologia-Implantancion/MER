<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "emocionvital";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener el día del formulario
$dia = $_POST['dia'];

// Preparar y ejecutar la consulta para verificar si el día existe
$sql_check = "SELECT ID_Fecha, COUNT(*) as count FROM fecha WHERE Dia = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $dia);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row = $result_check->fetch_assoc();

if ($row['count'] > 0) {
    // Si el día existe, actualizar el status a 1
    $id_fecha = $row['ID_Fecha'];
    $sql_update = "UPDATE fecha SET Status = 1 WHERE Dia = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("s", $dia);
    $stmt_update->execute();
    echo "Día actualizado correctamente.";
} else {
    // Si el día no existe, insertar una nueva fila con status 1 y obtener el ID_Fecha insertado
    $sql_insert = "INSERT INTO fecha (Dia, Status) VALUES (?, 1)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("s", $dia);
    $stmt_insert->execute();
    $id_fecha = $conn->insert_id;  // Obtener el ID_Fecha recién insertado
    echo "Día insertado correctamente.";
}

// Actualizar el estado de las citas correspondientes al día modificado
$sql_update_cita = "UPDATE cita SET Status = 'Suspendida' WHERE Id_Fecha = ?";
$stmt_update_cita = $conn->prepare($sql_update_cita);

if ($stmt_update_cita === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt_update_cita->bind_param("i", $id_fecha);
if ($stmt_update_cita->execute() === false) {
    die("Error en la ejecución de la consulta: " . $stmt_update_cita->error);
}

// Redirigir a index.php/#agendar
header("Location: index.php#configuracion");
exit();
?>



