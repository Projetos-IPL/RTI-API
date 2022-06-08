<?php


include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Controller/Controller.php';

class ViewsController extends Controller
{

    public function __construct()
    {
        $ALLOWED_METHODS = array(
            GET
        );

        $AUTHORIZATION_MAP = array(
            GET => false,
        );

        $REQ_BODY_SPEC = array();

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN
        );

        $ALLOWED_URL_PARAMS = ['viewName'];

        parent::__construct($ALLOWED_METHODS,
                            $AUTHORIZATION_MAP,
                            $REQ_BODY_SPEC,
                            REQ_HEADER_SPEC: $REQ_HEADER_SPEC,
                            ALLOWED_URL_PARAMS: $ALLOWED_URL_PARAMS);
    }

    protected function routeRequest()
    {
        $reqMethod = $_SERVER['REQUEST_METHOD'];

        switch ($reqMethod) {
            case GET:
                self::getHandler();
                break;
            default:
                methodNotAvailable($reqMethod);
                break;
        }
    }


    private function getHandler()
    {
        // Se o nome da vista não for especificado, fazer query à vista de estatisticas
        $viewName = $this->URL_PARAMS['viewName'] ?? 'general_stats_view';

        // Fazer consulta à base de dados
        try {
            $queryString = "SELECT * FROM " . $viewName;
            $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            successfulDataFetchResponse(json_encode($result ?: array()));
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

}