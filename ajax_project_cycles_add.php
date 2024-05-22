<?php 
$module_name = 'project_cycles';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));
//$paged = (isset($_GET['paged']) && $_GET['paged']>1)?$_GET['paged']:1;

$addFlag=authorizeAccess($module_name,'add');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_POST['addCycle']))
{
    if($addFlag)
    {
        $title=$crud->escape_string($_GET['cycle']);
        $type=$crud->escape_string($_POST['pc_type']);
        if($type == 'Auto')
        {
            $details = $crud->escape_string_array($_POST['details']);
        }
        else if($type == 'Manual')
        {
            $attachment_error='';
            $uimagefile='';
            if(!empty($_FILES['filesToUpload']))
            {
                $file = $_FILES['filesToUpload'];

                      
                $file_name = $file['name'];
                @$file_ext=strtolower(end(explode('.',$file_name)));
                @$mimetype = mime_content_type($file['tmp_name']);


                $expensions= array("pdf","doc","docx");
                //array("jpeg","jpg","png");
                $mimes = array('application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document');


                if(!empty($file_ext) && !empty($file_name)  && !empty($mimetype) && in_array($file_ext,$expensions) && in_array($mimetype,$mimes)) 
                {
                    $folder_name='uploads/project_images';
                        
                    $imagefile =$folder_name."/ProjectCycle_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                    if(move_uploaded_file($file["tmp_name"],$imagefile))
                    {
                        $uimagefile = $imagefile;
                        $details = array('url'=>$uimagefile);
                    }
                }
                else
                {
                    $attachment_error = 'Invlaid file formate, Please upload PDF or Word document.';
                }
            }
        }
        
        if(empty($attachment_error))
        {
            $sql="INSERT INTO project_cycles(title,type,defaults,added_by,project_id,department_id) values('$title','$type','".json_encode($details)."','".$_SESSION['id']."','$pid','".$_SESSION['department_id']."')";
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {
                $crud->execute("INSERT INTO authorities(`label`, `designation`, `name`,`sign`, `status`,  `type`, `ref_id`, `dep_id`, `admin_id`,`added_by`) values('Created By','".$_SESSION['ppmsRoleName']."','".$_SESSION['aname']."','','-1','PC','$result','".$_SESSION['department_id']."','".$_SESSION['id']."','".$_SESSION['id']."')");


                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      '.$title.' created !!
                                    </div>';
                                    
                $crud->log($title.'('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 Unable to create new '.$title.'.
                                </div>';
            }
        }
        else
        {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                             '.$attachment_error.'
                            </div>';

        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to add Project Cycles
                                </div>';
    }
}

echo json_encode($return);