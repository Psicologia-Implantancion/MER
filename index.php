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

function isUserLoggedIn($conn) {
    return isset($_SESSION['username']);
}

function redirectToLogin() {
    header("Location: index.php#login-form");
    exit();
}

function logout() {
    session_unset();
    session_destroy();
    header("Location: index.php#login-form");
    exit();
}

function getUserType($conn, $username) {
    $sql = "SELECT Tipo FROM login WHERE Usuario='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        return $row["Tipo"];
    } else {
        return null;
    }
}

function isPsicologo($conn) {
    $username = $_SESSION['username'];
    $userType = getUserType($conn, $username);
    return $userType === 'Psicologo';
}

function isAdmin($conn) {
    $username = $_SESSION['username'];
    $userType = getUserType($conn, $username);
    return $userType === 'Administrador';
}

function loadMunicipiosOptions($conn) {
    $sql = "SELECT municipios.id_municipio, municipios.municipio, estados.estado 
            FROM municipios 
            INNER JOIN estados ON municipios.id_estado = estados.id_estado";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $municipio_options = "<option value=''>Seleccione...</option>";
        while ($row = $result->fetch_assoc()) {
            $municipio_id = $row["id_municipio"];
            $municipio = $row["municipio"];
            $estado = $row["estado"];
            $municipio_options .= "<option value='$municipio_id'>$municipio ($estado)</option>";
        }
    } else {
        $municipio_options = "<option value=''>No hay Municipios disponibles</option>";
    }

    return $municipio_options;
}

function loadEstadosOptions($conn) {
    $sql = "SELECT id_estado, estado FROM estados";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $estado_options = "<option value=''>Seleccione...</option>";
        while ($row = $result->fetch_assoc()) {
            $estado_id = $row["id_estado"];
            $estado_nombre = $row["estado"];
            $estado_options .= "<option value='$estado_id'>$estado_nombre</option>";
        }
    } else {
        $estado_options = "<option value=''>No hay estados disponibles</option>";
    }

    return $estado_options;
}

function loadTipoCedulaOptions($conn) {
    $sql = "SHOW COLUMNS FROM paciente LIKE 'Tipo_Cedula'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $enum_values = explode(",", str_replace("'", "", substr($row['Type'], 5, -1)));

        $options = "";
        foreach ($enum_values as $value) {
            $options .= "<option value=\"$value\">$value</option>";
        }
    } else {
        $options = "<option value=''>Error al obtener valores del enum</option>";
    }

    return $options;
}

function getPrecioCitaAdulto($conn) {
    $sql_tipo_cita_adulto = "SELECT Precio FROM tipo_cita WHERE Tipo = 'Adulto'";
    $result_tipo_cita_adulto = $conn->query($sql_tipo_cita_adulto);

    if ($result_tipo_cita_adulto->num_rows > 0) {
        $row_tipo_cita_adulto = $result_tipo_cita_adulto->fetch_assoc();
        return $row_tipo_cita_adulto["Precio"];
    } else {
        return 0; 
    }
}

function getPrecioCitaPareja($conn) {
    $sql_tipo_cita_Pareja = "SELECT Precio FROM tipo_cita WHERE Tipo = 'Pareja'";
    $result_tipo_cita_Pareja = $conn->query($sql_tipo_cita_Pareja);

    if ($result_tipo_cita_Pareja->num_rows > 0) {
        $row_tipo_cita_Pareja = $result_tipo_cita_Pareja->fetch_assoc();
        return $row_tipo_cita_Pareja["Precio"];
    } else {
        return 0; 
    }
}

function getPrecioCitaInfante($conn) {
    $sql_tipo_cita_Infante = "SELECT Precio FROM tipo_cita WHERE Tipo = 'Infantil'";
    $result_tipo_cita_Infante = $conn->query($sql_tipo_cita_Infante);

    if ($result_tipo_cita_Infante->num_rows > 0) {
        $row_tipo_cita_Infante = $result_tipo_cita_Infante->fetch_assoc();
        return $row_tipo_cita_Infante["Precio"];
    } else {
        return 0; 
    }
}

function registerUser($conn, $email, $username, $password, $password_repeat) {
    if ($password != $password_repeat) {
        echo '<script>
                alert("Las contraseñas no coinciden");
                window.history.back(); // Volver a la página anterior
              </script>';
        exit();
    }

    $password_md5 = md5($password);

    $check_user_sql = "SELECT * FROM login WHERE Usuario='$username'";
    $result = $conn->query($check_user_sql);

    if ($result->num_rows == 0) {
        $sql = "INSERT INTO login (Usuario, Password, Correo) VALUES ('$username', '$password_md5', '$email')";

        if ($conn->query($sql) === TRUE) {
            echo '<script>
        alert("Registro existoso");
        window.history.back(); // Volver a la página anterior
      </script>';
        } else {
            return "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo '<script>
        alert("Nombre de usuario en uso");
        window.history.back(); // Volver a la página anterior
      </script>';
        exit();
    }
}

function loginUser($conn, $user, $password) {
    $password_md5 = md5($password);

    $sql = "SELECT ID_Login, Usuario FROM login WHERE Usuario='$user' AND Password='$password_md5'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();  

        $_SESSION['username'] = $row['Usuario'];
        $_SESSION['user_id'] = $row['ID_Login'];  

        $user_id = $row['ID_Login'];

        $user_type = getUserType($conn, $row['Usuario']);
        $_SESSION['user_type'] = $user_type ? $user_type : 'Usuario';

        echo "El ID del usuario es: " . $user_id;

        header("Location: index.php#agendar");
        exit();
    } else {
        return "Usuario no encontrado o contraseña incorrecta";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    logout();
}

$error_message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password_repeat = $_POST['password_repeat'];

        $error_message = registerUser($conn, $email, $username, $password, $password_repeat);
    } elseif (isset($_POST['login'])) {
        $user = $_POST['user'];
        $password = $_POST['password'];

        $error_message = loginUser($conn, $user, $password);
    }
}




?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DB Krato</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .hide {
      display: none;
    }
  </style>
