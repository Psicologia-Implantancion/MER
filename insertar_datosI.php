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
    $appointmentTime = $_POST['appointmentTime'];
    $amount = $_POST['monto'];

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
if (!preg_match('/^[0-9]{4}-[0-9]{7}$/', $phone)) {
    echo '
        <script>
            alert("El teléfono ingresado no es válido, el formato debe ser parecido al siguiente 0123-4567890");
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
    $verif_correo = mysqli_query($conn, "SELECT COUNT(*) FROM paciente WHERE Correo LIKE '$email'");
    if (mysqli_num_rows($verif_correo) > 0){
        echo'
            <script>
                alert("El correo proporcionado ya esta registrado");
                window.history.back(); 
            </script> 
            ';
        exit();
    }

   // Validación de formato para el campo de cédula 
if (!preg_match('/^[0-9]+$/', $_POST['idNumber'])) { 
    echo "La cédula debe contener solo números."; 
    exit(); 
}     

switch($vejp){
    case "V":
        if($cedula < 1 || $cedula > 99999999 || !preg_match('/[0-9]{9}$/', $cedula)){
            echo'
            <script>
                alert("Error en la cedula");
                window.history.back(); 
            </script> 
            ';
            exit();
        }
    break;
    case "E":
        if($cedula < 1 || $cedula > 999999999 || !preg_match('/[0-9]{10}$/', $cedula)){
            echo'
            <script>
                alert("Error en la cedula");
                window.history.back(); 
            </script> 
            ';
            exit();
        }
    break;
    case "J":
        if($cedula < 1 || $cedula > 9999999 || !preg_match('/[0-9]{7}$/', $cedula)){
            echo'
            <script>
                alert("Error en la cedula");
                window.history.back(); 
            </script> 
            ';
            exit();
        }
    break;
}
    
$verif_cedula = mysqli_query($con, "SELECT COUNT(*) FROM paciente WHERE Cedula LIKE '$cedula'");
if (mysqli_num_rows($verif_cedula) > 0){
    echo'
        <script>
            alert("El correo proporcionado ya esta registrado");
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
        echo "El primer nombre y segundo nombre solo pueden contener letras y espacios."; 
        exit(); 
    } 
    
           // Validación de primer apellido y segundo apellido 
        if (!preg_match('/^[a-zA-Z ]+$/', $_POST['childFirstLastName']) || !preg_match('/^[a-zA-Z ]+$/', $_POST['childSecondLastName'])) { 
        echo "El primer apellido y segundo apellido solo pueden contener letras y espacios."; 
        exit(); 
    }
    // Validación de la fecha de nacimiento 
    $childBirthDate = $_POST['childBirthDate']; 
        $hoy = date("Y-m-d"); 
 
       if ($childBirthDate>= $hoy) { 
       echo "La fecha de nacimiento debe ser anterior a la fecha actual."; 
       exit(); 
    }  





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
