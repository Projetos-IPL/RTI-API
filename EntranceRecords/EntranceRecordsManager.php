<?php


class EntranceRecordsManager
{
    public string $ENTRANCE_RECORDS_TABLE_NAME = 'entrance_logs';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Função para obter um array de registos
     * @return array Associative Array de registos
     */
    public function getEntranceRecords(): array
    {
        $queryString = "SELECT * FROM " . $this->ENTRANCE_RECORDS_TABLE_NAME;
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return $result ?: array();
    }

    /** Função para obter registos por rfid
     * @param string $rfid
     * @return array | false Array de pessoas ou falso se não forem encontrados dados
     */
    public function getEntranceRecordsByRFID(string $rfid) : array | false
    {
        $queryString = "SELECT * FROM " . $this->ENTRANCE_RECORDS_TABLE_NAME . " WHERE rfid = '" . $rfid . "'";
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    /** Função para criar um registo de entrada a partir de um rfid, o acesso é determinado
     *  e atribuido nesta função.
     * @param string $rfid rfid do novo registo de entrada
     * @throws PersonNotFoundException
     * @throws Exception
     */
    public function createEntranceRecord(string $rfid)
    {
        // Verificar se pessoa existe, uma exceção é levantada quando não encontram uma pessoa.
        $peopleManager = new PeopleManager($this->pdo);
        if (!$peopleManager->getPersonByRFID($rfid)) {
            throw new PersonNotFoundException();
        }

        // Adicionar permissão
        $sql = "INSERT INTO " . $this->ENTRANCE_RECORDS_TABLE_NAME . " (rfid)
                    VALUES (?)";

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
