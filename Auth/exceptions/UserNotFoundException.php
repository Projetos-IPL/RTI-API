<?php

    class UserNotFoundException extends Exception {
        public function __construct(string $username= "", int $code = 0, ?Throwable $previous = null)
        {
            parent::__construct('User ' . $username . " doesn't exist.", $code, $previous);
        }
    }
