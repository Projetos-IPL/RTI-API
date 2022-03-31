<?php

    include '../utils/headerConfig.php';

    $PEOPLE_FILE_PATH = "../files/pessoas.json";
    $people_json_string = file_get_contents($PEOPLE_FILE_PATH);
    $people = json_decode($people_json_string, true);

    setHeaders();

    switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                http_response_code(200);
                echo $people_json_string;
                break;
            case 'POST':
                break;
            case 'PUT':
                break;
            case 'DELETE':
                break;
    }
