<?php
#VALIDAR SI LLEGAR POR METODO POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if(isset($_POST["cedulaEmpleador"])){
        //Si el valor que viene en el campo  de cedula es numerico lo guardamos en la variable $cedula, si no generamos un error.
        $cedula = $_POST["cedulaEmpleador"];  //capturamos el valor de la cedula del empleador
        # echo "El Numero Cedula del empleador es: " .$cedula;   //mostramos en pantalla el numero de cédula del empleador
    }else{
        echo "Error en el método HTTP
        <br>Debe utilizar el método POST para enviar los datos";
        exit();

        //PONER UN ALERT QUE NO HAY NUVERO DE CEDULA VALIDO Y ABORTAR
    }
        
    #  ↓↓↓ CONSULTAR CLIENTE ↓↓↓
    $curlCliente = curl_init();

    curl_setopt_array($curlCliente, array(
        CURLOPT_URL => 'https://crm.wolkvox.com/server/API/v2/custom/query.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS =>'{
            "operation":"techcon",
            "wolkvox-token":"7b74656368636f6e7d2d7b32303232303632343132323830357d",
            "module":"contacts",
            "field":"ID Contacto",
            "value": '.$cedula.'
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: PHPSESSID=03jeh67ajirv2ha35og473ca8a'
        ),
    ));

    $responseCliente = curl_exec($curlCliente);

    curl_close($curlCliente);
    echo $responseCliente; 

    # FIN CONSULTA CLIENTE

    //decodificar la respuesta del Curl CLIENTE en JSON
    $responseClienteJson = json_decode($responseCliente, false);
    
    # SI EL CLIENTE EXISTE
    if($responseClienteJson -> msg == "1 records were are found"){
        
        /*decodificamos el json en matriz con parametro true*/
        $decode_jsonT = json_decode($responseCliente, true);
        $data = $decode_jsonT['data'];
        
        /* Accedemos al array DATA */
        foreach($data as $datos){

            # Capturamos Wolkbox_ID
            $wolkvox_id = $datos["wolkvox_id"];
            
            # DATOS EMPLEADOR
            $nombreEmpleador    = $datos["namecontact"];
            $tipoDocEmpleador   = $datos["Tipo ID Contacto"];
            $nroDocEmpleador    = $datos["ID Contacto"];

            # DATOS EMPLEADO
            $nombreEmpleado     = $datos["Nombre Empleado"];
            $tipoDocEmpleado    = $datos["Tipo ID"];
            $nroDocEmpleado     = $datos["ID Empleado"];
            $inicioContrato     = $datos["Fecha Inicio"];
            
            # AUX DE TTE
            $bool_aux_tte       =$datos["No Incluye Auxilio de Tte"];       
            
            # DATOS NOMIMA
            $frecuenciaPago     =$datos["Frecuencia de Pago"];
            $tipoContrato       =$datos["Tipo de Contrato"];
            $salarioDiario      =$datos["Salario por dia"]["value"];
        }

        # INICIALIZACIÓN DE VARIABLES
    
        #VALIDACIÓN DE CONTACTOS SI INCLUYE O NO EL AUXILIO DE TTRANSPORTE
        #si el auxilio de transporte llega como false se inicializa en 0
        if ($bool_aux_tte == false ){
            $auxTte = 0;
            $auxTteIncluido = "true";
        }else{
            $auxTteIncluido = "false";
        }

        if ($frecuenciaPago == "Mensual" ){
            $desprendibleNomina = "Mensual";
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
        #$responseNominaJson = json_decode($responseNomina, false);
        

        # VALIDACION NOMINA CON 0 REGISTROS
        /* if($responseNominaJson -> msg == "There are no values in the specified page"){
            
            # LLEVAR A PRIMER CONTACTO HTML CON DATOS DE MODULO CONTACTO 
            echo '<script type="text/javascript">'
            , 'alert("Nomina No Encontrada").innerHTML = "No se Encontró Registro de nomina"'
            , '</script>'
            ;

            //Redirigir a Campo apra Crear nomina.
                        
            echo '<form id="redirectForm" action="../crear-nomina.php" method="GET">';
            echo '<input type="hidden" name="cedulaEmpleador" value="'.$cedula.'">';               

            echo '</form>';
            echo '<script>document.getElementById("redirectForm").submit();</script>';
            exit();
        } 

        # FIN VALIDACION NOMINA CON 0 REGISTROS

        else{
            echo "====================================== PRUEBA QUE NO ENTRA AL IF ======================================";
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
                $wolkvox_id         = obtenerValorSeguro($datosNomina["wolkvox_id"]);

                
                // Imprimir resultados para verificar
                #echo "Salario por dia: " . $pagoDiario . "<br/>";                                               
                if($wolkvox_id != NULL){
                    //DETENER FOREACH
                    break;
                }
            }
        } */
        
        

        if($periodoQuincena == "Segunda"){
            validarDosUltimos($responseNomina, $cedula);
        }
        else{
            
            echo '<form id="redirectForm" action="../index-nomina.php" method="post">';
            echo '<input type="hidden" name="cedulaEmpleador" value="'.$cedula.'">';               

            echo '</form>';
            echo '<script>document.getElementById("redirectForm").submit();</script>';
            exit();
        }

        #_____________________________ECHOS DE VALIDACION_______________________________________
        /* echo "<br/><br/>De Modulo Contactos <br/>";
        echo "El Auxilio está incluido o no?: " .$auxTteIncluido."<br/>";
        echo "<br/>De Modulo Nomina<br/>";
        echo "<br/>Fecha de Nomina: " .$periodoNomina. "<br>";
        echo "Periodo de Quincena: " .$periodoQuincena. "<br>";
         */
    }

    


#SI NO LLEGA POR EL METODO POST PRESENTAR ERROR    
}else{
    echo "Error: Formulario no enviado por método POST";
    exit;
}


