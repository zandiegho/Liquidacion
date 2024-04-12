<?php
include '../php/curl_functions.php';


if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Iterar sobre todas las variables recibidas
    foreach ($_POST as $key => $value) {
        // Verificar si la variable está definida y no es nula
        if (isset($value)) {
            // Hacer algo con la variable, como sanitizarla o utilizarla
            # echo "La variable $key está definida y no es nula. Valor: $value<br>";
            $nuevoSalarioDia = $_POST['salarioxDia'];
            $idNomina = $_POST["idNomina"];
            $name = $_POST["empleador"];
            $idcliente = $_POST["IdEmpleador"];
            $empleado = $_POST["empleado"];
            $idempleado = $_POST["docEmpleado"];
            $wolkbox_id = $_POST["wolkbox_id"];

            # echo "EL wolkbox id es: " . $wolkbox_id . "<br/>" ;
            # echo "EL id Empleador es: " . $idcliente . "<br/>" ;
            # echo "El nuevo valor salario por día es: ". $nuevoSalarioDia . "<br/>" ;
        } else {
            echo "La variable $key no está definida o es nula.<br/>";
        }
    }

    # INICIO CURL INIT

    $curlUpdateNomina = curl_init();

    curl_setopt_array($curlUpdateNomina, array(
    CURLOPT_URL => $LINK_UPDATE,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
        "operation": "techcon",
        "module": "Nomina",
        "wolkvox-id": "'.$wolkbox_id.'",
        "fields": {
            "Salario por dia": {
                    "type": "currency",
                    "value": "'.$nuevoSalarioDia.'",
                    "symbol": "COP",
                    "convert": '.$nuevoSalarioDia.'
            }            
        }
    }',
    CURLOPT_HTTPHEADER => array(
        'Wolkvox-Token: '.$WOLKVOX_TOKEN.'',
        'Content-Type: application/json',
        'Cookie: PHPSESSID=sjnu2s2egn92ldo8vsqsek1ui3'
    ),
    ));

    $responseUpdate = curl_exec($curlUpdateNomina);

    curl_close($curlUpdateNomina);
    echo $responseUpdate;
}



