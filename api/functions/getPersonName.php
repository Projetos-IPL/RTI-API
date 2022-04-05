<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/People/PeopleManager.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/People/PeopleUtils.php';

    requestConfig();

    $req_body = json_decode(file_get_contents('php://input'), true) ?: array();

    if (!isset($req_body['rfid']) || count($req_body) != 1) {
        wrongFormatResponse();
        exit();
    }

    // Tentar obter pessoa e enviar nome como resposta
    try {
        $peopleArr = PeopleManager::getPeople();
        $index = PeopleUtils::getPersonIndex($req_body['rfid']);
        $personName = $peopleArr[$index]["primNome"] . " " . $peopleArr[$index]["ultNome"];

        http_response_code(200);
        echo json_encode(
            array(
                'name'=> $personName
            )
        );
    } catch (PersonNotFoundException $e) {
        unprocessableEntityResponse($e->getMessage());
    } catch (FileReadException $e) {
        internalErrorResponse($e->getMessage());
    }

