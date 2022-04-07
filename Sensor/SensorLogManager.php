<?php

    abstract class SensorLogManager {

        public const SENSOR_LOG_FILE_LOC = ROOTPATH.'/files/';
        public const SENSOR_FILE_NAME = 'sensorLogs.json';
        public const SENSOR_FILE_PATH = self::SENSOR_LOG_FILE_LOC . self::SENSOR_FILE_NAME;

        /**
         * @throws FileReadException
         */
        public static function getSensorLogs(): array
        {
            $file_contents = file_get_contents(self::SENSOR_FILE_PATH);
            $permissionsArr = json_decode($file_contents, true);

            if ($permissionsArr === null) {
                throw new FileReadException(self::SENSOR_FILE_NAME);
            }

            return $permissionsArr;
        }
    }
