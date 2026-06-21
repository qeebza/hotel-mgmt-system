<?php

class DB
{
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            $db_info = array(
                "host" => "127.0.0.1",
                "port" => "3307",
                "user" => "root",
                "pass" => "",
                "name" => "hotel",
                "charset" => "UTF-8"
            );

            try {
                self::$instance = new PDO(
                    "mysql:host=" . $db_info['host'] . ';port=' . $db_info['port'] . ';dbname=' . $db_info['name'],
                    $db_info['user'],
                    $db_info['pass']
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                self::$instance->query('SET NAMES utf8');
                self::$instance->query('SET CHARACTER SET utf8');
            } catch (PDOException $error) {
                error_log("Database connection failed: " . $error->getMessage());
                http_response_code(500);
                echo "Server error occurred. Please try again later.";
                exit;
            }
        }

        return self::$instance;
    }
}
