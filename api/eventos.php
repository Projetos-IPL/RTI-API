<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/Events/EventsController.php';

(new EventsController())->handleRequest();