<?php
//include_once 'config.php';
//include('config.php');	


/**
 * @author  Haymer Barbetti
 * @see https://github.com/
 * used to get mysql database connection
 */
class DatabaseService{
    
    private $conf;
    
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_password;
    private $connection;


    public function setConfig($configs) {
         $this->conf = $configs;
         $obj=json_decode($this->conf);
		 $this->db_host = $obj->host;
         $this->db_name = $obj->db_name;
         $this->db_user = $obj->username;
         $this->db_password= $obj->password;

         //echo  json_encode($obj);
         //echo  "  \n host: ".$this->db_host;
	}
    public function getConnection(){
        
        
        //$this->setConfig();    
        $this->connection = null;

        try{
            $this->connection = new PDO("mysql:host=" . $this->db_host . ";dbname=" . $this->db_name, $this->db_user, $this->db_password);
        }catch(PDOException $exception){
            echo "Connection failed: " . $exception->getMessage();
        }

        return $this->connection;
    }
}
?>