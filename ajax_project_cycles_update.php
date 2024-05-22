<?php 
$module_name = 'project_cycles';
include('classes/config.php');
authenticate_ajax();

//$pid=intval($crud->escape_string($_GET['pid']));
//$paged = (isset($_GET['paged']) && $_GET['paged']>1)?$_GET['paged']:1;

$editFlag=authorizeAccess($module_name,'edit');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);
if(isset($_GET['initiateCycle']) && isset($_GET['updateCycle']) && $_GET['updateCycle']>0)
{
    $title=$crud->escape_string($_GET['cycle']);
    $cycle_id=$crud->escape_string($_GET['updateCycle']);

    $editcycle_details=$crud->getData("SELECT project_cycles.*,departments.title as departmentName,projects.title as projectName 
                                        from project_cycles   
                                            left join departments ON project_cycles.department_id=departments.id
                                            left join projects ON project_cycles.project_id=projects.id
                                             where project_cycles.id = $cycle_id");

    if($editcycle_details != false && count($editcycle_details)>0)
    {
        $editcycle_details = $editcycle_details[0];

        $auth_details=$crud->getData("SELECT * from authorities where type='PC' and ref_id = ".$cycle_id." order by sort_order ASC");
        $first_authority =0;
        $second_authority=0;
        if($auth_details != false && count($auth_details)>0)
        {   
            foreach($auth_details as $auth)
            {
                if($first_authority == 0)
                {
                    $first_authority = $auth;
                }
                else if($second_authority == 0)
                {
                    $second_authority = $auth;
                }
            }
        }

        $return['status']=false;

        if(count($auth_details)>1){ 
            if($first_authority['admin_id'] == $_SESSION['id']){
                if(isset($_SESSION['asign']) && !empty($_SESSION['asign']) && file_exists($_SESSION['asign'])){
                    $sql="UPDATE project_cycles set level=1,updated_by='".$_SESSION['id']."' where id=".$cycle_id;             
                    $result = $crud->execute($sql);


                    $sql = "UPDATE authorities set sign='".$_SESSION['asign']."', signed_date='".date("Y-m-d")."',status=1 where id=".$first_authority['id'];
                    $result = $crud->execute($sql);

                    $sql = "UPDATE authorities set status=0 where id=".$second_authority['id'];
                    $result = $crud->execute($sql);

                    if($result != false)
                    {
                        //Send Email to Second Authority to approve APO
                        $query ="SELECT admin.* FROM admin WHERE id=".$second_authority['admin_id']." limit 1";

                        $user = $crud->getData($query);
                        if($user != false && count($user)>0)
                        {            

                            $user=$user[0];
                            if(!empty($user['email']))
                            {                            
                                $AJKEmail = new AJKEmail($title.' Approval Needed',array(array($user['display_name'],$user['email'])));
                                $AJKEmail->email_body($user['display_name'],$_SESSION['aname'].'<br>'.$_SESSION['ppmsRoleName'].'<br>'.$_SESSION['department_name'],'A '.$title.' has been marked to you for approvel please find details below:<br>
                                    Type: '.$title.'<br>
                                    Project: '.$editcycle_details['projectName'].'<br>
                                    Department: '.$editcycle_details['departmentName'].'<br><br>
                                    <a href="https://ppms.ajk.gov.pk/project_cycles_print.php?pid='.$editcycle_details['project_id'].'&cycle='.$title.'&cycle_id='.$editcycle_details['id'].'">Click Here</a> to see the '.$title.'<br>'
                                );
                                $resp = $AJKEmail->send();
                                if(!$resp['status'])
                                {
                                    $crud->log('Email not sent:'.$resp['message'],$_SESSION['id']);
                                }
                            }
                        }

                        $return['status']=true;
                        $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                          Project '.$title.' Initiated, and you will not be able to edit this '.$title.'!!
                                        </div>';
                    }
                    else
                    {
                        $return['msg']='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Upanle to initiate the '.$title.', Please try again.
                                </div>';
                    }
                }
                else{
                    $return['msg']='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  <a href="profile.php" >Upload your Signature</a> to initiate the '.$title.'. Make sure to save your changes.
                                </div>';
                }
            }
            else{
                $return['msg']='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Only first authority can initiate the '.$title.'.
                                </div>';

            }
        }
        else{
            $return['msg']='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  There should be 2 or more authorties to initiate the '.$title.'.
                                </div>';
        }
    }
    else
    {
        $return['msg']='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              '.$title.' not found.
                            </div>';

    }
    
}
if(isset($_POST['updateCycle']))
{
    if($editFlag)
    {
        $title=$crud->escape_string($_GET['cycle']);
        $cycle_id=$crud->escape_string($_POST['cycle_id']);
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
          $sql="UPDATE project_cycles set title='$title',defaults='".json_encode($details)."',updated_by='".$_SESSION['id']."' where id=".$cycle_id;
         
          $result = $crud->execute($sql);

          if($result != false)
          {
            //Update Authorities
            $count=0;

            foreach($_POST['label'] as $value)
            {
                $auth_query="";

                
                if(!empty($_POST['label'][$count]) && !empty($_POST['auth_dep'][$count]) && !empty($_POST['auth_user'][$count]))
                {
                    $display_name = $_SESSION['ppmsRoleName'];
                    $designation = $_SESSION['aname'];

                    if($_POST['auth_user'][$count] != $_SESSION['id'])
                    {
                        $iquery ="SELECT admin.display_name, roles.title FROM admin left join roles on admin.role=roles.id WHERE admin.id = ".$_POST['auth_user'][$count]." limit 1";

                        $usert = $crud->getData($iquery);
                        $display_name = $usert[0]['title'];
                        $designation = $usert[0]['display_name'];
                    }

                    if(isset($_POST['auth_id'][$count]) && $_POST['auth_id'][$count]>0)
                    {
                        $auth_query = "UPDATE authorities set label='".$_POST['label'][$count]."',designation='".$designation."',name='".$display_name."',sign='',signed_date=NULL,status='-1',dep_id='".$_POST['auth_dep'][$count]."',admin_id='".$_POST['auth_user'][$count]."',sort_order='$count',updated_by='".$_SESSION['id']."' where id=".$_POST['auth_id'][$count];
                    } 
                    else
                    {
                        $auth_query = "INSERT INTO authorities(`label`, `designation`, `name`, `status`,  `type`, `ref_id`, `dep_id`, `admin_id`,`added_by`,sort_order) values('".$_POST['label'][$count]."','".$designation."','".$display_name."','-1','PC','$cycle_id','".$_POST['auth_dep'][$count]."','".$_POST['auth_user'][$count]."','".$_SESSION['id']."','$count')";

                    }
                }
                //echo $auth_query.'<br>';
                if(!empty($auth_query))
                    $crud->execute($auth_query);

                $count++;
            }

            if(isset($_POST['auth_del']) && count($_POST['auth_del'])>0)
            {
                $auth_query = "DELETE from authorities where id IN (".implode(',',$_POST['auth_del']).")";
                $crud->execute($auth_query);
            }
            $return['status']=true;
            $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  '.$title.' Updated !!
                                </div>';
                                
            $crud->log($title.'('.$result.') Added',$_SESSION['id']);
          }
          else
          {
              $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                               Unable to update '.$title.'.
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
                                 You do not have access to update Project Cycles.
                                </div>';
    }
}

echo json_encode($return);