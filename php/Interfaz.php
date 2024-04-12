<?php
include 'curl_functions.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST["cedulaEmpleador"])){
        //Si el valor que viene en el campo  de cedula es numerico lo guardamos en la variable $cedula, si no generamos un error.
        $cedula = $_POST["cedulaEmpleador"];  //capturamos el valor de la cedula del empleador
        //echo "El Numero Cedula del empleador es: " .$cedula;   //mostramos en pantalla el numero de cédula del empleador
    }else{
        echo "Error en el documento recibido
        <br>Verifique nuevamente  para enviar los datos";
        exit();
    }           

    #VALIDAR CLIENTE
    $datosClienteArray = ObtenerDatosCliente($cedula);
    // Verificas si la decodificación fue exitosa y si el nombre del cliente está presente en los datos
    if ($datosClienteArray) {
            
        // Array con los nombres de los campos que deseas obtener
        $camposDeseados = array('No Incluye Auxilio de Tte', 'Frecuencia de Pago', 'namecontact', 'ID Contacto' , 'Condicion Laboral' ,'Tipo de Labor', 'Nombre Empleado', 'ID Empleado' );
        
        // Array para almacenar los valores obtenidos
        $valoresCliente = array();
        
        // Iteras sobre cada campo deseado y obtienes su valor del array de datos del cliente
        foreach ($camposDeseados as $campo) {
            // Verificas si el campo existe en los datos del cliente y lo agregas al array de valores
            if (isset($datosClienteArray['data'][0][$campo])) {
                $valoresCliente[$campo] = $datosClienteArray['data'][0][$campo];
            } else {
                // Si el campo no existe, puedes asignar un valor predeterminado o dejarlo en blanco
                $valoresCliente[$campo] = 'No disponible';
            }
        }
        
        // Ahora $valoresCliente contiene todos los valores que necesitas del cliente
        // Puedes acceder a cada valor utilizando su nombre de campo como clave en el array
        foreach ($valoresCliente as $campo => $valor) {
            echo ucfirst($campo) . ": " . $valor . "<br>"; // ucfirst() para capitalizar el nombre del campo
        }
    } else {
        // Manejar el caso en el que no se pudieron obtener los datos del cliente
        echo "No se pudieron obtener los datos del cliente.";
    }
        
    #VALIDAR NOMINA
    $curlNomina = curl_init();
    
    curl_setopt_array($curlNomina, array(
        CURLOPT_URL => 'https://crm.wolkvox.com/server/API/v2/custom/query.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "operation":"techcon",
            "wolkvox-token":"7b74656368636f6e7d2d7b32303232303632343132323830357d",
            "module":"Nomina",
            "field":"ID Contacto",
            "value": '.$cedula.'
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: PHPSESSID=gmdot8iv0o2gk6p3nof7c4vn08'
        ),
    ));

    $responseNomina = curl_exec($curlNomina);
    
    curl_close($curlNomina);
    #echo $responseNomina;
    
    # FIN VALIDACION NOMINA
    
    # CONVERTIR RESPUESTA DE NOMINA A JSON
    $responseNominaJson =json_decode($responseNomina, false);
    
    # VALIDACION NOMINA CON 0 REGISTROS
    if($responseNominaJson -> msg == "0 records were are found"){
        
        # LLEVAR A PRIMER CONTACTO HTML CON DATOS DE MODULO CONTACTO 
        echo '<script type="text/javascript">'
        , 'document.getElementById("nominaEncontrada").innerHTML = "No se Encontró Registro de nomina"'
        , '</script>'
        ;
    }
    
    # FIN VALIDACION NOMINA CON 0 REGISTROS
    
    else{
        # VALIDACION NOMINA CON 1 O MAS REGISTROS
        $decode_jsonNomina = json_decode($responseNomina, true);
        $dataNomina = $decode_jsonNomina["data"];
        
        foreach ($dataNomina as $datosNomina) {        
            
            $baseMinimo         = obtenerValorSeguro($datosNomina["Base Minimo"]);
            $nombreEmpleadorNom = obtenerValorSeguro($datosNomina["Nombre contacto"]);
            $nombreEmpleado     = obtenerValorSeguro($datosNomina["Nombre Empleado"]);
            $tipoLabor          = obtenerValorSeguro($datosNomina["Tipo de Labor"]);
            $periodoNomina      = obtenerValorSeguro($datosNomina["Periodo Nomina"]);
            $pagoDiario         = obtenerValorSeguro($datosNomina["Salario por dia"]);
            $diasLaborados      = obtenerValorSeguro($datosNomina["Dias Laborados"]);
            $bonificacion       = obtenerValorSeguro($datosNomina["Bonificacion"]);
            $bonoDiario         = obtenerValorSeguro($datosNomina["Bono Diario"]);
            $deduccionPension   = obtenerValorSeguro($datosNomina["Deduccion Pension"]);
            $deduccionSalud     = obtenerValorSeguro($datosNomina["Deduccion Salud"]);
            $valorHorasExtr     = obtenerValorSeguro($datosNomina["Vlr Horas Extras"]);
            $rutaArchivo        = obtenerValorSeguro($datosNomina["Archivo Liquidacion"]);
            $totalAuxTte1       = obtenerValorSeguro($datosNomina["Aux Transporte"]);
            $valorPrima         = obtenerValorSeguro($datosNomina["Vlr Prima"]);
            $periodoQuincena    = obtenerValorSeguro($datosNomina["Quincena"]); 
            $periodoNomina      = obtenerValorSeguro($datosNomina["Periodo Nomina"]);   
            $totalDevengado     = obtenerValorSeguro($datosNomina["Total Devengado"]);
            $pagoNeto           = obtenerValorSeguro($datosNomina["Neto a Pagar"]);
            $wolkvox_id_nom     = obtenerValorSeguro($datosNomina["wolkvox_id"]);

            $idNomina = $datosNomina["Nombre contacto"]["value_id"];

            
            
            // Imprimir resultados para verificar
            #echo "Salario por dia: " . $pagoDiario . "<br/>";                                               
            if($wolkvox_id_nom != NULL){
                //DETENER FOREACH
                break;
            }
        }
    }
}else{
    echo "Error en el metodo post
    <br>Verifique nuevamente  para enviar los datos";
    exit();
}


