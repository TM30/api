<?php namespace Data\Persistence\PDO;

use Data\Persistence\Persistence as Persistence;
use Data\Persistence\PDO\Result;

/**
 * this class essentially serves as a DB Mock for development purpose alone.
 * Makes UNit Testing possible without a need to use \Mocks or reach for the actual Data Store.
 * Class InMemoryPersistence
 * @package Collabo\Data\Persistence\InMemory
 */
class PDOPersistence implements Persistence
{
    private static $pdo;
    private $builder;
    private $query = "";

    public function __construct($table = null)
    {
        if ($table)
            $this->builder = new PDOQueryBuilder($table);
        self::$pdo = $this->createPDOInstance();
    }

    /**
     * This persists data to DB.
     * @param $data
     */
    public function persist($data)
    {
        $sql = $this->builder->insert($data)->getQuery();
        $this->query = $sql;
        $statement = self::$pdo->connection->prepare($sql);
        $statement->execute($data);
        return self::$pdo->connection->lastInsertId();
    }

    /**This function does a resource update.
     * @param $data
     * @param $id
     * @return null
     */
    public function update($data, $id)
    {
        $sql = $this->builder->update($data)->where('id', '=')->getQuery();
        $this->query = $sql;
        $statement = self::$pdo->connection->prepare($sql);

        $statement->bindValue(":id", $id);

        $statement->execute();

        return $this->getResult($statement);

    }

    /**
     * This function removes a records complete.
     * @param $id
     */
    public function remove($id)
    {
        $sql = $this->builder->delete()->where('id', '=')->getQuery();
        $this->query = $sql;
        $statement = self::$pdo->connection->prepare($sql);
        $statement->bindValue(":id", $id);
        $statement->execute();
    }

    /**
     * This persists to two tables using PDO Transaction
     * THe key of the other table is the last insert id of the first table
     * @param $data
     * @return null|string
     */
    public function persistWithTransaction($data){
        if (func_get_arg(2)) {
            self::$pdo->connection->beginTransaction();
            $lastInsertId = $this->persist($data);
            $this->builder->resetTable(func_get_arg(1));
            $tableData = func_get_arg(2);

            foreach ($tableData as $key=>$value) {
                if ($tableData[$key] == "{foreignKey}") {
                    $tableData[$key] = $lastInsertId;
                    break;
                }
            }

            $this->persist($tableData);
            $this->builder->resetTable();
            if (self::$pdo->connection->commit())
                return $lastInsertId;
        }
        return null;
    }

    /**
     * THis retrieves ALL data from DB.
     * @return array
     */
    public function retrieveAll($columns = false)
    {

        if ($columns) {
            $sql = $this->builder->select($columns)->getQuery();
        } else {
            $sql = $this->builder->select()->getQuery();
        }

        $this->query = $sql;
        $statement = self::$pdo->connection->prepare($sql);
        $statement->execute();

        /*$arrayObject = new \ArrayObject();
        while (($result = $statement->fetch(\PDO::FETCH_OBJ)) !== false) {
            $arrayObject->append($result);
        }
        return $arrayObject->getIterator();*/

        return $this->getResult($statement);
    }

    /**
     * This retrieves Data using client's parameters.
     * @return array
     */
    public function retrieveBy()
    {
        $searchWith = func_get_arg(0);
        $operator = func_get_arg(1);
        $value = func_get_arg(2);

        $sql = $this->builder->select()->where($searchWith, $operator)->getQuery();
        $statement = self::$pdo->connection->prepare($sql);

        $this->query = $sql ." Value =".$value;

        $statement->bindValue(":{$searchWith}", $value);
        $statement->execute();

        return $this->getResult($statement);
    }

    /**
     * THis enable client (Calling code) to write custom queries.
     * @param $query
     * @return array
     */
    public function customQuery($query){
         if (func_get_args()) {
             $statement = self::$pdo->connection->prepare($query);

             if (count(func_get_args()) > 1) {
                 $valuesToBind = func_get_arg(1);
                 if (is_array($valuesToBind)) {
                     foreach ($valuesToBind as $key=>$value)
                         $statement->bindValue($key, $value);
                     $this->query = $query;
                 } else {
                     $statement->bindValue(func_get_arg(1), func_get_arg(2));
                     $this->query = $query." ".func_get_arg(1)." ".func_get_arg(2);
                 }
             }

             $statement->execute();
             return $this->getResult($statement);
         }
        return null;
    }

    /**
     * This returns the built query string. Used basically for tests.
     * @return string
     */
    public function showQuery()
    {
        return $this->query;
    }

    /**
     * This function returns an instance of the pdo connection
     * @return PDOConnection
     */
    private function createPDOInstance()
    {
        if (isset(self::$pdo)) {
            return self::$pdo;
        }
        /*return new PDOConnection(array(
            "host" => "localhost",
            "dataBaseName" => "api",
            "username" => "root",
            "password" => "fileopen"
        ));*/

        return new PDOConnection(array(
            "host" => "localhost",
            "dataBaseName" => "api",
            "username" => "root2",
            "password" => "123456789"
        ));
    }

    public function getResult($PDOStatement)
    {
        $result = $PDOStatement->fetchAll(\PDO::FETCH_OBJ);
        if ($result) {
            /*if (sizeof($result) > 1)
                return $result;*/
            return $result;
        }
        return null;
    }

    public function retrieve($needle){}
}
