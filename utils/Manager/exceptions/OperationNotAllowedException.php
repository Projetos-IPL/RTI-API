<?php

    class OperationNotAllowedException extends Exception {
        public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
        {
            parent::__construct("Operação não permitida. " . $message, $code, $previous);
        }
    }
