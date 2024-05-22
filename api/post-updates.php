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

function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}


if(isset($_POST))
{

        $details=$crud->escape_string($_POST['details']);
        $msg = $validation->check_empty($_POST, array(array('details','Update Details')));
        $project_id=intval($crud->escape_string($_POST['project_id']));
        $department_id=intval($crud->escape_string($_POST['department_id']));
        $user_id=intval($crud->escape_string($_POST['added_by']));
        $deliverable_id=intval($crud->escape_string($_POST['deliverable_id']));

        if($msg != null) {
            $return['msg'] = 'Please correct the following errors:<br>'.$msg;
            http_response_code(400); 
        }    
        else {
            $attachment_error='';
            $imagefiles=array();
            if(!empty($_FILES['filesToUpload']))
            {
                $file_ary = reArrayFiles($_FILES['filesToUpload']);
                if(!empty($file_ary[0]["name"]))
                {
                    $imageLinks='';
                    foreach($file_ary as $file)
                    {
                      
                        $file_name = $file['name'];
                        @$file_ext=strtolower(end(explode('.',$file_name)));
                        @$mimetype = mime_content_type($file['tmp_name']);

                       
                        $expensions= array("jpg","jpeg","png","mp3","mp4","pdf","doc","docx","xls","xlsx","ppt","pptx");
                        //array("jpeg","jpg","png");
                        $mimes = array('image/jpeg', 'image/png','audio/mpeg','video/mp4','audio/mp4','application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation');


                        if(!empty($file_ext) && !empty($file_name)  && !empty($mimetype) && in_array($file_ext,$expensions) && in_array($mimetype,$mimes)) //,"mp4","mp3","webm"
                        {
                            $folder_name='uploads/project_images';
                                
                            $imagefile =$folder_name."/update_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                            
                            if(move_uploaded_file($file["tmp_name"],'../'.$imagefile))
                            {
                                $imagefiles[] = $imagefile;
                            }
                        }
                    }
                }
            }

            $sql="INSERT INTO updates(details,images,added_by,project_id,department_id,deliverable_id) values('$details','".implode(",",$imagefiles)."','".$user_id."','$project_id','".$department_id."','".$deliverable_id."')";
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {

                $return['data']=$result;
                http_response_code(201); 
            }
            else
            {
                $return['msg'] = 'Unable to create new update.';
                http_response_code(400); 
            }
        }

    
}

echo json_encode($return);