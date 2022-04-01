<?php

    class FileWriteException extends Exception {
        public function __construct($fileName = "", $code = 0, Throwable $previous = null)
        {
            $message = "Failed to write data to " . $fileName;
            parent::__construct($message, $code, $previous);
        }
    }