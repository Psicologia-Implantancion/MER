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
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "emocionvital";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $Psicologa_id = 1;
    $tipo_cita_id = 1; 

    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $cedula = $_POST['cedula'];
    $vejp = $_POST['vejp'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $correo = $_POST['correo'];
    $estado_id = $_POST['estado'];
    $municipio_id = $_POST['municipio'];
    $descripcion_direccion = $_POST['descripcion'];
    $fecha_cita = $_POST['fecha_cita'];
    $hora_atencion = $_POST['hora_atencion'];
    $monto = $_POST['monto'];

   

        $sql_direccion = "INSERT INTO direccion (`ID_Estados`, `ID_Municipio`, `Descripcion`)
                          VALUES ('$estado_id', '$municipio_id', '$descripcion_direccion')";

        if ($conn->query($sql_direccion) === TRUE) {
            $direccion_id = $conn->insert_id;

            $sql_paciente = "INSERT INTO paciente (`Primer Nombre`, `Segundo Nombre`, `Primer Apellido`, `Segundo Apellido`, `Teléfono`, `Fecha_Nacimiento`, `Correo`, `ID_Direccion`, `Cedula`, `Tipo_Cedula`)
                                           VALUES ('$primer_nombre', '$segundo_nombre', '$primer_apellido', '$segundo_apellido', '$telefono', '$fecha_nacimiento', '$correo', '$direccion_id', '$cedula', '$vejp')";

            if ($conn->query($sql_paciente) === TRUE) {
                $paciente_id = $conn->insert_id;

                $sql_fecha = "INSERT INTO fecha (`Dia`)
                              VALUES ('$fecha_cita')";

                if ($conn->query($sql_fecha) === TRUE) {
                    $fecha_id = $conn->insert_id;

                    
                    $sql_cita = "INSERT INTO cita (`ID_Fecha`, `Hora`, `Monto`, `Id_TipoCita`, `ID_Paciente`, `ID_Psicologa`,`ID_Login`)
                                           VALUES ('$fecha_id', '$hora_atencion', '$monto', '$tipo_cita_id', '$paciente_id', '$Psicologa_id', '$user_id')";       
       
                    if ($conn->query($sql_cita) === TRUE) {
                        header("Location: index.php#agendar");
                        exit();
                    } else {
                        echo "Error al insertar cita: " . $conn->error;
                    }
                } else {
                    echo "Error al insertar fecha: " . $conn->error;
                }
            } else {
                echo "Error al insertar paciente: " . $conn->error;
            }
        } else {
            echo "Error al insertar dirección: " . $conn->error;
        }
   

    
    $conn->close();
} else {
    
    header("Location: index.php");
    exit();
}
?>


