<?php

    include_once 'exceptions/DupulicateRFIDException.php';
    include_once '_constants.php';

    function _addPerson($PERSON) {
        // Obter pessoas do ficheiro
        $people_json_string = _getPeople();
        $people = json_decode($people_json_string, true);

        // Confirmar unicidade do rfid, lançar exceção se for repetido
        foreach($people as $p) {
            if ($PERSON["rfid"] == $p["rfid"]) {
                throw new DupulicateRFIDException("Já existe uma pessoa associada a esse rfid.");
            }
        }

        // Adicionar pessoa ao array e gravar no ficheiro
        $people[] = $PERSON;
        if (!file_put_contents(PEOPLE_FILE_PATH, json_encode($people))) {
            throw new FileWriteException();
        }
    }