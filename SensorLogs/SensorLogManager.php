<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';

class SensorLogManager extends Manager
{

    public function __construct()
    {
        $SENSOR_LOGS_FILE_LOC = ROOTPATH . '/files/';
        $SENSOR_LOGS_FILE_NAME = 'sensorLogs.json';
        $SENSOR_LOGS_SCHEMA = array('sensorType', 'value', 'timestamp');

        $ALLOWED_OPERATIONS = array(
            ManagerUtils::READ,
            ManagerUtils::WRITE,
        );

        parent::__construct(
            'SENSOR_LOG',
            $SENSOR_LOGS_FILE_LOC,
            $SENSOR_LOGS_FILE_NAME,
            $SENSOR_LOGS_SCHEMA,
            $ALLOWED_OPERATIONS);
    }

    /** Função para obter os registos de sensor
     * @throws FileReadException
     * @throws OperationNotAllowedException
     */
    public function getSensorLogs(): array
    {
        return $this->getEntityData();
    }

    /** Função para adicionar o registo de sensor
     * @throws FileReadException
     * @throws FileWriteException
     * @throws DataSchemaException
     * @throws OperationNotAllowedException
     * @throws InvalidSensorTypeException
     */
    public function addSensorLog(array $log)
    {
        if (!SensorLogUtils::validateSensorType($log['sensorType'])) {
            throw new InvalidSensorTypeException($log['sensorType']);
        }
        $this->addEntity($log);
    }
}
