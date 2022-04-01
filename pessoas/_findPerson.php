<?php

    include_once 'exceptions/PersonNotFoundException.php';

    function _findPerson($rfid) {
        // Obter pessoas armazenadas
        $people_json_string = _getPeople();
        $people = json_decode($people_json_string, true);

        foreach ($people as $person) {
            if ($person["rfid"] == $rfid) {
                return $person;
            }
        }

        throw new PersonNotFoundException($rfid);
    }
