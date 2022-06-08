<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/Auth/AuthController.php';

(new AuthController())->handleRequest();




