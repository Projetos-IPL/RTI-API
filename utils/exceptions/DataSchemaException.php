<?php

    class DataSchemaException extends Exception {
        public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
        {
            parent::__construct("Esquema de entidade incorreto. " . $message, $code, $previous);
        }
    }
