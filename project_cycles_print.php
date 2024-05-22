<?php
$module_name = 'projects';
include('classes/config.php');
authenticate();

$pid=intval($crud->escape_string($_GET['pid']));
$cycle=$crud->escape_string($_GET['cycle']);
$cycle_id=$crud->escape_string($_GET['cycle_id']);


$alert='';

if(isset($_GET['movePC']) && $_GET['movePC']>=1)
{

    if(!isset($_SESSION['asign']) || empty($_SESSION['asign']) || !file_exists($_SESSION['asign'])){
        $alert= '<div style="with:100%;margin:20px 0;"><div class="alert alert-error"><a href="profile.php" >Upload your Signature</a> to Approve the '.$cycle.' or Return with Remarks.</div></div>';
    }
    else
    {
        $level=intval($crud->escape_string($_GET['movePC']));
        
        $auth_details=$crud->getData("SELECT * from authorities where type='PC' and ref_id = ".$cycle_id." and status<>1 order by sort_order ASC");
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
                    break;
                }
            }
        }

        if($first_authority != 0)
        {
            if($first_authority['admin_id'] == $_SESSION['id'])
            {
                $sql="UPDATE project_cycles set level='".$level."',updated_by='".$_SESSION['id']."' where id=".$cycle_id;             
                $result = $crud->execute($sql);

                $sql = "UPDATE authorities set sign='".$_SESSION['asign']."', signed_date='".date("Y-m-d")."',status=1 where id=".$first_authority['id'];
                $result = $crud->execute($sql);
            }
        }
        if($second_authority != 0)
        {
            $editCycle_details=$crud->getData("SELECT project_cycles.*,departments.title as departmentName,projects.title as projectName 
                                        from project_cycles   
                                            left join departments ON project_cycles.department_id=departments.id
                                            left join projects ON project_cycles.project_id=projects.id
                                             where project_cycles.id = $cycle_id");
            
            $editCycle_details = $editCycle_details[0];

            $sql = "UPDATE authorities set status=0 where id=".$second_authority['id'];
            $result = $crud->execute($sql);

            if($result != false)
            {
                //Send Email to Second Authority to approve PC
                $query ="SELECT admin.* FROM admin WHERE id=".$second_authority['admin_id']." limit 1";

                $user = $crud->getData($query);
                if($user != false && count($user)>0)
                {            

                    $user=$user[0];
                    if(!empty($user['email']))
                    {                            
                        $AJKEmail = new AJKEmail($cycle.' Approval Needed',array(array($user['display_name'],$user['email'])));
                        $AJKEmail->email_body($user['display_name'],$_SESSION['aname'].'<br>'.$_SESSION['ppmsRoleName'].'<br>'.$_SESSION['department_name'],'A  '.$cycle.' has been marked to you for approvel please find details below:<br>
                            Type: '. $cycle.'<br>
                            Project: '.$editCycle_details['projectName'].'<br>
                            Department: '.$editCycle_details['departmentName'].'<br><br>
                            <a href="https://ppms.ajk.gov.pk/project_cycles_print.php?pid='.$editCycle_details['project_id'].'&cycle='.$cycle.'&cycle_id='.$editCycle_details['id'].'">Click Here</a> to see the '.$cycle.'<br>'

                        );
                        $resp = $AJKEmail->send();
                        if(!$resp['status'])
                        {
                            $crud->log('Email not sent:'.$resp['message'],$_SESSION['id']);
                        }
                    }
                }

                $alert='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                   '.$cycle.' Moved Forward, and you will not be able to access this  '.$cycle.'!!
                                </div>';
            }
            else
            {
                $alert='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                          Unable to sign the  '.$cycle.', Please try again.
                        </div>';
            }
            header('location: approvals.php?sucmsg= '.$cycle.' hasn been forwarded.');
            exit;
        }
        else
        {
            header('location: approvals.php?sucmsg= '.$cycle.' hasn been Approved.');
            exit;
        }

    }
}
if(isset($_POST['addRemark']) && !empty($_POST['addRemark']))
{
    if(!isset($_SESSION['asign']) || empty($_SESSION['asign']) || !file_exists($_SESSION['asign'])){
        $alert= '<div style="with:100%;margin:20px 0;"><div class="alert alert-error"><a href="profile.php" >Upload your Signature</a> to Approve the  '.$cycle.' or Return with Remarks.</div></div>';
    }
    else
    {
        $remarks=$crud->escape_string($_POST['remarks']);
        $level=$crud->escape_string($_POST['level']);
        $auth_id_remarks=$crud->escape_string($_POST['auth_id_remarks']);
        $auth_id_remarks_pre=$crud->escape_string($_POST['auth_id_remarks_pre']);
        $user_id_return_to=$crud->escape_string($_POST['user_id_return_to']);

        $isql="INSERT INTO remarks(type,ref_id,designation,name,department,remarks,added_by ) values('PC','$cycle_id','".$_SESSION['ppmsRoleName']."','".$_SESSION['aname']."','".$_SESSION['department_name']."','$remarks','".$_SESSION['id']."')";

        $res = $crud->execute($isql);
        if($res != false)
        {

            if($user_id_return_to>0)
            {
                $sql="UPDATE project_cycles set level=".($level-2).",updated_by='".$_SESSION['id']."' where id=".$cycle_id;             
                $result = $crud->execute($sql);
                
                $sql = "UPDATE authorities set sign='', signed_date=NULL,status=2 where id=".$auth_id_remarks;
                $result = $crud->execute($sql);

                $sql = "UPDATE authorities set status=0 where id=".$auth_id_remarks_pre;
                $result = $crud->execute($sql);

                $query ="SELECT admin.* FROM admin WHERE id=".$user_id_return_to." limit 1";

                $user = $crud->getData($query);
                if($user != false && count($user)>0)
                {            

                    $user=$user[0];
                    if(!empty($user['email']))
                    {  

                        $editCycle_details=$crud->getData("SELECT project_cycles.*,departments.title as departmentName,projects.title as projectName 
                                                    from project_cycles   
                                                        left join departments ON project_cycles.department_id=departments.id
                                                        left join projects ON project_cycles.project_id=projects.id
                                                         where project_cycles.id = $cycle_id");
                        
                        $editCycle_details = $editCycle_details[0];

                        $AJKEmail = new AJKEmail($cycle.' Rejected!',array(array($user['display_name'],$user['email'])));

                        $AJKEmail->email_body($user['display_name'],$_SESSION['aname'].'<br>'.$_SESSION['ppmsRoleName'].'<br>'.$_SESSION['department_name'],'An '.$cycle.' has been rejected with remarks. Please see details below:<br>
                            Type: '.$cycle.'<br>
                            Project: '.$editCycle_details['projectName'].'<br>
                            Department: '.$editCycle_details['departmentName'].'<br>
                            Remarks: '.$remarks.'<br>
                            <a href="https://ppms.ajk.gov.pk/project_cycles_print.php?pid='.$editCycle_details['project_id'].'&cycle='.$cycle.'&cycle_id='.$editCycle_details['id'].'">Click Here</a> to see the '.$cycle.'<br>'
                        );
                        $resp = $AJKEmail->send();
                        if(!$resp['status'])
                        {
                            $crud->log('Email not sent:'.$resp['message'],$_SESSION['id']);
                        }
                    }
                }

                $alert='<div class="alert alert-success alert-dismissible fade in" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                      Remark has been saved and '.$cycle.' has been sent back for corrections.
                    </div>';
            }
            else
            {
                $alert='<div class="alert alert-success alert-dismissible fade in" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                      Remark has been saved.
                    </div>';
            }
        }
        else
        {
            $alert='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                      Unable to save the remarks, Please try again.
                    </div>';

        }
    }
}




$approvals = $crud->getData("SELECT * FROM authorities where admin_id=".$_SESSION['id']." and status<>-1 and type='PC' and ref_id=".$cycle_id);


if($approvals != false && count($approvals)>0)
{
    $cycles_details=$crud->getData("select project_cycles.* from project_cycles left join projects on project_cycles.project_id=projects.id where project_cycles.id = $cycle_id");
}
else
{
    $cycles_details=$crud->getData("SELECT project_cycles.* from project_cycles left join projects on project_cycles.project_id=projects.id  where $_my_projects_condition and  project_cycles.id='".$cycle_id."' order by id ASC");
}





if($cycles_details == false || count($cycles_details)<1)
{
    header("location:{$site_url}/dashboard.php");exit();
}
else
{
    $cycles_details = $cycles_details[0];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Project Cycle Print | <?php echo  $site_title?></title>
    <?php include_once('include/head.php');?>
        <style type="text/css">
            body {
                -webkit-print-color-adjust:exact;
                color-adjust:exact;
            }
            .bg_td{background-color: #d4d4d4!important;}
            table, th, td,.table,.table th,.table td
            {
              border-color: #777!important;
              ;
            }
            .authority{margin-left: 40px; text-align: center;float: right;width: 200px}
            .authority .sign_wrapper{border-bottom: 1px solid #777;height: 110px;padding: 20px 0;margin-bottom: 10px;display: flex;justify-content: end;flex-direction: column;align-items: center;}
            .authority .sign_wrapper img{max-width: 100px;max-height: 100px;}
        </style>
</head>
<body <?php if(isset($_GET['action']) && $_GET['action']=='print'){?>onload="window.print()"<?php }?>>
    <div class="wrapper" id="apopdf" style="background: none;width: 1000px;margin: 20px auto;padding: 0;box-shadow: none;border: 0;">
        <div class="row"><?php echo $alert;?></div>


        <?php $blocks = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $cycles_details['defaults']),true);?>
        <ol style="width: 1100px;margin:0 auto;">
            <?php echo cycle_levels_simple($blocks)?>
        </ol>

        <div class="row">
            <?php 
                $auth_details=$crud->getData("SELECT * from authorities where type='PC' and ref_id = ".$cycle_id." order by sort_order DESC");
                $alert='';
                $remarksFlag=false;
                $total_auth = count($auth_details);
                $level = 0;
                $auth_id_remarks = 0;
                $auth_id_remarks_pre = 0;
                $user_id_return_to = 0;

                for($auth_count=0; $auth_count<$total_auth; $auth_count++)
                {
                    $btns='';
                    $auth = $auth_details[$auth_count];
                    if(!isset($_GET['action']))
                    {
                        if($auth_id_remarks > 0 && $auth_id_remarks_pre==0)
                        {
                            $auth_id_remarks_pre = $auth['id'];                                
                            $user_id_return_to = $auth['admin_id'];                                
                        }

                        if($auth['status']==0 && $auth['admin_id']==$_SESSION['id'])
                        {
                            if(!isset($_SESSION['asign']) || empty($_SESSION['asign']) || !file_exists($_SESSION['asign'])){
                                $alert= '<div style="with:100%;margin:20px 0;"><div class="alert alert-error"><a href="profile.php" >Upload your Signature</a> to Approve the apo or Return with Remarks.</div></div>';
                            }
                            else{

                                $suc_btn_label = 'Sign & Move Forward';
                                $remarks_label = 'Return with Remarks';

                                $url = '';
                                if( $auth_count == 0 )
                                {
                                    $suc_btn_label = 'Sign & Approve';
                                }
                                if( ($auth_count+1) == $total_auth )
                                {
                                    $remarks_label = 'Add Remarks';
                                }


                                $level = $total_auth - $auth_count;
                                $auth_id_remarks = $auth['id'];
                                $btns= '<center>';
                                    $btns.= '<a class="btn btn-success" href="project_cycles_print.php?pid='.$pid.'&cycle='.$cycle.'&cycle_id='.$cycle_id.'&movePC='.($level).'" style="margin-top: 16px;" onclick="return confirm(\'Are you sure to sign and move forward?\');">'.$suc_btn_label.'</a><br>';

                                    $btns.= '<a class="btn btn-primary showModule"  style="margin-top: 16px;" data-target="addRemarksModule">'.$remarks_label.'</a>';
                                $btns.= '</center>';
                                    $remarksFlag=true;
                            }
                        }
                        
                    }

                    ?>

                    <div class="authority">
                        <div class="sign_wrapper"><?php if($auth['status']==1){?><img src="<?php echo $auth['sign'];?>"><br><?php echo date("d M, Y", strtotime($auth['signed_date'])); }else if($auth['status']==0){ echo $btns;}?></div>

                        <?php                                        
                            echo '<strong>'.$auth['label'].'</strong><br>';
                            echo ''.$auth['designation'].'<br>';
                            echo ''.$auth['name'].'';
                       ?>
                    </div>
                    <?php
                   
                }           
            ?>

        </div>
        <?php if(!isset($_GET['action'])){?>
            <div class="row"><?php echo $alert;?></div>
            <?php if($remarksFlag){?>
                <div class="module addRemarksModule" style="display: none;margin: 20px 0;">
                    <div class="module-head">
                        <h3>Add Remarks <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addRemarksModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
                    </div>
                    <div class="module-body">
                        <form class="form-horizontal row-fluid ajaxForm" name="addRemarkForm" method="post"  enctype="multipart/form-data" action="project_cycles_print.php?pid=<?php echo $pid;?>&cycle=<?php echo $cycle;?>&cycle_id=<?php echo $cycle_id;?>">
                            <div class="control-group">
                                <label class="control-label" for="basicinput">Remarks</label>
                                <div class="controls">
                                    <textarea  name="remarks" class="span12 tip" required=""></textarea>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <input type="submit" name="addRemarkButton" value="<?php if($user_id_return_to==0){echo 'Add Remarks';}else{echo 'Add Remark & Return';}?>" class="btn btn-primary submitbtn">
                                    <input type="hidden" name="addRemark" value="addRemark">
                                    <input type="hidden" name="level" value="<?php echo $level;?>">
                                    <input type="hidden" name="auth_id_remarks" value="<?php echo $auth_id_remarks;?>">
                                    <input type="hidden" name="auth_id_remarks_pre" value="<?php echo $auth_id_remarks_pre;?>">
                                    <input type="hidden" name="user_id_return_to" value="<?php echo $user_id_return_to;?>">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php }?>
            <?php $apo_remarks = $crud->getData("SELECT remarks.*,admin.display_name,admin.admin_image  from remarks left join admin on remarks.added_by=admin.id where ref_id=".$cycle_id." and type='PC' order by added_date DESC");?>
            <?php if($apo_remarks != false && count($apo_remarks)>0){?>
                <div class="stream-list">
                    <?php foreach($apo_remarks as $row){
                        $user_image=$row['admin_image'];
                        if(empty($user_image)){
                             $user_image='images/user.png';
                        }
                    ?>
                        <div class="media stream">
                            <a href="#" class="media-avatar medium pull-left">
                                <img src="<?php echo $user_image;?>">
                            </a>
                            <div class="media-body">
                                <div class="stream-headline">
                                    <h5 class="stream-author">
                                        <?php echo $row['display_name'];?> <small><b class="feed-date"  data-toggle="tooltip" title="<?php echo $row["added_date"];?>"><?php echo date("d M, Y", strtotime($row["added_date"]));?></b></small>
                                    </h5>
                                    <div class="stream-text"><?php echo $row['remarks'];?></div>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                </div>
            <?php }?>
        <?php }?>
        
        <?php if(isset($_GET['action']) && $_GET['action']=='pdf'){?>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
            <script type="text/javascript">
                "use strict";
                    window.onload = function(){
                        var apo = document.getElementById("apopdf");
                        console.log(apo);
                        console.log(window);
                        html2pdf().from(apo).save();
                    };
            </script>
        <?php }else{?>
            <?php include_once('include/foot.php');?>
        <?php }?>
    </div>
</body>
</html>