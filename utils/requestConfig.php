<?php

    include 'preflightHandler.php';

    function requestConfig($allowed_methods) {
        header('Access-Control-Allow-Origin: *');
        preflightHandler(); // CORS
        if (in_array($_SERVER['REQUEST_METHOD'], $allowed_methods));
        header("Content-Type: application/json; charset=utf-8");
    }

