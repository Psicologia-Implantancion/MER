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
    $partnerTime = $_POST['partnerTime'];
    $monto = $_POST['monto'];

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
                    if ($conn->query($sql_fecha) === TRUE) {
                        $fecha_id = $conn->insert_id;

                        // Inserción de la cita
                        $sql_cita = "INSERT INTO cita (ID_Fecha, Hora, Monto, Id_TipoCita, ID_Paciente, ID_Pareja, ID_Psicologa, ID_Login)
                                     VALUES ('$fecha_id', '$partnerTime', '$monto', 3, '$paciente_id', '$pareja_id', 1, '$user_id')";
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
