<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/ActuatorLogs/ActuatorLogsUtils.php';

    abstract class ActuatorLogsManager {

        public const ACTUATOR_LOGS_FILE_LOC = ROOTPATH.'/files/';
        public const ACTUATOR_LOGS_FILE_NAME = 'actuatorLogs.json';
        public const ACTUATOR_LOGS_FILE_PATH = self::ACTUATOR_LOGS_FILE_LOC . self::ACTUATOR_LOGS_FILE_NAME;

        /** Função para obter os registos de atuadores armazenados em ficheiro
         * @throws FileReadException
         */
        public static function getActuatorLogs(): array
        {
            $file_contents = file_get_contents(self::ACTUATOR_LOGS_FILE_PATH);
            $permissionsArr = json_decode($file_contents, true);

            if ($permissionsArr === null) {
                throw new FileReadException(self::ACTUATOR_LOGS_FILE_NAME);
            }

            return $permissionsArr;
        }

        /** Função para adicionar um registo de atuador aos logs armazenados em ficheiro
         * @throws FileReadException
         * @throws FileWriteException
         * @throws DataSchemaException
         */
        public static function addActuatorLogs(array $log) {
            if (!ActuatorLogsUtils::validateActuatorLogsSchema($log)) {
                throw new DataSchemaException();
            }

            $logArr = self::getActuatorLogs();
            $logArr[] = $log;

            self::overwriteActuatorLogsFile($logArr);
        }

        /** Função para sobreescrever o ficheiro dos registos de atuador
         * @throws DataSchemaException
         * @throws FileWriteException
         */
        private static function overwriteActuatorLogsFile(array $logArr) {
            // Validar integridade dos dados
            foreach($logArr as $log) {
                if (!ActuatorLogsUtils::validateActuatorLogsSchema($log)) {
                    throw new DataSchemaException("Esquema dos registos de atuador corrupto, as alterações não foram efetuadas.");
                }
            }
            // Armazenar
            $encodedArray = json_encode(array_values($logArr));
            if(!file_put_contents(self::ACTUATOR_LOGS_FILE_PATH, $encodedArray)) {
                throw new FileWriteException(self::ACTUATOR_LOGS_FILE_PATH);
            }
        }




    }
