<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/EntranceRecordsImages/EntranceRecordsImagesManager.php';


class EntranceRecordsImagesController extends Controller
{

    private EntranceRecordsImagesManager $entranceRecordsImagesManager;

    public function __construct()
    {

        $ALLOWED_METHODS = [GET, POST];

        $AUTHORIZATION_MAP = array(
            GET => false,
            POST => false
        );

        $REQ_BODY_SPEC = array(
            POST => ['entrance_log_id', 'image']
        );

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN,
            POST => X_AUTH_TOKEN
        );

        $ALLOWED_URL_PARAMS = ['entrance_log_id'];

        parent::__construct($ALLOWED_METHODS, $AUTHORIZATION_MAP, $REQ_BODY_SPEC, $REQ_HEADER_SPEC, $ALLOWED_URL_PARAMS);
    }

    protected function routeRequest()
    {

        $this->entranceRecordsImagesManager = new EntranceRecordsImagesManager($this->pdo);

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
            if (count($this->URL_PARAMS) == 0) {
                $recordsArr = $this->entranceRecordsImagesManager->getEntranceRecordsImages();
            } else {
                $recordsArr = $this->entranceRecordsImagesManager->getEntranceRecordsImagesFiltered($this->URL_PARAMS);
            }

            $recordsJSONEncoded = json_encode(array_values($recordsArr));
            successfulDataFetchResponse($recordsJSONEncoded);

        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    public function postHandler()
    {
        // Tentar adicionar registo
        try {
            $this->entranceRecordsImagesManager->addEntranceRecordImage($this->REQ_BODY);
            objectWrittenSuccessfullyResponse($this->REQ_BODY);
        } catch (PersonNotFoundException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

}
