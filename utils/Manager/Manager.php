<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/DataSchemaException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/FileReadException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/FileWriteException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Manager/ManagerUtils.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Manager/exceptions/OperationNotAllowedException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Manager/exceptions/EntityNotFoundException.php';


    abstract class Manager {

        protected string $ENTITY_NAME;          // Nome da entidade
        protected string $ENTITY_FILE_LOC;      // Deprecated
        protected string $ENTITY_TABLE_NAME;    // Nome da tabela da entidade
        protected string $ENTITY_FILE_PATH;     // Deprecated
        protected array $ENTITY_SCHEMA;         // Esquema da entidade
        protected array $ALLOWED_OPERATIONS;    // Operações permitidas à entidade
        protected PDO $pdo;                     // PHP Data Object para interagir com a base de dados

        public function __construct(
            string $ENTITY_NAME,
            string $ENTITY_FILE_LOC,
            string $ENTITY_FILE_NAME,
            array $ENTITY_SCHEMA,
            array $ALLOWED_OPERATIONS,
            PDO $pdo
        ) {
            $this->ENTITY_NAME = $ENTITY_NAME;
            $this->ENTITY_FILE_LOC = $ENTITY_FILE_LOC;
            $this->ENTITY_TABLE_NAME = $ENTITY_FILE_NAME;
            $this->ENTITY_FILE_PATH = $ENTITY_FILE_LOC . $ENTITY_FILE_NAME;
            $this->ENTITY_SCHEMA = $ENTITY_SCHEMA;
            $this->ALLOWED_OPERATIONS = $ALLOWED_OPERATIONS;
            $this->pdo = $pdo;
        }

        /** Função para obter os dados do ficheiro da entidade, valida a permissão.
         * @throws OperationNotAllowedException
         */
        public function getEntityData() : array
        {
            if (!ManagerUtils::verifyOperationPermission(ManagerUtils::READ, $this->ALLOWED_OPERATIONS)) {
                throw new OperationNotAllowedException();
            }

            return $this->getEntityDataInternal();
        }

        /** Função para obter os da entidade, sem validar permissão.
         * @return array
         */
        private function getEntityDataInternal() : array
        {
            $queryString = "SELECT * FROM " . $this->ENTITY_TABLE_NAME;
            $stmt = $this->pdo->query($queryString, PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }

        /** Função para adicionar um registo da entidade.
         * @throws DataSchemaException
         * @throws OperationNotAllowedException
         */
        protected function addEntity(array $entity) {

            if (!ManagerUtils::verifyOperationPermission(ManagerUtils::WRITE, $this->ALLOWED_OPERATIONS)) {
                throw new OperationNotAllowedException();
            }
            if (!ManagerUtils::validateEntity($this->ENTITY_SCHEMA, $entity)) {
                throw new DataSchemaException();
            }

            // Ordenar schema e entity para construir o comando SQL
            ksort($entity);  // ksort porque $entity é um assoc. array
            sort($this->ENTITY_SCHEMA);

            $sql = "INSERT INTO " . $this->ENTITY_TABLE_NAME . "(". implode(",", $this->ENTITY_SCHEMA) . ")" .
                   "VALUES ('" . implode("','", $entity) . "')";

            $stmt = $this->pdo->prepare($sql);
            $this->pdo->beginTransaction();
            $stmt->execute();
            $this->pdo->commit();
        }

        /** Função para atualizar um registo da entidade
         * @param array $oldEntity
         * @param array $newEntity
         * @return void
         * @throws OperationNotAllowedException
         * @throws DataSchemaException
         * @throws FileReadException
         * @throws EntityNotFoundException
         * @throws FileWriteException
         */
        protected function updateEntity(array $oldEntity, array $newEntity) {

            if (!ManagerUtils::verifyOperationPermission(ManagerUtils::UPDATE, $this->ALLOWED_OPERATIONS)) {
                throw new OperationNotAllowedException();
            }

            if (!ManagerUtils::validateEntity($this->ENTITY_SCHEMA, $newEntity)) {
                throw new DataSchemaException();
            }

            $entityArr = self::getEntityDataInternal();
            $entityIndex = ManagerUtils::findEntityIndex($entityArr, $oldEntity);
            if ($entityIndex == ENTITY_NOT_FOUND_INDEX) {
                throw new EntityNotFoundException();
            } else {
                $entityArr[$entityIndex] = $newEntity;
                self::overwriteEntityFile($entityArr);
            }
        }

        /** Função para apagar o registo de uma entidade
         * @throws EntityNotFoundException
         * @throws DataSchemaException
         * @throws FileWriteException
         * @throws OperationNotAllowedException
         * @throws FileReadException
         */
        protected function deleteEntity(array $entity) {

            if (!ManagerUtils::verifyOperationPermission(ManagerUtils::DELETE, $this->ALLOWED_OPERATIONS)) {
                throw new OperationNotAllowedException();
            }

            $entityArr = self::getEntityDataInternal();
            $entityIndex = ManagerUtils::findEntityIndex($entityArr, $entity);
            if ($entityIndex == ENTITY_NOT_FOUND_INDEX) {
                throw new EntityNotFoundException();
            } else {
                unset($entityArr[$entityIndex]);
                self::overwriteEntityFile($entityArr);
            }
        }

        /** Função para sobreescrever o ficheiro de armazenamento da entidade
         * @throws DataSchemaException
         * @throws FileWriteException
         */
        private function overwriteEntityFile(array $entityArray) {
            // Validar schema das entidades no array
            foreach ($entityArray as $entity) {
                if (!ManagerUtils::validateEntity($this->ENTITY_SCHEMA, $entity)) {
                    throw new DataSchemaException("Esquema de " . $this->ENTITY_NAME . ", as alterações não foram efetuadas.");
                }
            }
            // Sobreescrever
            $encodedArray = json_encode(array_values($entityArray));
            $success = file_put_contents($this->ENTITY_FILE_PATH, $encodedArray);
            if (!$success) {
                throw new FileWriteException($this->ENTITY_TABLE_NAME);
            }
        }




    }