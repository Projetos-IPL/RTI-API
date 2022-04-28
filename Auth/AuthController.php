<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';


    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/AuthUtils.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/UserManager.php';

    abstract class AuthController extends Controller {

        public static string $JWT_KEY = 'chave_generica';
        public static string $JWT_ALG = 'HS256';

        public static function handleRequest() {
            requestConfig();
            self::$REQ_BODY = json_decode(file_get_contents('php://input'), true) ?: array();

            switch ($_SERVER['REQUEST_METHOD']) {
                case GET:
                    self::getHandler();
                    break;
                case POST:
                    self::postHandler();
                    break;
                default:
                    methodNotAvailable($_SERVER['REQUEST_METHOD']);
                    break;
            }
        }

        private static function getHandler() {
            if (!self::validateGetRequest(self::$REQ_BODY)) {
                wrongFormatResponse();
                return;
            }

            if (AuthUtils::verifyJWT(self::$REQ_BODY['token'])) {
                http_response_code(200);
                $res_body = json_encode(array(
                    'message' => 'Autorizado'
                ));
                echo $res_body;
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
                    'token' => AuthUtils::getJWT(self::$REQ_BODY['username']),
                    'timestamp' => (new DateTime)->getTimeStamp()
                ));
                echo $res_body;
            } catch (UserNotFoundException | WrongCredentialsException) {
                notAuthrorizedResponse();
            } catch (FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }

        }

        private static function validateGetRequest(array $req_body): bool
        {
            if (!isset($req_body['token'])) {
                return false;
            }

            return true;
        }

        private static function validatePostRequest(array $req_body): bool
        {
            if (!isset($req_body['username']) || !isset($req_body['password'])) {
                return false;
            }

            return true;
        }



    }