</head>
<div>
<div id="header-nav">
<?php if (isUserLoggedIn($conn)): ?>
    <nav class="header-nav">
        <!-- Menú para usuarios autenticados -->
        <ul>
            <?php if ($_SESSION['user_type'] !== 'Administrador'): ?> <!-- Verificar si no es un administrador -->
                <li class="dropdown">
                    <a href="#" class="dropbtn">Agendar cita</a>
                    <div class="dropdown-content">
                        <a href="#" onclick="showForm('individual-form')">Individual</a>
                        <a href="#" onclick="showForm('partner-form')">Pareja</a>
                        <a href="#" onclick="showForm('infant-form')">Infante</a>
                    </div>
                </li>
                <?php if ($_SESSION['user_type'] !== 'Psicologo'): ?> <!-- Mostrar solo si no es un Psicologo -->
                    <li><a href="#" onclick="showAgendadas()">Citas Agendadas</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($_SESSION['user_type'] === 'Psicologo'): ?>
                    <li><a href="#" onclick="showhistorial('historial-form')">Historial Medico</a></li>
                    <li><a href="#" onclick="showlista('Lista-de-citas')">Lista de citas</a></li>
                <?php endif; ?>
            <li>
                <form id="logout-form" method="post">
                    <input type="hidden" name="logout">
                    <a href="#" id="logout-link" onclick="document.getElementById('logout-form').submit()">Cerrar Sesión</a>
                </form>
            </li>
        </ul>
    </nav>
<?php else: ?>
    <!-- Menú para usuarios no autenticados -->
    <header>
        <nav>
            <ul>
                <li><a href="#" id="login-link">Iniciar sesión</a></li>
                <li><a href="#" id="register-link">Registrarse</a></li>
            </ul>
        </nav>
    </header>
<?php endif; ?>


<div id="login-form" class="container hide">
  <div class=form>
    <h2>Iniciar Sesión</h2>
    <form method="post">
      <label for="user">Usuario</label>
      <input type="text" id="user" name="user" required>

      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" required>

      <input type="submit" name="login" value="Ingresar">
    </form>
    <a id="reset-pass-link" class="reset-container">
      <label class="reset-pass-label" for="reset_pass">Recuperar Contraseña</label>
    </a>
  </div>
</div>

<div id="register-form" class="container hide">
  <div class="form">
    <h2>Registro</h2>
    <form method="post">
      <label for="email">Correo electrónico</label>
      <input type="email" id="email" name="email" required>

      <label for="username">Nombre de usuario</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" required>
      
      <label for="password_repeat">Repetir contraseña</label>
      <input type="password" id="password_repeat" name="password_repeat" required>

      <input type="submit" name="register" value="Registrarse">
    </form>
  </div>
</div>

<div id="individual-form" class="container hide">
    <form method="POST" action="insertar_datos.php">
        <div class="section-title">Datos Del Paciente</div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="firstName">Primer Nombre *</label>
                <input type="text" id="firstName" name="primer_nombre" required>
            </div>
            <div class="input-field half-width">
                <label for="secondName">Segundo Nombre *</label>
                <input type="text" id="secondName" name="segundo_nombre">
            </div>
        </div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="firstLastName">Primer Apellido *</label>
                <input type="text" id="firstLastName" name="primer_apellido" required>
            </div>
            <div class="input-field half-width">
                <label for="lastName">Segundo Apellido *</label>
                <input type="text" id="lastName" name="segundo_apellido">
            </div>
        </div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="vejp">VEJP</label>
                <select id="vejp" name="vejp">
                    <?php echo loadTipoCedulaOptions($conn); ?>
                </select>
            </div>
            <div class="input-field half-width">
                <label for="cedula">Cédula *</label>
                <input type="text" id="cedula" name="cedula">
            </div>
        </div>
        <div class="input-field full-width">
            <label for="phone">Teléfono *</label>
            <input type="tel" id="phone" name="telefono" required>
        </div>
        <div class="input-field full-width">
            <label for="dob">Fecha de Nacimiento *</label>
            <input type="date" id="dob" name="fecha_nacimiento" required>
        </div>
        <div class="input-field full-width">
            <label for="email">Correo *</label>
            <input type="email" id="email" name="correo" required>
        </div>
        <div class="input-field half-width">
            <label for="state">Estado *</label>
            <select id="state" name="estado" required onchange="showMunicipios()">
                <?php echo loadEstadosOptions($conn); ?>
            </select>
        </div>
        <div class="input-field half-width">
            <label for="city">Municipio *</label>
            <select id="city" name="municipio" required disabled>
                <?php echo loadMunicipiosOptions($conn); ?>
            </select>
        </div>
        <div class="input-field full-width">
            <label for="direccion">Descripción de dirección:</label>
            <input type="text" id="direccion" name="descripcion" placeholder="Ingrese una descripción de su dirección">
        </div>
        <div class="section-title">Datos de la cita</div>
        <div class="input-field full-width">
            <label for="date">Fecha de la Cita *</label>
            <input type="date" id="date" name="fecha_cita" required>
        </div>
        <div class="input-field full-width">
            <label for="hora_atencion">Hora de Atención (45min) *</label>
            <input type="time" id="hora_atencion" name="hora_atencion" required onchange="actualizarHoraFin()">
        </div>
        <div class="input-field full-width">
            <label for="hora_fin">Hora de Fin *</label>
            <input type="time" id="hora_fin" name="hora_fin" readonly>
        </div>
        <div class="input-field full-width">
            <label for="price">Monto *</label>
            <input type="text" id="price" name="monto" value="<?php echo getPrecioCitaAdulto($conn); ?>" required readonly>
        </div>
        <button type="submit">Agendar</button>
    </form>
</div>


