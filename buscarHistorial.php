<?php
// Iniciar la sesión
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

// Suponiendo que $cedula contiene la cédula a buscar
$cedula = $_GET['cedula'];

// Consulta SQL para buscar el historial médico por cédula
$sql = "SELECT 
    historial_medico.*,
    factores_que_motivan.FactoresQueMotivan,
    factores_que_motivan.Referido_por,
    factores_que_motivan.Diagnostico_Organico,
    factores_que_motivan.ActitudDeLosPadres,
    factores_que_motivan.EstadoEmocionalNiño,
    factoresfisicos.Desarollo_prenata_natal,
    factoresfisicos.Desarrollo_Primera_Infancia,
    factoresfamiliares.Nombre_Madre,
    factoresfamiliares.Apellido_Madre,
    factoresfamiliares.Nombre_Padre,
    factoresfamiliares.Apellido_Padre,
    factoresfamiliares.Salus_Fisica_Madre,
    factoresfamiliares.Salus_Fisica_Padre,
    factoresfamiliares.Nivel_Educativo_Madre,
    factoresfamiliares.Nivel_Educativo_Padre,
    factoresfamiliares.Trabajo_Actual_Madre,
    factoresfamiliares.Trabajo_Actual_Padre,
    factoresfamiliares.Horario_Trabajo_Madre,
    factoresfamiliares.Horario_Trabajo_Padre,
    factoresfamiliares.Habitos_De_la_Madre,
    factoresfamiliares.Habitos_De_la_Padre,
    factoresfamiliares.Perdida_Algun_Familiar,
    factoresfamiliares.Quien_Era,
    factoresfamiliares.Como_fue,
    factoresfamiliares.Edad_Que_Tenia_Infante,
    factoresfamiliares.Presencio_Suceso,
    factoresfamiliares.Reaccion_Del_Infante,
    factoresfamiliares.Accidentes_Infante,
    factoresfamiliares.Castigos_Graves,
    factoresfamiliares.De_Parte_Quien,
    factoresfamiliares.Edad_Infante,
    factoresfamiliares.Problemas_Infante,
    factoresfamiliares.Problemas_Fisicos,
    factores_personalidad_conducta.Historia_Sexual,
    factores_personalidad_conducta.Comida,
    factores_personalidad_conducta.Sueno,
    factores_personalidad_conducta.Eliminaciones,
    factores_personalidad_conducta.Manias_Tics,
    factores_personalidad_conducta.Timido,
    factores_personalidad_conducta.Agresivo,
    factores_personalidad_conducta.Tranquilo,
    factores_personalidad_conducta.Irritable,
    factores_personalidad_conducta.Alegre,
    factores_personalidad_conducta.Triste,
    factores_personalidad_conducta.Cooperador,
    factores_personalidad_conducta.Negatividad,
    factores_personalidad_conducta.Sereno,
    factores_personalidad_conducta.Impulsivo,
    factores_personalidad_conducta.Confiado_en_si,
    factores_personalidad_conducta.Frio,
    factores_personalidad_conducta.Sociable,
    factores_personalidad_conducta.Retardado,
    factores_personalidad_conducta.Equilibrado,
    factores_personalidad_conducta.Nervioso,
    factores_personalidad_conducta.Cariñoso,
    factores_personalidad_conducta.Inseguro,
    factores_personalidad_conducta.Juega,
    factores_personalidad_conducta.No_juega,
    factores_personalidad_conducta.Controlado,
    factores_personalidad_conducta.Emotivo,
    factores_personalidad_conducta.Seguro,
    factores_personalidad_conducta.Amable,
    factores_personalidad_conducta.Desconsiderado,
    factores_personalidad_conducta.Laborioso,
    factores_personalidad_conducta.Perezoso,
    factores_personalidad_conducta.Desconfiado,
    factores_personalidad_conducta.Dominante,
    factores_personalidad_conducta.Sumiso,
    factores_personalidad_conducta.Disciplinado,
    factores_personalidad_conducta.Indisciplinado,
    factores_personalidad_conducta.Rebelde,
    factores_personalidad_conducta.Obediente,
    factores_personalidad_conducta.Ordenado,
    factores_personalidad_conducta.Desordenado,
    factores_personalidad_conducta.Tendencias_Destructivas,
    factores_hereditarios.Incidencias_Anomalias,
    factores_hereditarios.Tratamientp_Medico,
    factores_hereditarios.Alcoholismo,
    factores_hereditarios.Abuso_Drogas,
    factores_hereditarios.Debilidad_Mental,
    factores_hereditarios.Convulciones_Desmayos_Temblores,
    factores_hereditarios.ETS,
    factores_hereditarios.Suicido,
    factores_hereditarios.Anormalidades,
    factores_hereditarios.Trastorno_Habla,
    factores_hereditarios.Trastorno_Vista,
    impresion_psicologa.Opinion,
    pronostico.Recomendaciones,
    pronostico.Plan_Psicoterapeutico
FROM 
    historial_medico 
