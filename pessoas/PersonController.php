<?php

    include_once '../utils/commonResponses.php';
    include_once '../utils/requestConfig.php';
    include_once '../utils/exceptions/FileWriteException.php';
    include_once '../utils/exceptions/FileReadException.php';
    include_once 'exceptions/PersonNotFoundException.php';
    include_once 'exceptions/NameUpdateException.php';

    include_once '_getPeople.php';
    include_once '_addPerson.php';
    include_once '_updatePerson.php';

    abstract class PersonController {

        public static function handleRequest() {

            requestConfig();

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
                default:
                    methodNotAvailable($_SERVER['REQUEST_METHOD']);
                    break;
            }
        }

        private static function getHandler() {
            try {
                $people_json_string = _getPeople();
                successfulDataFetchResponse($people_json_string);
            } catch (FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private static function postHandler() {
            // Obter corpo do pedido e validar schema
            $person = json_decode(file_get_contents('php://input'), true);
            if (!self::validateSchema($person)) {
                wrongFormatResponse();
            };

            // Tentar adicionar pessoa
            try {
                _addPerson($person);
                objectWrittenSuccessfullyResponse($person);
            } catch (RFIDException $e) {
                unprocessableEntityResponse($e->getMessage());
            } catch (FileWriteException | FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private static function putHandler() {
            $req_body = json_decode(file_get_contents('php://input'), true);

            // TODO - PUT Request Schema validation

            try {
                _updatePerson($req_body['rfid'], $req_body['data']);
                objectWrittenSuccessfullyResponse($req_body['data']);
            } catch (FileReadException | FileWriteException $e) {
                internalErrorResponse($e->getMessage());
            } catch (PersonNotFoundException | NameUpdateException $e) {
                unprocessableEntityResponse($e->getMessage());
            }
        }

        public static function validateSchema($person) {
            if ($person == null) return false;
            if (count($person) != 3) return false;
            if (!isset($person["primNome"]) || !isset($person["ultNome"]) || !isset($person["rfid"])) {
                return false;
            }

            return true;
        }
    }

