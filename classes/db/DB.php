<?php
namespace classes\db;

use PDO;
use classes\config\Constants;

class DB{

    private static $conn;

    private static function connect(){
        try {
            $constants = new Constants();
            $dbName = $constants->getDbName();
            $pdo = new PDO("mysql:host=".$constants->getDbHost(), $constants->getDbUsername(), $constants->getDbPassword());
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!self::isDbInitialized($pdo, $dbName)) {
                self::initDb($pdo);
            }
            
            DB::$conn = new PDO("mysql:host=".$constants->getDbHost().";dbname=".$dbName, $constants->getDbUsername(), $constants->getDbPassword());
            DB::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::applyMigrations(DB::$conn);
        }
        catch(PDOException $e){
        }
    }

    private static function isDbExists(PDO $pdo, string $dbName): bool {
        $stmt = $pdo->query("SHOW DATABASES LIKE '$dbName'");
        if ($stmt->fetch() === false) {
            return false;
        }
        return true;
    }

    private static function countTables(PDO $pdo, string $dbName): int
    {
        $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbName'");
        $count = $stmt->fetchColumn();
        return (int)$count;
    }

    private static function isDbInitialized(PDO $pdo, string $dbName): bool
    {
        return self::isDbExists($pdo, $dbName) && self::countTables($pdo, $dbName) > 0;
    }

    private function initDb(PDO $pdo){
        $createDbScript = file_get_contents(__DIR__ . '/../../misc/createDb.sql');
        $pdo->exec($createDbScript);
        $initTables = file_get_contents(__DIR__ . '/../../misc/insert.sql');
        $pdo->exec($initTables);
    }

    private static function applyMigrations(PDO $pdo) {
        $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
        $tableExists = $stmt->fetch() !== false;

        $pdo->exec('CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            script_name VARCHAR(255) NOT NULL,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );');

        $migrationFiles = glob(__DIR__ . '/../../misc/update/*.sql');
        sort($migrationFiles);

        if (!$tableExists) {
            // Table did not exist, this is a fresh DB.
            // Just record all migrations as applied.
            $stmt = $pdo->prepare('INSERT INTO migrations (script_name) VALUES (?)');
            foreach ($migrationFiles as $file) {
                $scriptName = basename($file);
                $stmt->execute([$scriptName]);
            }
        } else {
            // Table existed, apply missing migrations.
            $stmt = $pdo->query('SELECT script_name FROM migrations');
            $appliedScripts = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($migrationFiles as $file) {
                $scriptName = basename($file);
                if (!in_array($scriptName, $appliedScripts)) {
                    $sql = file_get_contents($file);
                    $pdo->exec($sql);

                    $stmt = $pdo->prepare('INSERT INTO migrations (script_name) VALUES (?)');
                    $stmt->execute([$scriptName]);
                }
            }
        }
    }

    /**
     * 
     * @return PDO
     */
    public static function getConn(){
        if(DB::$conn == null)
            DB::connect();
        return DB::$conn;
    }

}
?>