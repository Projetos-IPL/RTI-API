<?php

class InvalidSensorTypeException extends Exception {
    public function __construct(string $sensorType = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("SensorType: '" . $sensorType . "' does not exist.", $code, $previous);
    }
}