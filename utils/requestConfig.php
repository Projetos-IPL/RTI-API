<?php

    include 'preflightHandler.php';

    function requestConfig() {
        preflightHandler(); // CORS
        header("Content-Type: application/json; charset=utf-8");
    }

