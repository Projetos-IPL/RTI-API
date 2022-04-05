<?php

    class DuplicatePermissionException extends Exception {
        public function __construct(string $rfid = "", int $code = 0, ?Throwable $previous = null)
        {
            parent::__construct('Já existe uma permissão para o rfid ' . $rfid, $code, $previous);
        }
    }