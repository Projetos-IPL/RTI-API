<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/People/PeopleManager.php';

class EntranceRecordsManager
{
    public string $ENTRANCE_RECORDS_TABLE_NAME = 'entrance_logs';
    public string $ENTRANCE_RECORDS_PERSON_VIEW = 'entrance_logs_person_view';

    private PDO $pdo;

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

    /** Função para obter um array de registos da view entrance_logs_person_view
     * @return array Associative Array de registos
     */
    public function getEntranceLogPersonViewRecords(): array
    {
        $queryString = "SELECT * FROM " . $this->ENTRANCE_RECORDS_PERSON_VIEW;
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return $result ?: array();
    }

    /** Função para obter registos filtrados por condições.
     * @param array $URL_PARAMS
     * @return array Associative Array de registos
     */
    public function getEntranceRecordsFiltered(array $URL_PARAMS) : array
    {

        // Se o parametro showPersonName for 1, a query deve ser feita à view
        if (isset($URL_PARAMS['showPersonName']) && $URL_PARAMS['showPersonName'] == 1) {
            $table = $this->ENTRANCE_RECORDS_PERSON_VIEW;
        } else {
            $table = $this->ENTRANCE_RECORDS_TABLE_NAME;
        }

        $queryString = "SELECT * FROM " . $table;
        $conditions = array();


        // Adicionar condição de rfid
        if (isset($URL_PARAMS['rfid'])) {
            $conditions[] = "rfid = " . $URL_PARAMS['rfid'];
        }

        // Adciionar condição de access
        if (isset($URL_PARAMS['access'])) {
            $conditions[] = "access = " . $URL_PARAMS['access'];
        }

        // Adicionar condição de data
        if (isset($URL_PARAMS['date'])) {
            $conditions[] = "FROM_UNIXTIME(timestamp, '%d-%m-%Y')  = '" . $URL_PARAMS['date'] . "'";
        }

        $i = 0;
        $conditionsCount = count($conditions);

        if ($conditionsCount != 0) {
            $queryString = $queryString . " WHERE ";
        }

        foreach ($conditions as $condition) {
            $queryString = $queryString . $condition;
            $i++;
            if ($i != $conditionsCount) {
                $queryString = $queryString . " AND ";
            }
        }

        // Filtrar por latest
        if (isset($URL_PARAMS['latest']) && $URL_PARAMS['latest'] > 0) {
            $queryString = $queryString . " ORDER BY timestamp DESC LIMIT " . $URL_PARAMS['latest'];
        }

        // Executar query
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        return $stmt->fetchAll() ?: array();
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
