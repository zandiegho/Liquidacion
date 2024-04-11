<?php

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
}



$curlUpdateNomina = curl_init();

curl_setopt_array($curlUpdateNomina, array(
  CURLOPT_URL => 'https://crm.wolkvox.com/server/API/v2/custom/update.php',
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
    'Wolkvox-Token: 7b74656368636f6e7d2d7b32303232303632343132323830357d',
    'Content-Type: application/json',
    'Cookie: PHPSESSID=sjnu2s2egn92ldo8vsqsek1ui3'
  ),
));

$responseUpdate = curl_exec($curlUpdateNomina);

curl_close($curlUpdateNomina);
echo $responseUpdate;


/*
"Nombre contacto": {
            "type": "search",
            "value": "'.$name.'",
            "value_id": "'.$idNomina.'",
            "searchModuleName": "contacts"
        },
        "ID Contacto": {
            "type": "search",
            "value": '.$idcliente.',
            "value_id": "'.$idNomina.'",
            "searchModuleName": "contacts"
        },
        "Nombre Empleado": {
            "type": "search",
            "value": "'.$empleado.'",
            "value_id": "'.$idNomina.'",
            "searchModuleName": "contacts"
        },
        "ID Empleado": {
            "type": "search",
            "value": '.$idempleado.',
            "value_id": "'.$idNomina.'",
            "searchModuleName": "contacts"
        },
*/