<div id="partner-form" class="container hide">
    <form method="POST" action="insertar_datosP.php">
        <div class="section-title">Datos Del Paciente</div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="firstName">Primer Nombre *</label>
                <input type="text" id="firstName" name="firstName">
            </div>
            <div class="input-field half-width">
                <label for="secondName">Segundo Nombre *</label>
                <input type="text" id="secondName" name="secondName">
            </div>
        </div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="firstLastName">Primer Apellido *</label>
                <input type="text" id="firstLastName" name="firstLastName">
            </div>
            <div class="input-field half-width">
                <label for="lastName">Segundo Apellido *</label>
                <input type="text" id="lastName" name="lastName">
            </div>
        </div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="vejp">VEJP</label>
                <select id="vejp" name="vejp">
                    <?php echo loadTipoCedulaOptions($conn); ?>
                </select>
            </div>
            <div class="input-field half-width">
                <label for="cedula">Cédula *</label>
                <input type="text" id="cedula" name="cedula">
            </div>
        </div>
        <div class="input-field full-width">
            <label for="phone">Teléfono *</label>
            <input type="tel" id="phone" name="phone">
        </div>
        <div class="input-field full-width">
            <label for="dob">Fecha de Nacimiento *</label>
            <input type="date" id="dob" name="dob">
        </div>
        <div class="input-field full-width">
            <label for="email">Correo *</label>
            <input type="email" id="email" name="email">
        </div>
        <div class="input-field half-width">
            <label for="state_partner">Estado *</label>
            <select id="state_partner" name="estado" required onchange="showMunicipiosPartner()">
                <?php echo loadEstadosOptions($conn); ?>
            </select>
        </div>
        <div class="input-field half-width">
            <label for="city_partner">Municipio *</label>
            <select id="city_partner" name="municipio" required disabled>
                <?php echo loadMunicipiosOptions($conn); ?>
            </select>
        </div>
        <div class="input-field full-width">
            <label for="direccion">Descripción de dirección:</label>
            <input type="text" id="direccion" name="descripcion" placeholder="Ingrese una descripción de su dirección">
        </div>
        <div class="section-title">Datos de la Pareja</div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="partnerFirstName">Primer Nombre *</label>
                <input type="text" id="partnerFirstName" name="partnerFirstName">
            </div>
            <div class="input-field half-width">
                <label for="partnerSecondName">Segundo Nombre *</label>
                <input type="text" id="partnerSecondName" name="partnerSecondName">
            </div>
        </div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="partnerFirstLastName">Primer Apellido *</label>
                <input type="text" id="partnerFirstLastName" name="partnerFirstLastName">
            </div>
            <div class="input-field half-width">
                <label for="partnerLastName">Segundo Apellido *</label>
                <input type="text" id="partnerLastName" name="partnerLastName">
            </div>
        </div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="vejp">VEJP</label>
                <select id="vejp" name="vejp">
                    <?php echo loadTipoCedulaOptions($conn); ?>
                </select>
            </div>
            <div class="input-field half-width">
                <label for="partnerCedula">Cédula *</label>
                <input type="text" id="partnerCedula" name="partnerCedula">
            </div>
        </div>
        <div class="input-field full-width">
            <label for="partnerPhone">Teléfono *</label>
            <input type="tel" id="partnerPhone" name="partnerPhone">
        </div>
        <div class="input-field full-width">
            <label for="partnerDob">Fecha de Nacimiento *</label>
            <input type="date" id="partnerDob" name="partnerDob">
        </div>
        <div class="input-field full-width">
            <label for="partnerEmail">Correo *</label>
            <input type="email" id="partnerEmail" name="partnerEmail">
        </div>
        <div class="input-field half-width">
            <label for="state">Estado *</label>
            <select id="state_p" name="estado2" required onchange="showMunicipiosP()">
                <?php echo loadEstadosOptions($conn); ?>
            </select>
        </div>
        <div class="input-field half-width">
            <label for="city">Municipio *</label>
            <select id="city_p" name="municipio2" required disabled>
                <?php echo loadMunicipiosOptions($conn); ?>
            </select>
        </div>
        <div class="input-field full-width">
            <label for="direccion">Descripción de dirección:</label>
            <input type="text" id="direccion" name="descripcion" placeholder="Ingrese una descripción de su dirección">
        </div>
        <div class="section-title">Datos de la cita</div>
        <div class="input-field full-width">
            <label for="partnerDate">Fecha de la Cita *</label>
            <input type="date" id="partnerDate" name="partnerDate">
        </div>
        <div class="input-field full-width">
            <label for="partnerTime">Hora de Atención (45min) *</label>
            <input type="time" id="partnerTime_P" name="partnerTime" required onchange="actualizarHoraFinP()">
        </div>
        <div class="input-field full-width">
            <label for="hora_fin">Hora de Fin *</label>
            <input type="time" id="hora_fin_P" name="hora_fin" readonly>
        </div>
        <div class="input-field full-width">
            <label for="price">Monto *</label>
            <input type="text" id="price" name="monto" value="<?php echo getPrecioCitaPareja($conn); ?>" required readonly>
        </div>
        <button type="submit">Agendar</button>
    </form>
</div>

