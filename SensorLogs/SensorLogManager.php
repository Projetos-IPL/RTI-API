<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/SensorLogs/SensorLogUtils.php';

    abstract class SensorLogManager {

        public const SENSOR_LOGS_FILE_LOC = ROOTPATH.'/files/';
        public const SENSOR_LOGS_FILE_NAME = 'sensorLogs.json';
        public const SENSOR_LOGS_FILE_PATH = self::SENSOR_LOGS_FILE_LOC . self::SENSOR_LOGS_FILE_NAME;

        /** Função para obter os registos de sensor
         * @throws FileReadException
         */
        public static function getSensorLogs(): array
        {
            $file_contents = file_get_contents(self::SENSOR_LOGS_FILE_PATH);
            $permissionsArr = json_decode($file_contents, true);

            if ($permissionsArr === null) {
                throw new FileReadException(self::SENSOR_LOGS_FILE_NAME);
            }

            return $permissionsArr;
        }

        /** Função para adicionar o registo de sensor
         * @throws FileReadException
         * @throws FileWriteException
         * @throws DataSchemaException
         */
        public static function addSensorLog(array $log) {
            if (!SensorLogUtils::validateSensorLogSchema($log)) {
                throw new DataSchemaException();
            }

            $logArr = self::getSensorLogs();
            $logArr[] = $log;
            self::overwriteSensorLogFile($logArr);
        }

        /** Função para sobreescrever o ficheiro de registos de sensor
         * @throws DataSchemaException
         * @throws FileWriteException
         */
        private static function overwriteSensorLogFile(array $logArr) {
            // Validar integridade dos dados
            foreach($logArr as $log) {
                if (!SensorLogUtils::validateSensorLogSchema($log)) {
                    throw new DataSchemaException("Esquema dos registos de sensor corrupto, as alterações não foram efetuadas.");
                }
            }
            // Armazenar
            $encodedArray = json_encode(array_values($logArr));
            if(!file_put_contents(self::SENSOR_LOGS_FILE_PATH, $encodedArray)) {
                throw new FileWriteException(self::SENSOR_LOGS_FILE_PATH);
            }
        }




    }
