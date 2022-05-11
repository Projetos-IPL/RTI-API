<?php


abstract class SensorLogUtils {

    /** Função para obter os tipos de sensor, do ficheiro de configurações
     * @return array
     */
    public static function getSensorTypes() : array
    {
        $sensorTypeFileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/config/sensorTypes.json');
        return json_decode($sensorTypeFileContent, associative: true);
    }

    /** Função para verificar se existe um sensorType com um determinado id
     * @param string $id
     * @return bool
     */
    public static function validateSensorType(string $id) : bool
    {
        $valid = false;
        foreach (self::getSensorTypes() as $sensorType) {
            if ($sensorType['id'] == $id) {
                $valid = true;
                break;
            }
        }
        return $valid;
    }

    /** Função para filtrar registos de sensor pelo tipo de sensor
     * @param array $logs Registos a ser filtrados
     * @param string $sensorTypeId
     * @return array Array de registos filtrada
     * @throws InvalidSensorTypeException
     */
    public static function filterLogsBySensorType(array $logs, string $sensorTypeId) : array
    {
        if (!self::validateSensorType($sensorTypeId)) {
            throw new InvalidSensorTypeException($sensorTypeId);
        }
        return array_filter($logs, function ($value) use ($sensorTypeId) {
            return $value['sensorType'] == $sensorTypeId;
        }, ARRAY_FILTER_USE_BOTH);
    }

    /** Função para obter o registo de sensor mais recente
     * @param array $logs Registos de sensor
     * @return void Registo de sensor mais recente
     */
    public static function getLatestLog(array $logs) : array
    {
        // Ordenar timestamp por ordem descrecente
        usort($logs, function ($item1, $item2) {
            return $item2['timestamp'] <=> $item1['timestamp'];
        });
        // O primeiro resultado é o mais recente
        return array($logs[0]);
    }
}