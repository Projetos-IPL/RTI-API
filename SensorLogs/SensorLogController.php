<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Controller/Controller.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/SensorLogs/SensorLogManager.php';

class SensorLogController extends Controller
{

    private SensorLogManager $sensorLogManager;

    public function __construct()
    {
        $ALLOWED_METHODS = [GET, POST];

        $AUTHORIZATION_MAP = array(
            GET => false,
            POST => false,
        );

        $REQ_BODY_SPEC = array(
            POST => ['sensorType', 'value', 'timestamp']
        );

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN,
            POST => X_AUTH_TOKEN
        );

        $this->sensorLogManager = new SensorLogManager();

        parent::__construct($ALLOWED_METHODS, $AUTHORIZATION_MAP, $REQ_BODY_SPEC, $REQ_HEADER_SPEC);
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

    public function getHandler()
    {
        try {
            $sensorLogsArr = $this->sensorLogManager->getSensorLogs();
            $sensorLogsJSONEncoded = json_encode(array_values($sensorLogsArr));
            successfulDataFetchResponse($sensorLogsJSONEncoded);
        } catch (FileReadException|OperationNotAllowedException $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function postHandler()
    {
        // Tentar adicionar registo de sensor
        try {
            $log = array(
                'sensorType' => $this->REQ_BODY['sensorType'],
                'value' => $this->REQ_BODY['value'],
                'timestamp' => $this->REQ_BODY['timestamp']
            );
            $this->sensorLogManager->addSensorLog($log);
            objectWrittenSuccessfullyResponse($log);
        } catch (DataSchemaException|FileReadException|FileWriteException|OperationNotAllowedException $e) {
            internalErrorResponse($e->getMessage());
        }
    }
}
