<?php

    include_once '../utils/constants.php';
    include_once '../utils/exceptions/FileReadException.php';

    abstract class UserManager {

        public const USER_FILE_LOC = ROOTPATH.'/files/';
        public const USER_FILE_NAME = 'users.json';
        public const USER_FILE_PATH = self::USER_FILE_LOC . self::USER_FILE_NAME;

        /**
         * @return array Associative Array de utilizadores
         * @throws FileReadException
         */
        public static function getUsers(): array
        {
            $file_contents = file_get_contents(self::USER_FILE_PATH);
            $userArr = json_decode($file_contents, true);

            if (!$userArr) {
                throw new FileReadException(self::USER_FILE_NAME);
            }

            return $userArr;
        }
    }