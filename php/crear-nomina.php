<?php



if(isset($_GET["cedulaEmpleador"])){
    $cedula =  $_GET['cedulaEmpleador']; 

    $SMLVM = 1300000; //constante Salario Minimo
    $SMLVD = $SMLVM / 30; //constante Salario Minimo Diario
    $AUX_TTE = 162000; // Constante Valor Aux Tte Legal
    $AUX_TTE_DIA = $AUX_TTE/30; // Constante Valor Auxilio de Tte por día 

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
            $tipoLaborContactos =$datos["Tipo de Labor"];

            $idwolkvox          =$datos["wolkvox_id"];
        }       
    }
    //echo $cedula;
?>

<!-- ============================================================================= -->
                                <!-- HTML CODE -->
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>INSERT NOMINA</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    </head>
    <body>
        <main>
            <div class="container">
                <h1>Insert Nomina</h1>
                <form action="../php/insert-nomina.php" method="POST">
                    <div class="row">
                        <div class="col col-md-6">
                            <Label for="cliente" class="col-sm-2 col-form-label">Cliente</Label>
                            <input type="text" class="form-control" name="empleador" id="cliente" value="<?php print($nombreEmpleador)?>" aria-label="readonly input example" readonly>  
                        </div><!-- Fin Col -->

                        <div class="col col-md-6">
                            <Label for="idCliente" class="col-sm-2 col-form-label">ID Cliente</Label>
                            <input type="text" class="form-control" name="idEmpleador" id="idCliente" value="<?php print($nroDocEmpleador)?>" aria-label="readonly input example" readonly>  
                        </div><!-- Fin Col -->  
                    </div><!-- Fin row -->

                    <hr>

                    <!-- =============================================================================== -->

                    <div class="row">
                        <div class="col col-md-4">
                            <Label for="empleado" class="col-sm-2 col-form-label">Empleado</Label>
                            <input type="text" class="form-control" name="empleado" id="cliente" value="<?php print($nombreEmpleado)?>" aria-label="readonly input example" readonly>  
                        </div><!-- Fin Col -->

                        <div class="col col-md-4">
                            <Label for="idEmpleado" class="col-sm-4 col-form-label">ID Empleado</Label>
                            <input type="text" class="form-control" name="idEmpleado" id="idEmpleado" value="<?php print($nroDocEmpleado)?>" aria-label="readonly input example" readonly>  
                        </div><!-- Fin Col -->  

                        <div class="col col-md-4">
                            <Label for="tipoLabor" class="col-sm-4 col-form-label">Tipo Labor</Label>
                            <input type="text" class="form-control" name="tipoLabor" id="tipoLabor" value="<?php print($tipoLaborContactos)?>" aria-label="readonly input example" readonly>  
                        </div><!-- Fin Col -->  
                    </div><!-- Fin row -->

                    <hr>
                    <!-- =============================================================================== -->

                    <div class="row">
                        <div class="col col-md-4">
                            <!-- CEAR SELECT -->
                            <Label for="select-perNomina" class="col-sm-2 col-form-label">Quincena</Label>
                            <select name="periodoNomina" id="select-perNomina" class="form-control">
                            <option value="" hidden>Selecciona un valor</option>
                            <option value="Primera" id="primerQuincena" >Primer Quicnena del Mes</option>
                            <option value="Segunda" id="SegundaQuincena" >Segunda Quicnena del Mes</option>
                            </select>
                        </div><!-- Fin Col -->

                        <div class="col col-md-4">
                            <Label for="select-baseMinimo" class="col-sm-4 col-form-label">Base Minimo</Label>
                            <select name="cliBono" class="form-control">
                                <option value="" hidden>Selecciona un valor</option>
                                <option value="true">Si, la base es el minimo</option>
                                <option value="false">No</option>
                            </select>
                        </div><!-- Fin Col -->  

                        <div class="col col-md-4">
                            <Label for="fecha-nomina" class="col-sm-4 col-form-label">Fecha de Nomina</Label>
                            <input type="date" class="form-control" name="perNomina" id="fecha-nomina" value="">  
                        </div><!-- Fin Col -->  
                    </div><!-- Fin row -->

                    <hr>
                    <!-- =============================================================================== -->

                    <div class="row">
                        <div class="col col-md-3">
                            <Label for="input-diasLab" class="col-sm-3 col-form-label">Dias Laborados</Label>
                            <input type="number" class="form-control" name="diaLab" id="input-diasLab" min="1" max="16" step="1">  
                        </div><!-- Fin Col -->

                        <div class="col col-md-3">
                            <label for="inp-salarioDia" class="col-sm-3 col-form-label">Salario por dia</label>
                            <input type="number" class="form-control" name="salarioxDia" id="inp-salarioDia" min="43353" value="<?php print($salarioDiario) ?>" >  
                        </div><!-- Fin Col -->  

                        <div class="col col-md-3">
                            <Label for="inp-auxTte" class="col-sm-3 col-form-label">Auxilio de Tte dia</Label>
                            <input type="number" class="form-control" name="auxTteNeto" id="inp-auxTte" min="0" value="0">  
                        </div><!-- Fin Col -->  

                        <div class="col col-md-3">
                            <Label for="inp-bono" class="col-sm-3 col-form-label">Bono Diario</Label>
                            <input type="number" class="form-control" name="bonoBase" id="inp-bono" min="0" value="0">  
                        </div><!-- Fin Col -->  
                    </div><!-- Fin row -->

                    <hr>
                    <!-- =============================================================================== -->

                    <div class="row">
                        <span><b>Horas Extras</b></span>

                        <div class="col col-md-3">
                            <Label for="input-HEDO" class="col-sm-4 col-form-label">Horas Extras diurnas</Label>
                            <input type="number" class="form-control" name="totalHEDO" id="input-HEDO" min="0" value="0">
                        </div><!-- Fin Col -->

                        <div class="col col-md-3">
                            <label for="input-HENO" class="col-sm-4 col-form-label">Horas Extras Nocturnas</label>
                            <input type="number" class="form-control" name="totalHENO" id="input-HENO" min="0" value="0" >  
                        </div><!-- Fin Col -->  

                        <div class="col col-md-3">
                            <Label for="input-HEDD" class="col-sm-4 col-form-label">Horas Festivas Diurnas</Label>
                            <input type="number" class="form-control" name="totalHEDD" id="input-HEDD" min="0" value="0">  
                        </div><!-- Fin Col -->  

                        <div class="col col-md-3">
                            <Label for="input-HEDN" class="col-sm-4 col-form-label">Horas Festivas Nocturnas</Label>
                            <input type="number" class="form-control" name="totalHEDN" id="input-HEDN" min="0" value="0">  
                        </div><!-- Fin Col -->  
                    </div><!-- Fin row -->

                    <input type="hidden" name="bool_auxTte" value="<?php echo $bool_aux_tte ? 'true'  : 'false'; ?>"/> 
                    <!-- <input type="number" name="salarioDia" value="<?php print($salarioDiario) ?>" hidden> -->
                    <input type="text" name="id_wolkvox" value="<?php print($idwolkvox) ?>" hidden>

                    <hr>
                    <input type="submit" class="btn btn-primary" value="Crear Registro">
                </form><!-- Fin Form -->
            </div><!-- fin container -->
        </main>

        <!-- Script boostrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>
    </body>
    </html>
<!-- ============================================================================= -->


<?php
} 