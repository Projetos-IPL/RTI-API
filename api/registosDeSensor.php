<?php

    include_once $_SERVER['DOCUMENT_ROOT'] . '/SensorLogs/SensorLogController.php';

    (new SensorLogController)->handleRequest();
