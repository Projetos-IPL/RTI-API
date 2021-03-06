<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/Manager/ManagerUtils.php';

class SensorLogManager
{

    public string $SENSOR_LOGS_TABLE_NAME = 'sensor_logs';
    public string $SENSOR_LOGS_VIEW_NAME = 'sensor_log_sensor_view';
    public array $SENSOR_LOGS_SCHEMA = array('sensorType', 'value');
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Função para obter os registos de sensor
     * @return array Associative Array de registos
     */
    public function getSensorLogs(): array
    {
        $queryString = "SELECT * FROM " . $this->SENSOR_LOGS_TABLE_NAME . " ORDER BY 1 DESC";
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return $result ?: array();
    }


    /** Função para obter registos filtrados por condições.
     * @param array $URL_PARAMS
     * @return array Associative Array de registos
     */
    public function getSensorLogsFiltered(array $URL_PARAMS) : array
    {
        // Se o parametro showSensorName for 1, a query deve ser feita à view
        if (isset($URL_PARAMS['showSensorName']) && $URL_PARAMS['showSensorName'] == 1) {
            $table = $this->SENSOR_LOGS_VIEW_NAME;
        } else {
            $table = $this->SENSOR_LOGS_TABLE_NAME;
        }

        $queryString = "SELECT * FROM " . $table;


        // Adicionar condição de sensorType
        if (isset($URL_PARAMS['sensorType'])) {
            $queryString = $queryString .  " WHERE sensor_id = " . $URL_PARAMS['sensorType'];
        }

        // Ordenar
        $queryString = $queryString . " ORDER BY 1 DESC";

        // Filtrar por latest
        if (isset($URL_PARAMS['latest']) && $URL_PARAMS['latest'] > 0) {
            $queryString = $queryString . " LIMIT " . $URL_PARAMS['latest'];
        }

        // Executar query
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        return $stmt->fetchAll() ?: array();
    }

    /** Função para adicionar o registo de sensor
     * @throws InvalidSensorTypeException
     * @throws DataSchemaException
     * @throws Exception
     */
    public function addSensorLog(array $log)
    {
        // Validar esquema
        if (!ManagerUtils::validateEntity($this->SENSOR_LOGS_SCHEMA, $log)) {
            throw new DataSchemaException();
        }

        // Validar tipo de sensor (sensor_id)
        if (!SensorLogUtils::validateSensorType($this->pdo, $log['sensorType'])) {
            throw new InvalidSensorTypeException($log['sensorType']);
        }

        // Adicionar registo de sensor
        $sql = "INSERT INTO " . $this->SENSOR_LOGS_TABLE_NAME . " (sensor_id, value)
                    VALUES (?, ?)";

        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            $stmt->execute(array($log['sensorType'], $log['value']));
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
