<?php
include_once 'config_db.php';

class GeneralModal extends DbConfig
{
    private $crud;
    public function __construct()
    {
        parent::__construct();
        $this->crud = new Crud();
    }
    public function insert_array($table, $insData)
    {
        $columns = implode(", ", array_keys($insData));
        foreach ($insData as $key => $value) {
            $escaped_values[] = $value;
        }
        $values  = implode("', '", $escaped_values);
        $sql = "INSERT INTO `$table`($columns) VALUES ('$values')";
        $result = $this->crud->insert_and_get_id($sql);
        if ($result != false) {
            return $result;
        } else {
            return false;
        }
    }
    public function update_array($table, $updateData, $condition)
    {
        $updateFields = [];
        foreach ($updateData as $key => $value) {
            // Escape the values appropriately
            $escaped_value = $value; // Adjust this if necessary to properly escape values
            $updateFields[] = "`$key` = '$escaped_value'";
        }
        $updateFieldsString = implode(", ", $updateFields);

        // Assuming the condition is a properly escaped string
        $sql = "UPDATE `$table` SET $updateFieldsString WHERE $condition";

        $result = $this->crud->execute($sql); // Assuming there is a method to execute the update query
        if ($result !== false) {
            return true;
        } else {
            return false;
        }
    }
}
