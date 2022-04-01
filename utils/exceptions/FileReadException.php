<?php

    class FileReadException extends Exception
    {
        public function __construct($fileName = "", $code = 0, Throwable $previous = null)
        {
            $message = "Failed to read data from " . $fileName;
            parent::__construct($message, $code, $previous);
        }
    }
