<?php

    include_once '../utils/requestConfig.php';
    include_once '../utils/commonResponses.php';

    include_once 'AuthUtils.php';
    include_once 'UserManager.php';

    abstract class AuthController {

        private static array $REQ_BODY;

        public static function handleRequest() {
            requestConfig();
            self::$REQ_BODY = json_decode(file_get_contents('php://input'), true) ?: array();

            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    self::postHandler();
                    break;
                default:
                    methodNotAvailable($_SERVER['REQUEST_METHOD']);
                    break;
            }
        }

        private static function postHandler() {
            if (!self::validatePostRequest(self::$REQ_BODY)) {
                wrongFormatResponse();
                return;
            }

            try {
                // Efetuar login
                $userIndex = AuthUtils::findUser(self::$REQ_BODY['username']);
                $usersArr = UserManager::getUsers();
                AuthUtils::login($usersArr[$userIndex], self::$REQ_BODY['password']);

                // Enviar resposta
                http_response_code(200);
                $res_body = json_encode(array(
                    'username' => self::$REQ_BODY['username'],
                    'token' => AuthUtils::getJWT(self::$REQ_BODY['username'])
                ));
                echo $res_body;
            } catch (UserNotFoundException | WrongCredentialsException) {
                notAuthrorizedResponse();
            } catch (FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }

        }

        private static function validatePostRequest(array $req_body): bool
        {
            if (!isset($req_body['username']) || !isset($req_body['password'])) {
                return false;
            }

            return true;
        }



    }