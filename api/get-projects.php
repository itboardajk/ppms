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
        $msg = $validation->check_empty($_POST, [['department_id','department_id']]);
        if ($msg != null) {
            $response['message'] = 'Please correct the following errors:<br>' . $msg;
            http_response_code(400); 
        } else {
            $department_id = $crud->escape_string($_POST['department_id']);
           

            // SQL query to check the user credentials
            $query = "
                SELECT * FROM projects
                WHERE department_id = '$department_id'";

            $result = $crud->getData($query);
            if ($result != false && count($result) > 0) {
                
                
                   
                 $projects = [];

    foreach ($result as $project) {
        $project_id = $project['id'];

        // Query to get all deliverables for the given project_id
        $deliverables_query = "
            SELECT * FROM deliverables
            WHERE project_id = '$project_id'";

        $deliverables_result = $crud->getData($deliverables_query);

        if ($deliverables_result != false && count($deliverables_result) > 0) {
            $project['deliverables'] = $deliverables_result;
        } else {
            $project['deliverables'] = [];
        }

        $projects[] = $project;
    }
                    $response['data'] = $projects;
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
