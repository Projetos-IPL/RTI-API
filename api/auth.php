<?php

    include "../auth/login.php";
    include "../utils/validation.php";
    include "../utils/commonResponses.php";
    include '../utils/requestConfig.php';

    requestConfig(array("POST"));

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        methodNotSupported($_SERVER['REQUEST_METHOD']);
    }

    // Obter utilizadores dos ficheiros
    $USERS_FILE_PATH = "../files/users.json";
    $users_json_string = file_get_contents($USERS_FILE_PATH);
    $users = json_decode($users_json_string, true);

    // Obter corpo do pedido e validar
    $req_body = json_decode(file_get_contents('php://input'), true);

    if ($req_body == NULL || !validateAuthBody($req_body)) {
        wrongFormatResponse();
    }

    // Iterar utilizadores existentes no sistema e tentar iniciar a sessão
    foreach ($users as $user) {
        login($user, $req_body["username"], $req_body["password"]);
    }

    // Se chegar aqui é porque não conseguiu iniciar a sessão
    http_response_code(401);
    echo json_encode(array(
        "message" => "O nome de utilizador ou password estão incorretos."
    ));

    exit();



