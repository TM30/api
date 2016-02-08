<?php namespace Data\Persistence\PDO;

/**
 * ADAPTER PATTERN (Structural Pattern)
 * An adapter class that encapsulates all possible actions executable on a table class.
 * This table class is passed in as an input to its constructor..
 * Class PDOQueryBuilderAdapter
 * @package Collabo\Data
 */

class PDOQueryBuilder
{
    private $sql;
    private $table;
    private $oldTable;

    public function __construct($table)
    {
        $this->sql = "";
        $this->table = $table;
        $this->oldTable = $table;
    }

    //Returns the complete selectFrom query
    public function select($array = false)
    {
        if ($array) {
            if (!empty($array)) {
                $filters = implode(", ", $array);
                $this->sql = "SELECT " . $filters . " FROM " . $this->table;
            }
        } else {
            $this->sql = "SELECT * FROM " . $this->table;
        }
        return $this;
    }

    //This function builds a query using inner-join
    public function selectJoin($otherTable, $joinWith, array $selection = null)
    {
        $statement = "SELECT ";

        if ($selection) {
            if (!empty($selection)) {
                foreach ($selection as $key => $value) {
                    if (is_int($key)) {
                        $statement .= "{$value}, ";
                    } else {
                        $statement .= "{$key} as {$value}, ";
                    }
                }
                $statement = substr($statement, 0, -2);
                $this->sql = "{$statement} FROM " . $this->table . " $joinWith join {$otherTable} ";
            }
        } else {
            $this->sql = "SELECT * FROM " . $this->table . " $joinWith join {$otherTable} ";
        }
        return $this;
    }

    //This does selection with count, sum or other functions
    public function selectFunction($func, $column, $as = false)
    {
        if ($as) {
            $this->sql = "SELECT " . $func . "(" . $column . ")" . " as " . $as . " FROM " . $this->table;
            return $this;
        }
        $this->sql = "SELECT " . $func . "(" . $column . ")" . " FROM " . $this->table;
        return $this;
    }

    //Returns the complete insertInto query
    public function insert($placeholderArray)
    {
        /*$base_values = "(";
        $values = " VALUES(";
        $statement = "INSERT INTO " . $this->table;
        if (!empty($placeholderArray)) {
            foreach ($placeholderArray as $key => $value) {
                $base_values .= "`{$key}`, ";
                $values .= ":$key, ";
            }

            $base_values = substr($base_values, 0, -2) . ")";
            $values = substr($values, 0, -2) . ")";
            $statement .= $base_values . $values;
            $this->sql = $statement;
        }
        return $this;*/
        $base_values = "(";
        $values = " VALUES(";
        $statement = "INSERT INTO ". $this->table ;
        if(!empty($placeholderArray))
        {
            foreach($placeholderArray as $key=>$value){
                $base_values .= "`{$key}`, ";
                $values .= "{$this->funCheck($value)}, ";
            }

            $base_values = substr($base_values, 0, -2) .")";
            $values =  substr($values, 0, -2) .")";
            $statement .= $base_values . $values;
            $this->sql = $statement;
        }
        return $this;
    }

    //Returns the complete update query
    public function update($placeholderArray)
    {
        $statement = "UPDATE " . $this->table . " SET ";
        if (!empty($placeholderArray)) {
            foreach ($placeholderArray as $key => $value) {
                $statement .= "`$key` = :{$this->funCheck($value)}, ";
            }
            $statement = substr($statement, 0, -2);
            $this->sql = $statement;
        }
        return $this;
    }

    //Returns the complete delete query
    public function delete()
    {
        $statement = "DELETE FROM " . $this->table;
        $this->sql = $statement;
        return $this;
    }

    //Builds query plus a where clause
    public function on($a, $b)
    {
        $this->sql .= " on " . "{$a}" . " = " . "{$b}";
        return $this;
    }

    //Builds query plus a where clause
    public function where($base, $operator)
    {
            $this->sql = $this->sql . " where " . "`$base`". " $operator" . " :$base";
            return $this;
    }

    //Builds query plus a where clause
    public function whereJoin($base, $operator)
    {
            $this->sql = $this->sql . " where " . "$base". " $operator" . " :$base";
            return $this;
    }

    //Builds query plus and and clause
    public function _and($base, $operator)
    {
            $this->sql = $this->sql . " and " . "`$base`". " $operator" . " :$base";
            return $this;
    }

    //Builds query plus and and clause
    public function _andJoin($base, $operator)
    {
            $this->sql = $this->sql . " and " . "$base". " $operator" . " :$base";
            return $this;
    }

    //Builds query plus and and clause
    public function innerJoin($table)
    {
        $this->sql = $this->sql . " inner join " . $table;
        return $this;
    }

    //Builds query plus and and clause
    public function orderBy($orderBy, $flag, $limit = false)
    {
        if ($limit) {
            $this->sql = $this->sql . " ORDER BY " . $orderBy . " $flag" . " limit $limit";
        } else {
            $this->sql = $this->sql . " ORDER BY " . $orderBy . " $flag";
        }
        return $this;
    }

    //Builds query plus and and clause
    public function groupBy($flag)
    {
        $this->sql = $this->sql . " GROUP BY " . $flag;
        return $this;
    }

    /**
     * Returns the complete query
     * @return string
     */
    public function getQuery()
    {
        return $this->sql;
    }

    /**
     * This functions print the built query
     */
    public function printSQL()
    {
        $sqlQuery = "There are no queries";
        if (($this->sql)) {
            $sqlQuery = $this->sql;
        }
        print($sqlQuery);
    }

    /**
     * This function prints the name of the current table to be used for this query
     */
    public function printTable()
    {
        print($this->table);
    }

    /**
     * This function prints the name of the current table to be used for this query
     */
    public function funCheck($value)
    {
        if (substr($value, 0, -strlen($value) + 2) === "{}") {
            return substr($value, 2, strlen($value) - 2);
        } else {
            $str = "'$value'";
            return $str;
        }
    }

    /**
     * This reset the table.
     * @param $tableName
     */
    public function resetTable($tableName  = false){
        if ($tableName)
            $this->table = $tableName;
        else
            $this->table = $this->oldTable;
    }
}
