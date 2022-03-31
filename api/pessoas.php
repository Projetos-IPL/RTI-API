<?php

    include '../utils/requestConfig.php';
    include '../pessoas/getPeople.php';

    requestConfig("GET, POST, PUT, DELETE");

    switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                getPeople();

                break;
            case 'POST':
                break;
            case 'PUT':
                break;
            case 'DELETE':
                break;
    }
