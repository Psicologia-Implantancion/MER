<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php#login-form");
    exit();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Recupera el ID del usuario desde la sesión
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
    $hora_atencion = $_POST['hora_atencion'] . ":00";
    $hora_fin = $_POST['hora_fin'] . ":00"; // Agregar segundos para la hora de fin 
    $duracion = 45;  



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

                              // Comprobar si la fecha ya está registrada
                              $sql = "SELECT COUNT(*) AS count FROM fecha WHERE Dia = ? AND Status = 1";
                              $stmt = $conn->prepare($sql);
                              
                              // Verificar si la preparación de la consulta fue exitosa
                              if ($stmt === false) {
                                  die("Error en la preparación de la consulta: " . $conn->error);
                              }
                              
                              // Vincular el parámetro
                              $stmt->bind_param("s", $fecha_cita);
                              
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

                    
                    $sql_cita = "INSERT INTO cita (`ID_Fecha`, `Hora`,`Hora_Fin` , `Monto`, `Id_TipoCita`, `ID_Paciente`, `ID_Psicologa`,`ID_Login`)
                                           VALUES ('$fecha_id', '$hora_atencion', '$hora_fin', '$monto', '$tipo_cita_id', '$paciente_id', '$Psicologa_id', '$user_id')";       
                    // Validar formato de la hora de inicio
if (!preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $hora_atencion)) {
    exit();
}

$nueva_cita_inicio = DateTime::createFromFormat('H:i:s', $hora_atencion);
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


