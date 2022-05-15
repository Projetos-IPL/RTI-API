<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Controller/Controller.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/exceptions/FileWriteException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/exceptions/FileReadException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/exceptions/DataSchemaException.php';


include_once $_SERVER['DOCUMENT_ROOT'] . '/People/exceptions/PersonNotFoundException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/People/exceptions/DuplicateRFIDException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/People/exceptions/NameUpdateException.php';

include_once 'PeopleManager.php';

class PeopleController extends Controller
{

    private PeopleManager $peopleManager;

    public function __construct()
    {
        $ALLOWED_METHODS = [GET, POST, PUT, DELETE];

        $AUTHORIZATION_MAP = array(
            GET => false,
            POST => false,
            DELETE => false,
            PUT => false
        );

        $REQ_BODY_SPEC = array(
            POST => ['first_name', 'last_name', 'rfid'],
            PUT => ['rfid', 'newRfid'],
            DELETE => ['rfid']
        );

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN,
            POST => X_AUTH_TOKEN,
            PUT => X_AUTH_TOKEN,
            DELETE => X_AUTH_TOKEN,
        );

        $ALLOWED_URL_PARAMS = ['rfid'];

        parent::__construct($ALLOWED_METHODS,
            $AUTHORIZATION_MAP,
            $REQ_BODY_SPEC,
            REQ_HEADER_SPEC: $REQ_HEADER_SPEC,
            ALLOWED_URL_PARAMS: $ALLOWED_URL_PARAMS);
    }

    protected function routeRequest()
    {

        $this->peopleManager = new PeopleManager($this->pdo);

        switch ($_SERVER['REQUEST_METHOD']) {
            case GET:
                self::getHandler();
                break;
            case POST:
                self::postHandler();
                break;
            case PUT:
                self::putHandler();
                break;
            case DELETE:
                self::deleteHandler();
                break;
            default:
                methodNotAvailable($_SERVER['REQUEST_METHOD']);
                break;
        }
    }


    private function getHandler()
    {
        try {
            $peopleArr = array();

            // Se não forem passados parametros de url fazer consulta generica
            if (count($this->URL_PARAMS) == 0) {
                $peopleArr = $this->peopleManager->getPeople();
            } else {
                // Se for passado um parâmetro de url 'rfid', devolver pessoa por rfid
                if (isset($this->URL_PARAMS['rfid'])) {
                    $peopleArr = $this->peopleManager->getPersonByRFID($this->URL_PARAMS['rfid']);
                }
            }

            $peopleJSONEncoded = json_encode($peopleArr);
            successfulDataFetchResponse($peopleJSONEncoded);
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function postHandler()
    {
        // Tentar adicionar pessoa
        try {
            $this->peopleManager->addPerson($this->REQ_BODY);
            objectWrittenSuccessfullyResponse($this->REQ_BODY);
        } catch (DuplicateRFIDException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (DataSchemaException|Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function putHandler()
    {
        // Tentar atualizar pessoa e responder com o resultado
        try {
            $this->peopleManager->updatePersonRFID($this->REQ_BODY['rfid'], $this->REQ_BODY['newRfid']);
            objectWrittenSuccessfullyResponse($this->REQ_BODY['newRfid']);
        } catch (DuplicateRFIDException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function deleteHandler()
    {
        // Tentar apagar pessoa e responder com o resultado
        try {
            $this->peopleManager->deletePerson($this->REQ_BODY['rfid']);
            objectDeletedSuccessfullyResponse($this->REQ_BODY);
        } catch (PersonNotFoundException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }
}

