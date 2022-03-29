<?php

    function validateAuthBody($body) {
        $valid = true;

        if (count($body) != 2) $valid = false;
        if (!isset($body["username"]) || !isset($body["password"])) $valid = false;

        return $valid;
    }