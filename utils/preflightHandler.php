<?php

    function preflightHandler($allowed_methods) {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            header('Access-Control-Allow-Methods: ' . $allowed_methods);
            header('Access-Control-Allow-Headers: *');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Max-Age: 3600');
            echo json_encode(array("message" => "Success"));
            exit();
        }
    }
