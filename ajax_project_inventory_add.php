<?php 
$module_name = 'inventory';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));
//$paged = (isset($_GET['paged']) && $_GET['paged']>1)?$_GET['paged']:1;

$addFlag=authorizeAccess($module_name,'add');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);
dd($_POST);

if(isset($_POST['addInventory']))
{
    if($addFlag)
    {
        $data = array();
        if (!empty($_POST['item'])) {
            $data['item'] =$crud->escape_string($_POST['item']);
        }
        if($_POST['item']=='other'){
            $data['item'] =$crud->escape_string($_POST['other_item']);
        }
        if (!empty($_POST['make'])) {
            $data['make'] =$crud->escape_string($_POST['make']);
        }
        if (!empty($_POST['model'])) {
            $data['model'] =$crud->escape_string($_POST['model']);
        }
        if (!empty($_POST['procurement_date'])) {
            $data['procurement_date'] =$crud->escape_string($_POST['procurement_date']);
        }
        if (!empty($_POST['procurement_date'])) {
            $data['procurement_date'] =$crud->escape_string($_POST['procurement_date']);
        }
        
        dd($data);
        $title=$crud->escape_string($_POST['title']);
        $address=$crud->escape_string($_POST['address']);
        $description=$crud->escape_string($_POST['description']);

        $focal_person=$crud->escape_string($_POST['focal_person']);
        $focal_person_email=$crud->escape_string($_POST['focal_person_email']);
        $focal_person_phone=$crud->escape_string($_POST['focal_person_phone']);
        
        $workstaions=(isset($_POST['workstaions']) && $_POST['workstaions']>0)?$crud->escape_string($_POST['workstaions']):0;
        $laptops=(isset($_POST['laptops']) && $_POST['laptops']>0)?$crud->escape_string($_POST['laptops']):0;
        $printers=(isset($_POST['printers']) && $_POST['printers']>0)?$crud->escape_string($_POST['printers']):0;
        $scanners=(isset($_POST['scanners']) && $_POST['scanners']>0)?$crud->escape_string($_POST['scanners']):0;

        $total_staff=(isset($_POST['total_staff']) && $_POST['total_staff']>0)?$crud->escape_string($_POST['total_staff']):0;
        $latitude=$crud->escape_string($_POST['latitude']);
        $longitude=$crud->escape_string($_POST['longitude']);      

        $msg = $validation->check_empty($_POST, array(array('title','Center Title'),array('address','Address'),array('focal_person','Focal Person'))); 

        if($msg != null) {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Please correct the following errors:<br>'.$msg.'
                                </div>';
        }    
        else {
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
                                
                            $imagefile =$folder_name."/center_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                            if(move_uploaded_file($file["tmp_name"],$imagefile))
                            {
                                $imagefiles[] = $imagefile;
                            }
                        }
                    }
                }
            }
            $fimage='';
            if(!empty($_FILES['fimage']))
            {
                $file = $_FILES['fimage']; 
                $file_name = $file['name'];
                @$file_ext=strtolower(end(explode('.',$file_name)));
                @$mimetype = mime_content_type($_FILES['fimage']['tmp_name']);


                $expensions= array("jpeg","jpg","png");
                $mimes = array('image/jpeg',  'image/png');
                
                if(!empty($file_ext) && !empty($file_name)  && !empty($mimetype) && in_array($file_ext,$expensions) && in_array($mimetype,$mimes) ){
                    $folder_name='uploads/project_images';
                        
                    $imagefile =$folder_name."/center_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                    if(move_uploaded_file($file["tmp_name"],$imagefile))
                    {
                        $fimage = $imagefile;
                    }
                }
            }

            $sql="INSERT INTO centers(title,address,description,focal_person,focal_person_email,focal_person_phone,workstaions,laptops,printers,scanners,total_staff,latitude,longitude,fimage,images,added_by,project_id,department_id) values('$title','$address','$description','$focal_person','$focal_person_email','$focal_person_phone','$workstaions','$laptops','$printers','$scanners','$total_staff','$latitude','$longitude','$fimage','".implode(",",$imagefiles)."','".$_SESSION['id']."','$pid','".$_SESSION['department_id']."')";
            
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {
                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project center created !!
                                    </div>';
                                    
                $crud->log('Center('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 Unable to create new center.
                                </div>';
            }
        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to add center
                                </div>';
    }
}

echo json_encode($return);