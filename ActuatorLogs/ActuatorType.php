    <?php
    
    abstract class ActuatorType {
    
        public const DOOR_ACTUATOR_ID = 1;
        public const DOOR_ACTUATOR_NAME = "Atuador da porta";
    
        public const LED_ID = 2;
        public const LED_NAME = "LED";

        public const BUZZER_ID = 3;
        public const BUZZER_NAME = "Buzzer";

        public const ALL_ACTUATORS = [
            ["id" => self::DOOR_ACTUATOR_ID, "name" => self::DOOR_ACTUATOR_NAME],
            ["id" => self::LED_ID, "name" => self::LED_NAME],
            ["id" => self::BUZZER_ID, "name" => self::BUZZER_NAME],
        ];
    
        /** Função para devolver o nome de um atuador através do seu id.
         * @param int $id id de um atuador
         * @return string Nome do atuador ou "Desconhecido" caso nenhum atuador esteja associado a esse id.
         */
        public static function getActuatorName(int $id): string
        {
            foreach (self::ALL_ACTUATORS as $actuator) {
                if ($actuator["id"] == $id) {
                    return $actuator["name"];
                }
            }
            return "Desconhecido";
        }
    
    }
