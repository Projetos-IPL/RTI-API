<?php

    class EntranceRecordNotFoundException extends Exception {
        public function __construct(string $rfid = "", int $code = 0, ?Throwable $previous = null)
        {
            parent::__construct('Não há registos de entrada do rfid ' . $rfid, $code, $previous);
        }
    }
