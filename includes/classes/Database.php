<?php

class Database{
    private $database = Array( // DB connection info
        "server" => "localhost",
        "username" => "calorie_db",
        "password" => "ilikecalories",
        "database" => "calorie_db"
    );

    public function connect(){
        $con = new mysqli(
            $this->database["server"],
            $this->database["username"],
            $this->database["password"],
            $this->database["database"]
        );
        if($con->connect_errno){
            print "Error: something went wrong connecting to the database";
            die();
        }
        else{  
            return $con;
        }
    }

}


?>