<?php

    include 'preflightHandler.php';

    function requestConfig() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        header("Access-Control-Allow-Headers: X-Requested-With");
        preflightHandler(); // CORS
        header("Content-Type: application/json; charset=utf-8");
    }

