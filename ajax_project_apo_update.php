<?php 
$module_name = 'APO';
include('classes/config.php');
authenticate_ajax();

$editFlag=authorizeAccess($module_name,'edit');
$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);
if(isset($_GET['initiateAPO']) && isset($_GET['updateAPO']) && $_GET['updateAPO']>0)
{
    $apo_id=intval($crud->escape_string($_GET['updateAPO']));
    $editapos_details=$crud->getData("SELECT apo.*,departments.title as departmentName,projects.title as projectName 
                                        from apo   
                                            left join departments ON apo.department_id=departments.id
                                            left join projects ON apo.project_id=projects.id
                                             where apo.id = $apo_id");
    if($editapos_details != false && count($editapos_details)>0)
    {
        $editapos_details = $editapos_details[0];

        $auth_details=$crud->getData("SELECT * from authorities where type='APO' and ref_id = ".$apo_id." order by sort_order ASC");
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
                    $sql="UPDATE apo set level=1,updated_by='".$_SESSION['id']."' where id=".$apo_id;             
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
                                $AJKEmail = new AJKEmail('APO Approval Needed',array(array($user['display_name'],$user['email'])));
                                $AJKEmail->email_body($user['display_name'],$_SESSION['aname'].'<br>'.$_SESSION['ppmsRoleName'].'<br>'.$_SESSION['department_name'],'An APO has been marked to you for approvel please find details below:<br>
                                    APO: '.$editapos_details['apo'].'<br>
                                    Project: '.$editapos_details['projectName'].'<br>
                                    Department: '.$editapos_details['departmentName'].'<br>
                                    <a href="https://ppms.ajk.gov.pk/print_apo.php?apo_id='.$editapos_details['id'].'">Click Here</a> to see the APO<br>'
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
                                          Project APO Initiated, and you will not be able to edit this APO!!
                                        </div>';
                    }
                    else
                    {
                        $return['msg']='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Upanle to initiate the APO, Please try again.
                                </div>';
                    }
                }
                else{
                    $return['msg']='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  <a href="profile.php" >Upload your Signature</a> to initiate the APO. Make sure to save your changes.
                                </div>';
                }
            }
            else{
                $return['msg']='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Only first authority can initiate the APO.
                                </div>';

            }
        }
        else{
            $return['msg']='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  There should be 2 or more authorties to initiate the APO.
                                </div>';
        }
    }
    else
    {
        $return['msg']='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              APO not found.
                            </div>';

    }
    
}
else if(isset($_POST['editAPO']) && isset($_GET['updateAPO']) && $_GET['updateAPO']>0)
{
    if($editFlag)
    {
		$apo=$crud->escape_string($_POST['apo']);
        $allocation=$crud->escape_string($_POST['allocation']);

        $apo_id=intval($crud->escape_string($_GET['updateAPO']));
        $msg = $validation->check_empty($_POST, array(array('apo','APO'),array('allocation','Allocation')));
        
        if($msg != null) {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Please correct the following errors:<br>'.$msg.'
                            </div>';
        }    
        else {
            
            $attachment_error='';
            $uimagefile='';
            if(!empty($_FILES['filesToUpload']) && !empty($_FILES['filesToUpload']['name']))
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
                        
                    $imagefile =$folder_name."/ProjectAPO_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                    if(move_uploaded_file($file["tmp_name"],$imagefile))
                    {
                        $uimagefile = ",file='".$imagefile."'";

                        $prev_fimage=$crud->escape_string($_POST['prev_fimage']);
                        if(!empty($prev_fimage))
                        {
                            @unlink($prev_fimage);
                        }
                    }
                }
                else
                {
                    $attachment_error = 'Invlaid file formate, Please upload PDF or Word document.';
                }
            }

            if(empty($attachment_error))
            {

                $sql="UPDATE apo set apo='$apo',allocation='$allocation',updated_by='".$_SESSION['id']."' $uimagefile where id=".$apo_id;
                 
                $result = $crud->execute($sql);

                if($result != false)
                {
                    $crud->execute("DELETE FROM apo_heads WHERE apo_id=".$apo_id);

                    foreach($_POST['subheads'] as $subhead_id)
                    {

                        if(isset($_POST['apo_allocation']) && !empty($_POST['apo_allocation'][$subhead_id]))
                            @$apo_allocation=$crud->escape_string($_POST['apo_allocation'][$subhead_id]);
                        else
                            $apo_allocation=0;

                        if(isset($_POST['apo_qty']) && !empty($_POST['apo_qty'][$subhead_id]))
                            @$apo_qty=$crud->escape_string($_POST['apo_qty'][$subhead_id]);
                        else
                            $apo_qty=0;
                        
                        if(isset($_POST['prog_qty']) && !empty($_POST['prog_qty'][$subhead_id]))
                            @$prog_qty=$crud->escape_string($_POST['prog_qty'][$subhead_id]);
                        else
                            $prog_qty=0;

                        if(isset($_POST['prog_expences']) && !empty($_POST['prog_expences'][$subhead_id]))
                            @$prog_expences=$crud->escape_string($_POST['prog_expences'][$subhead_id]);
                        else
                            $prog_expences=0;

                        if(isset($_POST['prog_status']) && !empty($_POST['prog_status'][$subhead_id]))
                            @$prog_status=$crud->escape_string($_POST['prog_status'][$subhead_id]);
                        else
                            $prog_status='';



                        $isql="INSERT INTO apo_heads(apo_id,head_id,prog_qty,prog_expences,prog_status,quantity,revised) values('$apo_id','$subhead_id','$prog_qty','$prog_expences','$prog_status','$apo_qty','$apo_allocation')";
                        $crud->execute($isql);

                    }   

                    //Update Authorities
                    if(isset($_POST['label']) && count($_POST['label'])>0)
                    {
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
                                    $auth_query = "INSERT INTO authorities(`label`, `designation`, `name`, `status`,  `type`, `ref_id`, `dep_id`, `admin_id`,`added_by`,sort_order) values('".$_POST['label'][$count]."','".$designation."','".$display_name."','-1','APO','$apo_id','".$_POST['auth_dep'][$count]."','".$_POST['auth_user'][$count]."','".$_SESSION['id']."','$count')";

                                }
                            }
                            //echo $auth_query.'<br>';
                            if(!empty($auth_query))
                                $crud->execute($auth_query);

                            $count++;
                        }

                    }

                    if(isset($_POST['auth_del']) && count($_POST['auth_del'])>0)
                    {
                        $auth_query = "DELETE from authorities where id IN (".implode(',',$_POST['auth_del']).")";
                        $crud->execute($auth_query);
                    }


                    $return['status']=true;
                    $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                          Project APO updated !!
                                        </div>';


                    $crud->log('APO('.$result.') updated',$_SESSION['id']);
                }
                else
                {
                    $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Unable to edit APO.
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
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  You do not have access to edit APO.
                                </div>';
    }
}
echo json_encode($return);