<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Controller/Controller.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Permissions/PermissionsManager.php';


class PermissionsController extends Controller
{
    private PermissionsManager $permissionsManager;

    public function __construct()
    {
        $ALLOWED_METHODS = [GET, POST, DELETE];

        $AUTHORIZATION_MAP = array(
            GET => false,
            POST => false,
            DELETE => false,
            PUT => false
        );

        $REQ_BODY_SPEC = array(
            POST => ['rfid'],
            DELETE => ['rfid']
        );

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN,
            POST => X_AUTH_TOKEN,
            DELETE => X_AUTH_TOKEN,
        );

        $this->permissionsManager = new PermissionsManager();

        parent::__construct($ALLOWED_METHODS, $AUTHORIZATION_MAP, $REQ_BODY_SPEC, $REQ_HEADER_SPEC);
    }
    
    protected function routeRequest()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case GET:
                $this->getHandler();
                break;
            case POST:
                $this->postHandler();
                break;
            case DELETE:
                $this->deleteHandler();
                break;
            default:
                methodNotAvailable($_SERVER['REQUEST_METHOD']);
                break;
        }
    }

    private function getHandler()
    {
        try {
            $permissionArr = $this->permissionsManager->getPermissions();
            $permissionsJSONEncoded = json_encode(array_values($permissionArr));
            successfulDataFetchResponse($permissionsJSONEncoded);
        } catch (FileReadException|OperationNotAllowedException $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function postHandler()
    {
        // Tentar adicionar permissão
        try {
            $id = $this->permissionsManager->addPermission($this->REQ_BODY['rfid']);
            objectWrittenSuccessfullyResponse(array('id' => $id, 'rfid' => $this->REQ_BODY));
        } catch (DataSchemaException|FileReadException|FileWriteException|OperationNotAllowedException $e) {
            internalErrorResponse($e->getMessage());
        } catch (DuplicatePermissionException|PersonNotFoundException $e) {
            unprocessableEntityResponse($e->getMessage());
        }
    }

    private function deleteHandler()
    {
        // Tentar apagar permissão
        try {
            $this->permissionsManager->deletePermission($this->REQ_BODY['rfid']);
            objectDeletedSuccessfullyResponse($this->REQ_BODY);
        } catch (PermissionNotFoundException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (FileReadException|FileWriteException|DataSchemaException|OperationNotAllowedException $e) {
            internalErrorResponse($e->getMessage());
        }
    }
}
