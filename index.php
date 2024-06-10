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

function loadMunicipiosOptions($conn, $selected_estado = null) {
    $sql = "SELECT municipios.id_municipio, municipios.municipio, estados.estado 
            FROM municipios 
            INNER JOIN estados ON municipios.id_estado = estados.id_estado";
    
    if ($selected_estado) {
        $sql .= " WHERE municipios.id_estado = ?";
    }
    
    $stmt = $conn->prepare($sql);
    if ($selected_estado) {
        $stmt->bind_param("i", $selected_estado);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $municipio_options = "<option value=''>Seleccione...</option>";
        while ($row = $result->fetch_assoc()) {
            $municipio_id = $row["id_municipio"];
            $municipio = $row["municipio"];
            $estado = $row["estado"];
            $municipio_options .= "<option value='$municipio_id'>$municipio</option>";
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

// Procesar la solicitud AJAX para cargar municipios
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_estado'])) {
    echo loadMunicipiosOptions($conn, $_POST['id_estado']);
    exit;
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
    // Validar contraseña
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,}$/', $password)) {
        return "La contraseña debe tener: Al menos 8 caracteres, Al menos una letra minúscula, Al menos una letra mayúscula, Al menos un número, Al menos un símbolo especial";
    }

    // Verificar que las contraseñas coincidan
    if ($password != $password_repeat) {
        return "Las contraseñas no coinciden";
    }

    // Verificar si el correo ya está en uso
    $check_email_sql = "SELECT * FROM login WHERE Correo = ?";
    $stmt_email_check = $conn->prepare($check_email_sql);
    $stmt_email_check->bind_param("s", $email);
    $stmt_email_check->execute();
    $result_email_check = $stmt_email_check->get_result();

    if ($result_email_check === false) {
        return "Error al verificar el correo: " . $conn->error;
    }

    if ($result_email_check->num_rows > 0) {
        return "El correo electrónico ya está en uso.";
    }

    // Verificar si el nombre de usuario ya está en uso
    $check_user_sql = "SELECT * FROM login WHERE Usuario = ?";
    $stmt_user_check = $conn->prepare($check_user_sql);
    $stmt_user_check->bind_param("s", $username);
    $stmt_user_check->execute();
    $result_user_check = $stmt_user_check->get_result();

    if ($result_user_check === false) {
        return "Error al verificar el nombre de usuario: " . $conn->error;
    }

    if ($result_user_check->num_rows == 0) {
        // Insertar nuevo usuario si el nombre de usuario no está en uso
        $password_md5 = md5($password);
        $insert_sql = "INSERT INTO login (Usuario, Password, Correo) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("sss", $username, $password_md5, $email);

        if ($stmt_insert->execute() === false) {
            return "Error al insertar el registro: " . $stmt_insert->error;
        }

        return "Registro exitoso";
    } else {
        return "Nombre de usuario en uso";
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

        if ($error_message === "Registro exitoso") {
            echo '<script>alert("Registro exitoso"); window.location.href = "index.php";</script>';
        } else {
            echo '<script>alert("' . $error_message . '"); window.history.back();</script>';
        }
    } elseif (isset($_POST['login'])) {
        $user = $_POST['user'];
        $password = $_POST['password'];

        $error_message = loginUser($conn, $user, $password);

        if ($error_message !== "") {
            echo '<script>alert("' . $error_message . '"); window.history.back();</script>';
        }
    }
}




?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DB Krato</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
                    <li><a href="#" onclick="showconf('configuracion')">Configuracion</a></li>
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

