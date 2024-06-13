
Para evitar que se sume el IVA nuevamente a los precios de los tipos de cita que no han sido modificados, necesitas modificar la lógica para calcular el nuevo precio solo si el precio del tipo de cita ha sido cambiado.

Aquí tienes una forma de hacerlo:

php
Copiar código
<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "emocionvital";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener y procesar los precios enviados por el formulario
foreach ($_POST as $key => $value) {
    // Verificar si el nombre del campo comienza con "precio_"
    if (strpos($key, 'precio_') === 0) {
        // Obtener el ID del tipo de cita desde el nombre del campo
        $id_tipo_cita = substr($key, strlen('precio_'));

        // Consultar el precio original del tipo de cita
        $sql_price = "SELECT Precio FROM tipo_cita WHERE Id_TipoCita = ?";
        $stmt_price = $conn->prepare($sql_price);
        $stmt_price->bind_param("i", $id_tipo_cita);
        $stmt_price->execute();
        $result_price = $stmt_price->get_result();
        $row_price = $result_price->fetch_assoc();
        $precio_original = $row_price['Precio'];

        // Verificar si el precio ha sido cambiado
        if ($value != $precio_original) {
            // Calcular el nuevo precio con el IVA
            $iva = $value * 0.16; // Calcular el valor del IVA (16%)
            $precio_con_iva = $value + $iva; // Calcular el precio con el IVA

            // Actualizar el precio del tipo de cita en la base de datos
            $sql_update = "UPDATE tipo_cita SET Precio = ? WHERE Id_TipoCita = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("di", $precio_con_iva, $id_tipo_cita);
            $stmt_update->execute();
        }
    }
}

// Redirigir de vuelta a la página de configuración
header("Location: index.php#configuracion");
exit();
?>
