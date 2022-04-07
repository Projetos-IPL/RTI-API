<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/Sensor/SensorLogManager.php';

    abstract class SensorLogController extends Controller {

        public static function handleRequest() {
            requestConfig();
            self::$REQ_BODY = json_decode(file_get_contents('php://input'));

            switch ($_SERVER['REQUEST_METHOD']) {
                case GET:
                    self::getHandler();
                    break;
                case POST:
                    break;
                default:
                    methodNotAvailable($_SERVER['REQUEST_METHOD']);
                    break;
            }
        }

        public static function getHandler() {
            try {
                $sensorLogsArr = SensorLogManager::getSensorLogs();
                $sensorLogsJSONEncoded = json_encode(array_values($sensorLogsArr));
                successfulDataFetchResponse($sensorLogsJSONEncoded);
            } catch (FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

    }
