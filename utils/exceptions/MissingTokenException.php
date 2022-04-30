<?php

    class MissingTokenException extends Exception {
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            $message = "Falta o token.";
            parent::__construct($message, $code, $previous);
        }
    }