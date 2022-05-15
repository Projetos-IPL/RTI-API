<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/exceptions/UserNotFoundException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/exceptions/WrongCredentialsException.php';

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    abstract class AuthUtils {

        public static string $JWT_KEY = 'chave_generica';
        public static string $JWT_ALG = 'HS256';

        /** Função para efetuar login
         * @param $pdo PDO PDO do controller
         * @param $username string username
         * @param $password string password
         * @throws WrongCredentialsException
         */
        public static function login(PDO $pdo, string $username, string $password) {
            $hashedPW = md5($password);
            // Esta query vai devolver 1 se encontrar um utilizador
            $queryString = "SELECT COUNT(1) FROM user 
                                    WHERE username = '" . $username . "' " .
                                    " AND password = '" .$hashedPW . "'";

            $stmt = $pdo->query($queryString, PDO::FETCH_NUM);

            // A query apenas vai receber registos de corresponder o utilizador e password.
            if ($stmt->fetch()[0] === '0') {
                throw new WrongCredentialsException();
            }

        }

        /** Função para gerar um token twt
         * @param string $username
         * @return string
         */
        public static function getJWT(string $username): string
        {
            $payload = [
                'username' => $username,
                'timestamp' => (new DateTime)->getTimestamp()
            ];

            return JWT::encode($payload, self::$JWT_KEY, self::$JWT_ALG);
        }

        /** Função para verificar se um token é válido
         * @param string $jwt
         * @return bool
         */
        public static function verifyJWT(string $jwt) : bool
        {
            try {
                JWT::decode($jwt, new Key(self::$JWT_KEY, self::$JWT_ALG));
                return true;
            } catch (Exception) {
                return false;
            }
        }

        /** Função para descodificar um token
         * @throws InvalidTokenException
         */
        public static function decodeJWT(string $jwt) : array
        {
            try {
                return (array) JWT::decode($jwt, new Key(self::$JWT_KEY, self::$JWT_ALG));
            } catch (Exception) {
                throw new InvalidTokenException();
            }

        }
    }
