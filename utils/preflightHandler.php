<?php

    function preflightHandler() {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            echo json_encode(array("message" => "Success"));
            exit();
        }
    }
