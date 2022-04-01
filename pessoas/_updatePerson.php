<?php

    include_once 'exceptions/NameUpdateException.php';
    include_once '_findPerson.php';

    function _updatePerson($rfid, $data) {
        $found = false;
        $people = json_decode(_getPeople(), true);

        foreach ($people as &$person) {
            if ($person['rfid'] == $rfid) {
                $found = true;
                if ($person['primNome'] != $data['primNome'] || $person['ultNome'] != $data['ultNome']) {
                    throw new NameUpdateException($data['rfid']);
                } else {
                 $person['rfid'] = $data['rfid'];
                }
            }
        }

        if (!$found) {
            throw new PersonNotFoundException($rfid);
        }

        // Guardar alterações em ficheiro
        if (!file_put_contents(PEOPLE_FILE_PATH, json_encode($people))) {
            throw new FileWriteException();
        }
    }
