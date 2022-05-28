<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/DB.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/ActuatorLogs/ActuatorLogUtils.php';

requestConfig();

try {

    $pdo = DB::connect();
    $sensorTypes = ActuatorLogUtils::getActuatorTypes($pdo);
    successfulDataFetchResponse(json_encode($sensorTypes));

} catch (DBConnectionException $e) {
    internalErrorResponse($e->getMessage());
}

