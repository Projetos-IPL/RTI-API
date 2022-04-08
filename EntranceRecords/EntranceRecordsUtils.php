<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/EntranceRecords/EntranceRecordsManager.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/EntranceRecords/exceptions/EntranceRecordNotFoundException.php';

    abstract class EntranceRecordsUtils {

        /**
         * Função para gerar um id único nos registos de entrada
         * @return int id único
         * @throws FileReadException
         */
        public static function generateNewId(): int
        {
            // Criar array com todos os ids das permissões
            $idArr = array(0);
            foreach (EntranceRecordsManager::getEntranceRecords() as $record) {
                $idArr[] = $record['id'];
            }

            // Devolver um valor acima do maior id encontrado
            return max($idArr) + 1;
        }

        /**
         * Função para validar o esquema de um registo de entrada
         * @param array $record registo a ser validado
         * @return bool True se válido, falso se não.
         */
        public static function validateEntranceRecordSchema(array $record): bool
        {
            if (count($record) != 4) return false;
            if (
                !isset($record["id"]) ||
                !isset($record["timestamp"]) ||
                !isset($record["access"]) ||
                !isset($record["rfid"])
            ) {
                return false;
            }

            return true;
        }

        /**
         * Função para obter um array com registos de entrada associados a um determinado rfid
         * @param string $rfid rfid a ser procurado
         * @throws FileReadException
         * @throws EntranceRecordNotFoundException Lançada quando não são encontrados registos
         */
        public static function getEntranceRecordsByRFID(string $rfid): array
        {
            $recordsFound = array();
            foreach(EntranceRecordsManager::getEntranceRecords() as $record) {
                if ($record['rfid'] === $rfid) {
                    $recordsFound[] = $record;
                }
            }

            if (count($recordsFound) != 0) {
                return $recordsFound;
            } else {
                throw new EntranceRecordNotFoundException($rfid);
            }

        }
    }
