<?php

    include 'preflightHandler.php';

    function requestConfig($allowed_methods) {
        preflightHandler($allowed_methods); // CORS
        header("Content-Type: application/json; charset=utf-8");
    }

