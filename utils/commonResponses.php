<?php

    function wrongFormatResponse() {
        http_response_code(400);
        echo json_encode(array("message" => "Request body not properly defined."));
        exit();
    }

    function methodNotAvailable($method) {
        http_response_code(400);
        echo json_encode(array("message" => $method . " is not available in this endpoint."));
        exit();
    }

    function internalErrorResponse($message) {
        http_response_code(503);
        echo json_encode(array("message" => $message));
        exit();
    }

    function successfulDataFetchResponse($fetchedData) {
        http_response_code(200);
        echo $fetchedData;
        exit();
    }

    function objectWrittenSuccessfullyResponse($object) {
        http_response_code(200);
        echo json_encode($object);
        exit();
    }

    function objectDeletedSuccessfullyResponse($object) {
        http_response_code(200);
        echo json_encode($object);
        exit();
    }

    function unprocessableEntityResponse($message) {
        http_response_code(422);
        echo json_encode(array("message" => $message));
        exit();
    }


