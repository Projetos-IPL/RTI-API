<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/UserManager.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/exceptions/UserNotFoundException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/exceptions/WrongCredentialsException.php';

    abstract class AuthUtils {

        /**
         * @param array $user Utilizador
         * @param $password string para efetuar login
         * @throws WrongCredentialsException
         */
        public static function login(array $user, string $password) {
            if ($user['password'] != $password) {
                throw new WrongCredentialsException();
            }
        }

        /**
         * @throws UserNotFoundException
         * @throws FileReadException
         */
        public static function findUser($username): int
        {
            foreach (UserManager::getUsers() as $key => $user) {
               if ($user['username'] == $username) {
                   return $key;
               }
            }

            throw new UserNotFoundException($username);
        }

        // TODO Implementar getJWT
        public static function getJWT(string $username): string
        {
            $payload = [
                'username' => $username,
                'timestamp' => (new DateTime)->getTimestamp()
            ];

            return JWT::encode($payload, AuthController::$JWT_KEY, AuthController::$JWT_ALG);
        }

        public static function verifyJWT(string $jwt) : bool
        {
            try {
                JWT::decode($jwt, new Key(AuthController::$JWT_KEY, AuthController::$JWT_ALG));
                return true;
            } catch (Exception) {
                return false;
            }
        }
    }
