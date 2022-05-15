<?php

    class DBConnectionException extends Exception {
        public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
        {
            parent::__construct("Connection failed: " . $message, $code, $previous);
        }
    }
