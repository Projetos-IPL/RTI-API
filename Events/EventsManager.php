<?php

class EventsManager
{

    public string $EVENT_QUEUE_TABLE = 'event_queue';
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getEvents(string | null $eventName = null) : array
    {
        $queryString = "SELECT * FROM " . $this->EVENT_QUEUE_TABLE;

        if ($eventName) {
            $queryString .= " WHERE event_name = '" . $eventName . "'";
        }

        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    /** Função para adicionar um evento à Event Queue
     * @param string $event_name Nome do evento
     * @return void
     * @throws Exception Se falhar a operação com a base de dados
     */
    public function addEventToQueue(string $event_name) : void
    {
        // Adicionar evento
        $sql = "INSERT INTO " . $this->EVENT_QUEUE_TABLE . " (event_name)
                    VALUES (?)";

        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            $stmt->execute(array($event_name));
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /** Função para remover um evento da Event Queue
     * @param string $event_name Nome do evento
     * @return void
     * @throws Exception Se falhar a operação com a base de dados
     */
    public function removeEventFromQueue(string $event_name) : void
    {

        // Remover evento
        $sql = "DELETE FROM " . $this->EVENT_QUEUE_TABLE . " WHERE event_name = (?)";

        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            $stmt->execute(array($event_name));
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;

        }
    }

}