<div id="infant-form" class="container hide">
    <form class="form" method="POST" action="insertar_datosI.php">
        <div class="input-field">
            <label for="firstName">Primer Nombre *</label>
            <input type="text" id="firstName" name="firstName" required>
        </div>
        <div class="input-field">
            <label for="secondName">Segundo Nombre</label>
            <input type="text" id="secondName" name="secondName">
        </div>
        <div class="input-field">
            <label for="firstLastName">Primer Apellido *</label>
            <input type="text" id="firstLastName" name="firstLastName" required>
        </div>
        <div class="input-field">
            <label for="secondLastName">Segundo Apellido</label>
            <input type="text" id="secondLastName" name="secondLastName">
        </div>
        <div class="input-field half-width">
            <label for="vejp">VEJP</label>
            <select id="vejp" name="vejp">
                <?php echo loadTipoCedulaOptions($conn); ?>
            </select>
        </div>
        <div class="input-field half-width">
            <label for="idNumber">Cédula *</label>
            <input type="text" id="idNumber" name="idNumber" required>
        </div>
        <div class="input-field full-width">
            <label for="direccion">Numero de hijo:</label>
            <input type="text" id="Nhijo" name="Nhijo" >
        </div>
        <div class="input-field">
            <label for="phone">Teléfono *</label>
            <input type="tel" id="phone" name="phone" required>
        </div>
        <div class="input-field">
            <label for="birthDate">Fecha de Nacimiento *</label>
            <input type="date" id="birthDate" name="birthDate" required>
        </div>
        <div class="input-field">
            <label for="email">Correo *</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="input-field half-width">
            <label for="state">Estado *</label>
            <select id="state_i" name="estado" required onchange="showMunicipiosI()">
                <?php echo loadEstadosOptions($conn); ?>
            </select>
        </div>
        <div class="input-field half-width">
            <label for="city">Municipio *</label>
            <select id="city_i" name="municipio" required disabled>
                <?php echo loadMunicipiosOptions($conn); ?>
            </select>
        </div>
        <div class="input-field full-width">
            <label for="direccion">Descripción de dirección:</label>
            <input type="text" id="direccion" name="descripcion" placeholder="Ingrese una descripción de su dirección">
        </div>
        
        <div class="input-field">
            <label for="childFirstName">Primer Nombre del Niño</label>
            <input type="text" id="childFirstName" name="childFirstName">
        </div>
        <div class="input-field">
            <label for="childSecondName">Segundo Nombre del Niño</label>
            <input type="text" id="childSecondName" name="childSecondName">
        </div>
        <div class="input-field">
            <label for="childFirstLastName">Primer Apellido del Niño *</label>
            <input type="text" id="childFirstLastName" name="childFirstLastName" required>
        </div>
        <div class="input-field">
            <label for="childSecondLastName">Segundo Apellido del Niño</label>
            <input type="text" id="childSecondLastName" name="childSecondLastName">
        </div>
        <div class="input-field">
            <label for="childBirthDate">Fecha de Nacimiento del Niño *</label>
            <input type="date" id="childBirthDate" name="childBirthDate" required>
        </div>
        <div class="input-field full-width">
            <label for="partnerDate">Fecha de la Cita *</label>
            <input type="date" id="partnerDate" name="partnerDate">
        </div>
        <div class="input-field full-width">
            <label for="appointmentTime">Hora de Atención (45min) *</label>
            <input type="time" id="appointmentTime_I" name="appointmentTime" required onchange="actualizarHoraFinI()">
        </div>
        <div class="input-field full-width">
            <label for="hora_fin">Hora de Fin *</label>
            <input type="time" id="hora_fin_I" name="hora_fin" readonly>
        </div>
        <div class="input-field full-width">
            <label for="price">Monto *</label>
            <input type="text" id="price" name="monto" value="<?php echo getPrecioCitaInfante($conn); ?>" required readonly>
        </div>
        <div class="input-field full-width">
            <button type="submit">Agendar</button>
        </div>
    </form>
</div>



<div id="citas-agendadas" class="container hide">
  <form id="citas-form" method="post" action="modificar_cita.php">
    <h3>Citas Pendientes</h3>
    <table>
      <thead>
        <tr>
          <th>Núm.Citas</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Tipo</th>
          <th>Status</th>
          <th>Seleccionar</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];  
        }

        $sql = "SELECT c.Id_Cita, f.Id_Fecha, f.Dia AS Fecha, c.Hora, tc.Tipo AS Tipo, c.Status
                FROM cita c
                INNER JOIN fecha f ON c.Id_Fecha = f.Id_Fecha
                INNER JOIN tipo_cita tc ON c.Id_TipoCita = tc.Id_TipoCita
                WHERE c.ID_Login = '$user_id'";
        $result = mysqli_query($conn, $sql);

        $contador = 1;
        $contadorr = 1;
        $citasPendientes = '';
        $citasRealizadas = '';

        while ($mostrar = mysqli_fetch_array($result)) {
            if ($mostrar['Status'] === 'Realizada' || $mostrar['Status'] === 'Suspendida' ) {
                $citasRealizadas .= '<tr>
                                      <td>' . $contador++ . '</td>
                                      <td>' . $mostrar['Fecha'] . '</td>
                                      <td>' . $mostrar['Hora'] . '</td>
                                      <td>' . $mostrar['Tipo'] . '</td>
                                      <td>' . $mostrar['Status'] . '</td>
                                    </tr>';
            } else {
                $citasPendientes .= '<tr>
                                      <td>' . $contadorr++ . '</td>
                                      <td>' . $mostrar['Fecha'] . '</td>
                                      <td>' . $mostrar['Hora'] . '</td>
                                      <td>' . $mostrar['Tipo'] . '</td>
                                      <td>' . $mostrar['Status'] . '</td>
                                      <td><input type="radio" name="cita_seleccionada" value="' . $mostrar['Id_Cita'] . '" data-id-fecha="' . $mostrar['Id_Fecha'] . '"></td>
                                    </tr>';
            }
        }

        echo $citasPendientes;
        ?>
      </tbody>
    </table>
    <div class="button-container">
      <input type="radio" id="reagendar" name="accion" value="reagendar">
      <label for="reagendar">Reagendar</label>
      <input type="radio" id="cancelar" name="accion" value="cancelar">
      <label for="cancelar">Cancelar</label>
    </div>
    <button type="button" onclick="modificarCita()">Modificar</button>
  </form>

  <!-- Tabla para Citas Realizadas -->
  <h3>Citas Realizadas</h3>
  <table>
    <thead>
      <tr>
        <th>Núm.Citas</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Tipo</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      echo $citasRealizadas;
      ?>
    </tbody>
  </table>
</div>