<div id="reset-pass-form" class="container hide">
    <div class="form">
      <h2>Recuperar contraseña</h2>
      <form method="post">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required>

        <input type="submit" value="Recuperar contraseña">
      </form>
    </div>
  </div>

  <div id="individual-form" class="container hide" >
    <form class="row g-3 needs-validation" novalidate method="POST" action="insertar_datos.php">
        <div class="mb-3">
            <h3 class="section-title">Datos Del Paciente</h3>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 form-floating mb-3">
                <input type="text" class="form-control" id="firstName" name="primer_nombre" placeholder="Primer Nombre" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer nombre">
                <label for="firstName" class="text-dark">Primer Nombre *</label>
                <div class="valid-feedback">Todo bien</div>
                    <div class="invalid-feedback">Por favor, ingrese solo letras en el primer nombre</div>
            </div>
            <div class="col-md-6 form-floating mb-3">
                <input type="text" class="form-control" id="secondName" name="segundo_nombre" placeholder="Segundo Nombre" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo nombre">
                <label for="secondName" class="text-dark">Segundo Nombre *</label>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Por favor, ingrese solo letras en el segundo nombre</div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 form-floating mb-3">
                <input type="text" class="form-control" id="firstLastName" name="primer_apellido" placeholder="Primer Apellido" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer apellido">
                <label for="firstLastName" class="text-dark">Primer Apellido *</label>
                <div class="valid-feedback">Todo bien</div>
                        <div class="invalid-feedback">Por favor, ingrese solo letras en el primer apellido</div>
            </div>
            <div class="col-md-6 form-floating mb-3">
                <input type="text" class="form-control" id="lastName" name="segundo_apellido" placeholder="Segundo Apellido" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo apellido">
                <label for="lastName" class="text-dark">Segundo Apellido *</label>
                <div class="valid-feedback">Todo bien</div>
                        <div class="invalid-feedback">Por favor, ingrese solo letras en el segundo apellido</div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 form-floating mb-3">
                <select class="form-select" id="vejp" name="vejp" aria-label="VEJP">
                    <?php echo loadTipoCedulaOptions($conn); ?>
                </select>
                <label for="vejp" class="text-dark">Tipo de Identificación</label>
            </div>
            <div class="col-md-6 form-floating mb-3">
                <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Cédula" required pattern="[0-9]+" title="Por favor, ingrese solo números">
                <label for="cedula" class="text-dark">Cédula *</label>
                <div class="valid-feedback">Todo bien</div>
                        <div class="invalid-feedback">Por favor, ingrese solo números</div>
            </div>
        </div>
        <div class="form-floating mb-3">
            <input type="tel" class="form-control" id="phone" name="telefono" placeholder="Teléfono" required pattern="[0-9]+" title="Por favor, ingrese solo números">
            <label for="phone" class="text-dark">Teléfono *</label>
            <div class="valid-feedback">Todo bien</div>
                    <div class="invalid-feedback">El Teléfono solo puede contener números</div>
        </div>
        <div class="form-floating mb-3">
            <input type="date" class="form-control" id="dob" name="fecha_nacimiento" placeholder="Fecha de Nacimiento" required max="<?php echo date('Y-m-d', strtotime('-1 day')); ?>">
            <label for="dob" class="text-dark">Fecha de Nacimiento *</label>
            <div class="valid-feedback">Todo bien</div>
                    <div class="invalid-feedback">Por favor, seleccione una fecha válida </div>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="correo" placeholder="Correo" required pattern="^\w+@(gmail\.com|hotmail\.com)$" title="Ingrese un correo válido (ej. usuario@gmail.com)">
            <label for="email" class="text-dark">Correo *</label>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese un correo válido (@gmail.com o @hotmail.com)</div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 form-floating mb-3">
                <select class="form-select" id="state" name="estado" placeholder="Estado" required onchange="showMunicipios()">
                    <?php echo loadEstadosOptions($conn); ?>
                </select>
                <label for="state" class="text-dark">Estado *</label>
                <div class="valid-feedback">Todo bien</div>
                        <div class="invalid-feedback">Seleccione un estado</div>
            </div>
            <div class="col-md-6 form-floating mb-3">
                <select class="form-select" id="city" name="municipio" placeholder="Municipio" required disabled>
                    <option value=''>Seleccione un estado primero</option>
                </select>
                <label for="city" class="text-dark">Municipio *</label>
            </div>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="direccion" name="descripcion" placeholder="Ingrese una descripción de su dirección" required>
            <label for="direccion" class="text-dark">Descripción de dirección</label>
            <div class="valid-feedback">Todo bien</div>
                    <div class="invalid-feedback">Invalido</div>
        </div>
        <div class="mb-3">
            <h3 class="section-title">Datos de la cita</h3>
        </div>
        <div class="form-floating mb-3">
            <input type="date" class="form-control" id="date" name="fecha_cita" placeholder="Fecha de la Cita" required min="<?php echo date('Y-m-d'); ?>">
            <label for="date" class="text-dark">Fecha de la Cita *</label>
            <div class="valid-feedback">Todo bien</div>
                    <div class="invalid-feedback">Por favor, seleccione una fecha válida </div>
        </div>
        <div class="form-floating mb-3">
            <input type="time" class="form-control" id="hora_atencion" name="hora_atencion" placeholder="Hora de Atención" required onchange="actualizarHoraFin()">
            <label for="hora_atencion">Hora de Atención (45min) *</label>
        </div>
        <div class="form-floating mb-3">
            <input type="time" class="form-control" id="hora_fin" name="hora_fin" placeholder="Hora de Fin" readonly>
            <label for="hora_fin">Hora de Fin *</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="price" name="monto" value="<?php echo getPrecioCitaAdulto($conn); ?>" placeholder="Monto" required readonly>
            <label for="price">Monto *</label>
        </div>
        <button type="submit" class="btn btn-primary">Agendar</button>
    </form>
</div>


