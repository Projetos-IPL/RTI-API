<?php

    abstract class ActuatorLogsUtils
    {
    
        public static function validateActuatorLogsSchema(array $log): bool
        {
            if (count($log) != 3) return false;
            if (!isset($log["actuatorype"]) ||
                !isset($log["timestamp"])
            ) {
                return false;
            }
            return true;
        }
    }