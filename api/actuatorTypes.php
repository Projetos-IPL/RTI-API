<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/ActuatorLogs/ActuatorType.php';
    
    requestConfig();
    
    $sctuatorTypes = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/config/sctuatorTypes.json');
    
    if ($sctuatorTypes == null) {
        internalErrorResponse("Falha ao ler ficheiro de configuração.");
    } else {
        successfulDataFetchResponse($sctuatorTypes);
    }
    