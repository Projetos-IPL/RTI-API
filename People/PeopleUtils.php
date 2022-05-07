<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/People/PeopleManager.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/People/exceptions/PersonNotFoundException.php';

    abstract class PeopleUtils {

        /**
         * @param array $person
         * @return bool
         */
        public static function validatePersonSchema(array $person): bool
        {
            if (count($person) != 3) return false;
            if (!isset($person["primNome"]) || !isset($person["ultNome"]) || !isset($person["rfid"])) {
                return false;
            }

            return true;
        }

        /**
         * @throws FileReadException
         * @throws PersonNotFoundException
         */
        public static function getPersonIndex(array $peopleArr, string $rfid): int
        {
            $index = -1;
            foreach($peopleArr as $key => $person) {
                if ($person['rfid'] == $rfid) {
                    $index = $key;
                    break;
                }
            }

            if ($index == -1) {
                throw new PersonNotFoundException($rfid);
            } else {
                return $index;
            }
        }

        /**
         * @param $rfid string rfid a ser validado
         * @return bool True se RFID for válido, False se não.
         * @throws FileReadException
         * @throws OperationNotAllowedException
         */
        public static function validateNewRFID(array $peopleArr, string $rfid): bool
        {
            try {
                self::getPersonIndex($peopleArr, $rfid);
                return false;
            } catch (PersonNotFoundException $e) {
                return true;
            }
        }

    }
