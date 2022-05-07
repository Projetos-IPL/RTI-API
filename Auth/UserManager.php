<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/FileReadException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Manager/Manager.php';

    class UserManager extends Manager {

        public function __construct()
        {
            $USER_FILE_LOC = ROOTPATH.'/files/';
            $USER_FILE_NAME = 'users.json';
            $USER_SCHEMA = array('username', 'password');
            $ALLOWED_OPERATIONS = array(ManagerUtils::READ);

            parent::__construct(
                'USER',
                $USER_FILE_LOC,
                $USER_FILE_NAME,
                $USER_SCHEMA,
                $ALLOWED_OPERATIONS);
        }
    }