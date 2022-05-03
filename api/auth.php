<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/AuthController.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';

    (new AuthController())->handleRequest();