validarDosUltimos($responseNomina);


 # INICIALIZACIÓN DE VARIABLES
    $frecuenciaPago = $valoresCliente["Frecuencia de Pago"];
    $bool_aux_tte = $valoresCliente["No Incluye Auxilio de Tte"];
    echo "incluye Auxilio de Tte: " .$bool_aux_tte. "<br/>";
    
    #si el auxilio de transporte llega como false se inicializa en 0
    if ($bool_aux_tte == true ){
        $auxTte = 0;
    }else{
        $auxTte = $totalAuxTte1;
    }
    
    if ($frecuenciaPago == "Mensual" ){
        $desprendibleNomina = "Mensual";
    }
    
    echo "Auxilio de Tte: " .$auxTte. "<br/>";


/* ------------------------FUNCIONES PHP------------------------------- */
// Definir una función para obtener valores seguros
function obtenerValorSeguro($clave, $default = null){
    // Verificar si la clave existe y no es nula
    if (isset($clave)) {
        // Verificar si la clave es un array con un campo "value"
        if (is_array($clave) && isset($clave['value'])) {
            return $clave['value'];
        } elseif ($clave !== "" || is_numeric($clave)) {
            // Si no es un array, o es un array sin "value", verificar si es un string no vacío o un número
            return $clave;
        }
    }

    return $default;
}

