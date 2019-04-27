<?php
/**
*    @author     Johan Kasselman <johankasselman@live.com>
*    @since         2015-09-28    V1
*
*/

class pdo_db {

    private $db;
    private $dbname;
    private $username;
    private $password;

    public function __construct($dbname, $username, $password) {
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        
        $this->connect();   
    }

    
    public function close() {
        $this->db = null;
    }

    public function connect() {
        try {
            return new PDO ("mysql:dbname=$this->dbname", "$this->username", "$this->password");
        } catch (PDOException $e) {
            $this->logsys .= "Failed to get DB handle: " . $e->getMessage() . "\n";
        }
    }

    public function exec($statement) {
      return $this->db->exec($statement);
    }

    public function query($statement) {
      return $this->db->query($statement);
    }

}
