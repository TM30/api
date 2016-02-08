<?php namespace Data\Persistence\PDO;

trait Connect
{
    /**
     * This function returns an instance of the pdo connection
     * @return PDOConnection
     */
    private function createPDOInstance()
    {
        if (isset(self::$pdo)) {
            return self::$pdo;
        }
        return new PDOConnection(array(
            "host" => "127.0.0.1",
            "dataBaseName" => "pencilco_immunize",
            "username" => "pencilco_admin",
            "password" => "macgrenor2015"
        ));

        /*return new PDOConnection(array(
            "host" => "localhost",
            "dataBaseName" => "immunize",
            "username" => "root2",
            "password" => "123456789"
        ));*/

    }
}
