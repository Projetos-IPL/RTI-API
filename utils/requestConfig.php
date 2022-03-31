<?php

    include 'preflightHandler.php';

    function requestConfig() {
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-Requested-With');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 3600');
        preflightHandler(); // CORS
        header("Content-Type: application/json; charset=utf-8");
    }

