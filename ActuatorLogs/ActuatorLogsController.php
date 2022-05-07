<?php


    include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Controller.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . '/ActuatorLogs/ActuatorLogsManager.php';

    class ActuatorLogsController extends Controller
    {

        public function __construct() {
            $AUTHORIZATION_MAP = array(
                GET => false,
                POST => false,
            );

            $REQ_BODY_SPEC = array (
                POST => ['actuatorType', 'value', 'timestamp']
            );

            $REQ_HEADER_SPEC = array (
                GET => X_AUTH_TOKEN,
                POST => X_AUTH_TOKEN
            );
            
            parent::__construct($AUTHORIZATION_MAP, $REQ_BODY_SPEC, $REQ_HEADER_SPEC);
        }

        protected function routeRequest()
        {
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

        public function getHandler()
        {
            try {
                $ActuatorLogssArr = ActuatorLogsManager::getActuatorLogs();
                $ActuatorLogssJSONEncoded = json_encode(array_values($ActuatorLogssArr));
                successfulDataFetchResponse($ActuatorLogssJSONEncoded);
            } catch (FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private function postHandler()
        {

            // Tentar adicionar registo de atuador
            try {
                $log = array(
                    'actuatorType' => $this->REQ_BODY['actuatorType'],
                    'value' => $this->REQ_BODY['value'],
                    'timestamp' => $this->REQ_BODY['timestamp']
                );
                ActuatorLogsManager::addActuatorLogs($log);
                objectWrittenSuccessfullyResponse($log);
            } catch (DataSchemaException | FileReadException | FileWriteException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

    }

