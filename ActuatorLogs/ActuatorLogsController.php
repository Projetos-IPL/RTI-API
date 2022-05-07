<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Controller/Controller.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/ActuatorLogs/ActuatorLogsManager.php';

class ActuatorLogsController extends Controller
{

    private ActuatorLogsManager $actuatorLogsManager;

    public function __construct()
    {
        $ALLOWED_METHODS = [GET, POST];

        $AUTHORIZATION_MAP = array(
            GET => false,
            POST => false,
        );

        $REQ_BODY_SPEC = array(
            POST => ['actuatorType', 'timestamp']
        );

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN,
            POST => X_AUTH_TOKEN
        );

        $this->actuatorLogsManager = new ActuatorLogsManager();

        parent::__construct($ALLOWED_METHODS, $AUTHORIZATION_MAP, $REQ_BODY_SPEC, $REQ_HEADER_SPEC);
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
            $ActuatorLogssArr = $this->actuatorLogsManager->getActuatorLogs();
            $ActuatorLogssJSONEncoded = json_encode(array_values($ActuatorLogssArr));
            successfulDataFetchResponse($ActuatorLogssJSONEncoded);
        } catch (FileReadException|OperationNotAllowedException $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function postHandler()
    {
        // Tentar adicionar registo de atuador
        try {
            $log = array(
                'actuatorType' => $this->REQ_BODY['actuatorType'],
                'timestamp' => $this->REQ_BODY['timestamp']
            );
            $this->actuatorLogsManager->addActuatorLog($log);
            objectWrittenSuccessfullyResponse($log);
        } catch (DataSchemaException|FileReadException|FileWriteException|OperationNotAllowedException $e) {
            internalErrorResponse($e->getMessage());
        }
    }

}

