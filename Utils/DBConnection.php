<?php

namespace App\Utils;

use PDO;
use PDOException;

class DBConnection
{
    private string $host = 'localhost';
    private string $db_name = 'se-project';
    private string $username = 'root';
    private string $password = '';
    private static $db = null;

    private function __construct()
    {
    }

    public static function getConnection()
    {
        if (self::$db === null) {
            try {
                $instance = new self();
                self::$db = new PDO("mysql:host={$instance->host};dbname={$instance->db_name}", $instance->username, $instance->password);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                echo "Connection error: " . $exception->getMessage();
            }
        }

        return self::$db;
    }
}