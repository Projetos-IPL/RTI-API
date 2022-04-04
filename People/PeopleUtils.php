<?php

    include_once 'PeopleManager.php';
    include_once 'exceptions/PersonNotFoundException.php';

    abstract class PeopleUtils {

        public static function validatePersonArray(array $person): bool
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
        public static function getPersonIndex(int $rfid): int
        {
            $peopleArr = PeopleManager::getPeople();
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
         * @param int $rfid rfid a ser validado
         * @return bool True se RFID for válido, False se não.
         * @throws FileReadException
         */
        public static function validateNewRFID(string $rfid): bool
        {
            try {
                self::getPersonIndex($rfid);
                return false;
            } catch (PersonNotFoundException $e) {
                return true;
            }
        }

    }
