<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';

    requestConfig();
    
    $sctuatorTypes = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/config/actuatorTypes.json');
    
    if ($sctuatorTypes == null) {
        internalErrorResponse("Falha ao ler ficheiro de configuração.");
    } else {
        successfulDataFetchResponse($sctuatorTypes);
    }
    