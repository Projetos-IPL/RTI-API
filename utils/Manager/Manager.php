<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/DataSchemaException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/FileReadException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/FileWriteException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Manager/ManagerUtils.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Manager/exceptions/OperationNotAllowedException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Manager/exceptions/EntityNotFoundException.php';


    abstract class Manager {

        protected string $ENTITY_NAME;

        protected string $ENTITY_FILE_LOC;
        protected string $ENTITY_FILE_NAME;
        protected string $ENTITY_FILE_PATH;

        protected array $ENTITY_SCHEMA;
        protected array $ALLOWED_OPERATIONS;

        public function __construct(
            string $ENTITY_NAME,
            string $ENTITY_FILE_LOC,
            string $ENTITY_FILE_NAME,
            array $ENTITY_SCHEMA,
            array $ALLOWED_OPERATIONS
        ) {
            $this->ENTITY_NAME = $ENTITY_NAME;
            $this->ENTITY_FILE_LOC = $ENTITY_FILE_LOC;
            $this->ENTITY_FILE_NAME = $ENTITY_FILE_NAME;
            $this->ENTITY_FILE_PATH = $ENTITY_FILE_LOC . $ENTITY_FILE_NAME;
            $this->ENTITY_SCHEMA = $ENTITY_SCHEMA;
            $this->ALLOWED_OPERATIONS = $ALLOWED_OPERATIONS;
        }

        /** Função para obter os dados do ficheiro da entidade, valida a permissão.
         * @throws FileReadException
         * @throws OperationNotAllowedException
         */
        public function getEntityData() : array
        {
            if (!ManagerUtils::verifyOperationPermission(ManagerUtils::READ, $this->ALLOWED_OPERATIONS)) {
                throw new OperationNotAllowedException();
            }

            return $this->getEntityDataInternal();
        }

        /** Função para obter os dados do ficheiro da entidade, sem validar permissão.
         * @return array
         * @throws FileReadException
         */
        protected function getEntityDataInternal() : array
        {
            $file_contents = file_get_contents($this->ENTITY_FILE_PATH);
            $entityArr = json_decode($file_contents, true);

            if ($entityArr === null) {
                throw new FileReadException($this->ENTITY_FILE_NAME);
            }

            return $entityArr;
        }

        /** Função para adicionar um registo da entidade ao ficheiro.
         * @throws DataSchemaException
         * @throws FileReadException
         * @throws FileWriteException
         * @throws OperationNotAllowedException
         */
        protected function addEntity(array $entity) {

            if (!ManagerUtils::verifyOperationPermission(ManagerUtils::WRITE, $this->ALLOWED_OPERATIONS)) {
                throw new OperationNotAllowedException();
            }
            if (!ManagerUtils::validateEntity($this->ENTITY_SCHEMA, $entity)) {
                throw new DataSchemaException();
            }

            $entityArr = self::getEntityDataInternal();
            $entityArr[] = $entity;
            self::overwriteEntityFile($entityArr);
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
                throw new FileWriteException($this->ENTITY_FILE_NAME);
            }
        }




    }