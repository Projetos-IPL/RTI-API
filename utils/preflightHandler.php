<?php

    function preflightHandler() {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');
            header('Access-Control-Max-Age: 3600');
            echo json_encode(array("message" => "Success"));
            exit();
        }
    }
