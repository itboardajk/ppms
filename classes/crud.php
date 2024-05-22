<?php
include_once 'config_db.php';
 
class Crud extends DbConfig
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getData($query)
    {        
        $result = $this->connection->query($query);
        
        if ($result == false) {
            return false;
        } 
        
        $rows = array();
        
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        
        return $rows;
    }
    public function insert_and_get_id($query) 
    {
        $result = $this->connection->query($query);
        
        if ($result == false) {
            echo 'Error: cannot execute the command ('. $this->connection->error.')';
            return false;
        } else {
            return $this->connection->insert_id;
        }        
    }
        
    public function execute($query) 
    {
        $result = $this->connection->query($query);
        
        if ($result == false) {
            echo 'Error: cannot execute the command ('. $this->connection->error.')';
            return false;
        } else {
            return true;
        }        
    }
     
    public function log($details) 
    {
        $this->execute("INSERT INTO log(log_by,details) VALUES('".$_SESSION['id']."','$details')"); 
    }

    public function escape_string($value)
    {
        return $this->connection->real_escape_string($value);
    }
    
    public function escape_string_array($value)
    {
        foreach($value as $key=>$val)
        {   
            if(is_array($val))
                $value[$key] = $this->escape_string_array($val);
            else
                $value[$key]= $this->escape_string($val);
        }
        return $value;
    }
}