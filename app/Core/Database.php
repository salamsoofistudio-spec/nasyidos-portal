<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $configPath =
            dirname(__DIR__, 3)
            . '/nasyidos_config.php';

        if (!file_exists($configPath)) {
            throw new RuntimeException(
                'NasyidOS configuration file not found.'
            );
        }

        $config = require $configPath;

        if (
            !isset($config['database']) ||
            !is_array($config['database'])
        ) {
            throw new RuntimeException(
                'Invalid database configuration.'
            );
        }

        $db = $config['database'];

        $dsn =
            'mysql:host=' .
            $db['host'] .
            ';dbname=' .
            $db['name'] .
            ';charset=' .
            $db['charset'];

        self::$connection = new PDO(
            $dsn,
            $db['username'],
            $db['password'],
            [
                PDO::ATTR_ERRMODE =>
                    PDO::ERRMODE_EXCEPTION,

                PDO::ATTR_DEFAULT_FETCH_MODE =>
                    PDO::FETCH_ASSOC,

                PDO::ATTR_EMULATE_PREPARES =>
                    false,
            ]
        );

        return self::$connection;
    }
}