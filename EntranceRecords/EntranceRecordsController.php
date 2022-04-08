<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . '/EntranceRecords/EntranceRecordsManager.php';

    abstract class EntranceRecordsController extends Controller {

        public static function handleRequest() {
            requestConfig();
            self::$REQ_BODY = json_decode(file_get_contents('php://input'), true) ?: array();

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

        public static function getHandler() {
            try {
                $recordsArr = EntranceRecordsManager::getEntranceRecords();
                $recordsJSONEncoded = json_encode(array_values($recordsArr));
                successfulDataFetchResponse($recordsJSONEncoded);
            } catch (FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        public static function postHandler() {
            if (!self::validatePostRequest(self::$REQ_BODY)) {
                wrongFormatResponse();
                return;
            }

            // Tentar adicionar registo
            try {
                $newRecord = EntranceRecordsManager::createEntranceRecord(self::$REQ_BODY['rfid']);
                objectWrittenSuccessfullyResponse($newRecord);
            } catch (PersonNotFoundException $e) {
                unprocessableEntityResponse($e->getMessage());
            } catch (DataSchemaException | FileReadException | FileWriteException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        public static function validatePostRequest(array $req_body): bool
        {
            if (count($req_body) != 1 || !isset($req_body['rfid'])) {
                return false;
            }
            return true;
        }




    }