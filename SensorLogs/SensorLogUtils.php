<?php

    abstract class SensorLogUtils
    {

        public static function validateSensorLogSchema(array $log): bool
        {
            if (count($log) != 3) return false;
            if (!isset($log["sensorType"]) ||
                !isset($log["value"])  ||
                !isset($log["timestamp"])
            ) {
                return false;
            }
            return true;
        }
    }