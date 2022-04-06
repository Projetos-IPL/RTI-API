<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';

    abstract class RecordsController extends Controller {

        public static function handleRequest() {
            requestConfig();
            self::$REQ_BODY = json_decode(file_get_contents('php://input'), true) ?: array();

            switch ($_SERVER['REQUEST_METHOD']) {
                case GET:
                    self::getHandler();
                    break;
                case POST:
                    self::postHandler();
                default:
                    methodNotAvailable($_SERVER['REQUEST_METHOD']);
            }
        }

        public static function getHandler() {
            try {
                $recordsArr = RecordsManager::getRecords();
                $recordsJSONEncoded = json_encode(array_values($recordsArr));
                successfulDataFetchResponse($recordsJSONEncoded);
            } catch (FileWriteException $e) {
                internalErrorResponse($e->getMessage());
            }
        }




    }