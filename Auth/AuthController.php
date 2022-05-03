<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/commonResponses.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/AuthUtils.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/UserManager.php';


    class AuthController extends Controller {

        public function __construct() {
            $AUTHORIZATION_MAP = array(
                GET => false,
                POST => true,
            );

            $REQ_BODY_SPEC = array (
                GET =>  'token',
                POST => ['username', 'password']
            );

            parent::__construct($AUTHORIZATION_MAP, $REQ_BODY_SPEC);
        }

        protected function routeRequest() {
            $reqMethod = $_SERVER['REQUEST_METHOD'];

            switch ($reqMethod) {
                case GET:
                    self::getHandler();
                    break;
                case POST:
                    self::postHandler();
                    break;
                default:
                    methodNotAvailable($reqMethod);
                    break;
            }
        }

        private function getHandler() {
            if (AuthUtils::verifyJWT($this->REQ_BODY['token'])) {
                http_response_code(200);
                $res_body = json_encode(array(
                    'message' => 'Autorizado'
                ));
                echo $res_body;
            }
        }

        private function postHandler() {
            try {
                // Efetuar login
                $userIndex = AuthUtils::findUser($this->REQ_BODY['username']);
                $usersArr = UserManager::getUsers();
                AuthUtils::login($usersArr[$userIndex], $this->REQ_BODY['password']);

                // Enviar resposta
                http_response_code(200);
                $res_body = json_encode(array(
                    'username' => $this->REQ_BODY['username'],
                    'token' => AuthUtils::getJWT($this->REQ_BODY['username']),
                    'timestamp' => (new DateTime)->getTimeStamp()
                ));
                echo $res_body;
            } catch (UserNotFoundException | WrongCredentialsException) {
                notAuthrorizedResponse();
            } catch (FileReadException $e) {
                internalErrorResponse($e->getMessage());
            }
        }
    }