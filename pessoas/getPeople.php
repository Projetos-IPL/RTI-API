<?php

    include 'personConstants.php';

    function getPeople() {
        global $PEOPLE_FILE_PATH;
        $people_json_string = file_get_contents($PEOPLE_FILE_PATH);
        http_response_code(200);
        echo $people_json_string ? $people_json_string : json_encode("[]");
    }