<div id="Lista-de-citas" class="container hide">
    <h1>Lista de citas</h1>
    <div class="table-container">
        <table>
        <h3>Citas Pendientes</h3>
            <thead>
                <tr>
                    <th>Nr cita</th>
                    <th>Fecha</th>
                    <th>Hora de Inicio</th>
                    <th>Hora de Finalizacion</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Seleccionar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT c.Id_Cita, f.Dia AS Fecha, c.Hora, c.Hora_Fin, tc.Tipo AS Tipo, c.Status
                        FROM cita c
                        INNER JOIN fecha f ON c.Id_Fecha = f.Id_Fecha
                        INNER JOIN tipo_cita tc ON c.Id_TipoCita = tc.Id_TipoCita
                        WHERE c.ID_Psicologa = '1'";
                $result = mysqli_query($conn, $sql);

                $contador = 1;
                $contadorr = 1;
                $citasPendientes = '';
                $citasRealizadas = '';

                while ($mostrar = mysqli_fetch_array($result)) {
                    if ($mostrar['Status'] === 'Realizada' || $mostrar['Status'] === 'Suspendida') {
                        $citasPendientes .= '<tr>
                                          <td>' . $contador++ . '</td>
                                          <td>' . $mostrar['Fecha'] . '</td>
                                          <td>' . $mostrar['Hora'] . '</td>
                                          <td>' . $mostrar['Hora_Fin'] . '</td>
                                          <td>' . $mostrar['Tipo'] . '</td>
                                          <td>' . $mostrar['Status'] . '</td>
                                          
                                        </tr>';
                    } else {
                        $citasRealizadas .= '<tr>
                                          <td>' . $contadorr++ . '</td>
                                          <td>' . $mostrar['Fecha'] . '</td>
                                          <td>' . $mostrar['Hora'] . '</td>
                                          <td>' . $mostrar['Hora_Fin'] . '</td>
                                          <td>' . $mostrar['Tipo'] . '</td>
                                          <td>' . $mostrar['Status'] . '</td>
                                          <td><input type="radio" name="cita" class="cita-radio" data-id="' . $mostrar['Id_Cita'] . '"></td>
                                        </tr>';
                    }
                }

                echo $citasRealizadas;
                ?>
            </tbody>
        </table>
    </div>
    <div class="actions">
        <div>
            <label>Modificar Status</label>
        </div>
        <div>
            <input type="radio" id="aprobar" name="status" value="Agendada">
            <label for="aprobar">Aprobar</label>
            <input type="radio" id="cancelar" name="status" value="Suspendida">
            <label for="cancelar">Cancelar</label>
            <input type="radio" id="Realizada" name="status" value="Realizada">
            <label for="Realizada">Realizada</label>
        </div>
        <div>
            <button id="modificar-btn">Modificar</button>
        </div>
        <h3>Citas Realizadas</h3>
        <table>
    <thead>
      <tr>
        <th>Núm.Citas</th>
        <th>Fecha</th>
        <th>Hora de Inicio</th>
        <th>Hora de Finalizacion</th>
        <th>Tipo</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      echo $citasPendientes;
      
      ?>
    </tbody>
  </table>
    </div>
</div>


