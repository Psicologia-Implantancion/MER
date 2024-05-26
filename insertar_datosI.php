<?php
session_start();

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

    // Variables para los datos del formulario
    $firstName = $_POST['firstName'];
    $secondName = $_POST['secondName'];
    $firstLastName = $_POST['firstLastName'];
    $secondLastName = $_POST['secondLastName'];
    $vejp = $_POST['vejp'];
    $cedula = $_POST['idNumber'];
    $phone = $_POST['phone'];
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
    $appointmentTime = $_POST['appointmentTime'];
    $amount = $_POST['monto'];

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
                if ($conn->query($sql_fecha) === TRUE) {
                    $fecha_id = $conn->insert_id;

                    $sql_cita = "INSERT INTO cita (ID_Fecha, Hora, Monto, Id_TipoCita, ID_Paciente, ID_CitaMenor, ID_Psicologa, ID_Login)
                                 VALUES ('$fecha_id', '$appointmentTime', '$amount', 2, '$paciente_id', '$menor_id', 1, '$user_id')";
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
