<?php


abstract class ActuatorLogUtils {

    public static string $ACTUATOR_TABLES = "actuator";

    /** Função para obter os tipos de atuador
     * @param PDO $pdo
     * @return array
     */
    public static function getActuatorTypes(PDO $pdo) : array
    {
        $queryString = "SELECT actuator_id FROM " . self::$ACTUATOR_TABLES;
        $stmt = $pdo->query($queryString, PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return $result ?: array();
    }

    /** Função para verificar se um actuatorType é válido
     * @param PDO $pdo
     * @param string $id
     * @return bool
     */
    public static function validateActuatorType(PDO $pdo, string $id) : bool
    {
        $valid = false;
        foreach (self::getActuatorTypes($pdo) as $actuatorType) {
            if ($actuatorType == $id) {
                $valid = true;
                break;
            }
        }
        return $valid;
    }

}