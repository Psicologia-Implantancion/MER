<?php

session_start();
/*hola */
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
    $sql_tipo_cita_Infante = "SELECT Precio FROM tipo_cita WHERE Tipo = 'Infante'";
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

    $sql = "SELECT * FROM login WHERE Usuario='$user' AND Password='$password_md5'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $_SESSION['username'] = $user;
        
        // Aquí configuramos el tipo de usuario en la sesión
        $user_type = getUserType($conn, $user);
        $_SESSION['user_type'] = $user_type ? $user_type : 'Usuario';

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
<body>
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
            <label for="time">Hora de Atención (45min) *</label>
            <input type="time" id="time" name="hora_atencion" required>
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
            <input type="time" id="partnerTime" name="partnerTime">
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
        <div class="input-field">
            <label for="appointmentTime">Hora de Atención (45min) *</label>
            <input type="time" id="appointmentTime" name="appointmentTime" required>
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
      <tr>
        <td>1</td>
        <td>12-03-2024</td>
        <td>8:45-9:30</td>
        <td>Individual</td>
        <td>Aprobada</td>
      </tr>
      <tr>
        <td>2</td>
        <td>23-03-2024</td>
        <td>10:00-10:45</td>
        <td>Infantil</td>
        <td>Cancelada</td>
      </tr>
      <tr>
        <td>3</td>
        <td>18-04-2024</td>
        <td>10:45 - 11:30</td>
        <td>Pareja</td>
        <td>Reagendada</td>
      </tr>
    </tbody>
  </table>
  <!-- Botones -->
  <div class="button-container">
    <button type="submit">Modificar</button>
    <button type="submit">Cancelar</button>
    <button type="submit">Regresar</button>
  </div>
</div>

<script>
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

</script>

</body>
</html>




