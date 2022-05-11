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
            POST => ['sensorType', 'value', 'timestamp']
        );

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN,
            POST => X_AUTH_TOKEN
        );

        $ALLOWED_URL_PARAMS = ['sensorType', 'latest'];

        $this->sensorLogManager = new SensorLogManager();

        parent::__construct($ALLOWED_METHODS,
                            $AUTHORIZATION_MAP,
                            $REQ_BODY_SPEC,
                            REQ_HEADER_SPEC: $REQ_HEADER_SPEC,
                            ALLOWED_URL_PARAMS: $ALLOWED_URL_PARAMS);
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

            // Se foram definidos parâmetros de URL, filtrar resultados
            if (count($this->URL_PARAMS) != 0) {

                // Filtrar por sensorType
                if (isset($this->URL_PARAMS['sensorType'])) {
                    $sensorLogsArr = SensorLogUtils::filterLogsBySensorType($sensorLogsArr, $this->URL_PARAMS['sensorType']);
                }

                // Filtrar por último
                if (isset($this->URL_PARAMS['latest']) && $this->URL_PARAMS['latest'] == 1) {
                    $sensorLogsArr = SensorLogUtils::getLatestLog($sensorLogsArr);
                }
            }

            $sensorLogsJSONEncoded = json_encode(array_values($sensorLogsArr));
            successfulDataFetchResponse($sensorLogsJSONEncoded);
        } catch (InvalidSensorTypeException $e) {
            unprocessableEntityResponse($e->getMessage());
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
        } catch (InvalidSensorTypeException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (DataSchemaException|FileReadException|FileWriteException|OperationNotAllowedException $e) {
            internalErrorResponse($e->getMessage());
        }
    }
}
