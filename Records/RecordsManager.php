<?php

    abstract class RecordsManager {

        public const RECORDS_FILE_LOC = ROOTPATH.'/files/';
        public const RECORDS_FILE_NAME = 'records.json';
        public const RECORDS_FILE_PATH = self::RECORDS_FILE_LOC . self::RECORDS_FILE_NAME;

        /** Função para obter um array de registos
         * @return array Associative Array de registos
         * @throws FileWriteException
         */
        public static function getRecords(): array
        {
            $file_contents = file_get_contents(self::RECORDS_FILE_PATH);
            $recordsArr = json_decode($file_contents, true);

            if ($recordsArr === null) {
                throw new FileWriteException(self::RECORDS_FILE_NAME);
            }

            return $recordsArr;
        }
    }
