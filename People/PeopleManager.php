<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Manager/ManagerUtils.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/exceptions/DataSchemaException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/exceptions/FileReadException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/exceptions/FileWriteException.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/People/exceptions/DuplicateRFIDException.php';


class PeopleManager
{
    public string $PEOPLE_TABLE_NAME = 'person';
    private array $PEOPLE_SCHEMA = array('rfid', 'first_name', 'last_name');
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Função para obter um array de pessoas
     * @return array Array de pessoas
     */
    public function getPeople(): array
    {
        $queryString = "SELECT * FROM " . $this->PEOPLE_TABLE_NAME;
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return $result ?: array();
    }


    /** Função para obter uma pessoa por rfid
     * @param string $rfid
     * @return array | false Array de pessoas ou falso se não forem encontrados dados
     */
    public function getPersonByRFID(string $rfid) : array | false
    {
        $queryString = "SELECT * FROM " . $this->PEOPLE_TABLE_NAME . " WHERE rfid = '" . $rfid . "'";
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        return $stmt->fetch();
    }


    /** Função para adicionar o registo de uma pessoa
     * @throws DuplicateRFIDException
     * @throws DataSchemaException
     * @throws Exception
     */
    public function addPerson(array $person)
    {
        if (!ManagerUtils::validateEntity($this->PEOPLE_SCHEMA, $person)) {
            throw new DataSchemaException();
        }

        // Verificar unicidade do RFID
        if (self::getPersonByRFID($person['rfid'])) {
            throw new DuplicateRFIDException('O rfid: ' . $person['rfid'] . ' já está associado a uma pessoa.');
        }

        $sql = "INSERT INTO " . $this->PEOPLE_TABLE_NAME . " (rfid, first_name, last_name) 
                    VALUES (?, ?, ?)";

        $values = [$person['rfid'], $person['first_name'], $person['last_name']];

        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            $stmt->execute($values);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }


    }

    /** Função para atualizar o rfid de uma pessoa
     * @throws DuplicateRFIDException
     * @throws Exception
     */
    public function updatePersonRFID(string $rfid, string $newRfid)
    {
        // Verificar unicidade do novo rfid
        if (self::getPersonByRFID($newRfid)) {
            throw new DuplicateRFIDException('O rfid: ' . $newRfid . ' já está associado a uma pessoa.');
        }

        // Guardar alterações
        $sql = "UPDATE " . $this->PEOPLE_TABLE_NAME . " SET rfid = (?) WHERE rfid = (?)";
        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            $stmt->execute(array($newRfid, $rfid));
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /** Função para apagar o registo de uma pessoa
     * @throws PersonNotFoundException
     * @throws Exception
     */
    public function deletePerson(string $rfid)
    {
        // Verificar se pessoa existe
        if (!self::getPersonByRFID($rfid)) {
            throw new PersonNotFoundException($rfid);
        }

        // Apagar registo
        $sql = "DELETE FROM " . $this->PEOPLE_TABLE_NAME . " WHERE rfid = (?)";
        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            $stmt->execute(array($rfid));
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}