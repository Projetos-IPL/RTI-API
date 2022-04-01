<?php

    function validateAuthBody($body) {
        $valid = true;

        if (count($body) != 2) $valid = false;

        if (!isset($body["username"]) || !isset($body["password"])) {
            $valid = false;
        }

        return $valid;
    }

    function validatePersonSchema($body) {
        $valid = true;

        if (count($body) != 3) $valid = false;

        if (!isset($body["primNome"]) || !isset($body["ultNome"]) || !isset($body["rfid"])) {
            $valid = false;
        }

        return $valid;
    }