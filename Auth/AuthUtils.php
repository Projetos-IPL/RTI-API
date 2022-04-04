<?php

    include_once 'UserManager.php';
    include_once 'exceptions/UserNotFoundException.php';
    include_once 'exceptions/WrongCredentialsException.php';

    abstract class AuthUtils {

        /**
         * @param array $user Utilizador
         * @param $password Password para efetuar login
         * @throws WrongCredentialsException
         */
        public static function login(array $user, $password) {
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
            return 'token';
        }
    }