function validarDosUltimos($jsonResp){    
        
    // Tu JSON completo
    #$json = $jsonResp; // Aquí debes colocar el JSON completo
    
    // Decodificar el JSON
    $data = json_decode($jsonResp, true);
    $dataNomina = $data["data"];
    // Obtener los últimos dos registros
    $ultimosDosRegistros = array_slice($dataNomina, 0, 2);

    // Inicializar arrays para almacenar los resultados
    $baseMinimo = [];
    $diasLaboradosArray = [];
    $bonificacion = [];
    $deduccionPensionArray = [];
    $deduccionSaludArray = [];
    $valorHorasExtr = [];


    // Iterar sobre los últimos dos registros
    foreach ($ultimosDosRegistros as $registro) {
        
        /* capturar resultado de ultimos dos documentos del json para cada variable obtenida en el for each */

        $baseMinimo[]               = obtenerValorSeguro($registro["Base Minimo"]);           
        $diasLaboradosArray[]       = obtenerValorSeguro($registro["Dias Laborados"]);
        $bonificacion[]             = obtenerValorSeguro($registro["Bonificacion"]);
        $deduccionPensionArray[]    = obtenerValorSeguro($registro["Deduccion Pension"]);
        $deduccionSaludArray[]      = obtenerValorSeguro($registro["Deduccion Salud"]);
        $valorHorasExtr[]           = obtenerValorSeguro($registro["Vlr Horas Extras"]);
        $periodoQuincena[]          = obtenerValorSeguro($registro["Quincena"]);
        $periodoNomina[]            = obtenerValorSeguro($registro["Periodo Nomina"]);

        $pagoDiarioArray[]          = obtenerValorSeguro($registro["Salario por dia"]);
        $auxTteArray[]              = obtenerValorSeguro($registro["Aux Transporte"]);
        $totalDevengadoArray[]      = obtenerValorSeguro($registro["Total Devengado"]);

    }
    
    $deduccionPensionNomina1 = $deduccionPensionArray[0];
    $deduccionPensionNomina2 = $deduccionPensionArray[1];
    $deduccionSaludNomina1 = $deduccionSaludArray[0];
    $deduccionSaludNomina2 = $deduccionSaludArray[1];
    $periodoNomina1 = $periodoNomina[0];        
    $periodoNomina2 = $periodoNomina[1];        
    $periodoQuincena1 = $periodoQuincena[0];
    $periodoQuincena2 = $periodoQuincena[1];

    $diasLaboradosActual = $diasLaboradosArray[0];
    $diasLaboradosAnterior = $diasLaboradosArray[1];



    # EXPORTAR RESULTADOS DE VARIABLES DE DEDUCCION EN SALUD Y PENSION

return  array( 'Deduccion_Pension_anterior_Quincena' => $deduccionPensionNomina2,
                'Deduccion_Pension_Actual_Quincena' => $deduccionPensionNomina1,
                'Deduccion_Salud_anterior_Quincena' => $deduccionSaludNomina2,
                'Deduccion_Salud_Actual_Quincena' => $deduccionSaludNomina1,
                'Deducción_en_Salud' => $deduccionSaludNomina1 + $deduccionSaludNomina2,
                'Deducción_en_Pension' => $deduccionPensionNomina1 + $deduccionPensionNomina2,
                'Periodo_Nomina_Actual' => $periodoNomina1,        
                'Periodo_Nomina_Anterior' => $periodoNomina2,        
                'Periodo_Quincena_Actual' => $periodoQuincena1,        
                'Periodo_Quincena_Anterior' => $periodoQuincena2,
                'Dias_laborados_actuales' => $diasLaboradosActual,
                'Dias_laborados_anteriores' => $diasLaboradosAnterior,
                'Pago_Diairio_Anterior' => $pagoDiarioArray[1],
                'Auxilio_TransPorte_Anterior' => $auxTteArray[1],
                'Total_Devengado_Anterior' => $totalDevengadoArray[1]
            );

}

# ALMACENAR EN VARIABLES EL RETURN DE LA FUNCION validarDosUltimos
$resultUtimas2Quincenas = validarDosUltimos($responseNomina);
$resultJson = json_encode($resultUtimas2Quincenas);

$decode_Ultimos2Validados = json_decode($resultJson, false);

$deducionPensionActual = $decode_Ultimos2Validados -> Deduccion_Pension_Actual_Quincena;
$deducionPensionAnteri = $decode_Ultimos2Validados -> Deduccion_Pension_anterior_Quincena;
$deducionSaludActual = $decode_Ultimos2Validados -> Deduccion_Salud_Actual_Quincena;
$deducionSaludAnteri = $decode_Ultimos2Validados -> Deduccion_Salud_anterior_Quincena;
$periodoNominaActual = $decode_Ultimos2Validados -> Periodo_Nomina_Actual;
$periodoNominaAnteri = $decode_Ultimos2Validados -> Periodo_Nomina_Anterior;
$periodoQuincenaActual = $decode_Ultimos2Validados -> Periodo_Quincena_Actual;
$periodoQuincenaAnterior = $decode_Ultimos2Validados -> Periodo_Quincena_Anterior;
$diasLaboradosActual = $decode_Ultimos2Validados -> Dias_laborados_actuales;
$diasLaboradosAnterior = $decode_Ultimos2Validados -> Dias_laborados_anteriores;
$salarioxDiaAnterior = $decode_Ultimos2Validados -> Pago_Diairio_Anterior;
$transporteAnterior = $decode_Ultimos2Validados -> Auxilio_TransPorte_Anterior;
$totalDevengadoAnterior = $decode_Ultimos2Validados -> Total_Devengado_Anterior;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interfaz CRM Unifika</title>
    <!-- Insertar Boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

