<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Controller/Controller.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/EntranceRecords/EntranceRecordsManager.php';

class EntranceRecordsController extends Controller
{

    private EntranceRecordsManager $entranceRecordsManager;

    public function __construct()
    {

        $ALLOWED_METHODS = [GET, POST];

        $AUTHORIZATION_MAP = array(
            GET => false,
            POST => false,
            DELETE => false,
            PUT => false
        );

        $REQ_BODY_SPEC = array(
            POST => ['rfid']
        );

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN,
            POST => X_AUTH_TOKEN,
        );

        $this->entranceRecordsManager = new EntranceRecordsManager();

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
        }
    }

    public function getHandler()
    {
        try {
            $recordsArr = $this->entranceRecordsManager->getEntranceRecords();
            $recordsJSONEncoded = json_encode(array_values($recordsArr));
            successfulDataFetchResponse($recordsJSONEncoded);
        } catch (FileReadException|OperationNotAllowedException $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    public function postHandler()
    {
        // Tentar adicionar registo
        try {
            $newRecord = $this->entranceRecordsManager->createEntranceRecord($this->REQ_BODY['rfid']);
            objectWrittenSuccessfullyResponse($newRecord);
        } catch (PersonNotFoundException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (DataSchemaException|FileReadException|FileWriteException|OperationNotAllowedException $e) {
            internalErrorResponse($e->getMessage());
        }
    }
}