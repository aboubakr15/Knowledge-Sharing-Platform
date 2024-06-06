<?php

namespace App\Utils;

class Validator
{
    public static function username(string $username): bool
    {
        return strlen($username) >= 4 && strlen($username) <= 20;
    }

    public static function password(string $password): bool
    {
        return strlen($password) >= 8;
    }

    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}