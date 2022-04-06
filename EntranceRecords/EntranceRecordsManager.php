<?php

use JetBrains\PhpStorm\ArrayShape;

include_once $_SERVER['DOCUMENT_ROOT'] . '/EntranceRecords/EntranceRecordsUtils.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/People/PeopleUtils.php';

    abstract class EntranceRecordsManager {

        public const ENTRANCE_RECORDS_FILE_LOC = ROOTPATH.'/files/';
        public const ENTRANCE_RECORDS_FILE_NAME = 'records.json';
        public const ENTRANCE_RECORDS_FILE_PATH = self::ENTRANCE_RECORDS_FILE_LOC . self::ENTRANCE_RECORDS_FILE_NAME;

        /** Função para obter um array de registos
         * @return array Associative Array de registos
         * @throws FileReadException
         */
        public static function getEntranceRecords(): array
        {
            $file_contents = file_get_contents(self::ENTRANCE_RECORDS_FILE_PATH);
            $recordsArr = json_decode($file_contents, true);

            if ($recordsArr === null) {
                throw new FileReadException(self::ENTRANCE_RECORDS_FILE_NAME);
            }

            return $recordsArr;
        }

        /**
         * Função para criar um registo de entrada a partir de um rfid, o acesso é determinado
         * e atribuido nesta função.
         * @return array Registo criado
         * @throws FileReadException
         * @throws FileWriteException
         * @throws PersonNotFoundException
         * @throws DataSchemaException
         */
        public static function createEntranceRecord(string $rfid) : array
        {
            // Verificar se pessoa existe, uma exceção é levantada quando não encontram uma pessoa.
            PeopleUtils::getPersonIndex($rfid);

            // Determinar permissão
            $access = true;
            try {
                PermissionsUtils::getPermissionIndex($rfid);
            } catch (PermissionNotFoundException) {
                $access = false;
            }

            $timestamp = $_SERVER['REQUEST_TIME'];
            $id = EntranceRecordsUtils::generateNewId();

            $newRecord = array(
                "id" => $id,
                "rfid" => $rfid,
                "access" => $access,
                "timestamp" => $timestamp
            );

            // Armazenar novo registo
            $recordsArr = self::getEntranceRecords();
            $recordsArr[] = $newRecord;
            self::overwriteRecordsFile($recordsArr);

            return $newRecord;
        }

        /**
         * Função para atualizar o rfid dos registos, utilizado quando uma pessoa
         * sofre de alteração de rfid.
         * @throws DataSchemaException
         * @throws FileWriteException
         * @throws FileReadException
         */
        public static function updateEntranceRecordsRFID(string $oldRfid, string $newRfid) {
            // Percorrer array dos registos e alterar valores
            $recordArr = self::getEntranceRecords();
            foreach ($recordArr as &$record) {
                if ($record['rfid'] == $oldRfid) {
                    $record['rfid'] = $newRfid;
                }
            }
            self::overwriteRecordsFile($recordArr);
        }

        /**
         * Função para apagar todos os registos assocaidos a um rfid.
         * @throws DataSchemaException
         * @throws FileReadException
         * @throws FileWriteException
         */
        public static function deleteEntranceRecords(string $rfid) {
            // Percorrer array dos registos e apagar registos com o rfid enviados por parâmetros
            $recordArr = self::getEntranceRecords();
            foreach ($recordArr as $key => $record) {
                if ($record['rfid'] == $rfid) {
                    unset($recordArr[$key]);
                }
            }
            self::overwriteRecordsFile($recordArr);
        }

        /**
         * Função para reescrever os dados armazenados no ficheiro dos registos.
         * @throws DataSchemaException
         * @throws FileWriteException
         */
        private static function overwriteRecordsFile(array $recordsArr) {
            // Validar integridade dos dados
            foreach($recordsArr as $record) {
                if (!EntranceRecordsUtils::validateEntranceRecordSchema($record)) {
                    throw new DataSchemaException("Esquema dos registos de entrada corrupto");
                }
            }
            // Armazenar
            $encodedArray = json_encode(array_values($recordsArr));
            if (!file_put_contents(self::ENTRANCE_RECORDS_FILE_PATH, $encodedArray)) {
                throw new FileWriteException(self::ENTRANCE_RECORDS_FILE_NAME);
            }
        }

    }
