<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Controller/Controller.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/emitDataStateUpdateEvent.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Permissions/PermissionsManager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Permissions/exceptions/DuplicatePermissionException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Permissions/exceptions/PermissionNotFoundException.php';


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

        $ALLOWED_URL_PARAMS = ['rfid'];

        parent::__construct($ALLOWED_METHODS,
            $AUTHORIZATION_MAP,
            $REQ_BODY_SPEC,
            REQ_HEADER_SPEC: $REQ_HEADER_SPEC,
            ALLOWED_URL_PARAMS: $ALLOWED_URL_PARAMS);
    }

    protected function routeRequest()
    {

        $this->permissionsManager = new PermissionsManager($this->pdo);

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
            $permissionArr = array();

            // Se não forem passados parametros de url fazer consulta generica
            if (count($this->URL_PARAMS) == 0) {
                $permissionArr = $this->permissionsManager->getPermissions();
            } else {
                // Se for passado um parâmetro de url 'rfid', devolver permissão por rfid
                if (isset($this->URL_PARAMS['rfid'])) {
                    $permissionArr = $this->permissionsManager->getPermissionByRFID($this->URL_PARAMS['rfid']);
                    if (!$permissionArr) {
                        throw new PermissionNotFoundException($this->URL_PARAMS['rfid']);
                    }
                }
            }

            $permissionsJSONEncoded = json_encode($permissionArr);
            successfulDataFetchResponse($permissionsJSONEncoded);
        } catch (PermissionNotFoundException $e){
            noContentResponse($e->getMessage());
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function postHandler()
    {
        // Tentar adicionar permissão
        try {
            $this->permissionsManager->addPermission($this->REQ_BODY['rfid']);
            objectWrittenSuccessfullyResponse($this->REQ_BODY);
            emitDataStateUpdateEvent(ET_PERMISSIONS);
        } catch (DuplicatePermissionException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function deleteHandler()
    {
        // Tentar apagar permissão
        try {
            $this->permissionsManager->deletePermission($this->REQ_BODY['rfid']);
            objectDeletedSuccessfullyResponse($this->REQ_BODY);
            emitDataStateUpdateEvent(ET_PERMISSIONS);
        } catch (PermissionNotFoundException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }
}
