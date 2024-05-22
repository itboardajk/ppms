<?php 
$module_name = 'expenses';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));
//$paged = (isset($_GET['paged']) && $_GET['paged']>1)?$_GET['paged']:1;

$addFlag=authorizeAccess($module_name,'add');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_POST['addExpenses']))
{
    if($addFlag)
    {
        $title=$crud->escape_string($_POST['title']);
        $cost=$crud->escape_string($_POST['cost']);
        $head_id=$crud->escape_string($_POST['head_id']);
        $details=$crud->escape_string($_POST['details']);

        $status=$crud->escape_string($_POST['status']);
        $release_date=$crud->escape_string($_POST['release_date']);
        if($status == 'Released')
        {
            $msg = $validation->check_empty($_POST, array(array('title','Expense Title'),array('cost','Cost'),array('head_id','Expense Head'),array('release_date','Expense Release Date')));
        }
        else
        {
           $msg = $validation->check_empty($_POST, array(array('title','Expense Title'),array('cost','Cost'),array('head_id','Expense Head'))); 
        }


        if($msg != null) {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Please correct the following errors:<br>'.$msg.'
                                </div>';
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
                                
                            $imagefile =$folder_name."/expense_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                            if(move_uploaded_file($file["tmp_name"],$imagefile))
                            {
                                $imagefiles[] = $imagefile;
                            }
                        }
                    }
                }
            }
            $head_id = explode(':', $head_id);
            $head_id = $head_id[0];
            if($status == 'Released')
            {
                $sql="INSERT INTO expenses(title,cost,details,status,release_date,head_id,images,added_by,project_id,department_id) values('$title','$cost','$details','$status','$release_date','$head_id','".implode(",",$imagefiles)."','".$_SESSION['id']."','$pid')";
            }
            else
            {
                $sql="INSERT INTO expenses(title,cost,details,status,head_id,images,added_by,project_id,department_id) values('$title','$cost','$details','$status','$head_id','".implode(",",$imagefiles)."','".$_SESSION['id']."','$pid','".$_SESSION['department_id']."')";
            }
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {

                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project expenses created !!
                                    </div>';
                                    
                $crud->log('Expenses('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 Unable to create new expenses.
                                </div>';
            }
        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to add expenses
                                </div>';
    }
}

echo json_encode($return);