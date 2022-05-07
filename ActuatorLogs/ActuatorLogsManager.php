<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';

class ActuatorLogsManager extends Manager
{

    public function __construct()
    {
        $ACTUATOR_LOGS_FILE_LOC = ROOTPATH . '/files/';
        $ACTUATOR_LOGS_FILE_NAME = 'actuatorLogs.json';
        $ACTUATOR_LOGS_SCHEMA = array('actuatorType', 'timestamp');

        $ALLOWED_OPERATIONS = array(
            ManagerUtils::READ,
            ManagerUtils::WRITE);

        parent::__construct(
            'ACTUATOR_LOG',
            $ACTUATOR_LOGS_FILE_LOC,
            $ACTUATOR_LOGS_FILE_NAME,
            $ACTUATOR_LOGS_SCHEMA,
            $ALLOWED_OPERATIONS);
    }

    /** Função para obter os registos de atuadores armazenados em ficheiro
     * @throws FileReadException
     * @throws OperationNotAllowedException
     */
    public function getActuatorLogs(): array
    {
        return $this->getEntityData();
    }

    /** Função para adicionar um registo de atuador aos logs armazenados em ficheiro
     * @throws FileReadException
     * @throws FileWriteException
     * @throws DataSchemaException
     * @throws OperationNotAllowedException
     */
    public function addActuatorLog(array $log)
    {
        $this->addEntity($log);
    }
}
