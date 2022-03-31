<?php

    function preflightHandler() {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
            header('Access-Control-Allow-Headers: X-Requested-With');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Max-Age: 3600');
            http_response_code(200);
            echo json_encode(array("message" => "Success"));
            exit();
        }
    }
