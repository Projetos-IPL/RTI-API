<?php


    abstract class Controller {

        protected static array $REQ_BODY;

        public abstract static function handleRequest();

    }

