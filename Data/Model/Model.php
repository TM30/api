<?php namespace Data\Model;

use Data\Persistence\Persistence;
use Data\Persistence\PDO\PDOPersistence;

abstract class Model
{
    protected $persistence;

    public function __construct(Persistence $persistence = null )
    {
        $tableName = $this->getSubClassName();
        $this->persistence = $persistence ?: new PDOPersistence($tableName);
    }

    /**
     * Finds a record by Id from this table.
     * @param $id
     * @return array
     */
    public function find($id)
    {
        return $this->persistence->retrieveBy("id", "=", intval($id));
    }

    public function findBy($search, $operator, $value)
    {
        return $this->persistence->retrieveBy($search,  $operator, $value);
    }

    /**
     * Finds a record by the given parameter, operator and value.
     * @return array
     */
    public function findAll($columns = false)
    {
        if ($columns)
            return $this->persistence->retrieveAll($columns);
        return $this->persistence->retrieveAll();
    }

    /**
     * Creates a new record and returns the last inserted id.
     * @param array $details
     * @return string
     */
    public function create(array $details)
    {
        return $this->persistence->persist($details);
    }

    /**
     * This funciton does an update.
     * @param $data
     * @param $id
     * @return null
     */
    public function update($data, $id)
    {
        return $this->persistence->update($data, $id);
    }

    /**
     * Removes a record completely.
     * @param $id
     */
    public function remove($id)
    {
        $this->persistence->remove($id);
    }

    /**
     * This function returns the built query..
     * @return string
     */
    public function showQuery()
    {
        return $this->persistence->showQuery();
    }

    /**
     *  This function gets the class name of a child class for use by the query builder for table resolution.
     * @param bool $ignorePluralization
     * @return string
     */
    private function getSubClassName($ignorePluralization = false)
    {
        $reflection = new \ReflectionClass($this); //Gets the class name of the child class
        $className = $reflection->getShortName();
        if ($ignorePluralization) {
            if ($className === "CaseClass")
                return "case";
            return  $className;
        }

        if ($className === "CaseClass")
            return "cases";
        return $className = strtolower($className) . "s";
    }

    public function getPersistenceObject()
    {
        return $this->persistence;
    }
}