</head>

<body>

    <main>
        <div class="container">
            <h1>Liquidacion Nomina Unifika</h1>
            <span>
                En UNIFIKA te apoyamos con la gestion de tus empleados por ello
                queremos confirmar los siguientes datos para la generación del
                comprobante de nomina.
            </span>

            <br><br>

            <div class="datosBasicos" id="datos_Basicos">

                <div class="empleador">
                    <div class="row">
                        <h2>Empleador</h2>
                        <div class="col">
                            <h5>Nombre Empleador</h5>
                            <span id="Nombre_Empleador"><?php print_r($valoresCliente["namecontact"]) ?></span>
                        </div><!-- Fin Col -->

                        <div class="col">
                            <h5>ID Empleador</h5>
                            <span id="Id_Empleador"><?php print_r($valoresCliente["ID Contacto"])  ?></span>
                        </div><!-- Fin Col -->
                    </div><!-- Fin Row -->
                </div><!-- Fin Empelador -->

                <br><br>

                <div class="empleado">
                    <div class="row">
                        <h2>Empleado</h2>
                        <div class="col">
                            <h5>Nombre Empleado</h5>
                            <span id="Nombre_Empleado"><?php print_r($valoresCliente["Nombre Empleado"]) ?></span>
                        </div>
                        <div class="col">
                            <h5>Id Empleado</h5>
                            <span id="ID_Empleado"><?php print_r($valoresCliente["ID Empleado"]) ?></span>
                        </div>
                        <div class="col">
                            <h5>Condicion Laboral</h5>
                            <span id="Tipo_Contrato"><?php print_r($valoresCliente["Condicion Laboral"]) ?></span>
                        </div>
                        <div class="col">
                            <h5>Tipo de Labor</h5>
                            <span id="Condicion_Laboral"><?php print_r($valoresCliente["Tipo de Labor"]) ?></span>
                        </div>
                    </div><!-- Fin Row Empleado -->
                </div><!-- FIn Empleado -->

                <br><br>

                <div class="nomina">

                    <div class="row">
                        <div class="col">
                            <p>
                                <?php echo $periodoQuincenaActual  . " " .  $periodoNominaActual ?>,
                                
                                <br><br><br>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>PagoDiario</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $pagoDiario ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Dias Laborados</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $diasLaborados?> </div>
                                </div>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Salario Base</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $pagoDiario * $diasLaborados?> </div>
                                </div>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Auxilio de Tte</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $auxTte ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Aux No Salarial</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $bonificacion ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Horas Extras </span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $valorHorasExtr ?> </div>
                                </div>

                                <div id="guion">____________________________________________________<br></div>


                                <div class="row">
                                    <div class="col col-sm-4"> <span>Total Devengado</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $totalDevengado ?> </div>
                                </div>


                                <br>
                                <div id="deducciones">Deducciones<br></div>
                                <div id="totalDeducciones">
                                    <p>
                                        Pension: <?php echo $deduccionPension ?> -- Salud:
                                        <?php echo $deduccionSalud  ?>
                                    </p>

                                </div>

                                <div id="netoAPagar">Neto A Pagar: <?php print_r($pagoNeto) ?> </div>

                            </p>

                        </div>

                        <br><br>

                        <div class="col">
                            <p>
                                <?php echo $periodoQuincenaAnterior ." ". $periodoNominaAnteri  ?>
                                <br><br><br>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Salario Diario</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $salarioxDiaAnterior ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Dias Laborados</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $diasLaboradosAnterior?> </div>
                                </div>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Salario Base</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3">
                                        <?php echo $salarioxDiaAnterior * $diasLaboradosAnterior?> </div>
                                </div>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Auxilio de Tte</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $transporteAnterior ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Aux No Salarial</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $bonificacion ?> </div>
                                </div>

                                <div class="row">
                                    <div class="col col-sm-4"> <span>Horas Extras </span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $valorHorasExtr ?> </div>
                                </div>

                                <div id="guion">____________________________________________________<br></div>


                                <div class="row">
                                    <div class="col col-sm-4"> <span>Total Devengado</span> </div>
                                    <div class="col col-sm-3"> <span>____________</span> </div>
                                    <div class="col col-sm-3"> <?php echo $totalDevengadoAnterior ?> </div>
                                </div>


                                <br>
                                <div id="deducciones">Deducciones<br></div>
                                <div id="totalDeducciones">
                                    <p>
                                        Pension: <?php echo $deducionPensionAnteri ?> -- Salud:
                                        <?php echo $deducionSaludAnteri ?>
                                    </p>

                                </div>

                                <div id="netoAPagar">Neto A Pagar: <?php print_r($pagoNeto) ?> </div>

                                <br><br>
                            </p>

                        </div>
                    </div>

                </div><!-- fin div Nomina -->

                
                
                
            </div><!-- fin Datos Basicos -->
            <hr>

            <div id="opcionBotones">
                <h6>Deseas Actualizar Alguno de Estos Datos?</h6>
            </div>


            <div class="botones" >
                <button type="submit" class="btn btn-primary mb-3" id="actualizar_Valor" onclick="MostrarValoresActualziables()">SI, Actualizar Datos</button>
            </div>

            <div class="botonImprimir">
                <button type="submit" class="btn btn-primary mb-3">NO, Imprimir Comprobante</button>
            </div>

            <hr>


            <!-- ------------------------------------------------------------------------------------------------------------------------------------------ -->
            <div class="valores-a-actualizar" id="valores_a_actualizar" hidden>

                <div class="fila-PagoDiario">

                    <div class="row">
                        <div class="col">
                            <h6>Salario Diario</h6>
                        </div>

                        <div class="col">
                            <input class="form-control form-control-sm" type="number" id="salarioDiario"
                                value="<?php print_r($salarioDiario) ?>" >
                        </div>


                        <div class="col">
                            <h6>Días Laborados</h6>
                        </div>

                        <div class="col">
                            <input class="form-control form-control-sm" type="text"
                                value="<?php print_r($diasLaborados) ?>" >
                        </div>



                    </div><!-- Fin Row -->
                </div> <!-- Fin fila Salario Diario y Dias Laborados -->

                <hr>

                <div class="fila-AuxTte">

                    <div class="row">
                        <div class="col">
                            <h6>Auxilio de Transporte</h6>
                        </div>

                        <div class="col">
                            <input class="form-control form-control-sm" type="text" value="<?php print_r($auxTte) ?>"
                                aria-label="readonly input">
                        </div>

                        <div class="col">
                            <h6>Bonificación</h6>
                        </div>

                        <div class="col">
                            <input class="form-control form-control-sm" type="text"
                                value="<?php print_r($bonificacion) ?>" aria-label="readonly input">
                        </div>


                    </div><!-- Fin Row -->
                </div> <!-- Fin fila Auxilio de Tte y bonificacion-->


                <hr>


                <div class="fila-Cesantias">

                    <div class="row">
                        <div class="col">
                            <h6>Cesantías</h6>
                        </div>

                        <div class="col">
                            <input class="form-control form-control-sm" type="text" value="Valor_Cesantías"
                                aria-label="readonly input">
                        </div>

                        <div class="col">
                            <h6>Vacaciones</h6>
                        </div>

                        <div class="col">
                            <input class="form-control form-control-sm" type="text" value="Valor_Vacaciones"
                                aria-label="readonly input">
                        </div>

                    </div><!-- Fin Row -->
                </div> <!-- Fin fila Cesantías y vacaciones -->

                <hr>

                <div class="fila-Prestamo">

                    <div class="row">

                        <div class="col">
                            <h6>Prima</h6>
                        </div>

                        <div class="col">
                            <input class="form-control form-control-sm" type="text"
                                value="<?php print_r($valorPrima) ?>" aria-label="readonly input">
                        </div>

                        <div class="col ">
                            <h6>Prestamo</h6>
                        </div>

                        <div class="col ">
                            <input class="form-control form-control-sm" type="text" value="Valor_Prestamo"
                                aria-label="readonly input" readonly>
                        </div>
                    </div> <!-- Fin Row -->
                </div><!-- Fin fila Prima y Prestamo -->

                <hr>

                <!-- //Se debe Validar cada tipo de hora que sequiera Modificar en cantidad, no en valor. -->
                <div class="fila-horasExtras">

                    <div class="row row-HorasExtras">
                        <h6>Horas Extras</h6>

                        <div class="col">
                            <label for="HED">Hora Extra Diurna</label>
                            <input class="form-control form-control-sm" type="number" id="HED" value="" min="0"
                                aria-label="readonly input" >
                        </div>

                        <div class="col">
                            <label for="HDN">Hora Extra nocturna</label>
                            <input class="form-control form-control-sm" id="HDN" type="number" value=""
                                min="0" aria-label="readonly input" >
                        </div>

                        <div class="col">
                            <label for="HEDD">Hora Extra Dom/Fest Diurna</label>
                            <input class="form-control form-control-sm" id="HEDD" type="number"
                                value="" min="0" aria-label="readonly input" >
                        </div>

                        <div class="col">
                            <label for="HEDN">Hora Extra Dom/Fest nocturna</label>
                            <input class="form-control form-control-sm" id="HEDN" type="number"
                                value=""  min="0" aria-label="readonly input" >
                        </div>
                    </div><!-- Fin Row -->
                </div> <!-- Fin fila Horas Extras -->

                <hr>

                <div class="col col-sm-3">
                    <input type="button" class="btn btn-primary" value="Guardar Cambios" onclick="ActualizarCRM()">
                </div>


            </div> <!-- Fin Valores a Actualizar -->

        </div> <!-- fin container -->

        <!-- FORMULARIO PARA PASAR VALORES AL UPDATE -->
        <form id="formulariocrm" action="updateNomina.php" method="post">
            <!-- Tus inputs y otros elementos del formulario aquí -->
            <input type="text" name="salarioxDia" id="salarioxDia" hidden>
            <input type="text" name="idNomina" id="idNomina" value="<?php print_r($idNomina) ?>" hidden>
            <input type="text" name="empleador" id="empleador" value="<?php print_r($nombreEmpleadorNom); ?>" hidden>
            <input type="text" name="IdEmpleador" id="idEmpleador" value="<?php print_r($nroDocEmpleador); ?>" hidden>
            <input type="text" name="empleado" id="empleado" value="<?php print_r($nombreEmpleadoContactos); ?>" hidden>
            <input type="text" name="docEmpleado" id="docEmpleado" value="<?php print_r($nroDocEmpleadoCon); ?>" hidden>
            <input type="text" name="wolkbox_id" id="wolkbox_Id" value="<?php print_r($wolkvox_id_nom); ?>" hidden>

            <?php # echo "La variable Wolbox_ID contienes el valor: "  . $wolkvox_id;?>
            
        </form>
    </main>

    <!-- Script boostrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    < <script>

        function MostrarValoresActualziables() {
            console.log("Boton Actualizar Presionado")
            var actualizarValores = document.getElementById("valores_a_actualizar");
            actualizarValores.removeAttribute("hidden")
        }


        function ActualizarCRM(){

            console.log("Se ha presionado el boton Guardar Cambios")

            // Capturamos los valores de las cajas de texto y asignamoslas a variables para ser utilizadas en el POST
            // Recojo los valores de las cajas de texto y las convierto en variables js
            let salarioDiario = parseInt(document.getElementById('salarioDiario').value);
            let idNomima = document.getElementById('idNomina').value;


            let empleador = document.getElementById('empleador').value;
            let idContacto = document.getElementById('idEmpleador').value;
            let empleado = document.getElementById('empleado').value;
            let docEmpleado = document.getElementById('docEmpleado').value;
            let wolkvox_Id = document.getElementById('wolkbox_Id').value;


            console.log("La variable a pasar es: " + wolkvox_Id);

            // Pego la información en el formulario para enviarlo al servidor
            document.forms["formulariocrm"].elements["salarioxDia"].value = salarioDiario;      
            document.forms["formulariocrm"].elements["idNomina"].value = idNomima;

            document.forms["formulariocrm"].elements["wolkbox_Id"].value = wolkvox_Id; 
            document.forms["formulariocrm"].elements["empleador"].value = empleador;
            document.forms["formulariocrm"].elements["idEmpleador"].value = idContacto; //VALIDAR ERROR QUE ESTA PASANDO SOBRE ESAT VARIABLE
            document.forms["formulariocrm"].elements["empleado"].value = empleado;
            document.forms["formulariocrm"].elements["docEmpleado"].value = docEmpleado;

            document.forms["formulariocrm"].submit();

        }
    </script> 
</body>

</html>