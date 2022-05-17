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
            POST => ['actuatorType']
        );

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN,
            POST => X_AUTH_TOKEN
        );

        $ALLOWED_URL_PARAMS = ['actuatorType', 'latest'];

        parent::__construct($ALLOWED_METHODS,
                            $AUTHORIZATION_MAP,
                            $REQ_BODY_SPEC,
                            REQ_HEADER_SPEC: $REQ_HEADER_SPEC,
                            ALLOWED_URL_PARAMS: $ALLOWED_URL_PARAMS);
    }

    protected function routeRequest()
    {

        $this->actuatorLogsManager = new ActuatorLogsManager($this->pdo);

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

            // Se foram definidos parâmetros de URL, filtrar resultados
            if (count($this->URL_PARAMS) == 0) {
                $actuatorLogsArr = $this->actuatorLogsManager->getActuatorLogs();
            } else {
                $actuatorLogsArr = $this->actuatorLogsManager->getActuatorLogsFiltered($this->URL_PARAMS);
            }
            $ActuatorLogssJSONEncoded = json_encode(array_values($actuatorLogsArr));
            successfulDataFetchResponse($ActuatorLogssJSONEncoded);
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function postHandler()
    {
        // Tentar adicionar registo de atuador
        try {
            $log = array(
                'actuatorType' => $this->REQ_BODY['actuatorType']);
            $this->actuatorLogsManager->addActuatorLog($this->REQ_BODY['actuatorType']);
            objectWrittenSuccessfullyResponse($log);
        } catch (DataSchemaException|Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

}

