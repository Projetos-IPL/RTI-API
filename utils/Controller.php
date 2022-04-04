<?php

    abstract class Controller {

        public abstract static function handleRequest();

        protected abstract static function getHandler();
        protected abstract static function putHandler();
        protected abstract static function deleteHandler();

        protected abstract static function validatePostRequest(array $req_body): bool;
        protected abstract static function validatePutRequest(array $req_body): bool;
        protected abstract static function validateDeleteRequest(array $req_body): bool;
    }

