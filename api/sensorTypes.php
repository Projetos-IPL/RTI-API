<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/SensorLogs/SensorLogUtils.php';

    requestConfig();

    $sensorTypes = SensorLogUtils::getSensorTypes();

    if (count($sensorTypes) == 0) {
        internalErrorResponse("Falha ao ler ficheiro de configuração.");
    } else {
        successfulDataFetchResponse(json_encode($sensorTypes));
    }
