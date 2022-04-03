<?php

    include_once '../utils/commonResponses.php';
    include_once '../utils/requestConfig.php';

    include_once 'PeopleManager.php';

    abstract class PersonController {

        private static array $REQ_BODY;

        public static function handleRequest() {

            requestConfig();

            self::$REQ_BODY = json_decode(file_get_contents('php://input'), true) ?: array();

            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    self::getHandler();
                    break;
                case 'POST':
                    self::postHandler();
                    break;
                case 'PUT':
                    self::putHandler();
                    break;
                case 'DELETE':
                    self::deleteHandler();
                    break;
                default:
                    methodNotAvailable($_SERVER['REQUEST_METHOD']);
                    break;
            }
        }

        private static function getHandler() {
            try {
                $peopleArr = PeopleManager::getPeople();
                $peopleJSONEncoded = json_encode(array_values($peopleArr));
                successfulDataFetchResponse($peopleJSONEncoded);
            } catch (FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private static function postHandler() {
            if (!self::validatePostRequest(self::$REQ_BODY)) {
                wrongFormatResponse();
            };

            // Tentar adicionar pessoa
            try {
                PeopleManager::addPerson(self::$REQ_BODY);
                objectWrittenSuccessfullyResponse(self::$REQ_BODY);
            } catch (DuplicateRFIDException $e) {
                unprocessableEntityResponse($e->getMessage());
            } catch (DataSchemaException | FileWriteException | FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private static function putHandler() {
            if (!self::validatePutRequest(self::$REQ_BODY)) {
                wrongFormatResponse();
            }

            // Tentar atualizar pessoa e responder com o resultado
            try {
                PeopleManager::updatePerson(self::$REQ_BODY['rfid'], self::$REQ_BODY['data']);
                objectWrittenSuccessfullyResponse(self::$REQ_BODY['data']);
            } catch (FileReadException | FileWriteException | DataSchemaException$e) {
                internalErrorResponse($e->getMessage());
            } catch (PersonNotFoundException | NameUpdateException | DuplicateRFIDException $e) {
                unprocessableEntityResponse($e->getMessage());
            }
        }

        private static function deleteHandler() {
            if (!self::validateDeleteRequest(self::$REQ_BODY)) {
                wrongFormatResponse();
            }

            // Tentar apagar pessoa e responder com o resultado
            try {
                PeopleManager::deletePerson(self::$REQ_BODY['rfid']);
                objectDeletedSuccessfullyResponse(self::$REQ_BODY);
            } catch (PersonNotFoundException $e) {
                unprocessableEntityResponse($e->getMessage());
            } catch (FileReadException | FileWriteException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private static function validatePostRequest(array $req_body): bool
        {
            if (!PeopleUtils::validatePersonArray($req_body)) {
                return false;
            }
           return true;
        }

        private static function validatePutRequest(array $req_body): bool
        {
            if (!isset($req_body["rfid"]) || !isset($req_body["data"])) {
                return false;
            }
            if (!PeopleUtils::validatePersonArray($req_body["data"])) {
                return false;
            }
            return true;
        }

        private static function validateDeleteRequest(array $req_body):bool
        {
            if (!isset($req_body["rfid"])) {
                return false;
            }
            return true;
        }


    }

