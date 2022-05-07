<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/Permissions/PermissionsController.php';

    (new PermissionsController())->handleRequest();