<div id="historial-form" class="container hide">
<body>
    <h1>HISTORIA CLÍNICA PSICOLÓGICA</h1>
    <form method="POST" action="insertarDatoshm.php">
   
        <div class="section">
            <h2>I. DATOS DE IDENTIFICACIÓN</h2>
            <div class="input-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="flex">
                <div class="input-group">
                    <label>Cédula:</label>
                    <input type="text" name="cedula" required>
                </div>
                <div class="input-group">
                    <label>Fecha de Nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" required>
                </div>
            </div>
            <div class="flex">
                <div class="input-group">
                    <label>Escolaridad:</label>
                    <input type="text" name="escolaridad">
                </div>
                <div class="input-group">
                    <label>Promedio:</label>
                    <input type="number" name="promedio" step="0.01">
                </div>
            </div>
            <div class="flex">
                <div class="input-group">
                    <label>Escuela:</label>
                    <input type="text" name="escuela">
                </div>
                <div class="input-group">
                    <label>Lugar que ocupa en la familia:</label>
                    <input type="text" name="lugar_familia">
                </div>
            </div>
            <div class="input-group">
                <label>Dirección:</label>
                <input type="text" name="direccion">
            </div>
            <div class="input-group">
                <label>Teléfono:</label>
                <input type="text" name="telefono">
            </div>
        </div>

       

        <div class="section">
            <h2>II. FACTORES QUE MOTIVAN A LA CONSULTA</h2>
            <div class="textarea-group">
                <label>Motivos de consulta:</label>
                <textarea name="motivos_consulta"></textarea>
            </div>
            <div class="input-group">
                <label>Referido por:</label>
                <input type="text" name="referido_por">
            </div>
            <div class="input-group">
                <label>Diagnóstico orgánico:</label>
                <input type="text" name="diagnostico_organico">
            </div>
            <div class="textarea-group">
                <label>Actitud de los padres ante el problema:</label>
                <textarea name="actitud_padres"></textarea>
            </div>
            <div class="textarea-group">
                <label>Estado emocional actual del Niño (a):</label>
                <textarea name="estado_emocional"></textarea>
            </div>
        </div>

        <div class="section">
            <h2>III. FACTORES FÍSICOS</h2>
            <div class="textarea-group">
                <label>1. DESARROLLO PRENATAL Y NATAL:</label>
                <textarea name="desarrollo_prenatal"></textarea>
            </div>
            <div class="textarea-group">
                <label>2. DESARROLLO DE LA PRIMERA INFANCIA:</label>
                <textarea name="desarrollo_primera_infancia"></textarea>
            </div>
        </div>

        <div class="section">
            <h2>IV. FACTORES FAMILIARES</h2>
            <div class="input-group">
                <label>1. DATOS FAMILIARES:</label>
                <div class="flex">
                    <div class="input-group">
                        <label>Papá - Nombre:</label>
                        <input type="text" name="papa_nombre">
                    </div>
                    <div class="input-group">
                        <label>Papá - Apellido:</label>
                        <input type="text" name="papa_apellido">
                    </div>

                    <div class="input-group">
                        <label>Mamá - Nombre:</label>
                        <input type="text" name="mama_nombre">
                    </div>
                    <div class="input-group">
                        <label>Mamá - Apellido:</label>
                        <input type="text" name="mama_apellido">
                    </div>

                </div>
                <div class="flex">
                    <div class="input-group">
                        <label>Papá - Salud física:</label>
                        <input type="text" name="papa_salud_fisica">
                    </div>
                    <div class="input-group">
                        <label>Mamá - Salud física:</label>
                        <input type="text" name="mama_salud_fisica">
                    </div>
                </div>
                <div class="flex">
                    <div class="input-group">
                        <label>Papá - Nivel educativo:</label>
                        <input type="text" name="papa_nivel_educativo">
                    </div>
                    <div class="input-group">
                        <label>Mamá - Nivel educativo:</label>
                        <input type="text" name="mama_nivel_educativo">
                    </div>
                </div>
                <div class="flex">
                    <div class="input-group">
                        <label>Papá - Trabajo actual:</label>
                        <input type="text" name="papa_trabajo_actual">
                    </div>
                    <div class="input-group">
                        <label>Mamá - Trabajo actual:</label>
                        <input type="text" name="mama_trabajo_actual">
                    </div>
                </div>
                <div class="flex">
                    <div class="input-group">
                        <label>Papá - Horario de trabajo:</label>
                        <input type="text" name="papa_horario_trabajo">
                    </div>
                    <div class="input-group">
                        <label>Mamá - Horario de trabajo:</label>
                        <input type="text" name="mama_horario_trabajo">
                    </div>
                </div>
                <div class="flex">
                    <div class="input-group">
                        <label>Papá - Hábitos:</label>
                        <input type="text" name="papa_habitos">
                    </div>
                    <div class="input-group">
                        <label>Mamá - Hábitos:</label>
                        <input type="text" name="mama_habitos">
                    </div>
                </div>
            </div>

            <div class="textarea-group">
                <label>2. EXPERIENCIAS TRAUMÁTICAS DEL NIÑO:</label>
                <textarea name="experiencias_traumaticas"></textarea>
            </div>
            <div class="input-group">
                <label>Pérdida de algún familiar o ser querido:</label>
                <input type="text" name="perdida_familiar">
            </div>
            <div class="input-group">
                <label>¿Quién era?:</label>
                <input type="text" name="quien_era">
            </div>
            <div class="textarea-group">
                <label>¿Cómo fue?:</label>
                <textarea name="como_fue"></textarea>
            </div>
            <div class="input-group">
                <label>Edad que tenía el niño:</label>
                <input type="text" name="edad_nino">
            </div>
            <div class="input-group">
                <label>¿Presenció el suceso?:</label>
                <input type="text" name="presencio_suceso">
            </div>
            <div class="textarea-group">
                <label>Reacción del niño ante esto:</label>
                <textarea name="reaccion_nino"></textarea>
            </div>
            <div class="textarea-group">
                <label>Accidentes del niño:</label>
                <textarea name="accidentes_nino"></textarea>
            </div>
            <div class="textarea-group">
                <label>Castigos graves:</label>
                <textarea name="castigos_graves"></textarea>
            </div>
            <div class="input-group">
                <label>De parte de quién:</label>
                <input type="text" name="parte_quien">
            </div>
            <div class="input-group">
                <label>Edad del niño:</label>
                <input type="text" name="edad_castigos">
            </div>
            <div class="textarea-group">
                <label>Los problemas del niño son causados por:</label>
                <textarea name="problemas_causas"></textarea>
            </div>
            <div class="textarea-group">
                <label>Problemas físicos:</label>
                <textarea name="problemas_fisicos"></textarea>
            </div>
        </div>

        <div class="section">
            <h2>V. FACTORES DE LA PERSONALIDAD Y CONDUCTA</h2>
            <div class="textarea-group">
                <label>1. HÁBITOS E INTERESES</label>
            </div>
            <div class="textarea-group">
                <label>a) COMIDA:</label>
                <textarea name="comida"></textarea>
            </div>
            <div class="textarea-group">
                <label>b) SUEÑO:</label>
                <textarea name="sueno"></textarea>
            </div>
            <div class="textarea-group">
                <label>c) ELIMINACIONES:</label>
                <textarea name="eliminaciones"></textarea>
            </div>
            <div class="textarea-group">
                <label>d) MANÍAS Y TICS:</label>
                <textarea name="manias_tics"></textarea>
            </div>
            <div class="textarea-group">
                <label>e) HISTORIA SEXUAL:</label>
                <textarea name="historia_sexual"></textarea>
            </div>

            <div class="textarea-group">
                <label>2. RASGOS DE CARÁCTER</label>
            </div>
            <div class="checkbox-group">
                <label><input type="checkbox" name="timido" value="Timido"> Tímido</label>
                <label><input type="checkbox" name="agresivo" value="Agresivo"> Agresivo</label>
                <label><input type="checkbox" name="tranquilo" value="Tranquilo"> Tranquilo</label>
                <label><input type="checkbox" name="irritable" value="Irritable"> Irritable</label>
                <label><input type="checkbox" name="alegre" value="Alegre"> Alegre</label>
                <label><input type="checkbox" name="triste" value="Triste"> Triste</label>
                <label><input type="checkbox" name="cooperativo" value="Cooperador"> Cooperador</label>
                <label><input type="checkbox" name="negativista" value="Negativista"> Negativista</label>
                <label><input type="checkbox" name="sereno" value="Sereno"> Sereno</label>
                <label><input type="checkbox" name="impulsivo" value="Impulsivo"> Impulsivo</label>
                <label><input type="checkbox" name="confiado" value="Confiado"> Confiado en sí</label>
                <label><input type="checkbox" name="frio" value="Frio"> Frío</label>
                <label><input type="checkbox" name="sociable" value="Sociable"> Sociable</label>
                <label><input type="checkbox" name="retardado" value="Retardado"> Retardado</label>
                <label><input type="checkbox" name="equilibrado" value="Equilibrado"> Equilibrado</label>
                <label><input type="checkbox" name="nervioso" value="Nervioso"> Nervioso</label>
                <label><input type="checkbox" name="carinoso" value="Carinoso"> Cariñoso</label>
                <label><input type="checkbox" name="inseguro" value="Inseguro"> Inseguro</label>
                <label><input type="checkbox" name="juega" value="Juega"> Juega</label>
                <label><input type="checkbox" name="no_juega" value="No_juega"> No juega</label>
                <label><input type="checkbox" name="controlado" value="Controlado"> Controlado</label>
                <label><input type="checkbox" name="emotivo" value="Emotivo"> Emotivo</label>
                <label><input type="checkbox" name="seguro" value="Seguro"> Seguro</label>
                <label><input type="checkbox" name="amable" value="Amable"> Amable</label>
                <label><input type="checkbox" name="desconsiderado" value="Desconsiderado"> Desconsiderado</label>
                <label><input type="checkbox" name="laborioso" value="Laborioso"> Laborioso</label>
                <label><input type="checkbox" name="perezoso" value="Perezoso"> Perezoso</label>
                <label><input type="checkbox" name="desconfiado" value="Desconfiado"> Desconfiado</label>
                <label><input type="checkbox" name="dominante" value="Dominante"> Dominante</label>
                <label><input type="checkbox" name="sumiso" value="Sumiso"> Sumiso</label>
                <label><input type="checkbox" name="disciplinado" value="Disciplinado"> Disciplinado</label>
                <label><input type="checkbox" name="indisiplinado" value="Indisciplinado"> Indisciplinado</label>
                <label><input type="checkbox" name="rebelde" value="Rebelde"> Rebelde</label>
                <label><input type="checkbox" name="obediente" value="Obediente"> Obediente</label>
                <label><input type="checkbox" name="ordenado" value="Ordenado"> Ordenado</label>
                <label><input type="checkbox" name="desordenado" value="Desordenado"> Desordenado</label>
                
            </div>
            <label>Tendencias Destructivas:</label>
                <textarea name="tendencias_destructivas"></textarea>
            
        </div>


        <div class="section">
            <h2>VI. FACTORES HEREDITARIOS</h2>
            <div class="textarea-group">
                <label>INCIDENCIA DE ANOMALÍAS EN FAMILIARES CONSANGUÍNEOS:</label>
                <textarea name="incidencia_anomalias"></textarea>
            </div>
            <div class="textarea-group">
                <label>TRATAMIENTO MÉDICO POR NERVIOSISMO:</label>
                <textarea name="tratamiento_nerviosismo"></textarea>
            </div>
            <div class="textarea-group">
                <label>ALCOHOLISMO (GRADO), MANIFESTACIONES, ETC:</label>
                <textarea name="alcoholismo"></textarea>
            </div>
            <div class="textarea-group">
                <label>ABUSO DE DROGAS, CALMANTES, ETC:</label>
                <textarea name="abuso_drogas"></textarea>
            </div>
            <div class="textarea-group">
                <label>DEBILIDAD MENTAL:</label>
                <textarea name="debilidad_mental"></textarea>
            </div>
            <div class="textarea-group">
                <label>CONVULSIONES, DESMAYOS, TEMBLORES, ETC:</label>
                <textarea name="convulsiones_desmayos"></textarea>
            </div>
            <div class="textarea-group">
                <label>ETS (ENFERMEDADES SEXUALES, FORMA, MOTIVOS):</label>
                <textarea name="ets"></textarea>
            </div>
            <div class="textarea-group">
                <label>SUICIDIO (FORMAS, MOTIVOS):</label>
                <textarea name="suicidio"></textarea>
            </div>
            <div class="textarea-group">
                <label>Anormalidades (PROSTITUCIÓN, CRIMINALIDAD, DELITOS, RECLUSIÓN, ETC):</label>
                <textarea name="anormalidades"></textarea>
            </div>
            <div class="textarea-group">
                <label>TRASTORNOS DEL HABLA (TARTAMUDEZ, SORDERA MUEDEZ, ETC):</label>
                <textarea name="trastornos_habla"></textarea>
            </div>
            <div class="textarea-group">
                <label>TRASTORNOS DE LA VISTA (CEGUERA, MIOPIA, ETC):</label>
                <textarea name="trastornos_vista"></textarea>
            </div>
        </div>

        <div class="section">
            <h2>VII. IMPRESIÓN PSICOLÓGICA</h2>
            <div class="textarea-group">
                <label>(Signos y síntomas, personalidad, adaptación psicológica a la enfermedad, al tratamiento, cirugía, e internamientos, relación médico-paciente-enfermera, expectativas ante la patología):</label>
                <textarea name="impresion_psicologica"></textarea>
            </div>
        </div>

        <div class="section">
            <h2>VIII. RECOMENDACIONES</h2>
            <div class="textarea-group">
                <textarea name="recomendaciones"></textarea>
            </div>
        </div>

        <div class="section">
            <h2>IX. PLAN PSICOTERAPÉUTICO</h2>
            <div class="textarea-group">
                <textarea name="plan_psicoterapeutico"></textarea>
            </div>
        </div>
        <input type="submit" name="Save" value="Guardar">
    </form>
