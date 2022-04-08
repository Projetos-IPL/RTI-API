<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . '/SensorLogs/SensorLogManager.php';

    abstract class SensorLogController extends Controller {

        public static function handleRequest() {
            requestConfig();
            self::$REQ_BODY = json_decode(file_get_contents('php://input'), true) ?: array();

            switch ($_SERVER['REQUEST_METHOD']) {
                case GET:
                    self::getHandler();
                    break;
                case POST:
                    self::postHandler();
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

        private static function postHandler() {
            if (!self::validatePostRequest(self::$REQ_BODY)) {
                wrongFormatResponse();
                return;
            }

            // Tentar adicionar registo de sensor
            try {
                SensorLogManager::addSensorLog(self::$REQ_BODY);
                objectWrittenSuccessfullyResponse(self::$REQ_BODY);
            } catch (DataSchemaException | FileReadException | FileWriteException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private static function validatePostRequest(array $req_body): bool
        {
            if (!SensorLogUtils::validateSensorLogSchema($req_body)) {
                return false;
            }
            return true;
        }

    }
