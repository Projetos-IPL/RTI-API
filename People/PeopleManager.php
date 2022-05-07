<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/exceptions/DataSchemaException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/exceptions/FileReadException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/exceptions/FileWriteException.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/Permissions/PermissionsManager.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/EntranceRecords/EntranceRecordsManager.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/People/exceptions/DuplicateRFIDException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/People/PeopleUtils.php';

class PeopleManager extends Manager
{

    public function __construct()
    {
        $PEOPLE_FILE_LOC = ROOTPATH . '/files/';
        $PEOPLE_FILE_NAME = 'pessoas.json';
        $PEOPLE_SCHEMA = array('rfid', 'primNome', 'ultNome');
        $ALLOWED_OPERATIONS = array(
            ManagerUtils::READ,
            ManagerUtils::WRITE,
            ManagerUtils::UPDATE,
            ManagerUtils::DELETE
        );

        parent::__construct(
            'User',
            $PEOPLE_FILE_LOC,
            $PEOPLE_FILE_NAME,
            $PEOPLE_SCHEMA,
            $ALLOWED_OPERATIONS);
    }

    /** Função para obter um array de pessoas
     * @throws FileReadException
     * @throws OperationNotAllowedException
     */
    public function getPeople(): array
    {
        return $this->getEntityData();
    }

    /** Função para adicionar o registo de uma pessoa
     * @throws DuplicateRFIDException
     * @throws DataSchemaException
     * @throws FileReadException
     * @throws FileWriteException
     * @throws OperationNotAllowedException
     */
    public function addPerson(array $person)
    {

        // Verificar unicidade do RFID
        if (!PeopleUtils::validateNewRFID($this->getPeople(), $person['rfid'])) {
            throw new DuplicateRFIDException('O rfid: ' . $person['rfid'] . ' já está associado a uma pessoa.');
        }

        $this->addEntity($person);
    }

    /** Função para atualizar o registo de uma pessoa
     * @throws DataSchemaException
     * @throws NameUpdateException
     * @throws DuplicateRFIDException
     * @throws FileReadException
     * @throws PersonNotFoundException
     * @throws FileWriteException
     * @throws OperationNotAllowedException
     * @throws EntityNotFoundException
     */
    public function updatePerson(string $rfid, array $newPersonData)
    {
        $peopleArr = $this->getPeople();
        $personIndex = PeopleUtils::getPersonIndex($peopleArr, $rfid);

        // Validar esquema da pessoa
        if (!PeopleUtils::validatePersonSchema($newPersonData)) {
            throw new DataSchemaException("Tentativa de atualizar pessoa com um esquema incorreto.");
        }

        // Verificar unicidade do novo rfid
        if (!PeopleUtils::validateNewRFID($this->getPeople(), $newPersonData['rfid'])) {
            throw new DuplicateRFIDException('O rfid: ' . $newPersonData['rfid'] . ' já está associado a uma pessoa.');
        }

        // Validar restrições de alteração dos dados de pessoas
        $oldPersonData = $peopleArr[$personIndex];

        if ($oldPersonData['primNome'] != $newPersonData['primNome'] ||
            $oldPersonData['ultNome'] != $newPersonData['ultNome']) {
            throw new NameUpdateException($rfid);
        }

        // Atualizar permissões associadas à pessoa
        try {
            PermissionsManager::updatePermission($rfid, $newPersonData['rfid']);
        } catch (PermissionNotFoundException) {
        }

        // Atualizar registos de entrada associados à pessoa
        EntranceRecordsManager::updateEntranceRecordsRFID($rfid, $newPersonData['rfid']);

        // Guardar alterações
        $this->updateEntity($oldPersonData, $newPersonData);
    }

    /**
     * @throws PersonNotFoundException
     * @throws DataSchemaException
     * @throws FileWriteException
     * @throws FileReadException
     * @throws OperationNotAllowedException
     * @throws EntityNotFoundException
     */
    public function deletePerson(string $rfid)
    {
        $peopleArr = $this->getEntityData();
        $index = PeopleUtils::getPersonIndex($peopleArr, $rfid);
        $person = $peopleArr[$index];

        // Apagar permissões associadas à pessoa
        try {
            PermissionsManager::deletePermission($rfid);
        } catch (PermissionNotFoundException) {
        }

        // Apagar registos associados à pessoa
        EntranceRecordsManager::deleteEntranceRecords($rfid);

        $this->deleteEntity($person);
    }
}