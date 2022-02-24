<?php
namespace Akaninyene\Upworkone;

use PDO;

class SQLiteConnection
{
    private $pdo;


    // Creating a singleton to maintain single connection to database
    public function connect()
    {
        if ($this->pdo == null) {
            try {
                $this->pdo = new PDO("sqlite:" . Config::DB_FILE);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $this->pdo;
    }
}