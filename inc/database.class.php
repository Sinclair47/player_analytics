<?php
class Database{
	private $stmt;
	private $host;
	private $user;
	private $pass;
	private $dbname;
	private $port;

	private $dbh;
	private $error;

	public function __construct($dbinfo){
		$this->host = $dbinfo['host'];
		$this->port = $dbinfo['port'];
		$this->dbname = $dbinfo['database_name'];
		$this->user = $dbinfo['username'];
		$this->pass = $dbinfo['password'];

        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbname;
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT	=> true,
            PDO::ATTR_ERRMODE		=> PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        );
        // Create a new PDO instanace
        try{
            @$this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }
        // Catch any errors
        catch(PDOException $e){
            $this->error = $e->getMessage();
        }
    }

    public function query($query){
    	$this->stmt = $this->dbh->prepare($query);
	}

	public function bind($param, $value, $type = null){
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

	public function execute(){
		#$file = file_get_contents("sql_log_dbclass.txt");   file_put_contents("sql_log_dbclass.txt", "\n### ".date('Y-m-d H:i:s')." ### \n". print_r($this->stmt->debugDumpParams())."\n" . $file);
		return $this->stmt->execute();
	}

	public function resultset(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function single(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function rowCount(){
		return $this->stmt->rowCount();
	}

	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}

	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}

	public function endTransaction(){
		return $this->dbh->commit();
	}

	public function cancelTransaction(){
		return $this->dbh->rollBack();
	}

	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}
	public function stmt(){
		return json_encode($this->stmt);
	}
}
