<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/SensorLogs/SensorType.php';

    requestConfig();

    $req_body = json_decode(file_get_contents('php://input'), true) ?: array();

    if (!isset($req_body['sensorType']) || count($req_body) != 1) {
        wrongFormatResponse();
        exit();
    }

    http_response_code(200);
    $sensorName = SensorType::getSensorName($req_body['sensorType']);

    echo json_encode(array(
        "name"=>$sensorName
        )
    );
