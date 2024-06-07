<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php#login-form");
    exit();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Recupera el ID del usuario desde la sesión
    echo "El ID del usuario es: " . $user_id;
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

    // Variables para los datos del paciente
    $firstName = $_POST['firstName'];
    $secondName = $_POST['secondName'];
    $firstLastName = $_POST['firstLastName'];
    $lastName = $_POST['lastName'];
    $vejp = $_POST['vejp'];
    $cedula = $_POST['cedula'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $estado = $_POST['estado'];
    $municipio = $_POST['municipio'];
    $descripcion = $_POST['descripcion'];

    // Variables para los datos de la pareja
    $partnerFirstName = $_POST['partnerFirstName'];
    $partnerSecondName = $_POST['partnerSecondName'];
    $partnerFirstLastName = $_POST['partnerFirstLastName'];
    $partnerLastName = $_POST['partnerLastName'];
    $partnerVejp = $_POST['vejp'];
    $partnerCedula = $_POST['partnerCedula'];
    $partnerPhone = $_POST['partnerPhone'];
    $partnerDob = $_POST['partnerDob'];
    $partnerEmail = $_POST['partnerEmail'];
    $estado2 = $_POST['estado2'];
    $municipio2 = $_POST['municipio2'];
    $descripcion2 = $_POST['descripcion'];

    // Variables para los datos de la cita
    $partnerDate = $_POST['partnerDate'];
    $partnerTime = $_POST['partnerTime'] . ":00";
    $monto = $_POST['monto'];
    $hora_fin = $_POST['hora_fin'] . ":00"; // Agregar segundos para la hora de fin 
    $duracion = 45;

    // Inserción de la dirección del paciente
    $sql_direccion = "INSERT INTO direccion (ID_Estados, ID_Municipio, Descripcion)
                      VALUES ('$estado', '$municipio', '$descripcion')";
    if ($conn->query($sql_direccion) === TRUE) {
        $direccion_id = $conn->insert_id;

        // Inserción del paciente
        $sql_paciente = "INSERT INTO paciente (`Primer Nombre`, `Segundo Nombre`, `Primer Apellido`, `Segundo Apellido`, `Teléfono`, `Fecha_Nacimiento`, `Correo`, `ID_Direccion`, `Cedula`, `Tipo_Cedula`)
                         VALUES ('$firstName', '$secondName', '$firstLastName', '$lastName', '$phone', '$dob', '$email', '$direccion_id', '$cedula', '$vejp')";
        
        
        
        if ($conn->query($sql_paciente) === TRUE) {
            $paciente_id = $conn->insert_id;

            // Inserción de la dirección de la pareja
            $sql_direccion2 = "INSERT INTO direccion (ID_Estados, ID_Municipio, Descripcion)
                               VALUES ('$estado2', '$municipio2', '$descripcion2')";
            if ($conn->query($sql_direccion2) === TRUE) {
                $direccion_id2 = $conn->insert_id;

                // Inserción de la pareja
                $sql_pareja = "INSERT INTO cita_pareja (`Primer Nombre1`, `Segundo Nombre1`, `Primer Apellido1`, `Segundo Apellido1`, `Telefono1`, `Fecha_Nacimiento1`, `Correo1`, `ID_Direccion1`, `Cedula1`, `Tipo_Cedula`)
                               VALUES ('$partnerFirstName', '$partnerSecondName', '$partnerFirstLastName', '$partnerLastName', '$partnerPhone', '$partnerDob', '$partnerEmail', '$direccion_id2', '$partnerCedula', '$partnerVejp')";
                
                
                
                if ($conn->query($sql_pareja) === TRUE) {
                    $pareja_id = $conn->insert_id;

                    // Inserción de la fecha de la cita
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
    // Si la fecha ya está registrada
    exit(); 
}
                    if ($conn->query($sql_fecha) === TRUE) {
                        $fecha_id = $conn->insert_id;

                        // Inserción de la cita
                        $sql_cita = "INSERT INTO cita (ID_Fecha, Hora, Hora_Fin, Monto, Id_TipoCita, ID_Paciente, ID_Pareja, ID_Psicologa, ID_Login)
                                     VALUES ('$fecha_id', '$partnerTime', '$hora_fin',  '$monto', 3, '$paciente_id', '$pareja_id', 1, '$user_id')";
                        // Validar formato de la hora de inicio
if (!preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $partnerTime)) {
    exit();
}

$nueva_cita_inicio = DateTime::createFromFormat('H:i:s', $partnerTime);
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
$sql_existentes = "SELECT `Hora` FROM `cita` WHERE `ID_Fecha` = '$fecha_id' AND `ID_Psicologa` = '$psicologa_id'";
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
                    echo "Error al insertar los datos de la pareja: " . $conn->error;
                }
            } else {
                echo "Error al insertar la dirección de la pareja: " . $conn->error;
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
