<?php


/** Função para emitir o evento de atualizar estado de entidade
 * @param string $dataEntity Entidade
 * @return void
 */
function emitDataStateUpdateEvent(string $dataEntity) : void
{
    $WSocketServerURL = "http://127.0.0.1:3001/dataStateUpdate/" . $dataEntity;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $WSocketServerURL);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
        "Accept: application/json",
        "Content-Type: application/json",
    );

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($curl);
    curl_close($curl);

    echo $resp;
}