INNER JOIN 
    factores_que_motivan ON historial_medico.ID_Factores = factores_que_motivan.ID_Factores 
INNER JOIN 
    factoresfisicos ON historial_medico.ID_Fisico = factoresfisicos.ID_Fisico 
INNER JOIN 
    factoresfamiliares ON historial_medico.ID_Familiares = factoresfamiliares.ID_Familiares 
INNER JOIN 
    factores_personalidad_conducta ON historial_medico.ID_Personalidad_Conducta = factores_personalidad_conducta.ID_Personalidad_Conducta 
INNER JOIN 
    factores_hereditarios ON historial_medico.ID_Hereditario = factores_hereditarios.ID_Hereditario 
INNER JOIN 
    impresion_psicologa ON historial_medico.ID_Impresion = impresion_psicologa.ID_Impresion 
INNER JOIN 
    pronostico ON historial_medico.ID_Pronostico = pronostico.ID_Pronostico 
WHERE 
    historial_medico.Cedula = '$cedula'";


$resultado = $conn->query($sql);


if ($resultado->num_rows > 0) {
    
    while ($fila = $resultado->fetch_assoc()) {
        echo "<h2>Datos de Identificacion:</h2>";
        echo "Nombre: " . $fila['Nombre'] . "<br>";
        echo "Cedula: " . $fila['Cedula'] . "<br>";
        echo "Fecha de Nacimiento: " . $fila['Fecha_Nacimiento'] . "<br>";
        echo "Telefono: " . $fila['Telefono'] . "<br>";
        echo "Direccion: " . $fila['Direccion'] . "<br>";
        echo "Escolaridad: " . $fila['Escolaridad'] . "<br>";
        echo "Lugar que ocupa en la familia: " . $fila['LugaQueOcupaFamilia'] . "<br>";
        echo "Promedio: " . $fila['Promedio'] . "<br>";
        echo "Escuela: " . $fila['Escuela'] . "<br>";

        echo "<h2>Factores que Motivan a la Consulta:</h2>";
        echo "Factores Que Motivan: " . $fila['FactoresQueMotivan'] . "<br>";
        echo "Referido por: " . $fila['Referido_por'] . "<br>";
        echo "Diagnostico Orgánico: " . $fila['Diagnostico_Organico'] . "<br>";
        echo "Actitud De Los Padres: " . $fila['ActitudDeLosPadres'] . "<br>";
        echo "Estado Emocional Niño: " . $fila['EstadoEmocionalNiño'] . "<br>";

        echo "<h2>Factores Físicos:</h2>";
        echo "Desarrollo Prenatal y Natal: " . $fila['Desarollo_prenata_natal'] . "<br>";
        echo "Desarrollo de la Primera Infancia: " . $fila['Desarrollo_Primera_Infancia'] . "<br>";

        echo "<h2>Factores Familiares:</h2>";
        echo "<h3>Datos Familiares:</h3>";
        echo "<h4>Datos del Padre:</h4>";
        echo "Nombre: " . $fila['Nombre_Padre'] . "<br>";
        echo "Apellido: " . $fila['Apellido_Padre'] . "<br>";
        echo "Salud física: " . $fila['Salus_Fisica_Padre'] . "<br>";
        echo "Nivel educativo: " . $fila['Nivel_Educativo_Padre'] . "<br>";
        echo "Trabajo actual: " . $fila['Trabajo_Actual_Padre'] . "<br>";
        echo "Horario de trabajo: " . $fila['Horario_Trabajo_Padre'] . "<br>";
        echo "Hábitos: " . $fila['Habitos_De_la_Padre'] . "<br>";
        echo "<h4>Datos de la Madre:</h4>";
        echo "Nombre: " . $fila['Nombre_Madre'] . "<br>";
        echo "Apellido: " . $fila['Apellido_Madre'] . "<br>";
        echo "Salud física: " . $fila['Salus_Fisica_Madre'] . "<br>";
        echo "Nivel educativo: " . $fila['Nivel_Educativo_Madre'] . "<br>";
        echo "Trabajo actual: " . $fila['Trabajo_Actual_Madre'] . "<br>";
        echo "Horario de trabajo: " . $fila['Horario_Trabajo_Madre'] . "<br>";
        echo "Hábitos: " . $fila['Habitos_De_la_Madre'] . "<br>";

        echo "<h3>Experiencias Traumáticas del Niño:</h3>";
        echo "Pérdida de algún familiar o ser querido: " . $fila['Perdida_Algun_Familiar'] . "<br>";
        echo "¿Quién era?: " . $fila['Quien_Era'] . "<br>";
        echo "¿Cómo fue?: " . $fila['Como_fue'] . "<br>";
        echo "Edad que tenía el niño: " . $fila['Edad_Que_Tenia_Infante'] . "<br>";
        echo "¿Presenció el suceso?: " . $fila['Presencio_Suceso'] . "<br>";
        echo "Reacción del niño ante esto: " . $fila['Reaccion_Del_Infante'] . "<br>";
        echo "Accidentes del niño: " . $fila['Accidentes_Infante'] . "<br>";
        echo "Castigos graves: " . $fila['Castigos_Graves'] . "<br>";
        echo "De parte de quién: " . $fila['De_Parte_Quien'] . "<br>";
        echo "Edad del niño: " . $fila['Edad_Infante'] . "<br>";
        echo "Los problemas del niño son causados por: " . $fila['Problemas_Infante'] . "<br>";
        echo "Problemas físicos: " . $fila['Problemas_Fisicos'] . "<br>";

        echo "<h2>Factores de la Personalidad y Conducta:</h2>";
        echo "<h3>Hábitos e Intereses:</h3>";
        echo "a) COMIDA: come bien, demasiado, desganado, aversiones, preferencias, etc:  " . $fila['Comida'] . "<br>";
        echo "b) SUEÑO: duerme bien, intranquilo, pesadillas, habla, grita en el sueño, miedo a dormir solo, prefiere dormir con el padre o madre, miedo a la obscuridad, etc:  " . $fila['Sueno'] . "<br>";
        echo "c) ELIMINACIONES: enuresis nocturnas, diurnas, se ensucia de día o de noche, diarreas frecuentes, estreñimiento habitual, etc: " . $fila['Eliminaciones'] . "<br>";
        echo "d) MANÍAS Y TICS: Se come las uñas,se jala el pelo, dedos en la nariz, muecas faciales, etc: " . $fila['Manias_Tics'] . "<br>";
        echo "e) HISTORIA SEXUAL: masturbación, seducción, juegos sexuales, etc: " . $fila['Historia_Sexual'] . "<br>";
        // Imprimir solo los rasgos de carácter que tengan un valor de 1
        $rasgos_caracter = [
            'Timido', 'Agresivo', 'Tranquilo', 'Irritable', 'Alegre', 'Triste',
            'Cooperador', 'Negatividad', 'Sereno', 'Impulsivo', 'Confiado_en_si',
            'Frio', 'Sociable', 'Retardado', 'Equilibrado', 'Nervioso', 'Cariñoso',
            'Inseguro', 'Juega', 'No_juega', 'Controlado', 'Emotivo', 'Seguro',
            'Amable', 'Desconsiderado', 'Laborioso', 'Perezoso', 'Desconfiado',
            'Dominante', 'Sumiso', 'Disciplinado', 'Indisciplinado', 'Rebelde',
            'Obediente', 'Ordenado', 'Desordenado'
        ];
        echo "<h3>Rasgos de Carácter:</h3>";
        foreach ($rasgos_caracter as $rasgo) {
            if ($fila[$rasgo] == 1) {
                echo str_replace("_", " ", ucfirst($rasgo)) . ": Sí<br>";
            }
        }
        echo "Tendencias Destructivas: " . $fila['Tendencias_Destructivas'] . "<br>";

        echo "<h2>Factores Hereditarios:</h2>";
        echo "Incidencia de anomalías en familiares consanguíneos: " . $fila['Incidencias_Anomalias'] . "<br>";
        echo "Tratamiento médico por nerviosismo: " . $fila['Tratamientp_Medico'] . "<br>";
        echo "Alcoholismo (grado), manifestaciones, etc: " . $fila['Alcoholismo'] . "<br>";
        echo "Abuso de drogas, calmantes, etc: " . $fila['Abuso_Drogas'] . "<br>";
        echo "Debilidad mental: " . $fila['Debilidad_Mental'] . "<br>";
        echo "Convulsiones, desmayos, temblores, etc: " . $fila['Convulciones_Desmayos_Temblores'] . "<br>";
        echo "ETS (enfermedades sexuales, forma, motivos): " . $fila['ETS'] . "<br>";
        echo "Suicidio (formas, motivos): " . $fila['Suicido'] . "<br>";
        echo "Anormalidades (prostitución, criminalidad, delitos, reclusión, etc): " . $fila['Anormalidades'] . "<br>";
        echo "Trastornos del habla (tartamudez, sordera mudez, etc): " . $fila['Trastorno_Habla'] . "<br>";
        echo "Trastornos de la vista (ceguera, miopía, etc): " . $fila['Trastorno_Vista'] . "<br>";

        echo "<h2>Impresión Psicológica:</h2>";
        echo "Signos y síntomas, personalidad, adaptación psicológica a la enfermedad, al tratamiento, cirugía, e internamientos, relación médico-paciente-enfermera, expectativas ante la patología: " . $fila['Opinion'] . "<br>";

        echo "<h2>Recomendaciones:</h2>";
        echo $fila['Recomendaciones'] . "<br>";

        echo "<h2>Plan Psicoterapéutico:</h2>";
        echo $fila['Plan_Psicoterapeutico'] . "<br>";
    }
} else {
    // Si no se encontraron resultados
    echo "No se encontraron resultados para la cédula ingresada.";
}

// Cerrar la conexión
$conn->close();
?>