<div id="partner-form" class="container hide">
    <form class="row g-3 needs-validation" novalidate method="POST" action="insertar_datosP.php">
        <div class="section-title">Datos Del Paciente</div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="firstName">Primer Nombre *</label> 
                </div>
            <input type="text" id="firstName" name="firstName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer nombre"> 
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo letras en el primer nombre</div>
            <div class="input-field half-width">
                <label for="secondName">Segundo Nombre *</label>
                <input type="text" id="secondName" name="secondName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo nombre">
                <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo letras en el segundo nombre</div>
            </div>
        </div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="firstLastName">Primer Apellido *</label>
                <input type="text" id="firstLastName" name="firstLastName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer apellido">
                <div class="valid-feedback">Todo bien</div>
                 <div class="invalid-feedback">Por favor, ingrese solo letras en el primer apellido</div>
            </div>
            <div class="input-field half-width">
                <label for="lastName">Segundo Apellido *</label>
                <input type="text" id="lastName" name="lastName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo apellido">
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Por favor, ingrese solo letras en el segundo apellido</div>
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
                <input type="text" id="cedula" name="cedula" required pattern="[0-9]+" title="Por favor, ingrese solo números">
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Por favor, ingrese solo números</div>
            </div>
        </div>
        <div class="input-field full-width">
            <label for="phone">Teléfono *</label>
            <input type="tel" id="phone" name="phone" required pattern="[0-9]+" title="Por favor, ingrese solo números">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">El Teléfono solo puede contener números</div>
        </div>
        <div class="input-field full-width">
            <label for="dob">Fecha de Nacimiento *</label>
            <input type="date" id="dob" name="dob" required max="<?php echo date('Y-m-d', strtotime('-1 day')); ?>">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, seleccione una fecha válida </div>
        </div>
        <div class="input-field full-width">
            <label for="email">Correo *</label>
            <input type="email" id="email" name="email" required pattern="^\w+@(gmail\.com|hotmail\.com)$" title="Ingrese un correo válido (ej. usuario@gmail.com)">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese un correo válido (@gmail.com o @hotmail.com)</div>
        </div>
        <div class="input-field half-width">
        <label for="state_partner">Estado *</label>
        <select id="state_partner" name="estado" required onchange="showMunicipiosPartner()">
            <?php echo loadEstadosOptions($conn); ?>
        </select>
        <div class="valid-feedback">Todo bien</div>
        <div class="invalid-feedback">Seleccione un estado</div>
    </div>
    <div class="input-field half-width">
        <label for="city_partner">Municipio *</label>
        <select id="city_partner" name="municipio" required disabled>
            <option value=''>Seleccione un estado primero</option>
        </select>
    </div>
        <div class="input-field full-width">
            <label for="direccion">Descripción de dirección:</label>
            <input type="text" id="direccion" name="descripcion" placeholder="Ingrese una descripción de su dirección">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Invalido</div>
        </div>
        <div class="section-title">Datos de la Pareja</div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="partnerFirstName">Primer Nombre *</label>
                <input type="text" id="partnerFirstName" name="partnerFirstName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer nombre">
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Por favor, ingrese solo letras en el primer nombre</div>
            </div>
            <div class="input-field half-width">
                <label for="partnerSecondName">Segundo Nombre *</label>
                <input type="text" id="partnerSecondName" name="partnerSecondName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo nombre">
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Por favor, ingrese solo letras en el segundo nombre</div>
            </div>
        </div>
        <div class="line-fields">
            <div class="input-field half-width">
                <label for="partnerFirstLastName">Primer Apellido *</label>
                <input type="text" id="partnerFirstLastName" name="partnerFirstLastName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer apellido">
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Por favor, ingrese solo letras en el primer apellido</div>
            </div>
            <div class="input-field half-width">
                <label for="partnerLastName">Segundo Apellido *</label>
                <input type="text" id="partnerLastName" name="partnerLastName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo apellido">
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Por favor, ingrese solo letras en el segundo apellido</div>
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
                <input type="text" id="partnerCedula" name="partnerCedula" required pattern="[0-9]+" title="Por favor, ingrese solo números">
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Por favor, ingrese solo números</div>
            </div>
        </div>
        <div class="input-field full-width">
            <label for="partnerPhone">Teléfono *</label>
            <input type="tel" id="partnerPhone" name="partnerPhone" required pattern="[0-9]+" title="Por favor, ingrese solo números">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">El Teléfono solo puede contener números</div>
        </div>
        <div class="input-field full-width">
            <label for="partnerDob">Fecha de Nacimiento *</label>
            <input type="date" id="partnerDob" name="partnerDob" required max="<?php echo date('Y-m-d', strtotime('-1 day')); ?>">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, seleccione una fecha válida </div>
        </div>
        <div class="input-field full-width">
            <label for="partnerEmail">Correo *</label>
            <input type="email" id="partnerEmail" name="partnerEmail" required pattern="^\w+@(gmail\.com|hotmail\.com)$" title="Ingrese un correo válido (ej. usuario@gmail.com)">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese un correo válido (@gmail.com o @hotmail.com)</div>
        </div>
        <div class="input-field half-width">
        <label for="state_p">Estado *</label>
        <select id="state_p" name="estado2" required onchange="showMunicipiosP()">
            <?php echo loadEstadosOptions($conn); ?>
        </select>
        <div class="valid-feedback">Todo bien</div>
         <div class="invalid-feedback">Seleccione un estado</div>
    </div>
    <div class="input-field half-width">
        <label for="city_p">Municipio *</label>
        <select id="city_p" name="municipio2" required disabled>
            <option value=''>Seleccione un estado primero</option>
        </select>
    </div>
        <div class="input-field full-width">
            <label for="direccion">Descripción de dirección:</label>
            <input type="text" id="direccion" name="descripcion" placeholder="Ingrese una descripción de su dirección">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Invalido</div>
        </div>
        <div class="section-title">Datos de la cita</div>
        <div class="input-field full-width">
            <label for="partnerDate">Fecha de la Cita *</label>
            <input type="date" id="partnerDate" name="partnerDate"  required min="<?php echo date('Y-m-d'); ?>">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, seleccione una fecha válida </div>
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
    <form class="row g-3 needs-validation" novalidate method="POST" action="insertar_datosI.php">
        <div class="input-field">
            <label for="firstName">Primer Nombre *</label>
            <input type="text" id="firstName" name="firstName"  required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer nombre">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo letras en el primer nombre</div>
        </div>
        <div class="input-field">
            <label for="secondName">Segundo Nombre</label>
            <input type="text" id="secondName" name="secondName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo nombre">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo letras en el segundo nombre</div>
        </div>
        <div class="input-field">
            <label for="firstLastName">Primer Apellido *</label>
            <input type="text" id="firstLastName" name="firstLastName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer apellido">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo letras en el primer apellido</div>
        </div>
        <div class="input-field">
            <label for="secondLastName">Segundo Apellido</label>
            <input type="text" id="secondLastName" name="secondLastName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo apellido">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo letras en el segundo apellido</div>
        </div>
        <div class="input-field half-width">
            <label for="vejp">VEJP</label>
            <select id="vejp" name="vejp">
                <?php echo loadTipoCedulaOptions($conn); ?>
            </select>
        </div>
        <div class="input-field half-width">
            <label for="idNumber">Cédula *</label>
            <input type="text" id="idNumber" name="idNumber" required pattern="[0-9]+" title="Por favor, ingrese solo números">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo números</div>
        </div>
        <div class="input-field full-width">
            <label for="direccion">Numero de hijo:</label>
            <input type="text" id="Nhijo" name="Nhijo" required pattern="[0-9]+" title="Por favor, ingrese solo números">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo números</div>
        </div>
        <div class="input-field">
            <label for="phone">Teléfono *</label>
            <input type="tel" id="phone" name="phone" required pattern="[0-9]+" title="Por favor, ingrese solo números">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo números</div>
        </div>
        <div class="input-field">
            <label for="birthDate">Fecha de Nacimiento *</label>
            <input type="date" id="birthDate" name="birthDate" required max="<?php echo date('Y-m-d', strtotime('-1 day')); ?>">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, seleccione una fecha válida </div>
        </div>
        <div class="input-field">
            <label for="email">Correo *</label>
            <input type="email" id="email" name="email" required pattern="^\w+@(gmail\.com|hotmail\.com)$" title="Ingrese un correo válido (ej. usuario@gmail.com)">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese un correo válido (@gmail.com o @hotmail.com)</div>
        </div>
        <div class="input-field half-width">
        <label for="state">Estado *</label>
        <select id="state_i" name="estado" required onchange="showMunicipiosI()">
            <?php echo loadEstadosOptions($conn); ?>
        </select>
        <div class="valid-feedback">Todo bien</div>
        <div class="invalid-feedback">Seleccione un estado</div>
    </div>
    <div class="input-field half-width">
        <label for="city">Municipio *</label>
        <select id="city_i" name="municipio" required disabled>
            <option value=''>Seleccione un estado primero</option>
        </select>
    </div>
        <div class="input-field full-width">
            <label for="direccion">Descripción de dirección:</label>
            <input type="text" id="direccion" name="descripcion" placeholder="Ingrese una descripción de su dirección" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Invalido</div>
        </div>
        
        <div class="input-field">
            <label for="childFirstName">Primer Nombre del Niño</label>
            <input type="text" id="childFirstName" name="childFirstName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo apellido">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Solo se permiten letras</div>
        </div>
        <div class="input-field">
            <label for="childSecondName">Segundo Nombre del Niño</label>
            <input type="text" id="childSecondName" name="childSecondName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo apellido">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Solo se permiten letras</div>
        </div>
        <div class="input-field">
            <label for="childFirstLastName">Primer Apellido del Niño *</label>
            <input type="text" id="childFirstLastName" name="childFirstLastName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo apellido">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Solo se permiten letras</div>
        </div>
        <div class="input-field">
            <label for="childSecondLastName">Segundo Apellido del Niño</label>
            <input type="text" id="childSecondLastName" name="childSecondLastName" required pattern="[A-Za-z]+" title="Solo se permiten letras en el segundo apellido">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Solo se permiten letras</div>
        </div>
        <div class="input-field">
            <label for="childBirthDate">Fecha de Nacimiento del Niño *</label>
            <input type="date" id="childBirthDate" name="childBirthDate" required max="<?php echo date('Y-m-d', strtotime('-1 day')); ?>">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Ingrese una fecha</div>
        </div>
        <div class="input-field full-width">
            <label for="partnerDate">Fecha de la Cita *</label>
            <input type="date" id="partnerDate" name="partnerDate" required min="<?php echo date('Y-m-d'); ?>">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Ingrese una fecha</div>
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
          <th>Hora de Inicio</th>
          <th>Hora de Finalizacion</th>
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

        $sql = "SELECT c.Id_Cita, f.Id_Fecha, f.Dia AS Fecha, c.Hora, c.Hora_Fin, tc.Tipo AS Tipo, c.Status
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
                                      <td>' . $mostrar['Hora_Fin'] . '</td>
                                      <td>' . $mostrar['Tipo'] . '</td>
                                      <td>' . $mostrar['Status'] . '</td>
                                    </tr>';
            } else {
                $citasPendientes .= '<tr>
                                      <td>' . $contadorr++ . '</td>
                                      <td>' . $mostrar['Fecha'] . '</td>
                                      <td>' . $mostrar['Hora'] . '</td>
                                      <td>' . $mostrar['Hora_Fin'] . '</td>
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
        <th>Hora de Inicio</th>
        <th>Hora de Finalizacion</th>
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
    <!-- Botones para insertar y buscar/ver -->
    <div class="section">
        <button onclick="mostrarInsertar()">Insertar</button>
        <button onclick="mostrarBuscar()">Buscar/Ver Historial</button>
    </div>

    <!-- Sección de búsqueda -->
    <div id="seccion-busqueda" style="display: none;">
        <h2>Búsqueda por Cédula</h2>
        <input type="text" id="cedula-busqueda" name="cedula" placeholder="Ingrese la cédula">
        <button onclick="buscarHistorial()">Buscar</button>
    </div>

    <!-- Resultado de la búsqueda -->
    <div id="resultado-busqueda"></div>

    <form class="row g-3 needs-validation" novalidate method="POST" action="insertarDatoshm.php" id="historial-formulario">
        <div class="section">
            <h2>I. DATOS DE IDENTIFICACIÓN</h2>
            <div class="input-field full-width">
            <label>Nombre:</label>
            <input type="text" name="nombre" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer nombre">
            <div class="valid-feedback">Todo bien</div>
             <div class="invalid-feedback">Inválido</div>
        </div>

            <div class="flex">
                <div class="input-field full-width">
                    <label>Cédula:</label>
                    <input type="text" name="cedula" required pattern="[0-9]+" title="Por favor, ingrese solo números">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo números</div>
                </div>
                <div class="input-field full-width">
                    <label>Fecha de Nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" required max="<?php echo date('Y-m-d', strtotime('-1 day')); ?>">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, seleccione una fecha válida </div>
                </div>
            </div>
            <div class="flex">
                <div class="input-field full-width">
                    <label>Escolaridad:</label>
                    <input type="text" name="escolaridad" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                </div>
            <div class="input-field full-width">
                 <label>Promedio:</label>
                <input type="number" name="promedio" step="0.01" min="0" max="20" required>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Inválido</div>
            </div>
            </div>
            <div class="flex">
                <div class="input-field full-width">
                    <label>Escuela:</label>
                    <input type="text" name="escuela" required>
                    <div class="valid-feedback">Todo bien</div>
                    <div class="invalid-feedback">Inválido</div>>
                </div>
                <div class="input-field full-width">
                    <label>Lugar que ocupa en la familia:</label>
                    <input type="text" name="lugar_familia" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>

                </div>
            </div>
            <div class="input-field full-width">
                <label>Dirección:</label>
                <input type="text" name="direccion" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="input-field full-width">
                <label>Teléfono:</label>
                <input type="text" name="telefono" required pattern="[0-9]+" title="Por favor, ingrese solo números">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo números</div>
            </div>
        </div>

       

        <div class="section">
            <h2>II. FACTORES QUE MOTIVAN A LA CONSULTA</h2>
        <div class="textarea-group">
            <label>Motivos de consulta:</label>
            <textarea name="motivos_consulta" required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
        </div>

            <div class="input-field full-width">
                <label>Referido por:</label>
                <input type="text" name="referido_por" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="input-field full-width">
                <label>Diagnóstico orgánico:</label>
                <input type="text" name="diagnostico_organico" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>Actitud de los padres ante el problema:</label>
                <textarea name="actitud_padres" required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>Estado emocional actual del Niño (a):</label>
                <textarea name="estado_emocional" required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
        </div>

        <div class="section">
            <h2>III. FACTORES FÍSICOS</h2>
            <div class="textarea-group">
                <label>1. DESARROLLO PRENATAL Y NATAL:</label>
                <textarea name="desarrollo_prenatal"required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>2. DESARROLLO DE LA PRIMERA INFANCIA:</label>
                <textarea name="desarrollo_primera_infancia"required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
        </div>

        <div class="section">
            <h2>IV. FACTORES FAMILIARES</h2>
            <div class="input-field full-width">
                <label>1. DATOS FAMILIARES:</label>
                <div class="flex">
                    <div class="input-field full-width">
                        <label>Papá - Nombre:</label>
                        <input type="text" name="papa_nombre" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer nombre">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo letras</div>
                    </div>
                    <div class="input-field full-width">
                        <label>Papá - Apellido:</label>
                        <input type="text" name="papa_apellido" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer nombre">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo letras</div>
                    </div>

                    <div class="input-field full-width">
                        <label>Mamá - Nombre:</label>
                        <input type="text" name="mama_nombre" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer nombre">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo letras</div>
                    </div>
                    <div class="input-field full-width">
                        <label>Mamá - Apellido:</label>
                        <input type="text" name="mama_apellido" required pattern="[A-Za-z]+" title="Solo se permiten letras en el primer nombre">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo letras</div>
                    </div>

                </div>
                <div class="flex">
                    <div class="input-field full-width">
                        <label>Papá - Salud física:</label>
                        <input type="text" name="papa_salud_fisica" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                    </div>
                    <div class="input-field full-width">
                        <label>Mamá - Salud física:</label>
                        <input type="text" name="mama_salud_fisica" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="input-field full-width">
                        <label>Papá - Nivel educativo:</label>
                        <input type="text" name="papa_nivel_educativo" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                    </div>
                    <div class="input-field full-width">
                        <label>Mamá - Nivel educativo:</label>
                        <input type="text" name="mama_nivel_educativo" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="input-field full-width">
                        <label>Papá - Trabajo actual:</label>
                        <input type="text" name="papa_trabajo_actual" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                    </div>
                    <div class="input-field full-width">
                        <label>Mamá - Trabajo actual:</label>
                        <input type="text" name="mama_trabajo_actual" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="input-field full-width">
                        <label>Papá - Horario de trabajo:</label>
                        <input type="text" name="papa_horario_trabajo" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                    </div>
                    <div class="input-field full-width">
                        <label>Mamá - Horario de trabajo:</label>
                        <input type="text" name="mama_horario_trabajo" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="input-field full-width">
                        <label>Papá - Hábitos:</label>
                        <input type="text" name="papa_habitos" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                    </div>
                    <div class="input-field full-width">
                        <label>Mamá - Hábitos:</label>
                        <input type="text" name="mama_habitos" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
                    </div>
                </div>
            </div>

            <div class="textarea-group">
                <label>2. EXPERIENCIAS TRAUMÁTICAS DEL NIÑO:</label>
            </div>
            <div class="input-field full-width">
                <label>Pérdida de algún familiar o ser querido:</label>
                <input type="text" name="perdida_familiar" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="input-field full-width">
                <label>¿Quién era?:</label>
                <input type="text" name="quien_era" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>¿Cómo fue?:</label>
                <textarea name="como_fue" required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="input-field full-width">
                <label>Edad que tenía el niño:</label>
                <input type="text" name="edad_nino" required pattern="[0-9]+" title="Por favor, ingrese solo números">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo números</div>
            </div>
            <div class="input-field full-width">
                <label>¿Presenció el suceso?:</label>
                <input type="text" name="presencio_suceso" required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>Reacción del niño ante esto:</label>
                <textarea name="reaccion_nino" required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>Accidentes del niño:</label>
                <textarea name="accidentes_nino" required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>Castigos graves:</label>
                <textarea name="castigos_graves"required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="input-field full-width">
                <label>De parte de quién:</label>
                <input type="text" name="parte_quien" required>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="input-field full-width">
                <label>Edad del niño:</label>
                <input type="text" name="edad_castigos" required pattern="[0-9]+" title="Por favor, ingrese solo números">
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Por favor, ingrese solo números</div>
            </div>
            <div class="textarea-group">
                <label>Los problemas del niño son causados por:</label>
                <textarea name="problemas_causas"required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>Problemas físicos:</label>
                <textarea name="problemas_fisicos"required></textarea>
            <div class="valid-feedback">Todo bien</div>
            <div class="invalid-feedback">Inválido</div>
            </div>
        </div>

        <div class="section">
            <h2>V. FACTORES DE LA PERSONALIDAD Y CONDUCTA</h2>
            <div class="textarea-group">
                <label>1. HÁBITOS E INTERESES</label>
            </div>
            <div class="textarea-group">
                <label>a) COMIDA:</label>
                <textarea name="comida" required></textarea>
                <div class="valid-feedback">Todo bien</div>
             <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>b) SUEÑO:</label>
                <textarea name="sueno" required></textarea>
                <div class="valid-feedback">Todo bien</div>
             <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>c) ELIMINACIONES:</label>
                <textarea name="eliminaciones" required></textarea>
                <div class="valid-feedback">Todo bien</div>
             <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>d) MANÍAS Y TICS:</label>
                <textarea name="manias_tics" required></textarea>
                <div class="valid-feedback">Todo bien</div>
             <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>e) HISTORIA SEXUAL:</label>
                <textarea name="historia_sexual" required></textarea>
                <div class="valid-feedback">Todo bien</div>
             <div class="invalid-feedback">Inválido</div>
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
                <textarea name="tendencias_destructivas" required pattern="[A-Za-z]+" title="Solo se permiten letras en tendencias destructivas"></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Solo puede contener letras</div>
        </div>


        <div class="section">
            <h2>VI. FACTORES HEREDITARIOS</h2>
            <div class="textarea-group">
                <label>INCIDENCIA DE ANOMALÍAS EN FAMILIARES CONSANGUÍNEOS:</label>
                <textarea name="incidencia_anomalias" required ></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>TRATAMIENTO MÉDICO POR NERVIOSISMO:</label>
                <textarea name="tratamiento_nerviosismo" required></textarea>
                <div class="valid-feedback">Todo bien</div>
             <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>ALCOHOLISMO (GRADO), MANIFESTACIONES, ETC:</label>
                <textarea name="alcoholismo" required></textarea>
                <div class="valid-feedback">Todo bien</div>
             <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>ABUSO DE DROGAS, CALMANTES, ETC:</label>
                <textarea name="abuso_drogas" required></textarea>
                <div class="valid-feedback">Todo bien</div>
             <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>DEBILIDAD MENTAL:</label>
                <textarea name="debilidad_mental" required pattern="[A-Za-z]+" title="Solo se permiten letras en DEBILIDAD MENTAL"></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Solo puede contener letras</div>
            </div>
            <div class="textarea-group">
                <label>CONVULSIONES, DESMAYOS, TEMBLORES, ETC:</label>
                <textarea name="convulsiones_desmayos" required ></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>ETS (ENFERMEDADES SEXUALES, FORMA, MOTIVOS):</label>
                <textarea name="ets" required pattern="[A-Za-z]+" title="Solo se permiten letras en ETS"></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Solo puede contener letras</div>
            </div>
            <div class="textarea-group">
                <label>SUICIDIO (FORMAS, MOTIVOS):</label>
                <textarea name="suicidio" required ></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>Anormalidades (PROSTITUCIÓN, CRIMINALIDAD, DELITOS, RECLUSIÓN, ETC):</label>
                <textarea name="anormalidades" required ></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Inválido</div>
            </div>
            <div class="textarea-group">
                <label>TRASTORNOS DEL HABLA (TARTAMUDEZ, SORDERA MUEDEZ, ETC):</label>
                <textarea name="trastornos_habla" required pattern="[A-Za-z]+" title="Solo se permiten letras en SUICIDIO (FORMAS, MOTIVOS)"></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Solo puede contener letras</div>
            </div>
            <div class="textarea-group">
                <label>TRASTORNOS DE LA VISTA (CEGUERA, MIOPIA, ETC):</label>
                <textarea name="trastornos_vista" required ></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Inválido</div>
            </div>
        </div>

        <div class="section">
            <h2>VII. IMPRESIÓN PSICOLÓGICA</h2>
            <div class="textarea-group">
                <label>(Signos y síntomas, personalidad, adaptación psicológica a la enfermedad, al tratamiento, cirugía, e internamientos, relación médico-paciente-enfermera, expectativas ante la patología):</label>
                <textarea name="impresion_psicologica" required ></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Inválido</div>
            </div>
        </div>

        <div class="section">
            <h2>VIII. RECOMENDACIONES</h2>
            <div class="textarea-group">
                <textarea name="recomendaciones" required></textarea>
                <div class="valid-feedback">Todo bien</div>
                <div class="invalid-feedback">Inválido</div>
            </div>
        </div>

        <div class="section">
            <h2>IX. PLAN PSICOTERAPÉUTICO</h2>
            <div class="textarea-group">
                <textarea name="plan_psicoterapeutico" required></textarea>
                <div class="valid-feedback">Todo bien</div>
             <div class="invalid-feedback">Inválido</div>
            </div>
        </div>
        <input type="submit" name="Save" value="Guardar">
    </form>
