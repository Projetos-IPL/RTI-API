<?php

class InvalidEventQueueActionException extends Exception
{
    public function __construct(string $action, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Ação à event queue inválida, ação recebida: " . $action, $code, $previous);
    }
}
