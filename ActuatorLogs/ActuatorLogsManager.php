<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/ActuatorLogs/ActuatorLogsUtils.php';

    abstract class ActuatorLogsManager {

        public const SENSOR_LOGS_FILE_LOC = ROOTPATH.'/files/';
        public const SENSOR_LOGS_FILE_NAME = 'actuatorLogs.json';
        public const SENSOR_LOGS_FILE_PATH = self::SENSOR_LOGS_FILE_LOC . self::SENSOR_LOGS_FILE_NAME;

        /**
         * @throws FileReadException
         */
        public static function getActuatorLogss(): array
        {
            $file_contents = file_get_contents(self::SENSOR_LOGS_FILE_PATH);
            $permissionsArr = json_decode($file_contents, true);

            if ($permissionsArr === null) {
                throw new FileReadException(self::SENSOR_LOGS_FILE_NAME);
            }

            return $permissionsArr;
        }

        /**
         * @throws FileReadException
         * @throws FileWriteException
         * @throws DataSchemaException
         */
        public static function addActuatorLogs(array $log) {
            $logArr = self::getActuatorLogss();
            $logArr[] = $log;
            self::overwritePermissionsFile($logArr);
        }

        /**
         * @throws DataSchemaException
         * @throws FileWriteException
         */
        private static function overwritePermissionsFile(array $logArr) {
            // Validar integridade dos dados
            foreach($logArr as $log) {
                if (!ActuatorLogsUtils::validateActuatorLogsSchema($log)) {
                    throw new DataSchemaException("Esquema dos registos de atuador corrupto, as alterações não foram efetuadas.");
                }
            }
            // Armazenar
            $encodedArray = json_encode(array_values($logArr));
            if(!file_put_contents(self::SENSOR_LOGS_FILE_PATH, $encodedArray)) {
                throw new FileWriteException(self::SENSOR_LOGS_FILE_PATH);
            }
        }




    }
