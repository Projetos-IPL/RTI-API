<?php

    // Função para efetuar o login
    function login($user, $username, $password) {
        if ($user["username"] == $username && $user["password"] == $password) {
            http_response_code(200);
            echo json_encode(array(
                "message" => "Sucesso",
                "token" => "futuro token"
            ));
            exit();
        }
    }