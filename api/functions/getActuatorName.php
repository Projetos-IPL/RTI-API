<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/ActuatorLogs/ActuatorType.php';

    requestConfig();

    $req_body = json_decode(file_get_contents('php://input'), true) ?: array();

    if (!isset($req_body['actuatorType']) || count($req_body) != 1) {
        wrongFormatResponse();
        exit();
    }

    http_response_code(200);
    $actuatorName = ActuatorType::getActuatorName($req_body['actuatorType']);

    echo json_encode(array(
            "name"=>$actuatorName
        )
    );
