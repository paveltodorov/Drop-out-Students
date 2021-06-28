<?php

class ConnectToDB
{
  public $host   = "localhost";
  public $db     = "drop out students"; 
  public $user   = "root"; 
  public $pass   = "";
  public $connToDB;

  public function __construct()
  {
    try
    {
     $this -> connToDB = new PDO("mysql:host=".$this -> host.";dbname=".$this -> db,$this -> user,$this -> pass);
    }
   catch (PDOException $e) 
   {   
    echo 'Connection failed: ' .$e->getMessage(); 
   }
 }
}
?>