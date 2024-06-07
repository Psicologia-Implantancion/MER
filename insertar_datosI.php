<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Recupera el ID del usuario desde la sesión
} else {
    echo "El usuario no está autenticado.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "emocionvital";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Variables para los datos del formulario
    $firstName = $_POST['firstName'];
    $secondName = $_POST['secondName'];
    $firstLastName = $_POST['firstLastName'];
    $secondLastName = $_POST['secondLastName'];
    $vejp = $_POST['vejp'];
    $cedula = $_POST['idNumber'];
    $phone = trim($_POST['phone']);
    $dob = $_POST['birthDate'];
    $email = $_POST['email'];
    $estado = $_POST['estado'];
    $municipio = $_POST['municipio'];
    $descripcion = $_POST['descripcion'];
    $Nhijo = $_POST['Nhijo'];
    $childFirstName = $_POST['childFirstName'];
    $childSecondName = $_POST['childSecondName'];
    $childFirstLastName = $_POST['childFirstLastName'];
    $childSecondLastName = $_POST['childSecondLastName'];
    $childBirthDate = $_POST['childBirthDate'];
    $partnerDate = $_POST['partnerDate'];
    $appointmentTime = $_POST['appointmentTime']. ":00";
    $amount = $_POST['monto'];
    $hora_fin = $_POST['hora_fin'] . ":00"; // Agregar segundos para la hora de fin 
    $duracion = 45;

    $sql_direccion = "INSERT INTO direccion (ID_Estados, ID_Municipio, Descripcion)
                      VALUES ('$estado', '$municipio', '$descripcion')";
    if ($conn->query($sql_direccion) === TRUE) {
        $direccion_id = $conn->insert_id;

        $sql_paciente = "INSERT INTO paciente (`Primer Nombre`, `Segundo Nombre`, `Primer Apellido`, `Segundo Apellido`, `Teléfono`, `Fecha_Nacimiento`, `Correo`, `ID_Direccion`, `Cedula`, `Tipo_Cedula`, `Num_Hijos`)
                         VALUES ('$firstName', '$secondName', '$firstLastName', '$secondLastName', '$phone', '$dob', '$email', '$direccion_id', '$cedula', '$vejp', '$Nhijo')";
        
        
        if ($conn->query($sql_paciente) === TRUE) {
            $paciente_id = $conn->insert_id;

            $sql_pareja = "INSERT INTO citamenor (`1erNombreInfante`, `2doNombreInfante`, `1erApellidoInfante`, `2doApellidoInfante`, `fecha nacimiento`)
                           VALUES ('$childFirstName', '$childSecondName', '$childFirstLastName', '$childSecondLastName', '$childBirthDate')";


            if ($conn->query($sql_pareja) === TRUE) {
                $menor_id = $conn->insert_id;





                $sql_fecha = "INSERT INTO fecha (Dia) VALUES ('$partnerDate')";
                // Comprobar si la fecha ya está registrada
$sql = "SELECT COUNT(*) AS count FROM fecha WHERE Dia = ? AND Status = 1";
$stmt = $conn->prepare($sql);

// Verificar si la preparación de la consulta fue exitosa
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Vincular el parámetro
$stmt->bind_param("s", $partnerDate);

// Ejecutar la declaración
$stmt->execute();

// Obtener el resultado
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
   
    exit(); 
}


                if ($conn->query($sql_fecha) === TRUE) {
                    $fecha_id = $conn->insert_id;

                    $sql_cita = "INSERT INTO cita (ID_Fecha, Hora, Hora_Fin, Monto, Id_TipoCita, ID_Paciente, ID_CitaMenor, ID_Psicologa, ID_Login)
                                 VALUES ('$fecha_id', '$appointmentTime', '$hora_fin', '$amount', 2, '$paciente_id', '$menor_id', 1, '$user_id')";
                                 // Validar formato de la hora de inicio
if (!preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $appointmentTime)) {

    exit();
}

$nueva_cita_inicio = DateTime::createFromFormat('H:i:s', $appointmentTime);
if (!$nueva_cita_inicio) {
    exit();
}

// Validar que la hora de inicio esté en los intervalos permitidos
$hora_inicio = (int)$nueva_cita_inicio->format('H');
if (!($hora_inicio >= 8 && $hora_inicio < 12) && !($hora_inicio >= 13 && $hora_inicio < 16)) {
    exit();
}

$nueva_cita_fin = clone $nueva_cita_inicio;
$nueva_cita_fin->modify("+$duracion minutes");

// Consultar citas existentes para la misma fecha y psicóloga
$sql_existentes = "SELECT `Hora` FROM `cita` WHERE `ID_Fecha` = '$fecha_id' AND `ID_Psicologa` = 1";
$result = $conn->query($sql_existentes);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cita_inicio = DateTime::createFromFormat('H:i:s', $row['Hora']);
        $cita_fin = clone $cita_inicio;
        $cita_fin->modify("+$duracion minutes");

        // Verificar si hay solapamiento
        if ($nueva_cita_inicio < $cita_fin && $nueva_cita_fin > $cita_inicio) {
            exit();
        }
    }
}
                    if ($conn->query($sql_cita) === TRUE) {
                        header("Location: index.php#agendar");
                        exit();
                    } else {
                        echo "Error al insertar la cita: " . $conn->error;
                    }
                } else {
                    echo "Error al insertar la fecha: " . $conn->error;
                }
            } else {
                echo "Error al insertar los datos del menor: " . $conn->error;
            }
        } else {
            echo "Error al insertar los datos del paciente: " . $conn->error;
        }
    } else {
        echo "Error al insertar la dirección del paciente: " . $conn->error;
    }

    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>