</body>
</div>

<div id="configuracion" class="container hide">
<body>
    <?php
$sql = "SELECT Dia FROM fecha WHERE Status = 1";
$result = $conn->query($sql);

$dias_no_disponibles = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $dias_no_disponibles[] = $row['Dia'];
    }
} ?>

    <h1>Modificar Fecha</h1>
    
    <!-- Formulario para agregar/modificar fecha -->
    <form action="modificar_fecha.php" method="post">
        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="dia" required>
        <button type="submit">Modificar Fecha</button>
    </form>
    
    <h2>Días No Disponibles</h2>
    <ul>
        <?php foreach ($dias_no_disponibles as $fecha): ?>
            <li><?php echo $fecha; ?></li>
        <?php endforeach; ?>
    </ul>
    </form>

    <div id="modificar-precios">

        <h1>Modificar Precios</h1>
        
        <form action="modificar_precios.php" method="post">
            <?php
            // Consulta para obtener los tipos de cita y sus precios
            $sql = "SELECT * FROM tipo_cita";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $id_tipo_cita = $row['Id_TipoCita'];
                    $tipo_cita = $row['Tipo'];
                    $precio = $row['Precio'];
            ?>
            <label for="precio_<?php echo $id_tipo_cita; ?>"><?php echo $tipo_cita; ?>:</label>
            <input type="number" id="precio_<?php echo $id_tipo_cita; ?>" name="precio_<?php echo $id_tipo_cita; ?>" value="<?php echo $precio; ?>" required>
            <?php
                }
            }
            ?>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>

