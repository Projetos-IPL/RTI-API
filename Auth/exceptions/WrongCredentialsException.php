<?php

    class WrongCredentialsException extends Exception {
        public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
        {
            parent::__construct("Palavra-passe incorreta.", $code, $previous);
        }
    }
