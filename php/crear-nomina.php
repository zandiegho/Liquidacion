<?php

include 'curl_functions.php';

if(isset($_GET["cedulaEmpleador"])){
    $cedula =  $_GET['cedulaEmpleador']; 

    //Obtenemos los valores del cliente 
    
    //DECLARAMOS LAS VARIABLES CONSTANTES
    $SMLVM = 1300000; //constante Salario Minimo
    $SMLVD = $SMLVM / 30; //constante Salario Minimo Diario
    $AUX_TTE = 162000; // Constante Valor Aux Tte Legal
    $AUX_TTE_DIA = $AUX_TTE/30; // Constante Valor Auxilio de Tte por día 
    
    $datosClienteArray = ObtenerDatosCliente($cedula);

    //VALIDAMOS DATOS QUE EXISTAN
    // Verificas si la decodificación fue exitosa y si el nombre del cliente está presente en los datos
    if ($datosClienteArray) {
            
        // Array con los nombres de los campos que deseas obtener
        $camposDeseados = array('namecontact', 'ID Contacto', 'Ciudad', 'Nombre Empleado', 'ID Empleado', 'Condicion Laboral' , 'Tipo de Labor' );
        
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

    /* ============================================================================================================================== */
   
    //Obtenemos los valores de la nomina 
    $datosNominaArray = obtenerDatosNomina($cedula);

     //VALIDAMOS DATOS QUE EXISTAN
    // Verificas si la decodificación fue exitosa y si el nombre del cliente está presente en los datos
    if ($datosNominaArray) {
            
        // Array con los nombres de los campos que deseas obtener
        $camposDeseadosNom = array('Nombre Empleado', 'ID Empleado', 'Tipo de Labor', 'Base Minimo', 'Dias Laborados', 'Salario por dia' , 'Aux Transporte' , 'Bono Diario' );
        
        // Array para almacenar los valores obtenidos
        $valoresNomina = array();
        
        // Iteras sobre cada campo deseado y obtienes su valor del array de datos del cliente
        foreach ($camposDeseadosNom as $campo) {
            // Verificas si el campo existe en los datos del cliente y lo agregas al array de valores
            if (isset($datosNominaArray['data'][0][$campo])) {
                $valoresNomina[$campo] = $datosNominaArray['data'][0][$campo];
            } else {
                // Si el campo no existe, puedes asignar un valor predeterminado o dejarlo en blanco
                $valoresNomina[$campo] = 'No disponible';
            }
        }
        
        // Ahora $valoresCliente contiene todos los valores que necesitas del cliente
        // Puedes acceder a cada valor utilizando su nombre de campo como clave en el array
        foreach ($valoresNomina as $campo => $valor) {
            //echo ucfirst($campo) . ": " . $valor . "<br>"; // ucfirst() para capitalizar el nombre del campo
        }
    } else {
        // Manejar el caso en el que no se pudieron obtener los datos del cliente
        echo "No se pudieron obtener los datos del cliente.";
    }

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
                            <input type="text" class="form-control" name="empleador" id="cliente" value="<?php print($valoresCliente["namecontact"])?>" aria-label="readonly input example" readonly>  
                        </div><!-- Fin Col -->

                        <div class="col col-md-6">
                            <Label for="idCliente" class="col-sm-2 col-form-label">ID Cliente</Label>
                            <input type="text" class="form-control" name="idEmpleador" id="idCliente" value="<?php print($valoresCliente["ID Contacto"])?>" aria-label="readonly input example" readonly>  
                        </div><!-- Fin Col -->  
                    </div><!-- Fin row -->

                    <hr>

                    <!-- =============================================================================== -->

                    <div class="row">
                        <div class="col col-md-4">
                            <Label for="empleado" class="col-sm-2 col-form-label">Empleado</Label>
                            <input type="text" class="form-control" name="empleado" id="cliente" value="<?php print($valoresNomina["Nombre Empleado"]["value"])?>" aria-label="readonly input example" readonly>  
                        </div><!-- Fin Col -->

                        <div class="col col-md-4">
                            <Label for="idEmpleado" class="col-sm-4 col-form-label">ID Empleado</Label>
                            <input type="text" class="form-control" name="idEmpleado" id="idEmpleado" value="<?php print($valoresNomina["ID Empleado"]["value"])?>" aria-label="readonly input example" readonly>  
                        </div><!-- Fin Col -->  

                        <div class="col col-md-4">
                            <Label for="tipoLabor" class="col-sm-4 col-form-label">Tipo Labor</Label>
                            <input type="text" class="form-control" name="tipoLabor" id="tipoLabor" value="<?php print($valoresNomina["Tipo de Labor"])?>" aria-label="readonly input example" readonly>  
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
                            <input type="number" class="form-control input" name="diaLab" id="input-diasLab" min="1" max="16" step="1" value="<?php print($valoresNomina["Dias Laborados"]) ?>">  
                        </div><!-- Fin Col -->

                        <div class="col col-md-3">
                            <label for="inp-salarioDia" class="col-sm-3 col-form-label">Salario por dia</label>
                            <input type="number" class="form-control input" name="salarioxDia" id="inp-salarioDia" min="43353" value="<?php print($valoresNomina["Salario por dia"]["value"]) ?>" >  
                        </div><!-- Fin Col -->  

                        <div class="col col-md-3">
                            <Label for="inp-auxTte" class="col-sm-3 col-form-label">Auxilio de Tte dia</Label>
                            <input type="number" class="form-control input" name="auxTteNeto" id="inp-auxTte" min="0" value="<?php print($valoresNomina["Aux Transporte"]["convert"] / $valoresNomina["Dias Laborados"]) ?>">  
                        </div><!-- Fin Col -->  

                        <div class="col col-md-3">
                            <Label for="inp-bono" class="col-sm-3 col-form-label">Bono Diario</Label>
                            <input type="number" class="form-control input" name="bonoBase" id="inp-bono" min="0" value="<?php print($valoresNomina["Bono Diario"]["value"]) ?>">  
                        </div><!-- Fin Col -->  
                    </div><!-- Fin row -->

                    <br>
                    <div class="row">
                        <div class="col">
                            <label for="Total Devengado">Total Devengado</label>
                        </div>
                        <div class="col">
                            <input type="number" name="totalDevengado" id="totalDevengado" aria-readonly="ReadAonly" readonly >
                        </div>
                    </div>

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

        <script>
            const inpSalarioDia = document.getElementById('inp-salarioDia')
            const inpDiasLaborados = document.getElementById('input-diasLab')
            const inpAuxtte = document.getElementById('inp-auxTte')
            const inpBoniDiario = document.getElementById('inp-bono')
            const inpTotalDevengado = document.getElementById('totalDevengado')

            function Calculartotal() {
                const salario = parseFloat(inpSalarioDia.value) || 0;
                const dias = parseFloat(inpDiasLaborados.value) || 0;
                const aux = parseFloat(inpAuxtte.value) || 0;
                const bono = parseFloat(inpBoniDiario.value) || 0;

                const total = salario * dias + aux + bono;

                // Actualizar el valor del input de total devengado
                inpTotalDevengado.value = total;
            }

            // Agregar un event listener a cada input para llamar a la función calcularTotal cuando cambie
            document.querySelectorAll('.input').forEach(input => {
                input.addEventListener('change', Calculartotal);
            });

            // Calcular el total inicial al cargar la página
            Calculartotal();

        </script>
    </body>
    </html>
<!-- ============================================================================= -->


<?php
} 