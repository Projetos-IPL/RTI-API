    <?php
    
    abstract class ActuatorType {
    
        public const DOOR_ACTUATOR_ID = 1;
        public const DOOR_ACTUATOR_NAME = "Atuador da porta";
    
        public const LED_ID = 2;
        public const LED_NAME = "LED";

        public const BUZZER_ID = 3;
        public const BUZZER_NAME = "Buzzer";
    

        public const ALL_ACTUATORS = [
            [self::DOOR_ACTUATOR_ID, self::DOOR_ACTUATOR_NAME],
            [self::LED_ID, self::LED_NAME],
            [self::BUZZER_ID, self::BUZZER_NAME]
        ];
    
        /** Função para devolver o nome de um atuador através do seu id.
         * @param int $id id de um atuador
         * @return string Nome do atuador ou "Desconhecido" caso nenhum atuador esteja associado a esse id.
         */
        public static function getActuatorName(int $id): string
        {
            foreach (self::ALL_ACTUATORS as $actuator) {
                if ($actuator(0) == $id) {
                    return $actuator(1);
                }
            }
            return "Desconhecido";
        }
    
    }
