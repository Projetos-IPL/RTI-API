<?php

    include 'preflightHandler.php';

    function requestConfig($allowed_methods) {
        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json; charset=utf-8");
        preflightHandler($allowed_methods); // CORS
    }

