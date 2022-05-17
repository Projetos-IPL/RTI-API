<?php


abstract class SensorLogUtils {

    public static string $SENSORS_TABLE = "sensor";

    /** Função para obter os tipos de sensor
     * @param PDO $pdo
     * @return array
     */
    public static function getSensorTypes(PDO $pdo) : array
    {
        $queryString = "SELECT sensor_id FROM " . self::$SENSORS_TABLE;
        $stmt = $pdo->query($queryString, PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return $result ?: array();
    }

    /** Função para verificar se um sensorType é válido
     * @param PDO $pdo
     * @param string $id
     * @return bool
     */
    public static function validateSensorType(PDO $pdo, string $id) : bool
    {
        $valid = false;
        foreach (self::getSensorTypes($pdo) as $sensorType) {
            if ($sensorType['sensor_id'] == $id) {
                $valid = true;
                break;
            }
        }
        return $valid;
    }

}