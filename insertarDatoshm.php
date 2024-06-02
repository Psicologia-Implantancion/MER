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

     // Validación de campos obligatorios 
     if (empty($_POST['motivos_consulta']) || empty($_POST['referido_por']) || empty($_POST['diagnostico_organico']) 
     || empty($_POST['actitud_padres']) || empty($_POST['estado_emocional'])) {  
          echo '
         <script>
             alert("Por favor complete todos los campos obligatorios2.");
             window.history.back(); 
         </script>
     '; 
         exit();
     } 

// Expresión regular para permitir solo letras y espacios 
$pattern = '/^[a-zA-Z ]+$/'; 
 
if (!preg_match($pattern, $motivoConsulta)) { 
    echo '
    <script>
        alert("El motivo de consulta solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
'; 
    exit(); 
} 
 
if (!preg_match($pattern, $referido)) { 
    echo '
    <script>
        alert("El campo referido por solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
';  
    exit(); 
} 
 
if (!preg_match($pattern, $diagnostico_organico)) { 
    echo '
    <script>
        alert("El diagnóstico orgánico solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
'; 
    exit(); 
} 
 
if (!preg_match($pattern, $actitud_padres)) { 
    echo '
    <script>
        alert("El campo actitud de los padres solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
'; 
    exit(); 
} 
 
if (!preg_match($pattern, $estado_emocional)) {  
    echo '
    <script>
        alert("El campo estado emocional solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

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

// validacion

if (!preg_match($pattern, $desarrollo_prenatal)) { 
    echo "El campo motivo de consulta solo puede contener letras y espacios."; 
    exit(); 
} 
 
if (!preg_match($pattern, $desarrollo_primera_infancia)) { 
    echo "El campo referido por solo puede contener letras y espacios."; 
    exit(); 
} 

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




// Validación

if (!preg_match($pattern, $papa_trabajo_actual)) { 
    echo '
    <script>
        alert("El campo Trabajo papa actual motivo de consulta solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
'; 
    exit(); 
} 

if (!preg_match($pattern, $mama_trabajo_actual)) { 
    echo '
    <script>
        alert("El campo Trabajo mama actual motivo de consulta solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
'; 
    exit(); 
} 
 
if (!preg_match($pattern, $papa_nombre)) { 
    echo '
    <script>
        alert("El campo nombre padre solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
';  
    exit(); 
}

if (!preg_match($pattern, $papa_apellido)) { 
    echo '
    <script>
        alert("El campo apellido padre solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
'; 
    exit(); 
} 
 
if (!preg_match($pattern, $mama_nombre)) { 
    echo '
    <script>
        alert("El campo nombre madre solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
';  
    exit(); 
} 


 
if (!preg_match($pattern, $mama_apellido)) { 
    echo '
    <script>
        alert("El campo apellido madre solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
'; 
    exit(); 
} 
 

if (!preg_match($pattern, $papa_salud_fisica)) { 
    echo '
    <script>
        alert("El campo papa salud fisica solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
'; 
    exit(); 
} 
 
if (!preg_match($pattern, $mama_salud_fisica)) { 
    echo '
    <script>
        alert("El campo mama salud fisica solo puede contener letras y espacios.");
        window.history.back(); 
    </script>
'; 
    exit(); 
} 

if (!preg_match($pattern, $papa_nivel_educativo)) {  
    echo '
    <script>
        alert("El campo papa nivel educativo solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $mama_nivel_educativo)) {  
    echo '
    <script>
        alert("El campo mama nivel educativo solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}


$papa_horario_trabajo = $_POST['papa_horario_trabajo'];
$mama_horario_trabajo = $_POST['mama_horario_trabajo'];

// Definir el filtro para permitir solo letras, números, guiones y guiones bajos 
$filtro = '/^[a-zA-Z0-9_-]+$/'; 
 
// Validar $papa_horario_trabajo 
$papa_horario_trabajo = $_POST['papa_horario_trabajo'] ?? ''; 
if (filter_var($papa_horario_trabajo, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>$filtro))) === false) { 
    // El valor contiene caracteres especiales, puedes manejarlo de acuerdo a tus necesidades 
    echo '
    <script>
        alert("El valor de papa_horario_trabajo contiene caracteres especiales.");
        window.history.back(); 
    </script>
'; 
    exit(); 
} 
 
// Validar $mama_horario_trabajo 
$mama_horario_trabajo = $_POST['mama_horario_trabajo'] ?? ''; 
if (filter_var($mama_horario_trabajo, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>$filtro))) === false) { 
    // El valor contiene caracteres especiales, puedes manejarlo de acuerdo a tus necesidades 
    echo '
    <script>
        alert("El valor de mama_horario_trabajo contiene caracteres especiales.");
        window.history.back(); 
    </script>
'; 
    exit();
} 
    
$papa_habitos = $_POST['papa_habitos'];
$mama_habitos = $_POST['mama_habitos'];

     // Validación de campos obligatorios 
     if (empty($_POST['papa_habitos']) || empty($_POST['mama_habitos'])) {  
          echo '
         <script>
             alert("Por favor complete todos los campos obligatorios.");
             window.history.back(); 
         </script>
     '; 
         exit();
     } 

if (!preg_match($pattern, $papa_habitos)) {  
    echo '
    <script>
        alert("El campo habitos padre solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $mama_habitos)) {  
    echo '
    <script>
        alert("El campo habitos madre solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

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


     // Validación de campos obligatorios 
     if (empty($_POST['perdida_familiar']) || empty($_POST['quien_era']) || empty($_POST['como_fue']) 
     || empty($_POST['edad_nino']) || empty($_POST['presencio_suceso']) || empty($_POST['reaccion_nino']) 
     || empty($_POST['accidentes_nino']) || empty($_POST['castigos_graves']) || empty($_POST['parte_quien']) 
     || empty($_POST['edad_castigos']) || empty($_POST['problemas_causas']) || empty($_POST['problemas_fisicos'])) {  
          echo '
         <script>
             alert("Por favor complete todos los campos obligatorios3.");
             window.history.back(); 
         </script>
     '; 
         exit();
     } 

// Validacion 

if (!preg_match($pattern, $perdida_familiar)) {  
    echo '
    <script>
        alert("El campo perdida familiar solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $quien_era)) {  
    echo '
    <script>
        alert("El campo quien era solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $como_fue)) {  
    echo '
    <script>
        alert("El campo como fue solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $presencio_suceso)) {  
    echo '
    <script>
        alert("El campo presencio suceso solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $reaccion_nino)) {  
    echo '
    <script>
        alert("El campo reaccion nino solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $accidentes_nino)) {  
    echo '
    <script>
        alert("El campo accidente nino solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $castigos_graves)) {  
    echo '
    <script>
        alert("El campo accidente nino solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $parte_quien)) {  
    echo '
    <script>
        alert("El campo parte quien solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

// Validación para edad nino 
if (!is_numeric($_POST['edad_nino'])) {  
    echo '
    <script>
        alert("El edad niño solo debe contener valores numéricos.");
        window.history.back(); 
    </script>
'; 
    exit();  
}

if (!preg_match($pattern, $problemas_causas)) {  
    echo '
    <script>
        alert("El campo problemas causas solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $problemas_fisicos)) {  
    echo '
    <script>
        alert("El campo problemas fisicos solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

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

     // Validación de campos obligatorios 
     if (empty($_POST['incidencia_anomalias']) || empty($_POST['tratamiento_nerviosismo']) || empty($_POST['alcoholismo']) 
     || empty($_POST['abuso_drogas']) || empty($_POST['debilidad_mental']) || empty($_POST['convulsiones_desmayos']) 
     || empty($_POST['ets']) || empty($_POST['suicidio']) || empty($_POST['anormalidades']) 
     || empty($_POST['trastornos_habla']) || empty($_POST['trastornos_vista'])) {  
          echo '
         <script>
             alert("Por favor complete todos los campos obligatorios4.");
             window.history.back(); 
         </script>
     '; 
         exit();
     } 



// Validaciones 

if (!preg_match($pattern, $incidencia_anomalias)) {  
    echo '
    <script>
        alert("El campo incidencia anomalias solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $tratamiento_nerviosismo)) {  
    echo '
    <script>
        alert("El campo tratamienti nerviosismo solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $alcoholismo)) {  
    echo '
    <script>
        alert("El campo alcoholismo solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $abuso_drogas)) {  
    echo '
    <script>
        alert("El campo abusos de drogas solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $debilidad_mental)) {  
    echo '
    <script>
        alert("El campo debilidad mental solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $convulsiones_desmayos)) {  
    echo '
    <script>
        alert("El campo convulsiones, desmayos solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $ets)) {  
    echo '
    <script>
        alert("El campo ets solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $suicidio)) {  
    echo '
    <script>
        alert("El campo suicicio solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $anormalidades)) {  
    echo '
    <script>
        alert("El campo anormalidades solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $trastornos_habla)) {  
    echo '
    <script>
        alert("El campo trastorno habla solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $trastornos_vista)) {  
    echo '
    <script>
        alert("El campo trastorno vista solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

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
// validacion

if (!preg_match($pattern, $comida)) {  
    echo '
    <script>
        alert("El campo comida solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $sueno)) {  
    echo '
    <script>
        alert("El campo sueño solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $eliminaciones)) {  
    echo '
    <script>
        alert("El campo eliminaciones solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $manias_tics)) {  
    echo '
    <script>
        alert("El campo manias, tics solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $historia_sexual)) {  
    echo '
    <script>
        alert("El campo historial sexual solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}


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
 

 // Validación de campos obligatorios 
if (empty($_POST['nombre']) || empty($_POST['cedula']) || empty($_POST['fecha_nacimiento']) 
|| empty($_POST['escolaridad']) || empty($_POST['promedio']) || empty($_POST['escuela']) 
|| empty($_POST['lugar_familia'])) {  
     echo '
    <script>
        alert("Por favor complete todos los campos obligatorios9.");
        window.history.back(); 
    </script>
'; 
    exit();
} 
// vaidacion

if (!preg_match($pattern, $nombre)) {  
    echo '
    <script>
        alert("El campo nombre solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

// Validación de formato para el campo de cédula 
if (!preg_match('/^[0-9]+$/', $_POST['cedula'])) { 
    echo '
    <script>
        alert("La cédula debe contener solo números.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}
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

// Validación de la fecha de nacimiento 
$fechaNac = $_POST['fecha_nacimiento']; 
$hoy = date("Y-m-d"); 
 
if ($fechaNac >= $hoy) { 
    echo "La fecha de nacimiento debe ser anterior a la fecha actual."; 
    exit(); 
} 

if (!preg_match($pattern, $escolaridad)) {  
    echo '
    <script>
        alert("El campo escolaridad solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}


if (filter_var($promedio, FILTER_VALIDATE_FLOAT) === false) { 
    echo '
    <script>
        alert("El promedio debe contener solo números con decimales.");
        window.history.back(); 
    </script>
'; 

    exit(); 
} 

if (!preg_match($pattern, $escuela)) {  
    echo '
    <script>
        alert("El campo escuela solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

if (!preg_match($pattern, $lugar_familia)) {  
    echo '
    <script>
        alert("El campo lugar familiar solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}


 $direccion =  $_POST['direccion'];
 $telefono =  $_POST['telefono'];

 // Validar que los campos de dirección y teléfono no estén vacíos 
if (!isset($_POST['direccion']) || empty($_POST['direccion'])) { 
    echo '
    <script>
        alert("Error: La direccion es un campo obligatorio.");
        window.history.back(); 
    </script>
'; 
    exit();
     
} 
 
if (!isset($_POST['telefono']) || empty($_POST['telefono'])) { 
    echo '
    <script>
        alert("Error: El telefono es un campo obligatorio.");
        window.history.back(); 
    </script>
'; 
    exit();
     
} 

 if (!preg_match($pattern, $direccion)) {  
    echo '
    <script>
        alert("El campo nombre solo puede contener letras y espacioss.");
        window.history.back(); 
    </script>
'; 
    exit(); 
}

// Validación para teléfono 
if (!is_numeric($_POST['telefono'])) {  
    echo '
    <script>
        alert("El teléfono debe ser un valor numérico.");
        window.history.back(); 
    </script>
';   
    exit();  
}


$sql_identificacion = "INSERT INTO historial_medico (ID_Psicologo,ID_Factores,ID_Fisico,ID_Familiares,ID_Personalidad_Conducta,ID_Hereditario,ID_Impresion,ID_Pronostico,Escolaridad, Promedio, Escuela, LugaQueOcupaFamilia, Telefono, Direccion, Cedula, Nombre, Fecha_Nacimiento)
                    VALUES (1,'$motivacion_id',' $Ffisicos_id','$Ffamiliares_id',' $Conduc_id ','$hereditario_id','$impresion_id','$impresion_id','$escolaridad', '$promedio', '$escuela', '$lugar_familia', '$telefono', '$direccion', '$cedula','$nombre', '$fechaNac')";
if ($conn->query($sql_identificacion) === TRUE) {
    header("Location: index.php#agendar");
    exit();
}



    $conn->close();

}


?>