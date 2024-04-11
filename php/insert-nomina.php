<?php 
if($_SERVER["REQUEST_METHOD"] == "POST"){   

    $SMLVM = 1300000; //constante Salario Minimo
    $SMLVD = $SMLVM / 30; //constante Salario Minimo Diario
    $AUX_TTE = 162000; // Constante Valor Aux Tte Legal
    $AUX_TTE_DIA = $AUX_TTE/30; // Constante Valor Auxilio de Tte por día 

    // Iterar sobre todas las variables recibidas
    foreach ($_POST as $key => $value) {
        // Verificar si la variable está definida y no es nula
        if (isset($value)) {
            // Hacer algo con la variable, como sanitizarla o utilizarla
            echo "La variable $key está definida y no es nula. Valor: $value<br>";
            $name = $_POST["empleador"];
            $idcliente = $_POST["idEmpleador"];
            $empleado = $_POST["empleado"];
            $idempleado = $_POST["idEmpleado"];
            $nomina = $_POST["periodoNomina"]; //si es primer a segunda quincena
            $clibono = $_POST["cliBono"]; //Booleano Base Minimo
            $tipoLabor = $_POST ["tipoLabor"];//Tipo de labor
            $perNomina = $_POST["perNomina"]; //fecha de la nomina
            $diaslab = $_POST["diaLab"]; //Días laborados 
            $salneto = $_POST["salarioxDia"];  //Salario neto por día neto
            $bonodbase = $_POST["bonoBase"]; //Bono diario

            $auxTteDia = $_POST["auxTteNeto"]; //  Auxilio Tte Neto 
            $auxneto = $auxTteDia * $diaslab; //  Auxilio Tte en pago quincena 

            $totalHEDO = $_POST["totalHEDO"];
            $totalHENO = $_POST["totalHENO"];
            $totalHEDD = $_POST["totalHEDD"];
            $totalHEDN = $_POST["totalHEDN"];

            $bool_aux_tte = $_POST["bool_auxTte"];
            $idwolkvox = $_POST["id_wolkvox"];

        } else {
            echo "La variable $key no está definida o es nula.<br/>";
        }   
    }

    # $salneto = $_POST["salarioNeto"]; // total devengado x

    //Validación si el salario incluye o no el auxilio de tte.
    if($bool_aux_tte == false){
        $auxneto = 0;
        $devneto = intval($diaslab) * intval($salneto);
    }else{
        $devneto = $auxneto + $diaslab * $salneto;
    }

    # si es base minimo hacer
    if($clibono == "false"){
        $bonodbase = 0;
    }else{
        //se toma el valor del día y se deja en SMLDV{
        $bonodbase = $salneto - $SMLVD;
        $salneto = $SMLVD;  

    }

    #$dedupension  // Deducción Pension x
    #$dedusalud  // Deducción Salud x
    //calcualr Salud y pension en base a dias laborados
    $ibc = null;
    $dedupension = 0;
    $dedusalud = 0;

    /* Si días laborados son entre uno y 7 el IBC es igual a 325.000 */
    if ($diaslab >= 1 && $diaslab <= 7) {
        $ibc = 325000;
        //echo "el ibc es: " , $ibc;

        /* Si días laborados son entre 8 y 14 el IBC es igual a 650.000 */
    } else if ($diaslab >= 8 && $diaslab <= 14) {
        $ibc = 650000;
        //echo "el ibc es: " . $ibc;

        /* Si días laborados son entre 15 y 21 el IBC es igual a 975.000 */
    } else if ($diaslab >= 15 && $diaslab <= 21) {
        $ibc = 975000;
        //echo "el ibc es: " . $ibc;

        /* Si días laborados son entre 22 y 30 el IBC es igual a 1'300.000 */
    } else if ($diaslab >= 22 && $diaslab <= 30) {
        $ibc = 1300000;
        $dedusalud = $ibc * 0.04;
        //echo "El IBC es: " . $ibc;
    }

    $dedupension = $ibc * 0.04;


    # $pagofinal // Neto a Pagar  = Total Devengado menos deducciones
    $pagofinal = $devneto - $dedupension - $dedusalud;


    # $primabase //Prima Base x
    #Prima de servicios 
    #Salario base mensual × número de días laborados / 360
    $primabase = 0;


    #$bononeto Bono Neto mensual  = Bono Diario por Numero de Días
    $bononeto = $bonodbase * $diaslab;

    
    #$TotalHr Total Valor Horas Extras
    $TotalHr = 0;

    $valorHoraDía = $salneto / 8;

    /**
     *  HORAS EXTRAS 
     * 
        * VALOR HORAS EXTRA
        * EXTRA DIURNA 25%
        * EXTRA NOCTURNA 75%
        * EXTRA DIUR/DOM 100%
        * EXTRA NOCT/DOM 150%
    */

    #SACAMOS VALORES DE HORAS EXTRAS SEGUN LA RELACIÓN ANTERIOR
    $valorHoraExtraDiurna = $valorHoraDía * 1.25; 
    $valorHoraExtraNocturna = $valorHoraDía * 1.75;
    $valorHoraExtraFestDiurna = $valorHoraDía * 2;
    $valorHoraExtraFestNocturna = $valorHoraDía * 2.50;

    $TotalHr = ($valorHoraExtraDiurna * $totalHEDD) +
                ($valorHoraExtraNocturna * $totalHENO) + 
                ($valorHoraExtraFestDiurna * $totalHEDD) + 
                ($valorHoraExtraFestNocturna * $totalHEDN);

    $canthrdia = $totalHEDD;
    $canthrnoche = $totalHENO;
    $canthrfestdia = $totalHEDD;
    $canthrfestnoche = $totalHEDN;




    $fecha_y_hora = date("Y-m-d H:i:s");
    //echo $fecha_y_hora;

    
    /* $wolkvox_year = $_POST["fechaAño"]; // año
    $wolkvox_month = $_POST["fechaMes"]; // Mes
    $wolkvox_day = $_POST["fechaDia"]; // dia
    $wolkvox_hour = $_POST["fechaHora"]; // hora
    $wolkvox_min = $_POST["fechaMinuto"]; // Minuto */

    #INGRESA CURL INSERT NOMINA

    $insertNomina = curl_init();

    curl_setopt_array($insertNomina, array(
        CURLOPT_URL => 'https://crm.wolkvox.com/server/API/v2/custom/insert.php', 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "operation":"techcon",
            "module": "Nomina",
            "fields": {
                "Nombre contacto": {
                    "type": "search",
                    "value": "'.$name.'",
                    "value_id": "'.$idwolkvox.'",
                    "searchModuleName": "contacts"
                },
                "ID Contacto": {
                    "type": "search",
                    "value": '.$idcliente.',
                    "value_id": "'.$idwolkvox.'",
                    "searchModuleName": "contacts"
                },
                "Nombre Empleado": {
                    "type": "search",
                    "value": "'.$empleado.'",
                    "value_id": "'.$idwolkvox.'",
                    "searchModuleName": "contacts"
                },
                "ID Empleado": {
                    "type": "search",
                    "value": '.$idempleado.',
                    "value_id": "'.$idwolkvox.'",
                    "searchModuleName": "contacts"
                },
                "Quincena" : "'.$nomina.'",
                "Base Minimo": '.$clibono.',
                "Tipo de Labor" : "'.$tipoLabor.'",
                "Periodo Nomina": "Quincena-'.$perNomina.'",
                "Archivo Liquidacion": "https://unifika.co/wp-content/forms/generarpdf/comprobantesnomina/'.$idcliente.'/'.$idempleado.'-Quincena-'.$perNomina.'.pdf",
                "Dias Laborados": '.$diaslab.',
                "Salario por dia": {
                        "type": "currency",
                        "value": "'.$salneto.'",
                        "symbol": "COP",
                        "convert": '.$salneto.'
                    },
                "Bono Diario": {
                        "type": "currency",
                        "value": "'.$bonodbase.'",
                        "symbol": "COP",
                        "convert": '.$bonodbase.'
                    },
                "Total Devengado": {
                        "type": "currency",
                        "value": "'.$devneto.'",
                        "symbol": "COP",
                        "convert": '.$devneto.'
                    },
                "Neto a Pagar": {
                        "type": "currency",
                        "value": "'.$pagofinal.'",
                        "symbol": "COP",
                        "convert": '.$pagofinal.'
                    },
                "Aux Transporte": {
                        "type": "currency",
                        "value": "'.$auxneto.'",
                        "symbol": "COP",
                        "convert": '.$auxneto.'
                    },
                "Vlr Prima": {
                        "type": "currency",
                        "value": "'.$primabase.'",
                        "symbol": "COP",
                        "convert": '.$primabase.'
                    },
                "Bonificacion": {
                        "type": "currency",
                        "value": "'.$bononeto.'",
                        "symbol": "COP",
                        "convert": '.$bononeto.'
                    },
                "Deduccion Pension": {
                        "type": "currency",
                        "value": "'.$dedupension.'",
                        "symbol": "COP",
                        "convert": '.$dedupension.'
                    },
                "Deduccion Salud": {
                        "type": "currency",
                        "value": "'.$dedusalud.'",
                        "symbol": "COP",
                        "convert": '.$dedusalud.'
                    },
                "Vlr Horas Extras": {
                        "type": "currency",
                        "value": "'.$TotalHr.'",
                        "symbol": "COP",
                        "convert": '.$TotalHr.'
                    },
                "Relacion Hr": {
                        "type": "table",
                        "status": "create",  
                        "value": [
                        {
                            "Fecha": "'.$fecha_y_hora.'",
                            "Hr Diaria": "'.$canthrdia.'",
                            "Hr Nocturna": "'.$canthrnoche.'",
                            "Hr Fest Diurna": "'.$canthrfestdia.'",
                            "Hr Fest Nocturna": "'.$canthrfestnoche.'"
                        }
                    ]},
                "Detalle Desprendible": ""            
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'Wolkvox-Token: 7b74656368636f6e7d2d7b32303232303632343132323830357d',
            'Content-Type: application/json',
            'Cookie: PHPSESSID=kgd7unf7gg6jiin0bs3mj105i2'
        ),
    ));

    $responseInsert = curl_exec($insertNomina);
    curl_close($insertNomina);
    echo $responseInsert;

    ////////////////////    OPERACIONES   ////////////////////   


}/* Fin IF POST Methos request */

?>