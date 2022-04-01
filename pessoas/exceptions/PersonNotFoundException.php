<?php

    class PersonNotFoundException extends Exception {
        public function __construct($personRFID = "", $code = 0, Throwable $previous = null) {
            $message = "Não foi encontrada nenhuma pessoa com o rfid: " . $personRFID;
            parent::__construct($message, $code, $previous);
        }
    }