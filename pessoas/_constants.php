<?php

    include_once '../utils/constants.php';

    defined('PEOPLE_FILE_LOC') or
        define('PEOPLE_FILE_LOC', ROOTPATH.'/files/');

    defined('PEOPLE_FILE_NAME') or
        define('PEOPLE_FILE_NAME', 'pessoas.json');

    defined('PEOPLE_FILE_PATH') or
        define ('PEOPLE_FILE_PATH', PEOPLE_FILE_LOC . PEOPLE_FILE_NAME);
