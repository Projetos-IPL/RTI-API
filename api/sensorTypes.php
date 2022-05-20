<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/DB.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/SensorLogs/SensorLogUtils.php';

requestConfig();

try {

    $pdo = DB::connect();
    $sensorTypes = SensorLogUtils::getSensorTypes($pdo);
    successfulDataFetchResponse(json_encode($sensorTypes));

} catch (DBConnectionException $e) {
    internalErrorResponse($e->getMessage());
}