/* ------------------------FUNCIONES PHP------------------------------- */
// Definir una función para obtener valores seguros
function obtenerValorSeguro($clave, $default = 0){
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

function validarDosUltimos($jsonResp, $cedula){    
        
    // Tu JSON completo
    #$json = $jsonResp; // Aquí debes colocar el JSON completo
    
    // Decodificar el JSON
    $data = json_decode($jsonResp, true);
    $dataNomina = $data["data"];
    // Obtener los últimos dos registros
    $ultimosDosRegistros = array_slice($dataNomina, 0, 2);

    // Inicializar arrays para almacenar los resultados
    $baseMinimo         = [];
    $diasLaborados      = [];
    $bonificacion       = [];
    $deduccionPension   = [];
    $deduccionSalud     = [];
    $valorHorasExtr     = [];
    $diasTrabajados     = [];


    // Iterar sobre los últimos dos registros
    foreach ($ultimosDosRegistros as $registro) {
        
        /* capturar resultado de ultimos dos documentos del json para cada variable obtenida en el for each */

        $baseMinimo[]               = $registro["Base Minimo"];           
        $diasLaboradosArray[]       = $registro["Dias Laborados"];
        $bonificacion[]             = $registro["Bonificacion"];
        $deduccionPensionArray[]    = $registro["Deduccion Pension"]["value"];
        $deduccionSaludArray[]      = $registro["Deduccion Salud"]["value"];
        $valorHorasExtr[]           = $registro["Vlr Horas Extras"];
    }

    echo "<br/>";
    
    if($baseMinimo[0] == true){
        $baseMinimoValidadoant = "true";
    }else{
        $baseMinimoValidadoant = "false";        
    }
    
    if($baseMinimo[1] == true){
        $baseMinimoValidadoact = "true";
    }else{
        $baseMinimoValidadoact = "false";        
    }
    
    $deduccionPensionNomina1 = $deduccionPensionArray[0];
    $deduccionPensionNomina2 = $deduccionPensionArray[1];
    $deduccionSaludNomina1 = $deduccionSaludArray[0];
    $deduccionSaludNomina2 = $deduccionSaludArray[1];

    echo '<form id="redirectForm" action="../index-nomina.php" method="post">';
    echo '<input type="hidden" name="cedulaEmpleador" value="'.$cedula.'">';
    echo '<input type="hidden" name="deduccionPension0" value="'.$deduccionPensionNomina1.'">';
    echo '<input type="hidden" name="deduccionPension1" value="'.$deduccionPensionNomina2.'">';
    echo '<input type="hidden" name="deduccionSalud0" value="'.$deduccionSaludNomina1.'">';
    echo '<input type="hidden" name="deduccionSalud1" value="'.$deduccionSaludNomina2.'">';
             

    echo '</form>';
    echo '<script>document.getElementById("redirectForm").submit();</script>';
    exit();         


    # EXPORTAR RESULTADOS DE VARIABLES DE DEDUCCION EN SALUD Y PENSION


}