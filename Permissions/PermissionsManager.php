<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Permissions/PermissionsUtils.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Permissions/exceptions/DuplicatePermissionException.php';

class PermissionsManager extends Manager
{

    public function __construct()
    {
        $PERMISSIONS_FILE_LOC = ROOTPATH . '/files/';
        $PERMISSIONS_FILE_NAME = 'permissoes.json';
        $PERMISSIONS_SCHEMA = array('id', 'rfid');

        $ALLOWED_OPERATIONS = array(
            ManagerUtils::READ,
            ManagerUtils::WRITE,
            ManagerUtils::DELETE
        );

        parent::__construct(
            'User',
            $PERMISSIONS_FILE_LOC,
            $PERMISSIONS_FILE_NAME,
            $PERMISSIONS_SCHEMA,
            $ALLOWED_OPERATIONS);
    }

    /** Função para obter um array de permissões
     * @return array Associative Array de permissões
     * @throws FileReadException
     * @throws OperationNotAllowedException
     */
    public function getPermissions(): array
    {
        return $this->getEntityData();
    }

    /** Função para adicionar uma nova permissão
     * @param string $rfid rfid da nova permissão
     * @return int Id da permissão criada
     * @throws DuplicatePermissionException
     * @throws DataSchemaException
     * @throws FileReadException
     * @throws FileWriteException
     * @throws PersonNotFoundException
     * @throws OperationNotAllowedException
     */
    public function addPermission(string $rfid): int
    {

        $permissionsArr = self::getPermissions();

        // Validar nova permissão
        if (!PermissionsUtils::validateNewPermission($permissionsArr, $rfid)) {
            throw new DuplicatePermissionException($rfid);
        }

        // Obter id para a permissão
        $id = PermissionsUtils::generateNewId($permissionsArr);

        $newPermission = array("id" => $id, "rfid" => $rfid);

        // Adicionar permissão
        $this->addEntity($newPermission);
        return $id;
    }

    /** Função para apagar uma permissão
     * @throws DataSchemaException
     * @throws FileWriteException
     * @throws FileReadException
     * @throws PermissionNotFoundException
     * @throws EntityNotFoundException
     * @throws OperationNotAllowedException
     */
    public function deletePermission(string $rfid)
    {
        $permissionsArr = self::getPermissions();
        $index = PermissionsUtils::getPermissionIndex($permissionsArr, $rfid);
        $this->deleteEntity($permissionsArr[$index]);
    }

    /** Função para atualizar uma permissão
     * @throws DataSchemaException
     * @throws FileReadException
     * @throws PermissionNotFoundException
     * @throws FileWriteException
     * @throws OperationNotAllowedException
     * @throws EntityNotFoundException
     */
    public function updatePermission(string $rfid, string $newRfid)
    {
        $permissionsArr = self::getPermissions();
        $index = PermissionsUtils::getPermissionIndex($permissionsArr, $rfid);
        $oldPermission = $permissionsArr[$index];
        $newPermission = array(
            "id"   =>$oldPermission['id'],
            "rfid" =>$rfid
        );
        $this->updateEntity($oldPermission, $newPermission);
    }


}