</body>
</div>



<script>

function modificarCita() {
    const citaSeleccionada = document.querySelector('input[name="cita_seleccionada"]:checked');
    const accionSeleccionada = document.querySelector('input[name="accion"]:checked');

    if (!citaSeleccionada) {
        alert('Por favor, seleccione una cita.');
        return;
    }

    if (!accionSeleccionada) {
        alert('Por favor, seleccione una acción (Reagendar o Cancelar).');
        return;
    }

    const idCita = citaSeleccionada.value;
    const idFecha = citaSeleccionada.getAttribute('data-id-fecha');

    if (accionSeleccionada.value === 'reagendar') {
        // Redireccionar a la pantalla para cambiar la fecha y hora, pasando también el id_fecha
        window.location.href = 'reagendar.php?id_cita=' + idCita + '&id_fecha=' + idFecha;
    } else if (accionSeleccionada.value === 'cancelar') {
        // Redireccionar a la página para cancelar la cita
        const form = document.getElementById('citas-form');
        form.action = 'modificar_cita.php';
        form.submit();
    }
}






document.getElementById('modificar-btn').addEventListener('click', function() {
    // Obtener la cita seleccionada
    const selectedCita = document.querySelector('input[name="cita"]:checked');
    if (!selectedCita) {
        alert('Por favor, seleccione una cita.');
        return;
    }
    
    // Obtener el nuevo estado seleccionado
    const selectedStatus = document.querySelector('input[name="status"]:checked');
    if (!selectedStatus) {
        alert('Por favor, seleccione un estado.');
        return;
    }
    
    const citaId = selectedCita.getAttribute('data-id');
    const newStatus = selectedStatus.value;

    // Enviar la solicitud para actualizar el estado
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_cita_status.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status === 200) {
            alert('Estado actualizado con éxito');
            location.reload();  // Recargar la página para mostrar los cambios
        } else {
            alert('Error al actualizar el estado');
        }
    };
    xhr.send(`id=${citaId}&status=${newStatus}`);
});

  function showForm(formId) {
    var forms = document.querySelectorAll('.container');
    for (var i = 0; i < forms.length; i++) {
        forms[i].classList.add('hide');
    }
    var formToShow = document.getElementById(formId);
    formToShow.classList.remove('hide');
  }

  function showAgendadas() {
    var forms = document.querySelectorAll('.container');
    for (var i = 0; i < forms.length; i++) {
        forms[i].classList.add('hide');
    }
    document.getElementById('citas-agendadas').classList.remove('hide');
  }

  function showhistorial() {
    var forms = document.querySelectorAll('.container');
    for (var i = 0; i < forms.length; i++) {
        forms[i].classList.add('hide');
    }
    document.getElementById('historial-form').classList.remove('hide');
  }

  function showlista() {
    var forms = document.querySelectorAll('.container');
    for (var i = 0; i < forms.length; i++) {
        forms[i].classList.add('hide');
    }
    document.getElementById('Lista-de-citas').classList.remove('hide');
  }

  const loginLink = document.getElementById('login-link');
  const registerLink = document.getElementById('register-link');
  const resetPassLink = document.getElementById('reset-pass-link');
  const loginForm = document.getElementById('login-form');
  const registerForm = document.getElementById('register-form');
  const resetPassForm = document.getElementById('reset-pass-form');
  const homeLink = document.getElementById('home-link');
  const logoutLink = document.getElementById('logout-link');
  const logoutForm = document.getElementById('logout-form');
  const body = document.querySelector('body');

  loginLink.addEventListener('click', () => {
    loginForm.classList.remove('hide');
    registerForm.classList.add('hide');
  });

  registerLink.addEventListener('click', () => {
    loginForm.classList.add('hide');
    registerForm.classList.remove('hide');
  });
  resetPassLink.addEventListener('click', (e) => {
    e.preventDefault();
    resetPassForm.classList.remove('hide');
    loginForm.classList.add('hide');
    registerForm.classList.add('hide');
  });
  homeLink.addEventListener('click', (e) => {
    e.preventDefault();
    loginForm.classList.add('hide');
    registerForm.classList.add('hide');
    resetPassForm.classList.add('hide');
  });
  logoutLink.addEventListener('click', () => {
    logoutForm.submit();
  });

  function showMunicipios() {
    var estadoSelect = document.getElementById("state");
    var municipioSelect = document.getElementById("city");
    
    
    municipioSelect.disabled = estadoSelect.value === "";
}
function showMunicipiosPartner() {
    var estadoSelect = document.getElementById("state_partner");
    var municipioSelect = document.getElementById("city_partner");
    municipioSelect.disabled = estadoSelect.value === "";
}

