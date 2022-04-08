<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/Permissions/PermissionsUtils.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/Permissions/exceptions/DuplicatePermissionException.php';

    abstract class PermissionsManager {

        public const PERMISSIONS_FILE_LOC = ROOTPATH.'/files/';
        public const PERMISSIONS_FILE_NAME = 'permissoes.json';
        public const PERMISSIONS_FILE_PATH = self::PERMISSIONS_FILE_LOC . self::PERMISSIONS_FILE_NAME;

        /**
         * @return array Associative Array de permissões
         * @throws FileReadException
         */
        public static function getPermissions(): array
        {
            $file_contents = file_get_contents(self::PERMISSIONS_FILE_PATH);
            $permissionsArr = json_decode($file_contents, true);

            if ($permissionsArr === null) {
                throw new FileReadException(self::PERMISSIONS_FILE_NAME);
            }

            return $permissionsArr;
        }

        /**
         * @param string $rfid rfid da nova permissão
         * @return int Id da permissão criada
         * @throws DuplicatePermissionException
         * @throws DataSchemaException
         * @throws FileReadException
         * @throws FileWriteException
         * @throws PersonNotFoundException
         */
        public static function addPermission(string $rfid): int
        {

            // Validar nova permissão
            if (!PermissionsUtils::validateNewPermission($rfid)) {
                throw new DuplicatePermissionException($rfid);
            }

            // Obter id para a permissão
            $id = PermissionsUtils::generateNewId();

            // Adicionar permissão
            $permissionsArr = self::getPermissions();
            $permissionsArr[] = array("id"=>$id, "rfid"=>$rfid);
            self::overwritePermissionsFile($permissionsArr);

            return $id;
        }

        /**
         * @throws DataSchemaException
         * @throws FileWriteException
         * @throws FileReadException
         * @throws PermissionNotFoundException
         */
        public static function deletePermission(string $rfid) {
            $index = PermissionsUtils::getPermissionIndex($rfid);
            $permissionsArr = self::getPermissions();
            unset($permissionsArr[$index]);
            self::overwritePermissionsFile($permissionsArr);
        }

        /**
         * @throws DataSchemaException
         * @throws FileReadException
         * @throws PermissionNotFoundException
         * @throws FileWriteException
         */
        public static function updatePermission(string $rfid, string $newRfid) {
            $index = PermissionsUtils::getPermissionIndex($rfid);
            $permissionArr = self::getPermissions();
            $permissionArr[$index]['rfid'] = $newRfid;
            self::overwritePermissionsFile($permissionArr);
        }

        /**
         * @throws DataSchemaException
         * @throws FileWriteException
         */
        private static function overwritePermissionsFile(array $permissionArr) {
            // Validar integridade dos dados
            foreach ($permissionArr as $permission) {
                if (!PermissionsUtils::validatePermissionSchema($permission)) {
                    throw new DataSchemaException("Esquema das permissões corrupto, as alterações não foram efetuadas.");
                }
            }
            // Armazenar
            $encodedArray = json_encode(array_values($permissionArr));
            if (!file_put_contents(self::PERMISSIONS_FILE_PATH, $encodedArray)) {
                throw new FileWriteException(self::PERMISSIONS_FILE_NAME);
            }
        }



    }