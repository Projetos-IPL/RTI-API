<?php

    function wrongFormatResponse() {
        http_response_code(400);
        echo json_encode(array("message" => "Request body not properly defined."));
        exit();
    }
