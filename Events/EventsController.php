<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/requestConfig.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/commonResponses.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Controller/Controller.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Events/EventsManager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/emitDataStateUpdateEvent.php';

class EventsController extends Controller
{
    private EventsManager $eventsManager;

    public function __construct()
    {
        $ALLOWED_METHODS = array(
            GET, POST
        );

        $AUTHORIZATION_MAP = array(
            GET => false,
            POST => false,
        );

        $REQ_BODY_SPEC = array(
            POST => ["event_name", "action"],
        );

        $REQ_HEADER_SPEC = array(
            GET => X_AUTH_TOKEN,
            POST => X_AUTH_TOKEN,
        );

        $ALLOWED_URL_PARAMS = ['eventName'];

        parent::__construct($ALLOWED_METHODS,
            $AUTHORIZATION_MAP,
            $REQ_BODY_SPEC,
            REQ_HEADER_SPEC: $REQ_HEADER_SPEC,
            ALLOWED_URL_PARAMS: $ALLOWED_URL_PARAMS);
    }

    protected function routeRequest()
    {
        $this->eventsManager = new EventsManager($this->pdo);

        $reqMethod = $_SERVER['REQUEST_METHOD'];

        switch ($reqMethod) {
            case GET:
                self::getHandler();
                break;
            case POST:
                self::postHandler();
                break;
            default:
                methodNotAvailable($reqMethod);
                break;
        }
    }


    private function getHandler()
    {
        // Obter eventos
        try {
            if (isset($this->URL_PARAMS["eventName"])) {
                $result = $this->eventsManager->getEvents($this->URL_PARAMS["eventName"]);
            } else {
                $result = $this->eventsManager->getEvents();
            }

            successfulDataFetchResponse(json_encode($result ?: array()));
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }

    private function postHandler()
    {
        // Adicionar evento à event queue
        try {
            //  Em função da action enviada no body, adicionar ou remover evento da event queue.
            switch ($this->REQ_BODY["action"]) {
                case EQ_ADD_ACTION:
                    $this->eventsManager->addEventToQueue($this->REQ_BODY["event_name"]);
                    emitDataStateUpdateEvent(ET_EVENTS);
                    break;
                case EQ_REMOVE_ACTION:
                    $this->eventsManager->removeEventFromQueue($this->REQ_BODY["event_name"]);
                    break;
                default:
                    throw new InvalidEventQueueActionException($this->REQ_BODY["event_name"]);
            }

            objectWrittenSuccessfullyResponse(["event_name" => $this->REQ_BODY["event_name"]]);
        } catch (InvalidEventQueueActionException $e) {
            unprocessableEntityResponse($e->getMessage());
        } catch (Exception $e) {
            internalErrorResponse($e->getMessage());
        }
    }



}