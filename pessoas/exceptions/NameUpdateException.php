<?php

    class NameUpdateException extends Exception {
        public function __construct($personRFID = "", $code = 0, Throwable $previous = null) {
            $message = "Tentativa de atualizar nome de pessoa com rfid: " . $personRFID . ". Não é permitido alterar o nome de pessoas.";
            parent::__construct($message, $code, $previous);
        }
    }
