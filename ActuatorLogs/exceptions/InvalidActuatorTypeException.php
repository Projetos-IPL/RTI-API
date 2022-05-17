<?php

class InvalidActuatorTypeException extends Exception {
    public function __construct(string $actuatorType = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("ActuatorType: '" . $actuatorType . "' does not exist.", $code, $previous);
    }
}