function showMunicipiosP() {
    var estadoSelect = document.getElementById("state_p");
    var municipioSelect = document.getElementById("city_p");
    municipioSelect.disabled = estadoSelect.value === "";
}

function showMunicipiosI() {
    var estadoSelect = document.getElementById("state_i");
    var municipioSelect = document.getElementById("city_i");
    municipioSelect.disabled = estadoSelect.value === "";
}
function esHoraValida(hora) {
            const [hours, minutes] = hora.split(':').map(Number);
            // Validar si está en el intervalo permitido
            if ((hours >= 8 && hours < 12) || (hours >= 13 && hours < 16)) {
                return true;
            }
            return false;
        }

        function validarHora() {
            const horaAtencion = document.getElementById('hora_atencion').value;
            if (horaAtencion && !esHoraValida(horaAtencion)) {
                alert('La hora de atención debe estar entre 08:00-12:00 o 13:00-16:00.');
                document.getElementById('hora_atencion').value = '';
                document.getElementById('hora_fin').value = '';
            } else {
                actualizarHoraFin();
            }
        }

        function actualizarHoraFin() {
            const horaAtencion = document.getElementById('hora_atencion').value;
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
        function validarHoraP() {
            const horaAtencion = document.getElementById('partnerTime_P').value;
            if (horaAtencion && !esHoraValida(horaAtencion)) {
                alert('La hora de atención debe estar entre 08:00-12:00 o 13:00-16:00.');
                document.getElementById('partnerTime_P').value = '';
                document.getElementById('hora_fin_P').value = '';
            } else {
                actualizarHoraFin();
            }
        }

        function actualizarHoraFinP() {
            const horaAtencion = document.getElementById('partnerTime_P').value;
            if (horaAtencion) {
                const [hours, minutes] = horaAtencion.split(':');
                const date = new Date();
                date.setHours(parseInt(hours));
                date.setMinutes(parseInt(minutes) + 45);
                date.setSeconds(0);

                const horaFin = date.toTimeString().substring(0, 5);
                document.getElementById('hora_fin_P').value = horaFin;
            } else {
                document.getElementById('hora_fin_P').value = '';
            }
        }
        function validarHoraI() {
            const horaAtencion = document.getElementById('appointmentTime_I').value;
            if (horaAtencion && !esHoraValida(horaAtencion)) {
                alert('La hora de atención debe estar entre 08:00-12:00 o 13:00-16:00.');
                document.getElementById('appointmentTime_I').value = '';
                document.getElementById('hora_fin_I').value = '';
            } else {
                actualizarHoraFinI();
            }
        }

        function actualizarHoraFinI() {
            const horaAtencion = document.getElementById('appointmentTime_I').value;
            if (horaAtencion) {
                const [hours, minutes] = horaAtencion.split(':');
                const date = new Date();
                date.setHours(parseInt(hours));
                date.setMinutes(parseInt(minutes) + 45);
                date.setSeconds(0);

                const horaFin = date.toTimeString().substring(0, 5);
                document.getElementById('hora_fin_I').value = horaFin;
            } else {
                document.getElementById('hora_fin_I').value = '';
            }
        }
</script>

</body>
</html>




