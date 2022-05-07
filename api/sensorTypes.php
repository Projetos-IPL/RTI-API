<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';

    requestConfig();

    $sensorTypes = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/config/sensorTypes.json');

    if ($sensorTypes == null) {
        internalErrorResponse("Falha ao ler ficheiro de configuração.");
    } else {
        successfulDataFetchResponse($sensorTypes);
    }
