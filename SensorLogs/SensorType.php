<?php

    abstract class SensorType {

        public const RFID_READER_ID = 1;
        public const RFID_READER_NAME = "RFID Reader";

        public const TEMPERATURE_READER_ID = 2;
        public const TEMPERATURE_READER_NAME = "Leitor de Temperatura";

        public const SMOKE_DETECTOR_ID = 3;
        public const SMOKE_DETECTOR_NAME = "Detetor de fumo";

        public const CARBON_MONOXIDE_DETECTOR_ID = 4;
        public const CARBON_MONOXIDE_DETECTOR_NAME = "Detetor de monóxido de carbono";

        public const ALL_SENSORS = [
            ["id" => self::RFID_READER_ID, "name" => self::RFID_READER_NAME],
            ["id" => self::TEMPERATURE_READER_ID, "name" => self::TEMPERATURE_READER_NAME],
            ["id" => self::SMOKE_DETECTOR_ID, "name" => self::SMOKE_DETECTOR_NAME],
            ["id" => self::CARBON_MONOXIDE_DETECTOR_ID, "name" => self::CARBON_MONOXIDE_DETECTOR_NAME]
        ];

        /** Função para devolver o nome de um sensor através do seu id.
         * @param int $id id de um sensor
         * @return string Nome do sensor ou "Desconhecido" caso nenhum sensor esteja associado a esse id.
         */
        public static function getSensorName(int $id): string
        {
            foreach (self::ALL_SENSORS as $sensor) {
                if ($sensor["id"] == $id) {
                    return $sensor["name"];
                }
            }
            return "Desconhecido";
        }

    }