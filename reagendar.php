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
        $nueva_hora = $_POST['hora']. ":00";
        $nueva_hfin = $_POST['hora_fin']. ":00";
        $duracion = 45;

        $sql_fecha = "UPDATE fecha SET Dia = '$nueva_fecha' WHERE Id_Fecha = '$id_fecha'";
        if (mysqli_query($conn, $sql_fecha)) {
            
            $sql_cita = "UPDATE cita SET Hora = '$nueva_hora', Hora_Fin = '$nueva_hfin',  Status = 'Reagendada' WHERE Id_Cita = '$id_cita'";
            
            // Validar formato de la hora de inicio
if (!preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $nueva_hora)) {
    echo '
    <script>
        alert("La hora de inicio debe estar en el formato HH:MM:SS.");
        window.history.back();
    </script>
    ';
    exit();
}

$nueva_cita_inicio = DateTime::createFromFormat('H:i:s', $nueva_hora);
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
$sql_existentes = "SELECT `Hora` FROM `cita` WHERE `ID_Fecha` = '$id_fecha' AND `ID_Psicologa` = '1'";
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
        <input type="time" id="hora" name="hora" required required onchange="actualizarHoraFin()">
        <label for="hora_fin">Hora de Fin *</label>
        <input type="time" id="hora_fin" name="hora_fin" readonly>
      
        <button type="submit">Reagendar</button>
    </form>
</body>
</html>

<script>
function esHoraValida(hora) {
            const [hours, minutes] = hora.split(':').map(Number);
            // Validar si está en el intervalo permitido
            if ((hours >= 8 && hours < 12) || (hours >= 13 && hours < 16)) {
                return true;
            }
            return false;
        }

        function validarHora() {
            const horaAtencion = document.getElementById('hora').value;
            if (horaAtencion && !esHoraValida(horaAtencion)) {
                alert('La hora de atención debe estar entre 08:00-12:00 o 13:00-16:00.');
                document.getElementById('hora').value = '';
                document.getElementById('hora_fin').value = '';
            } else {
                actualizarHoraFin();
            }
        }

        function actualizarHoraFin() {
            const horaAtencion = document.getElementById('hora').value;
            if (horaAtencion) {
                const [hours, minutes] = horaAtencion.split(':');
                const date = new Date();
                date.setHours(parseInt(hours));
                date.setMinutes(parseInt(minutes) + 45);
                date.setSeconds(0);

                const horaFin = date.toTimeString().substring(0, 5);
                document.getElementById('hora_fin').value = horaFin;
            } else {
                document.getElementById('hora_fin').value = '';
            }
        }

        </script>