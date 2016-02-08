<?php namespace Data\Persistence\PDO;

trait Result
{
    public function getResult($PDOStatement)
    {
        $result = $PDOStatement->fetchAll(\PDO::FETCH_OBJ);
        if ($result) {
            if (sizeof($result) > 1)
                return $result;
            return $result[0];
        }
        return null;
    }
}