</body>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

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

  function showconf() {
    var forms = document.querySelectorAll('.container');
    for (var i = 0; i < forms.length; i++) {
        forms[i].classList.add('hide');
    }
    document.getElementById('configuracion').classList.remove('hide');
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
            var estadoSelect = document.getElementById('state');
            var municipioSelect = document.getElementById('city');
            var estado_id = estadoSelect.value;

            if (estado_id === '') {
                municipioSelect.innerHTML = "<option value=''>Seleccione un estado primero</option>";
                municipioSelect.disabled = true;
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    municipioSelect.innerHTML = xhr.responseText;
                    municipioSelect.disabled = false;
                }
            };
            xhr.send('id_estado=' + estado_id);
        }
        function showMunicipiosPartner() {
            var estadoSelect = document.getElementById('state_partner');
            var municipioSelect = document.getElementById('city_partner');
            var estado_id = estadoSelect.value;

            if (estado_id === '') {
                municipioSelect.innerHTML = "<option value=''>Seleccione un estado primero</option>";
                municipioSelect.disabled = true;
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    municipioSelect.innerHTML = xhr.responseText;
                    municipioSelect.disabled = false;
                }
            };
            xhr.send('id_estado=' + estado_id);
        }

        function showMunicipiosP() {
            var estadoSelect = document.getElementById('state_p');
            var municipioSelect = document.getElementById('city_p');
            var estado_id = estadoSelect.value;

            if (estado_id === '') {
                municipioSelect.innerHTML = "<option value=''>Seleccione un estado primero</option>";
                municipioSelect.disabled = true;
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    municipioSelect.innerHTML = xhr.responseText;
                    municipioSelect.disabled = false;
                }
            };
            xhr.send('id_estado=' + estado_id);
        }

