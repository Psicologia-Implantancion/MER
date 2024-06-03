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
        
        // Validación de primer nombre y segundo nombre 
    if (!preg_match('/^[a-zA-Z ]+$/', $_POST['firstName']) || !preg_match('/^[a-zA-Z ]+$/', $_POST['secondName'])) { 
    echo '<script>
    alert("El primer nombre y segundo nombre solo pueden contener letras y espacios.");
    window.history.back(); 
  </script>
  ';
   exit();
} 

       // Validación de primer apellido y segundo apellido 
    if (!preg_match('/^[a-zA-Z ]+$/', $_POST['firstLastName']) || !preg_match('/^[a-zA-Z ]+$/', $_POST['secondLastName'])) { 
    echo '<script>
    alert("El primer apellido y segundo apellido solo pueden contener letras y espacios.");
    window.history.back(); 
  </script>
  ';
   exit();
} 

// Validación para teléfono 
if (!is_numeric($_POST['phone'])) {  
    echo '
        <script>
            alert("El teléfono ingresado no es válido");
            window.history.back(); 
        </script> 
    ';
    exit();
}

// Validación de la fecha de nacimiento 
$dob = $_POST['birthDate']; 
$hoy = date("Y-m-d"); 
 
if ($dob>= $hoy) { 
    echo '<script>
    alert("La fecha de nacimiento debe ser anterior a la fecha actual.");
    window.history.back(); 
  </script>
  ';
   exit(); 
} 
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    if($correo_valido && strpos($correo_valido, "@gmail.com") == false || $correo_valido && strpos($correo_valido, "@hotmail.com") == false){
        echo '
        <script>
            alert("Error al guardar el correo electrónico");
            window.history.back(); 
        </script>
    ';
    exit;
    }
   

   // Validación de formato para el campo de cédula 
if (!preg_match('/^[0-9]+$/', $_POST['idNumber'])) { 
    echo'<script>
    alert("La cédula debe contener solo números");
    window.history.back(); 
</script> 
';
exit(); 
}     

   

        
    if (!preg_match('/^\d+$/', $Nhijo)) {
        echo'<script>
            alert("El número de hijos debe ser un número");
            window.history.back(); 
        </script> 
        ';
    exit();
    }
        
        
        
        if ($conn->query($sql_paciente) === TRUE) {
            $paciente_id = $conn->insert_id;

            $sql_pareja = "INSERT INTO citamenor (`1erNombreInfante`, `2doNombreInfante`, `1erApellidoInfante`, `2doApellidoInfante`, `fecha nacimiento`)
                           VALUES ('$childFirstName', '$childSecondName', '$childFirstLastName', '$childSecondLastName', '$childBirthDate')";

       // Validación de primer nombre y segundo nombre 
        if (!preg_match('/^[a-zA-Z ]+$/', $_POST['childFirstName']) || !preg_match('/^[a-zA-Z ]+$/', $_POST['childSecondName'])) { 
            echo '<script>
            alert("El primer nombre del niño y segundo nombre del niño solo pueden contener letras y espacios.");
            window.history.back(); 
          </script>
          ';
           exit();
    } 
    
           // Validación de primer apellido y segundo apellido 
        if (!preg_match('/^[a-zA-Z ]+$/', $_POST['childFirstLastName']) || !preg_match('/^[a-zA-Z ]+$/', $_POST['childSecondLastName'])) { 
            echo '<script>
            alert("El primer apellido del niño y segundo apellido del niño solo pueden contener letras y espacios.");
            window.history.back(); 
          </script>
          ';
           exit();
    }
    // Validación de la fecha de nacimiento 
    $childBirthDate = $_POST['childBirthDate']; 
        $hoy = date("Y-m-d"); 
 
       if ($childBirthDate>= $hoy) { 
       echo'
        <script>
            alert("La fecha de nacimiento debe ser anterior a la fecha actual.");
            window.history.back(); 
        </script> 
        ';
    exit();
    }  





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
    // Si la fecha ya está registrada
    echo '
    <script>
        alert("La fecha ya se encuentra registrada y no está disponible.");
        window.history.back(); 
    </script>
    '; 
    exit(); 
}


                if ($conn->query($sql_fecha) === TRUE) {
                    $fecha_id = $conn->insert_id;

                    $sql_cita = "INSERT INTO cita (ID_Fecha, Hora, Hora_Fin, Monto, Id_TipoCita, ID_Paciente, ID_CitaMenor, ID_Psicologa, ID_Login)
                                 VALUES ('$fecha_id', '$appointmentTime', '$hora_fin', '$amount', 2, '$paciente_id', '$menor_id', 1, '$user_id')";
                                 // Validar formato de la hora de inicio
if (!preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $appointmentTime)) {
    echo '
    <script>
        alert("La hora de inicio debe estar en el formato HH:MM:SS.");
        window.history.back();
    </script>
    ';
    exit();
}

$nueva_cita_inicio = DateTime::createFromFormat('H:i:s', $appointmentTime);
if (!$nueva_cita_inicio) {
    echo '
    <script>
        alert("La hora de inicio no es válida.");
        window.history.back();
    </script>
    ';
    exit();
}

// Validar que la hora de inicio esté en los intervalos permitidos
$hora_inicio = (int)$nueva_cita_inicio->format('H');
if (!($hora_inicio >= 8 && $hora_inicio < 12) && !($hora_inicio >= 13 && $hora_inicio < 16)) {
    echo '
    <script>
        alert("La hora de atención debe estar entre 08:00-12:00 o 13:00-16:00.");
        window.history.back();
    </script>
    ';
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
            echo '
            <script>
                alert("La cita se solapa con una existente.");
                window.history.back();
            </script>
            ';
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
