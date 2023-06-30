<?php

include_once("dbconfig.php");

class Database
{

    private $host      = DB_HOST;
    private $user      = DB_USER;
    private $pass      = DB_PASS;
    private $dbname    = DB_NAME;
    private $dbh;
    private $error;
    private $stmt;

    public function __construct()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        // $options = array(
        //     PDO::ATTR_PERSISTENT    => true,
        //     PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION,
        // 	PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        // 	PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8"
        // );
        try {
            // $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            $this->dbh = new PDO("pgsql:host={$this->host};port=5432;dbname={$this->dbname};user={$this->user};password={$this->pass}");
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo 'ERROR!';
            print_r($e);
        }
    }


    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }


    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute()
    {
        return $this->stmt->execute();
    }

    public function resultset()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    public function beginTransaction()
    {
        return $this->dbh->beginTransaction();
    }

    public function endTransaction()
    {
        return $this->dbh->commit();
    }

    public function cancelTransaction()
    {
        return $this->dbh->rollBack();
    }

    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }

    public function closeConnection()
    {
        $this->stmt = null;
        $this->dbh = null;
    }
}
