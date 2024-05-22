<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Include configuration and classes
include("../classes/crud.php");
include("../classes/validation.php");

$crud = new Crud();
$validation = new Validation();

// Define the response array
$response = [
    'data' => null
];

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Check if the required fields are provided
        $msg = $validation->check_empty($_POST, [['project_id','project_id']]);
        if ($msg != null) {
            $response['message'] = 'Please correct the following errors:<br>' . $msg;
            http_response_code(400); 
        } else {
            $project_id = $crud->escape_string($_POST['project_id']);
           

            // SQL query to check the user credentials
            $query = "SELECT updates.*, admin.display_name FROM updates LEFT JOIN admin ON admin.id = updates.added_by WHERE updates.project_id = '$project_id'";

            $result = $crud->getData($query);
            if ($result != false && count($result) > 0) {
                    $response['data'] = $result;
                    http_response_code(200); 
                
            } else {
                 $response['data'] = [];
                http_response_code(200); 
            }
        }
    } catch (Exception $e) {
        $response['message'] = 'An error occurred during the process: ' . $e->getMessage();
        http_response_code(500);
    }
} else {
    $response['message'] = 'Invalid request method.';
    http_response_code(405);
}

// Set header to JSON
header('Content-Type: application/json');

// Send JSON response
echo json_encode($response);
?>
