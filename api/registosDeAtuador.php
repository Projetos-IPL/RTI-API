<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/ActuatorLogs/ActuatorLogsController.php';

(new ActuatorLogsController())->handleRequest();
