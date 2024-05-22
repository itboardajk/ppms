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
        $msg = $validation->check_empty($_POST, [['username', 'Username'], ['password', 'Password']]);
        if ($msg != null) {
            $response['message'] = 'Please correct the following errors:<br>' . $msg;
            http_response_code(400); 
        } else {
            $username = $crud->escape_string($_POST['username']);
            $password = md5($crud->escape_string($_POST['password']));

            // SQL query to check the user credentials
            $query = "
                SELECT admin.*, roles.accesses, roles.title as role_name, roles.parent_id as role_parent, departments.title as department_name, departments.focal_person as dfp
                FROM admin
                LEFT JOIN roles ON admin.role = roles.id
                LEFT JOIN departments ON admin.department_id = departments.id
                WHERE (admin.username = '$username' OR admin.email = '$username' OR admin.cnic = '$username') AND admin.password = '$password'
                LIMIT 1";

            $result = $crud->getData($query);
            if ($result != false && count($result) > 0) {
                $user = $result[0];
                if ($user['status'] == 0) {
                    $response['message'] = "Your account has been disabled, please contact administrator.";
                    http_response_code(403); 
                } else {
                    $response['token'] = $user['id'];
                    $response['data'] = [
                        'id' => $user['id'],
                        'username' => $username,
                        'display_name' => $user['display_name'],
                        'role' => $user['role_name'],
                        'department' => $user['department_name'],
                        'department_id' => $user['department_id'],
                        'last_login' => $user['last_login']
                    ];
                    http_response_code(200); 
                }
            } else {
                $response['message'] = "Invalid username or password";
                http_response_code(401); 
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
