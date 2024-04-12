<?php

include '../php/curl_functions.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['cedulaEmpleador'])) {
        $docEmpleador = $_POST['cedulaEmpleador'];  //capturamos el valor del input  de cedula en la variable docEmpleador

        
        // Llamas a la función en curl_functions.php para obtener los datos del cliente
        $datosClienteArray = ObtenerDatosCliente($docEmpleador);
        $datosNominaArray = obtenerDatosNomina($docEmpleador);

        /* echo "<br/>";
        var_dump($datosClienteArray['data'][0]);
        echo "<br/>";  */
        
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


        //VERIFICAR SI LA DECODIFICAICON DE LOS CAMPOS ES CORRECTA
        if($datosNominaArray){

            //Tomamos los campos de nomina que necesitamos
            $nominasEncontradas = $datosNominaArray -> {'Total records'};
            
            if($nominasEncontradas != 0){
                $cantidadNominas =  $nominasEncontradas;
            }else{
                $cantidadNominas = 0;
            }
        }



    }


/* 
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
            "value": '.$docEmpleador.'
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: PHPSESSID=03jeh67ajirv2ha35og473ca8a'
        ),
    ));

    $responseCliente = curl_exec($curlCliente);

    curl_close($curlCliente);
    # echo $responseCliente; 

    # FIN CONSULTA CLIENTE

    //decodificar la respuesta del Curl CLIENTE en JSON
    $responseClienteJson = json_decode($responseCliente, false);
    
    # SI EL CLIENTE EXISTE
    if($responseClienteJson -> msg == "1 records were are found"){
        
        /*decodificamos el json en matriz con parametro true
        $decode_jsonT = json_decode($responseCliente, true);
        $data = $decode_jsonT['data'];
        
        /* Accedemos al array DATA 
        foreach($data as $datos){

            # Capturamos Wolkbox_ID
            $wolkvox_id = $datos["wolkvox_id"];
            
            # DATOS EMPLEADOR
            $cntnombreEmpleador    = $datos["namecontact"];
            $cnttipoDocEmpleador   = $datos["Tipo ID Contacto"];
            $cntnroDocEmpleador    = $datos["ID Contacto"];
            $ctnCiudadEmpleador    = $datos["Ciudad"];

            # DATOS EMPLEADO
            $cntnombreEmpleado     = $datos["Nombre Empleado"];
            $cnttipoDocEmpleado    = $datos["Tipo ID"];
            $cntnroDocEmpleado     = $datos["ID Empleado"];
            $cntinicioContrato     = $datos["Fecha Inicio"];
            
            # AUX DE TTE
            $cntbool_aux_tte       =$datos["No Incluye Auxilio de Tte"];       
            
            # DATOS NOMIMA
            $cntfrecuenciaPago     =$datos["Frecuencia de Pago"];
            $cnttipoContrato       =$datos["Tipo de Contrato"];
            $cntsalarioDiario      =$datos["Salario por dia"]["value"];
            $cnttipoLabor          =$datos["Tipo de Labor"];
            $cntcondicionLaboral   =$datos["Condicion Laboral"];
        }
    }//fin if response cliente Valido  
*/
    
    ?>

    <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Liquidación Nomina Unifika</title>
                <!-- Insertar Boostrap -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
                integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        </head>
        <body onload="pintarDatos()">
            <main>
                <div class="container">

                    <h1>Liquidación de Nomina Unifika</h1>
                    
                    <h2 style="font-size: small;">sxsdjkd sdj askd asdj askdj awskenwqnd sandsaljnd </h2>

                    <div class="datos-cliente" id="div-datosCliente" hidden>
                        <div class="row">
                            <div class="col col-sm-4 col-lg-4 text-center">
                                <h6>Nombre cliente</h6>
                                <span id="span-nombrecliente">Nombre Cliente</span>
                            </div><!-- fin col -->

                            <div class="col col-sm-4 col-lg-4 text-center">
                                <h6>Id Cliente</h6>
                                <span id="span-idcliente">Id Cliente</span>
                            </div><!-- fin col -->

                            <div class="col col-sm-4 col-lg-4 text-center">
                                <h6>Ciudad Cliente</h6>
                                <span id="span-ciudadCliente">Ciudad Cliente</span>
                            </div><!-- fin col -->
                        </div><!-- fin row -->
                        <hr>
                    </div><!-- fin  datos-cliente -->
                    
                    <div class="datos-empleado"  id="div-datosempleado" hidden>
                        <div class="row">
                            <div class="col col-sm-4 col-lg-3 text-center">
                                <h6>Nombre Empleado</h6>
                                <span id="span-nombreEmpleado">Nombre Empleado</span>
                            </div><!-- fin col -->

                            <div class="col col-sm-4 col-lg-3 text-center">
                                <h6>Id Empleado</h6>
                                <span id="span-idEmpleado">Id Empleado</span>
                            </div><!-- fin col -->

                            <div class="col col-sm-4 col-lg-3 text-center">
                                <h6>Condicion Laboral</h6>
                                <span id="span-condicionLaboral">Condicion Laboral</span>
                            </div><!-- fin col -->

                            <div class="col col-sm-4 col-lg-3 text-center">
                                <h6>Tipo Labor</h6>
                                <span id="span-tipoLabor">Tipo Labor</span>
                            </div><!-- fin col -->

                        </div><!--  fin row -->

                        <hr>
                    </div><!-- fin  datos-empleado -->

                    
                    <p id="p-infoNominas" >Se han encontrado <span id="span-nominas">0</span> registros de nomina para este cliente, puedes ver, insertar, editar, o imprimir sus comprobantes de pago </p>
                    
                    
                    <div class="div-btns" id="div-botones" >
                        <hr>

                        <div class="row justify-content-center mt-4">
                            
                            <div class="col-3 text-center">
                                <input type="button" class="btn btn-primary" id="btn-verNomina" value="Ver Nominas Anteriores" onclick="VerNomina()" disabled>
                            </div><!-- fin col -->
                            
                            <div class="col-3 text-center">
                                <input type="button" class="btn btn-primary" id="btn-insertar" value="Insertar Nomina" onclick="insertNomina()" disabled>
                            </div><!-- fin col -->
                            
                            <div class="col-3 text-center">
                                <input type="button" class="btn btn-primary" id="btn-actualizar" value="Actualizar Nomina" onclick="updateNomina()" disabled>
                            </div><!-- fin col -->
                            
                            <div class="col-3 text-center">
                                <input type="button" class="btn btn-primary" id="btn-imprimir" value="Imprimir ultima nomina" onclick="printNomina()" disabled>
                            </div><!-- fin col -->
                        </div><!-- fin row -->
                        
                    </div>
                        
                    <div class="div-form" id="divForm">

                    </div>
                </div><!-- fin container -->
            </main>
            <!-- Script boostrap -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
            </script>  
            
            <script>
                function pintarDatos(){
                    if(nombreEmpleador != ""){
                        console.log("Dom Cargado")

                        var divEmpleador = document.getElementById('div-datosCliente');     
                        divEmpleador.removeAttribute("hidden") 

                        var divEmpleado = document.getElementById('div-datosempleado');     
                        divEmpleado.removeAttribute("hidden") 


                        var nombreEmpleador = document.getElementById('span-nombrecliente')
                        nombreEmpleador.innerHTML = "<?php echo $valoresCliente["namecontact"] ?>"

                        var idEmpleador = document.getElementById('span-idcliente')
                        idEmpleador.innerHTML ="<?php echo $valoresCliente["ID Contacto"] ?>"

                        var ciudadEmpleador = document.getElementById('span-ciudadCliente')
                        ciudadEmpleador.innerHTML = "<?php echo $valoresCliente["Ciudad"] ?>" 

                        var nombreEmpleado = document.getElementById('span-nombreEmpleado')
                        nombreEmpleado.innerHTML = "<?php echo $valoresCliente["Nombre Empleado"] ?>"

                        var idEmpleado = document.getElementById('span-idEmpleado')
                        idEmpleado.innerHTML = "<?php echo $valoresCliente["ID Empleado"] ?>"

                        var condicionLaboral = document.getElementById('span-condicionLaboral')
                        condicionLaboral.innerHTML = "<?php echo $valoresCliente["Condicion Laboral"] ?>"

                        var tipoLabor = document.getElementById('span-tipoLabor')
                        tipoLabor.innerHTML = "<?php echo $valoresCliente["Tipo de Labor"] ?>"

                        var cantidadNominas = document.getElementById('span-nominas')
                        cantidadNominas.innerHTML = "<?php echo $nominasEncontradas ?>";

                        if(cantidadNominas != 0){
                            //si hay nominas puede verlas, insertar editarlas o imprimirlas
                            btnVer = document.getElementById('btn-verNomina')
                            btnInsertar = document.getElementById('btn-insertar')
                            btnActualizar = document.getElementById('btn-actualizar')
                            btnImprimir = document.getElementById('btn-imprimir')

                            btnVer.removeAttribute("disabled")
                            btnInsertar.removeAttribute("disabled")
                            btnActualizar.removeAttribute("disabled")
                            btnImprimir.removeAttribute("disabled")
                        }else{
                            //SI NO HHAY NOMINAS PUEDE UNICAMENTE INSERTAR
                            btnInsertar = document.getElementById('btn-insertar')
                            btnInsertar.removeAttribute("disabled")                            
                        }

                        
                    }
                    else{
                        console.log(nombreEmpleado + ' no cargado')
                    }
                }

                function VerNomina(){
                    //llevar a interfaz Nomina
                    console.log("Boton Ver Nomina Presionado")

                    //CREATE FORM IN div whit id = divForm
                    var divForm = document.createElement('div')                    
                    divForm.setAttribute('id', 'divForm')

                    var form = document.createElement('form')
                    form.setAttribute('id', 'formNomina')
                    form.setAttribute("method", "POST")
                    form.action = '../php/interfaz.php'

                    
                    var inputDedPen1 = document.createElement('input')
                    inputDedPen1.setAttribute('type', 'number')
                    inputDedPen1.setAttribute('name', 'cedulaEmpleador')
                    inputDedPen1.setAttribute('value' , '<?php echo $docEmpleador ?>')
                    inputDedPen1.setAttribute('hidden', 'true')

                    form.appendChild(inputDedPen1)
                    divForm.appendChild(form)
                    document.body.appendChild(divForm)                 
                    
                    form.submit();
                    exiit();

                }

                function insertNomina(){
                    //llevar a Insertar Nomina
                }

                function updateNomina(){
                    //llevar a update Nomina
                }

                function printNomina(){
                    //llevar a imprimir nomina
                    //Este es un jemeplo para la peticion GET
                    //https://unifika.co/wp-content/forms/generarpdf/imprimir.php?idcliente=909090
                    window.location.href = 'https://unifika.co/wp-content/forms/generarpdf/imprimir.php?idcliente=<?php echo $docEmpleador ?>'

                }

            </script>

        </body>
        </html>

<?php
}//fin metodo post
else{
    echo "Error en el método HTTP
    <br>Debe utilizar el método POST para enviar los datos";
    exit();
}
