<?php

    include_once '../utils/exceptions/FileReadException.php';
    include_once '_constants.php';

    function _getPeople() {
        $people = file_get_contents(PEOPLE_FILE_PATH);
        if ($people) {
            return $people;
        } else {
            throw new FileReadException(PEOPLE_FILE_NAME);
        }
    }

