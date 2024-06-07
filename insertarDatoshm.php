<?php
session_start();

// Información de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "emocionvital";

// Crear conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}





// variables de motivacion a la consulta
if ($_SERVER["REQUEST_METHOD"] == "POST") {

$motivoConsulta = $_POST['motivos_consulta'];
$referido = $_POST['referido_por'];
$diagnostico_organico = $_POST['diagnostico_organico'];
$actitud_padres = $_POST['actitud_padres'];
$estado_emocional = $_POST['estado_emocional'];

     


$sql_motivacion = "INSERT INTO factores_que_motivan (FactoresQueMotivan, Referido_por, Diagnostico_Organico, ActitudDeLosPadres, EstadoEmocionalNiño)
                      VALUES ('$motivoConsulta', '$referido', '$diagnostico_organico', '$actitud_padres', '$estado_emocional')";
if ($conn->query($sql_motivacion) === TRUE) {
    // Obtiene el ID de la dirección insertada
    $motivacion_id = $conn->insert_id;
}else{
    echo "Error al insertar en la base de datos:  " . $conn->error;

}
// variables de factores fisicos

$desarrollo_prenatal = $_POST['desarrollo_prenatal'];
$desarrollo_primera_infancia = $_POST['desarrollo_primera_infancia'];


$Sql_Ffisicos = "INSERT INTO factoresfisicos (Desarollo_prenata_natal, Desarrollo_Primera_Infancia)
                      VALUES ('$desarrollo_prenatal', '$desarrollo_primera_infancia')";
if ($conn->query($Sql_Ffisicos) === TRUE) {
    // Obtiene el ID de la dirección insertada
    $Ffisicos_id = $conn->insert_id;
}else{
    echo "Error al insertar en la base de datos:  " . $conn->error;

}

// variables de factores familiares

$papa_nombre = $_POST['papa_nombre'];
$papa_apellido = $_POST['papa_apellido'];

$mama_nombre = $_POST['mama_nombre'];
$mama_apellido = $_POST['mama_apellido'];


$papa_salud_fisica = $_POST['papa_salud_fisica'];
$mama_salud_fisica = $_POST['mama_salud_fisica'];

$papa_nivel_educativo = $_POST['papa_nivel_educativo'];
$mama_nivel_educativo = $_POST['mama_nivel_educativo'];

$papa_trabajo_actual = $_POST['papa_trabajo_actual'];
$mama_trabajo_actual = $_POST['mama_trabajo_actual'];


$papa_horario_trabajo = $_POST['papa_horario_trabajo'];
$mama_horario_trabajo = $_POST['mama_horario_trabajo'];

    
$papa_habitos = $_POST['papa_habitos'];
$mama_habitos = $_POST['mama_habitos'];

    

// variables de EXPERIENCIAS TRAUMÁTICAS DEL NIÑO

$perdida_familiar = $_POST['perdida_familiar'];
$quien_era = $_POST['quien_era'];
$como_fue = $_POST['como_fue'];
$edad_nino = $_POST['edad_nino'];
$presencio_suceso = $_POST['presencio_suceso'];
$reaccion_nino = $_POST['reaccion_nino'];
$accidentes_nino = $_POST['accidentes_nino'];
$castigos_graves = $_POST['castigos_graves'];
$parte_quien = $_POST['parte_quien'];
$edad_castigos = $_POST['edad_castigos'];
$problemas_causas = $_POST['problemas_causas'];
$problemas_fisicos = $_POST['problemas_fisicos'];


$Sql_Ffamiliares = "INSERT INTO factoresfamiliares (Nombre_Madre, Apellido_Madre,Nombre_Padre,Apellido_Padre, Salus_Fisica_Madre, Salus_Fisica_Padre, Nivel_Educativo_Madre, Nivel_Educativo_Padre, Trabajo_Actual_Madre, Trabajo_Actual_Padre, Horario_Trabajo_Madre, Horario_Trabajo_Padre, Habitos_De_la_Madre, Habitos_De_la_Padre, Perdida_Algun_Familiar, Quien_Era, Como_fue, Edad_Que_Tenia_Infante, Presencio_Suceso, Reaccion_Del_Infante, Accidentes_Infante, Castigos_Graves, De_Parte_Quien, Edad_Infante, Problemas_Infante, Problemas_Fisicos)
                VALUES ('$mama_nombre', '$mama_apellido', '$papa_nombre', '$papa_apellido', '$mama_salud_fisica',' $papa_salud_fisica', '$mama_nivel_educativo', ' $papa_nivel_educativo',' $mama_trabajo_actual' , ' $papa_trabajo_actual', '$mama_horario_trabajo', ' $papa_horario_trabajo','$mama_habitos','$papa_habitos', '$perdida_familiar', '$quien_era', '$como_fue', ' $edad_nino', ' $presencio_suceso', ' $reaccion_nino', '$accidentes_nino', ' $castigos_graves', ' $parte_quien', ' $edad_castigos', ' $problemas_causas', ' $problemas_fisicos')";
if ($conn->query($Sql_Ffamiliares) === TRUE) {
    // Obtiene el ID de la direcci
    $Ffamiliares_id = $conn->insert_id;
}else{
    echo "Error al insertar en la base de datos:  " . $conn->error;

}



$incidencia_anomalias = $_POST['incidencia_anomalias'];
$tratamiento_nerviosismo = $_POST['tratamiento_nerviosismo'];
$alcoholismo = $_POST['alcoholismo'];
$abuso_drogas = $_POST['abuso_drogas'];
$debilidad_mental = $_POST['debilidad_mental'];
$convulsiones_desmayos = $_POST['convulsiones_desmayos'];
$ets = $_POST['ets'];
$suicidio = $_POST['suicidio'];
$anormalidades = $_POST['anormalidades'];
$trastornos_habla = $_POST['trastornos_habla'];
$trastornos_vista = $_POST['trastornos_vista'];


$Sql_FHereditarios = "INSERT INTO factores_hereditarios (Incidencias_Anomalias, Tratamientp_Medico, Alcoholismo, Abuso_Drogas, Debilidad_Mental, Convulciones_Desmayos_Temblores, ETS, Suicido, Anormalidades, Trastorno_Habla, Trastorno_Vista)
                VALUES ('$incidencia_anomalias', '$tratamiento_nerviosismo', ' $alcoholismo', '$abuso_drogas', '$debilidad_mental',' $convulsiones_desmayos', '$ets', ' $suicidio',' $anormalidades' , ' $trastornos_habla', '$trastornos_vista')";

if ($conn->query($Sql_FHereditarios) === TRUE) {
    // Obtiene el ID de la direcci
    $hereditario_id = $conn->insert_id;
}else{
    echo "Error al insertar en la base de datos:  " . $conn->error;

}

$impresion_psicologica = $_POST['impresion_psicologica'];

$Sql_impresion = "INSERT INTO impresion_psicologa (Opinion)
VALUES ('$impresion_psicologica')";

if ($conn->query( $Sql_impresion) === TRUE) {
// Obtiene el ID de la direcci
$impresion_id = $conn->insert_id;
}else{
    echo "Error al insertar en la base de datos:  " . $conn->error;

}

$recomendaciones = $_POST['recomendaciones'];
$plan_psicoterapeutico = $_POST['plan_psicoterapeutico'];

$Sql_pronostico = "INSERT INTO pronostico (Recomendaciones, Plan_Psicoterapeutico)
VALUES ('$recomendaciones', '$plan_psicoterapeutico')";

if ($conn->query( $Sql_pronostico) === TRUE) {
// Obtiene el ID de la direcci
$impresion_id = $conn->insert_id;
}else{
    echo "Error al insertar en la base de datos:  " . $conn->error;

}

// VARIABLES DE FACTORES DE LA PERSONALIDAD Y CONDUCTA


$comida = $_POST['comida'];
$sueno = $_POST['sueno'];
$eliminaciones = $_POST['eliminaciones'];
$manias_tics = $_POST['manias_tics'];
$historia_sexual = $_POST['historia_sexual'];




    $timido = isset($_POST['timido']) ? 1 : 0;
    $agresivo = (isset($_POST['agresivo'])) ? 1 : 0;
    $tranquilo = (isset($_POST['tranquilo'])) ? 1 : 0;
    $irritable = (isset($_POST['irritable'])) ? 1 : 0;
    $alegre = (isset($_POST['alegre'])) ? 1 : 0;
    $triste = (isset($_POST['triste'])) ? 1 : 0;
    $cooperativo = (isset($_POST['cooperativo'])) ? 1 : 0;
    $negativista = (isset($_POST['negativista'])) ? 1 : 0;
    $sereno = (isset($_POST['sereno'])) ? 1 : 0;
    $impulsivo = (isset($_POST['impulsivo'])) ? 1 : 0;
    $confiado = (isset($_POST['confiado'])) ? 1 : 0;
    $frio = (isset($_POST['frio'])) ? 1 : 0;
    $sociable = (isset($_POST['sociable'])) ? 1 : 0;
    $retardado = (isset($_POST['retardado'])) ? 1 : 0;
    $equilibrado = (isset($_POST['equilibrado'])) ? 1 : 0;
    $nervioso = (isset($_POST['nervioso'])) ? 1 : 0;
    $carinoso = (isset($_POST['carinoso'])) ? 1 : 0;
    $inseguro = (isset($_POST['inseguro'])) ? 1 : 0;
    $juega = (isset($_POST['juega'])) ? 1 : 0;
    $no_juega = (isset($_POST['no_juega'])) ? 1 : 0;
    $controlado = (isset($_POST['controlado'])) ? 1 : 0;
    $emotivo = (isset($_POST['emotivo'])) ? 1 : 0;
    $seguro = (isset($_POST['seguro'])) ? 1 : 0;
    $amable = (isset($_POST['amable'])) ? 1 : 0;
    $desconsiderado = (isset($_POST['desconsiderado'])) ? 1 : 0;
    $laborioso = (isset($_POST['laborioso'])) ? 1 : 0;
    $perezoso = (isset($_POST['perezoso'])) ? 1 : 0;
    $desconfiado = (isset($_POST['desconfiado'])) ? 1 : 0;
    $dominante = (isset($_POST['dominante'])) ? 1 : 0;
    $sumiso = (isset($_POST['sumiso'])) ? 1 : 0;
    $disciplinado = (isset($_POST['disciplinado'])) ? 1 : 0;
    $indisiplinado = (isset($_POST['indisiplinado'])) ? 1 : 0;
    $rebelde = (isset($_POST['rebelde'])) ? 1 : 0;
    $obediente = (isset($_POST['obediente'])) ? 1 : 0;
    $ordenado = (isset($_POST['ordenado'])) ? 1 : 0;
    $desordenado = (isset($_POST['desordenado'])) ? 1 : 0;
    $tendencias_destructivas = $_POST['tendencias_destructivas'];

     // Validación de campos obligatorios
    $sql_FConduc = "INSERT INTO factores_personalidad_conducta (Comida, Sueno, Eliminaciones, Manias_Tics, Historia_Sexual, Timido, Agresivo, Tranquilo, Irritable, Alegre, Triste, Cooperador, Negatividad, Sereno, Impulsivo, Confiado_en_si, Frio, Sociable, Retardado, Equilibrado, Nervioso, Cariñoso, Inseguro, Juega, No_juega, Controlado, Emotivo, Seguro, Amable, Desconsiderado, Laborioso, Perezoso, Desconfiado, Dominante, Sumiso, Disciplinado, Indisciplinado, Rebelde, Obediente, Ordenado, Desordenado, Tendencias_Destructivas)
                                                     VALUES ('$comida','$sueno ','$eliminaciones ','$manias_tics ','$historia_sexual ','$timido', '$agresivo', ' $tranquilo', '$irritable', '$alegre',' $triste', '$cooperativo', ' $negativista',' $sereno' , ' $impulsivo', '$confiado', ' $frio','$sociable','$retardado', '$equilibrado', '$nervioso', '$carinoso', ' $inseguro', ' $juega', ' $no_juega', '$controlado', ' $emotivo', ' $seguro', ' $amable', ' $desconsiderado', ' $laborioso', '$perezoso', '$desconfiado', ' $dominante', ' $sumiso', ' $disciplinado', '$indisiplinado', '$rebelde', '$obediente', ' $ordenado', ' $desordenado', ' $tendencias_destructivas')";

    if ($conn->query($sql_FConduc) === TRUE) {
        // Obtiene el ID de la direcci
        $Conduc_id = $conn->insert_id;
    }else{
        echo "Error al insertar en la base de datos:  " . $conn->error;
    
    }


    //variables de identificacion
  $nombre = $_POST['nombre'];
  $cedula = $_POST['cedula'];
 $fechaNac = $_POST['fecha_nacimiento'];
 $escolaridad = $_POST['escolaridad'];
 $promedio =  $_POST['promedio'];
 $escuela =  $_POST['escuela'];
 $lugar_familia =  $_POST['lugar_familia'];
 


// Comprobar si la cédula ya está registrada
$sql = "SELECT * FROM historial_medico WHERE Cedula = ? ";
$stmt = $conn->prepare($sql);

// Verificar si la preparación de la consulta fue exitosa
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Vincular el parámetro
$stmt->bind_param("s", $cedula);

// Ejecutar la declaración
$stmt->execute();

// Obtener el resultado
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si la cédula ya está registrada
    echo '
    <script>
        alert("La cédula ya se encuentra registrada.");
        window.history.back(); 
    </script>
    '; 
    exit(); 
}
 



 $direccion =  $_POST['direccion'];
 $telefono =  $_POST['telefono'];




 $sql_identificacion = "INSERT INTO historial_medico (ID_Psicologo,ID_Factores,ID_Fisico,ID_Familiares,ID_Personalidad_Conducta,ID_Hereditario,ID_Impresion,ID_Pronostico,Escolaridad, Promedio, Escuela, LugaQueOcupaFamilia, Telefono, Direccion, Cedula, Nombre, Fecha_Nacimiento)
 VALUES (1,'$motivacion_id',' $Ffisicos_id','$Ffamiliares_id',' $Conduc_id ','$hereditario_id','$impresion_id','$impresion_id','$escolaridad', '$promedio', '$escuela', '$lugar_familia', '$telefono', '$direccion', '$cedula','$nombre', '$fechaNac')";
if ($conn->query($sql_identificacion) === TRUE) {
header("Location: index.php#agendar");
exit();
}



    $conn->close();

}


?>