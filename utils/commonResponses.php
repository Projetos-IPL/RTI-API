<?php

    function wrongFormatResponse() {
        http_response_code(400);
        echo json_encode(array("message" => "Request body not properly defined."));
    }

    function methodNotAvailable($method) {
        http_response_code(400);
        echo json_encode(array("message" => $method . " is not available in this endpoint."));
    }


    function internalErrorResponse($message) {
        http_response_code(503);
        echo json_encode(array("message" => $message));
    }

    function successfulDataFetchResponse($fetchedData) {
        http_response_code(200);
        echo $fetchedData;
    }

    function objectWrittenSuccessfullyResponse($object) {
        http_response_code(200);
        echo json_encode($object);
    }

    function objectDeletedSuccessfullyResponse($object) {
        http_response_code(200);
        echo json_encode($object);
    }

    function unprocessableEntityResponse($message) {
        http_response_code(422);
        echo json_encode(array("message" => $message));
    }

    function notAuthrorizedResponse() {
        http_response_code(401);
        echo json_encode(array("message" => "Acesso não autorizado."));
    }


