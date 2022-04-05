<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/People/PeopleUtils.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/Permissions/exceptions/PermissionNotFoundException.php';

    abstract class PermissionsUtils {

        /**
         * @param array $permission
         * @return bool
         */
        public static function validatePermissionSchema(array $permission): bool
        {
            if (count($permission) != 2) return false;
            if (!isset($permission["id"]) || !isset($permission["rfid"])) {
                return false;
            }

            return true;
        }

        /**
         * @throws FileReadException
         */
        public static function generateNewId(): int
        {
            // Criar array com todos os ids das permissões
            $idArr = array(0); // Incializado com um elemento 0 para quando não existirem permissões
            foreach(PermissionsManager::getPermissions() as $permission) {
                $idArr[] = $permission['id'];
            }

            // Devolver um valor acima do maior id encontrado
            return max($idArr) + 1;
        }

        /**
         * @throws FileReadException
         * @throws PermissionNotFoundException
         */
        public static function getPermissionByRFID(string $rfid): array
        {
            foreach (PermissionsManager::getPermissions() as $permission) {
                if ($permission['rfid'] == $rfid) {
                    return $permission;
                }
            }

            throw new PermissionNotFoundException($rfid);
        }

        /**
         * @param string $rfid rfid da nova permissão a ser validada
         * @return bool
         * @throws FileReadException
         * @throws PersonNotFoundException
         */
        public static function validateNewPermission(string $rfid): bool
        {

            try {
                PeopleUtils::getPersonIndex($rfid);
                self::getPermissionByRFID($rfid);
                return false;
            } catch (PermissionNotFoundException) {
                return true;
            }
        }

        /**
         * @throws PermissionNotFoundException
         * @throws FileReadException
         */
        public static function getPermissionIndex(string $rfid): int
        {
            foreach(PermissionsManager::getPermissions() as $key => $permission) {
                if ($permission['rfid'] == $rfid) {
                    return $key;
                }
            }
            throw new PermissionNotFoundException($rfid);
        }
    }
