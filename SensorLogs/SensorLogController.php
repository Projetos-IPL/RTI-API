<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Controller/Controller.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/SensorLogs/SensorLogManager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/SensorLogs/SensorLogUtils.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/SensorLogs/exceptions/InvalidSensorTypeException.php';

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
            POST => ['sensorType', 'value']
        );

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN,
            POST => X_AUTH_TOKEN
        );

        $ALLOWED_URL_PARAMS = ['sensorType', 'latest'];

        parent::__construct($ALLOWED_METHODS,
                            $AUTHORIZATION_MAP,
                            $REQ_BODY_SPEC,
                            REQ_HEADER_SPEC: $REQ_HEADER_SPEC,
                            ALLOWED_URL_PARAMS: $ALLOWED_URL_PARAMS);
    }

    protected function routeRequest()
    {

        $this->sensorLogManager = new SensorLogManager($this->pdo);

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
            // Se foram definidos parâmetros de URL, filtrar resultados
            if (count($this->URL_PARAMS) == 0) {
                $sensorLogsArr = $this->sensorLogManager->getSensorLogs();
            } else {
                $sensorLogsArr = $this->sensorLogManager->getSensorLogsFiltered($this->URL_PARAMS);
            }

            $sensorLogsJSONEncoded = json_encode(array_values($sensorLogsArr));
            successfulDataFetchResponse($sensorLogsJSONEncoded);
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function postHandler()
    {
        // Tentar adicionar registo de sensor
        try {
            $log = array(
                'sensorType' => $this->REQ_BODY['sensorType'],
                'value' => $this->REQ_BODY['value']);
            $this->sensorLogManager->addSensorLog($log);
            objectWrittenSuccessfullyResponse($log);
        } catch (InvalidSensorTypeException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (DataSchemaException|Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }
}
