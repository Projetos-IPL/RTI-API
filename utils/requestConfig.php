<?php

    include 'preflightHandler.php';

    function requestConfig($allowed_methods) {
        preflightHandler(); // CORS
        if (in_array($_SERVER['REQUEST_METHOD'], $allowed_methods));
        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json; charset=utf-8");
    }

