<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . '/SensorLogs/SensorLogManager.php';

    class SensorLogController extends Controller {

        public function __construct() {
            $AUTHORIZATION_MAP = array(
                GET => false,
                POST => false,
            );

            $REQ_BODY_SPEC = array (
                POST => ['sensorType', 'value', 'timestamp']
            );

            $REQ_HEADER_SPEC = array (
                GET => X_AUTH_TOKEN,
                POST => X_AUTH_TOKEN
            );


            parent::__construct($AUTHORIZATION_MAP, $REQ_BODY_SPEC, $REQ_HEADER_SPEC);
        }

        protected function routeRequest()
        {
            $reqMethod = $_SERVER['REQUEST_METHOD'];

            switch ($reqMethod) {
                case GET:
                    self::getHandler();
                    break;
                case POST:
                    self::postHandler();
                    break;
                default:
                    methodNotAvailable($reqMethod);
                    break;
            }
        }

        public function getHandler() {
            try {
                $sensorLogsArr = SensorLogManager::getSensorLogs();
                $sensorLogsJSONEncoded = json_encode(array_values($sensorLogsArr));
                successfulDataFetchResponse($sensorLogsJSONEncoded);
            } catch (FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private function postHandler() {

            // Tentar adicionar registo de sensor
            try {
                $log = array(
                    'sensorType' => $this->REQ_BODY['sensorType'],
                    'value' => $this->REQ_BODY['value'],
                    'timestamp' => $this->REQ_BODY['timestamp']
                );
                SensorLogManager::addSensorLog($log);
                objectWrittenSuccessfullyResponse($log);
            } catch (DataSchemaException | FileReadException | FileWriteException $e) {
                internalErrorResponse($e->getMessage());
            }
        }
    }
