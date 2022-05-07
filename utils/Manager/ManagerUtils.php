<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';

    abstract class ManagerUtils {

        public const READ = 'READ';
        public const WRITE = 'WRITE';
        public const UPDATE = 'UPDATE';
        public const DELETE = 'DELETE';

        /** Função para verificar se uma determinada operação é permitida no mapa de permissão de operações
         * @param string $operation
         * @param array $ALLOWED_OPERATIONS
         * @return bool
         */
        public static function verifyOperationPermission(string $operation, array $ALLOWED_OPERATIONS) : bool
        {
            $isOperationAllowed = false;
            foreach($ALLOWED_OPERATIONS as $op) {
                if ($op == $operation) {
                    $isOperationAllowed = true;
                    break;
                }
            }

            return $isOperationAllowed;
        }

        /** Função para validar uma entidade, ao nível do esquema
         * @param array $entity
         * @param array $entitySchema
         * @return bool
         */
        public static function validateEntity(array $entitySchema, array $entity) : bool
        {
            $valid = true;
            foreach ($entitySchema as $prop) {
                if (!isset($entity[$prop])) {
                    $valid = false;
                    break;
                }
            }
            if (count($entitySchema) != count($entity)) {
                $valid = false;
            }
            return $valid;
        }

        /** Função para encontrar o index de uma entidade num array
         * @param array $entityArr
         * @param array $entity
         * @return int index da entidade ou ENTITY_NOT_FOUND_INDEX se não encontrar nenhuma.
         */
        public static function findEntityIndex(array $entityArr, array $entity) : int
        {
            foreach ($entityArr as $key => $et) {
                if ($et == $entity) {
                    return $key;
                }
            }
            return ENTITY_NOT_FOUND_INDEX;
        }
    }
