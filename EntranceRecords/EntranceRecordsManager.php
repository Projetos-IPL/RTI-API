<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/EntranceRecords/EntranceRecordsUtils.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/People/PeopleUtils.php';

class EntranceRecordsManager extends Manager
{

    public function __construct()
    {
        $ENTRANCE_RECORDS_FILE_LOC = ROOTPATH . '/files/';
        $ENTRANCE_RECORDS_FILE_NAME = 'entranceRecords.json';
        $ENTRANCE_RECORDS_SCHEMA = array('id', 'rfid', 'access', 'timestamp');
        $ALLOWED_OPERATIONS = array(
            ManagerUtils::READ,
            ManagerUtils::WRITE,
            ManagerUtils::UPDATE,
            ManagerUtils::DELETE);

        parent::__construct(
            'ENTRANCE_RECORD',
            $ENTRANCE_RECORDS_FILE_LOC,
            $ENTRANCE_RECORDS_FILE_NAME,
            $ENTRANCE_RECORDS_SCHEMA,
            $ALLOWED_OPERATIONS);
    }

    /** Função para obter um array de registos
     * @return array Associative Array de registos
     * @throws FileReadException
     * @throws OperationNotAllowedException
     */
    public function getEntranceRecords(): array
    {
        return $this->getEntityData();
    }

    /** Função para criar um registo de entrada a partir de um rfid, o acesso é determinado
     *  e atribuido nesta função.
     * @return array Registo criado
     * @throws FileReadException
     * @throws FileWriteException
     * @throws PersonNotFoundException
     * @throws DataSchemaException
     * @throws OperationNotAllowedException
     */
    public function createEntranceRecord(string $rfid): array
    {
        // Verificar se pessoa existe, uma exceção é levantada quando não encontram uma pessoa.
        $peopleManager = new PeopleManager();
        PeopleUtils::getPersonIndex($peopleManager->getPeople(), $rfid);

        // Determinar permissão
        $access = true;
        try {
            $permissionsManager = new PermissionsManager();
            PermissionsUtils::getPermissionIndex($permissionsManager->getPermissions(), $rfid);
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
        $this->addEntity($newRecord);
        return $newRecord;
    }

    /** Função para atualizar o rfid dos registos, utilizado quando uma pessoa
     *  sofre de alteração de rfid.
     * @throws DataSchemaException
     * @throws FileWriteException
     * @throws FileReadException
     * @throws OperationNotAllowedException
     * @throws EntityNotFoundException
     */
    public function updateEntranceRecordsRFID(string $oldRfid, string $newRfid)
    {
        // Percorrer array dos registos e alterar valores
        $recordArr = self::getEntranceRecords();
        foreach ($recordArr as &$record) {
            if ($record['rfid'] == $oldRfid) {
                $newRecord = $record;
                $newRecord['rfid'] = $newRfid;
                $this->updateEntity($record, $newRecord);
            }
        }
    }

    /** Função para apagar todos os registos assocaidos a um rfid.
     * @throws DataSchemaException
     * @throws FileReadException
     * @throws FileWriteException
     * @throws OperationNotAllowedException
     * @throws EntityNotFoundException
     */
    public function deleteEntranceRecords(string $rfid)
    {
        // Percorrer array dos registos e apagar registos com o rfid enviados por parâmetros
        $recordArr = self::getEntranceRecords();
        foreach ($recordArr as $key => $record) {
            if ($record['rfid'] == $rfid) {
                $this->deleteEntity($record);
            }
        }
    }
}