function showMunicipiosI() {
            var estadoSelect = document.getElementById('state_i');
            var municipioSelect = document.getElementById('city_i');
            var estado_id = estadoSelect.value;

            if (estado_id === '') {
                municipioSelect.innerHTML = "<option value=''>Seleccione un estado primero</option>";
                municipioSelect.disabled = true;
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    municipioSelect.innerHTML = xhr.responseText;
                    municipioSelect.disabled = false;
                }
            };
            xhr.send('id_estado=' + estado_id);
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

         function mostrarInsertar() {
            // Mostrar el formulario de inserción
            document.getElementById('historial-formulario').setAttribute('action', 'insertarDatoshm.php');
            document.getElementById('historial-formulario').style.display = 'block';
            document.getElementById('seccion-busqueda').style.display = 'none';
            document.getElementById('resultado-busqueda').innerHTML = ''; // Limpiar resultados anteriores
        }

        function mostrarBuscar() {
            // Mostrar la sección de búsqueda
            document.getElementById('historial-formulario').style.display = 'none';
            document.getElementById('seccion-busqueda').style.display = 'block';
            document.getElementById('resultado-busqueda').innerHTML = ''; // Limpiar resultados anteriores
        }

        function buscarHistorial() {
            var cedula = document.getElementById('cedula-busqueda').value;

            // Verificar que se haya ingresado una cédula
            if (!cedula) {
                alert('Ingrese una cédula para realizar la búsqueda.');
                return;
            }

            // Realizar una solicitud AJAX para buscar el historial médico por cédula
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Si la solicitud se completó correctamente, mostrar el resultado de la búsqueda
                        document.getElementById('resultado-busqueda').innerHTML = xhr.responseText;
                    } else {
                        // Si ocurrió un error, mostrar un mensaje de error
                        alert('Error al realizar la búsqueda. Inténtelo de nuevo.');
                    }
                }
            };

            // Configurar la solicitud AJAX
            xhr.open('GET', 'buscarHistorial.php?cedula=' + cedula, true);
            xhr.send();
        }

</script>

<script>

// Ejemplo de JavaScript inicial para deshabilitar el envío de formularios si hay campos no válidos
(function () {
  'use strict'

  // Obtener todos los formularios a los que queremos aplicar estilos de validación de Bootstrap personalizados
  var forms = document.querySelectorAll('.needs-validation')

  // Bucle sobre ellos y evitar el envío
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()

</script>

</body>
</html>




