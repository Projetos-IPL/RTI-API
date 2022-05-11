<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller/Controller.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/FileWriteException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/FileReadException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/DataSchemaException.php';


    include_once $_SERVER['DOCUMENT_ROOT'].'/People/exceptions/PersonNotFoundException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/People/exceptions/DuplicateRFIDException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/People/exceptions/NameUpdateException.php';

    include_once 'PeopleManager.php';

    class PeopleController extends Controller {
        
        private PeopleManager $peopleManager;

        public function __construct() {

            $ALLOWED_METHODS = [GET, POST, PUT, DELETE];

            $AUTHORIZATION_MAP = array(
                GET => false,
                POST => false,
                DELETE => false,
                PUT => false
            );

            $REQ_BODY_SPEC = array(
                POST => ['primNome', 'ultNome', 'rfid'],
                PUT => ['rfid', "data" => ["primNome", "ultNome", "rfid"]],
                DELETE => ['rfid']
            );

            $REQ_HEADER_SPEC = array(
                GET => X_AUTH_TOKEN,
                POST => X_AUTH_TOKEN,
                PUT => X_AUTH_TOKEN,
                DELETE => X_AUTH_TOKEN,
            );

            $ALLOWED_URL_PARAMS = ['rfid'];

            $this->peopleManager = new PeopleManager();

            parent::__construct($ALLOWED_METHODS,
                                $AUTHORIZATION_MAP,
                                $REQ_BODY_SPEC,
                                REQ_HEADER_SPEC: $REQ_HEADER_SPEC,
                                ALLOWED_URL_PARAMS: $ALLOWED_URL_PARAMS);
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


        private function getHandler() {
            try {
                $peopleArr = $this->peopleManager->getPeople();

                // Se for passado um parâmetro de url 'rfid', filtrar lista por esse rfid
                if (count($this->URL_PARAMS) != 0 && isset($this->URL_PARAMS['rfid'])) {
                    $index = PeopleUtils::getPersonIndex($peopleArr, $this->URL_PARAMS['rfid']);
                    $peopleArr = array($peopleArr[$index]);
                }

                $peopleJSONEncoded = json_encode(array_values($peopleArr));
                successfulDataFetchResponse($peopleJSONEncoded);
            } catch (FileReadException | OperationNotAllowedException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private function postHandler() {
            // Tentar adicionar pessoa
            try {
                $this->peopleManager->addPerson($this->REQ_BODY);
                objectWrittenSuccessfullyResponse($this->REQ_BODY);
            } catch (DuplicateRFIDException $e) {
                unprocessableEntityResponse($e->getMessage());
            } catch (DataSchemaException | FileWriteException | FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private function putHandler() {
            // Tentar atualizar pessoa e responder com o resultado
            try {
                $this->peopleManager->updatePerson($this->REQ_BODY['rfid'], $this->REQ_BODY['data']);
                objectWrittenSuccessfullyResponse($this->REQ_BODY['data']);
            } catch (FileReadException | FileWriteException | DataSchemaException$e) {
                internalErrorResponse($e->getMessage());
            } catch (PersonNotFoundException | NameUpdateException | DuplicateRFIDException $e) {
                unprocessableEntityResponse($e->getMessage());
            }
        }

        private function deleteHandler() {
            // Tentar apagar pessoa e responder com o resultado
            try {
                $this->peopleManager->deletePerson($this->REQ_BODY['rfid']);
                objectDeletedSuccessfullyResponse($this->REQ_BODY);
            } catch (PersonNotFoundException $e) {
                unprocessableEntityResponse($e->getMessage());
            } catch (DataSchemaException | FileReadException | FileWriteException $e) {
                internalErrorResponse($e->getMessage());
            }
        }
    }

