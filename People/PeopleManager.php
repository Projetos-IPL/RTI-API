<?php

    include_once '../utils/constants.php';
    include_once '../utils/exceptions/DataSchemaException.php';
    include_once '../utils/exceptions/FileReadException.php';
    include_once '../utils/exceptions/FileWriteException.php';

    include_once 'exceptions/DuplicateRFIDException.php';
    include_once 'PeopleUtils.php';

    abstract class PeopleManager {

        public const PEOPLE_FILE_LOC = ROOTPATH.'/files/';
        public const PEOPLE_FILE_NAME = 'pessoas.json';
        public const PEOPLE_FILE_PATH = self::PEOPLE_FILE_LOC . self::PEOPLE_FILE_NAME;

        /**
         * @return array Associative Array de pessoas
         * @throws FileReadException
         */
        public static function getPeople(): array
        {
            $file_contents = file_get_contents(self::PEOPLE_FILE_PATH);
            $peopleArr = json_decode($file_contents, true);

            if (!$peopleArr) {
                throw new FileReadException(self::PEOPLE_FILE_NAME);
            }

            return $peopleArr;
        }

        /**
         * @throws DuplicateRFIDException
         * @throws DataSchemaException
         * @throws FileReadException
         * @throws FileWriteException
         */
        public static function addPerson(array $person) {

            // Validar esquema da pessoa
            if (!PeopleUtils::validatePersonArray($person)) {
                throw new DataSchemaException("Tentativa de adicionar pessoa com esquema incorreto.");
            }

            // Verificar unicidade do RFID
            if (!PeopleUtils::validateNewRFID($person['rfid'])) {
                throw new DuplicateRFIDException('O rfid: ' . $person['rfid'] . ' já está associado a uma pessoa.');
            }

            // Adicionar pessoa
            $peopleArr = self::getPeople();
            $peopleArr[] = $person;
            self::overwritePeopleFile($peopleArr);
        }

        /**
         * @throws DataSchemaException
         * @throws NameUpdateException
         * @throws DuplicateRFIDException
         * @throws FileReadException
         * @throws PersonNotFoundException
         * @throws FileWriteException
         */
        public static function updatePerson(int $rfid, array $newPersonData) {
            $personIndex = PeopleUtils::getPersonIndex($rfid);

            // Validar esquema da pessoa
            if (!PeopleUtils::validatePersonArray($newPersonData)) {
                throw new DataSchemaException("Tentativa de atualizar pessoa com um esquema incorreto.");
            }

            // Verificar unicidade do novo rfid
            if (!PeopleUtils::validateNewRFID($newPersonData['rfid'])) {
                throw new DuplicateRFIDException('O rfid: ' . $newPersonData['rfid'] . ' já está associado a uma pessoa.');
            }

            // Validar restrições de alteração dos dados de pessoas
            $peopleArr = self::getPeople();
            $oldPersonData = $peopleArr[$personIndex];

            if ($oldPersonData['primNome'] != $newPersonData['primNome'] ||
                $oldPersonData['ultNome'] != $newPersonData['ultNome'])
            {
                throw new NameUpdateException($rfid);
            }

            // Guardar alterações
            $peopleArr[$personIndex] = $newPersonData;
            self::overwritePeopleFile($peopleArr);

        }

        /**
         * @throws PersonNotFoundException
         * @throws DataSchemaException
         * @throws FileWriteException
         * @throws FileReadException
         */
        public static function deletePerson($rfid) {
            $index = PeopleUtils::getPersonIndex($rfid);
            $peopleArr = self::getPeople();
            unset($peopleArr[$index]); // Eliminar pessoa do array peopleArr;
            self::overwritePeopleFile($peopleArr);
        }

        /**
         * @throws FileWriteException
         * @throws DataSchemaException
         */
        private static function overwritePeopleFile(array $peopleArr) {
            // Validar integridade dos dados
            foreach ($peopleArr as $person) {
                if (!PeopleUtils::validatePersonArray($person)) {
                    throw new DataSchemaException("Esquema das pessoas corrupto, não foram efetuadas alterações.");
                }
            }
            // Armazenar
            $encodedArray = json_encode(array_values($peopleArr));
            if (!file_put_contents(self::PEOPLE_FILE_PATH, $encodedArray)) {
                throw new FileWriteException();
            }
        }
    }