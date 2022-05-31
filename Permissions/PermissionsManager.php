<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Permissions/exceptions/DuplicatePermissionException.php';

class PermissionsManager
{

    public string $PERMISSION_TABLE_NAME = 'permission';
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Função para obter um array de permissões
     * @return array Associative Array de permissões

     */
    public function getPermissions(): array
    {
        $queryString = "SELECT * FROM " . $this->PERMISSION_TABLE_NAME;
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    /** Função para obter uma permissão por rfid
     * @param string $rfid
     * @return array | false Array de pessoas ou falso se não forem encontrados dados
     */
    public function getPermissionByRFID(string $rfid) : array | false
    {
        $queryString = "SELECT * FROM " . $this->PERMISSION_TABLE_NAME . " WHERE rfid = '" . $rfid . "'";
        $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
        return $stmt->fetch();
    }

    /** Função para adicionar uma nova permissão
     * @param string $rfid rfid da nova permissão
     * @throws DuplicatePermissionException
     * @throws Exception
     */
    public function addPermission(string $rfid)
    {
        // Validar nova permissão
        if (self::getPermissionByRFID($rfid)) {
            throw new DuplicatePermissionException($rfid);
        }

        // Adicionar permissão
        $sql = "INSERT INTO " . $this->PERMISSION_TABLE_NAME . " (rfid)
                    VALUES (?)";

        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            $stmt->execute(array($rfid));
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }

    }

    /** Função para apagar uma permissão
     * @throws PermissionNotFoundException
     * @throws Exception
     */
    public function deletePermission(string $rfid)
    {
        // Verificar se permissão existe
        if (!self::getPermissionByRFID($rfid)) {
            throw new PermissionNotFoundException($rfid);
        }

        // Apagar registo
        $sql = "DELETE FROM " . $this->PERMISSION_TABLE_NAME . " WHERE rfid = (?)";
        $stmt = $this->pdo->prepare($sql);

        try {
            $this->pdo->beginTransaction();
            $stmt->execute(array($rfid));
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}