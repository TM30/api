<?php namespace Data\Persistence\PDO;

class PDOConnection
{
    private $host;
    private $dataBaseName;
    private $username;
    private $password;
    public $connection;
    protected $errorMessages;
    private $checkIfErrorExist = false;

    public function __construct(array $connectionDetails, $driver = "mysql")
    {
        $this->host = $connectionDetails['host'];
        $this->dataBaseName = $connectionDetails['dataBaseName'];
        $this->username = $connectionDetails['username'];
        $this->password =  $connectionDetails['password'];
        try {
            $this->connection = new \PDO(
                "mysql:host=$this->host;dbname=$this->dataBaseName",
                $this->username,
                $this->password
            );
        } catch (\PDOException $e) {
            //Throws an error message.
            $this->checkIfErrorExist = true;
            $this->errorMessages = $e->getMessage();
        }
    }

    /**
     * Checks for any error iin connection.
     * @return bool|string
     */
    public function connectionStatus()
    {
        if ($this->checkIfErrorExist) {
            //Connection Unsuccessful
            return $this->errorMessages;
        }
        //Connection Successful
        return "Successful";
    }
}
