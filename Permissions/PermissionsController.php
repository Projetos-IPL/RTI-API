<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/Permissions/PermissionsManager.php';


    abstract class PermissionsController extends Controller {

        public static function handleRequest()
        {
            requestConfig();
            self::$REQ_BODY = json_decode(file_get_contents('php://input'), true) ?: array();

            switch ($_SERVER['REQUEST_METHOD']) {
                case GET:
                    self::getHandler();
                    break;
                case POST:
                    self::postHandler();
                    break;
                case DELETE:
                    self::deleteHandler();
                    break;
                default:
                    methodNotAvailable($_SERVER['REQUEST_METHOD']);
                    break;
            }
        }

        private static function getHandler() {
            try {
                $permissionArr = PermissionsManager::getPermissions();
                $permissionsJSONEncoded = json_encode(array_values($permissionArr));
                successfulDataFetchResponse($permissionsJSONEncoded);
            } catch (FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private static function postHandler() {
            if (!self::validatePostRequest(self::$REQ_BODY)) {
                wrongFormatResponse();
                return;
            }

            // Tentar adicionar permissão
            try {
                $id = PermissionsManager::addPermission(self::$REQ_BODY['rfid']);
                objectWrittenSuccessfullyResponse(array('id'=>$id, 'rfid'=>self::$REQ_BODY));
            } catch (DataSchemaException | FileReadException | FileWriteException $e) {
                internalErrorResponse($e->getMessage());
            } catch (DuplicatePermissionException | PersonNotFoundException $e) {
                unprocessableEntityResponse($e->getMessage());
            }
        }

        private static function deleteHandler() {
            if (!self::validateDeleteRequest(self::$REQ_BODY)) {
                wrongFormatResponse();
                return;
            }

            // Tentar apagar permissão
            try {
                PermissionsManager::deletePermission(self::$REQ_BODY['rfid']);
                objectDeletedSuccessfullyResponse(self::$REQ_BODY);
            } catch (PermissionNotFoundException $e) {
                unprocessableEntityResponse($e->getMessage());
            } catch (FileReadException | FileWriteException | DataSchemaException $e) {
                internalErrorResponse($e->getMessage());
            }
        }

        private static function validatePostRequest(array $req_body): bool
        {
            if (count($req_body) != 1) return false;
            if (!isset($req_body["rfid"]) ) return false;
            return true;
        }

        private static function validateDeleteRequest(array $req_body): bool
        {
            if (!isset($req_body["rfid"])) {
                return false;
            }
            return true;
        }

    }
