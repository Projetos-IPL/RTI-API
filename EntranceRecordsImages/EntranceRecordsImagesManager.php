<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/People/PeopleManager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/People/exceptions/PersonNotFoundException.php';


class EntranceRecordsImagesManager
{
    public string $ENTRANCE_RECORDS_IMAGES_TABLE_NAME = 'entrance_logs_images';

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Função para obter um array de registos
     * @return array Associative Array de registos
     */
    public function getEntranceRecordsImages(): array
    {
        $queryString = "SELECT * FROM " . $this->ENTRANCE_RECORDS_IMAGES_TABLE_NAME . " ORDER BY 1 DESC";
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return $result ?: array();
    }


    /** Função para obter registos filtrados por condições.
     * @param array $URL_PARAMS
     * @return array Associative Array de registos
     */
    public function getEntranceRecordsImagesFiltered(array $URL_PARAMS) : array
    {

        $queryString = "SELECT * FROM " . $this->ENTRANCE_RECORDS_IMAGES_TABLE_NAME;

        $conditions = array();

        // Adicionar condição de entrance_log_id
        if (isset($URL_PARAMS['entrance_log_id'])) {
            $conditions[] = "entrance_log_id = " . $URL_PARAMS['entrance_log_id'];
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

        // Ordenar
        $queryString = $queryString . " ORDER BY 1 DESC";

        // Executar query
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (!$result) {
            return array();
        }

        foreach ($result as &$r) {
            $r['image'] = base64_encode($r['image']);
        }

        return $result;
    }


    /** Função para adicionar uma imagem a um registo de entrada
     * @param array $REQ_BODY
     * @throws Exception
     */
    public function addEntranceRecordImage(array $REQ_BODY)
    {
        // Adicionar registo
        $sql = "INSERT INTO " . $this->ENTRANCE_RECORDS_IMAGES_TABLE_NAME . " VALUES (?, ?)";

        $image = chunk_split(base64_encode($REQ_BODY['image']));

        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            $stmt->execute(array($REQ_BODY['entrance_log_id'],$image));
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
