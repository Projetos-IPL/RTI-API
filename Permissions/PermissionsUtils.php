<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/People/PeopleUtils.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/Permissions/exceptions/PermissionNotFoundException.php';

    abstract class PermissionsUtils {

        /** Função para gerar um novo id para uma permissão.
         * @throws FileReadException
         */
        public static function generateNewId(array $permissionsArr): int
        {
            // Criar array com todos os ids das permissões
            $idArr = array(0); // Incializado com um elemento 0 para quando não existirem permissões
            foreach($permissionsArr as $permission) {
                $idArr[] = $permission['id'];
            }

            // Devolver um valor acima do maior id encontrado
            return max($idArr) + 1;
        }

        /** Função para obter uma permissão através do RFID
         * @throws FileReadException
         * @throws PermissionNotFoundException
         */
        public static function getPermissionByRFID(array $permissionsArr, string $rfid): array
        {
            foreach ($permissionsArr as $permission) {
                if ($permission['rfid'] == $rfid) {
                    return $permission;
                }
            }

            throw new PermissionNotFoundException($rfid);
        }

        /** Função para validar uma nova permissão
         * @param string $rfid rfid da nova permissão a ser validada
         * @return bool
         * @throws FileReadException
         * @throws PersonNotFoundException
         * @throws OperationNotAllowedException
         */
        public static function validateNewPermission(array $permissionsArr, string $rfid): bool
        {
            try {
                // Se encontrar uma permissão com este rfid ela é inválida, logo é devolvido falso.
                // Quando não é encontrada uma permissão é lançada a exceção PermissionNotFoundException.
                $peopleManager = new PeopleManager();
                $peopleArr = $peopleManager->getPeople();
                PeopleUtils::getPersonIndex($peopleArr, $rfid);
                self::getPermissionByRFID($permissionsArr, $rfid);
                return false;
            } catch (PermissionNotFoundException) {
                return true;
            }
        }

        /**
         * @throws PermissionNotFoundException
         * @throws FileReadException
         */
        public static function getPermissionIndex(array $permissionsArr, string $rfid): int
        {
            foreach($permissionsArr as $key => $permission) {
                if ($permission['rfid'] == $rfid) {
                    return $key;
                }
            }
            throw new PermissionNotFoundException($rfid);
        }
    }
