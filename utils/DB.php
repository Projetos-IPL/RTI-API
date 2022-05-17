<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/DBConnectionException.php';

class DB
{

    /** Função para criar a conecção com a base de dados
     * @throws DBConnectionException
     */
    public static function connect() : PDO
    {

        $data_source_name =
            "mysql:host=" . DB_HOST . ";" .
            "dbname=" . DB_DATABASE_NAME . ";" .
            "charset=" . DB_CHARSET;

        if (str_contains(php_uname(), 'PT-JT7Q5D3')) {
            $data_source_name =
                "mysql:host=" . 'localhost;port=3306' . ";" .
                "dbname=" . DB_DATABASE_NAME . ";" .
                "charset=" . DB_CHARSET;
        }

        try {
            $pdo = new PDO(
                dsn: $data_source_name,
                username: DB_USER,
                password: DB_PASSWORD,
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            throw new DBConnectionException($e->getMessage());
        }
    }

}