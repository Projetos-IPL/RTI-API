<?php

    class PermissionNotFoundException extends Exception {
        public function __construct(string $rfid = "", int $code = 0, ?Throwable $previous = null)
        {
            parent::__construct('Não há permissão cedida para o rfid ' . $rfid, $code, $previous);
        }
    }
