<?php

    class InvalidTokenException extends Exception {
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            $message = "Token inválido.";
            parent::__construct($message, $code, $previous);
        }
    }