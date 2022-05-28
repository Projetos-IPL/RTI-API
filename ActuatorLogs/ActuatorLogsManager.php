<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Manager/ManagerUtils.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/ActuatorLogs/exceptions/InvalidActuatorTypeException.php';

class ActuatorLogsManager
{

    public string $ACTUATOR_LOGS_TABLE_NAME = 'actuator_logs';
    public string $ACTUATOR_LOGS_VIEW_NAME = 'actuator_logs_actuator_view';

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Função para obter os registos de atuadores armazenados em ficheiro
     * @return array Associative Array de registos
     */
    public function getActuatorLogs(): array
    {
        $queryString = "SELECT * FROM " . $this->ACTUATOR_LOGS_TABLE_NAME;
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return $result ?: array();
    }

    /** Função para obter registos filtrados por condições.
     * @param array $URL_PARAMS
     * @return array Associative Array de registos
     */
    public function getActuatorLogsFiltered(array $URL_PARAMS) : array
    {

        if (isset($URL_PARAMS['showActuatorName']) && $URL_PARAMS['showActuatorName'] == 1) {
            $table = $this->ACTUATOR_LOGS_VIEW_NAME;
        } else {
            $table = $this->ACTUATOR_LOGS_TABLE_NAME;
        }

        $queryString = "SELECT * FROM " . $table;

        // Adicionar condição de sensorType
        if (isset($URL_PARAMS['actuatorTyoe'])) {
            $queryString = $queryString .  " WHERE actuator_id = " . $URL_PARAMS['actuatorTyoe'];
        }

        $queryString = $queryString . " ORDER BY 1 DESC";

        // Condição latest
        if (isset($URL_PARAMS['latest']) && $URL_PARAMS['latest'] > 0) {
            $queryString = $queryString . " LIMIT " . $URL_PARAMS['latest'];
        }

        // Executar query
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        return $stmt->fetchAll() ?: array();
    }

    /** Função para adicionar um registo de atuador aos logs armazenados em ficheiro
     * @throws DataSchemaException
     * @throws Exception
     */
    public function addActuatorLog(string $actuatorType)
    {
        // Validar tipo de sensor (sensor_id)
        if (!ActuatorLogUtils::validateActuatorType($this->pdo, $actuatorType)) {
            throw new InvalidActuatorTypeException($actuatorType);
        }

        // Adicionar registo de sensor
        $sql = "INSERT INTO " . $this->ACTUATOR_LOGS_TABLE_NAME . " (actuator_id)
                    VALUES (?)";

        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            $stmt->execute(array($actuatorType));
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }    }
}
