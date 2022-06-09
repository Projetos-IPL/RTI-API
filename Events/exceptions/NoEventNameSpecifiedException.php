<?php

class NoEventNameSpecifiedException extends Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Não foi especificado o nome do evento a apagar.", $code, $previous);
    }
}