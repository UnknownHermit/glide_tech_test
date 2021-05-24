<?php
/**
 * Incredibly barebones SQL class used to connect to the calorie database
 */
class Database{
    private $database = Array( // DB connection info
        "server" => "localhost",
        "username" => "calorie_db",
        "password" => "ilikecalories",
        "database" => "calorie_db"
    );

    private $db; // Our connection

    public function __construct(){
        $this->db = new mysqli(
            $this->database["server"],
            $this->database["username"],
            $this->database["password"],
            $this->database["database"]
        );

        if($this->db->connect_errno){
            print "Error: something went wrong connecting to the database";
            die();
        }
    }


    /**
     * Runs an SQL query
     */
    public function query($sql){
        return $this->db->query($sql);
    }

    /**
     * Escapes a string for insertion into the database
     */
    public function escape_string($string){
        return $this->db->real_escape_string($string);
    }

    /**
     * Returns the last inserted id this connection created
     */
    public function insert_id(){
        return $this->db->insert_id;
    }

    /**
     * Returns the numrows from the provided sql result object
     */
    public function num_rows($sqlResult){
        return $sqlResult->num_rows;
    }

    /**
     * Returns all data from the provided sql result object
     */
    public function fetch_assoc($sqlResult){
        $data = Array();
        while($row = $sqlResult->fetch_assoc()){
            $data[] = $row; 
        }
        return $data;
    }

}


?>