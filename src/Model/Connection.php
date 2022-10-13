<?php
/**
 * Database connection
 *
 */

namespace App\Model;

use \PDO;

/**
 * Connection to the database
 * to config the connection login password and dbname it's in /config/db.php
 * 
 * this connection class use singleton design patern to connect DB
 */
class Connection
{

    private static $pdoInstance;

    private static $user = APP_DB_USER;

    private static $host = APP_DB_HOST;

    private static $password = APP_DB_PWD;

    private static $dbName = APP_DB_NAME;

    private function __construct()
    {
        try {
            switch (APP_DB) {
                case "mysql":
                    $dsn = 'mysql:host=' . self::$host . '; dbname=' . self::$dbName . '; charset=utf8';
                    self::$pdoInstance = new PDO(
                        $dsn,
                        self::$user,
                        self::$password
                    );
                    break;
                case "sqlite":
                    self::$pdoInstance = new PDO('sqlite:' . APP_DB_PATH);
                    break;
            }
            self::$pdoInstance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // show errors in DEV environment
            if (!APP_PROD) {
            self::$pdoInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch (\PDOException $e) {
            echo ('Error !: ' . $e->getMessage());
        }
        
        
    }


    /**
     * @return PDO $pdo
     */
    public static function getInstance(): PDO
    {
        if (!isset(self::$pdoInstance)) {
            new self();
        }
        return self::$pdoInstance;
    }
}
