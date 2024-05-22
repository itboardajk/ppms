<?php
$module_name = 'apo';
include('classes/config.php');
authenticate();

$viewFlag=authorizeAccess($module_name,'view');
$addFlag=authorizeAccess($module_name,'add');
$editFlag=authorizeAccess($module_name,'edit');
$deleteFlag=authorizeAccess($module_name,'delete');

if(!$viewFlag){header("location:{$site_url}/dashboard.php");exit();}


$apo_id=intval($crud->escape_string($_GET['apo_id']));

$alert='';

if(isset($_GET['moveAPO']) && $_GET['moveAPO']>=1)
{

    if(!isset($_SESSION['asign']) || empty($_SESSION['asign']) || !file_exists($_SESSION['asign'])){
        $alert= '<div style="with:100%;margin:20px 0;"><div class="alert alert-error"><a href="profile.php" >Upload your Signature</a> to Approve the apo or Return with Remarks.</div></div>';
    }
    else
    {
        $level=intval($crud->escape_string($_GET['moveAPO']));
        
        $auth_details=$crud->getData("SELECT * from authorities where type='APO' and ref_id = ".$apo_id." and status<>1 order by sort_order ASC");
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
                $sql="UPDATE apo set level='".$level."',updated_by='".$_SESSION['id']."' where id=".$apo_id;             
                $result = $crud->execute($sql);

                $sql = "UPDATE authorities set sign='".$_SESSION['asign']."', signed_date='".date("Y-m-d")."',status=1 where id=".$first_authority['id'];
                $result = $crud->execute($sql);
            }
        }
        if($second_authority != 0)
        {
            $editapos_details=$crud->getData("SELECT apo.*,departments.title as departmentName,projects.title as projectName 
                                        from apo   
                                            left join departments ON apo.department_id=departments.id
                                            left join projects ON apo.project_id=projects.id
                                             where apo.id = $apo_id");
            
            $editapos_details = $editapos_details[0];

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

                $alert='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Project APO Moved Forward, and you will not be able to access this APO!!
                                </div>';
            }
            else
            {
                $alert='<div class="alert alert-danger alert-dismissible fade in" role="alert">
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                          Unable to sign the APO, Please try again.
                        </div>';
            }
            header('location: approvals.php?sucmsg=APO hasn been forwarded.');
            exit;
        }
        else
        {
            header('location: approvals.php?sucmsg=APO hasn been Approved.');
            exit;
        }

    }
}
if(isset($_POST['addRemark']) && !empty($_POST['addRemark']))
{
    if(!isset($_SESSION['asign']) || empty($_SESSION['asign']) || !file_exists($_SESSION['asign'])){
        $alert= '<div style="with:100%;margin:20px 0;"><div class="alert alert-error"><a href="profile.php" >Upload your Signature</a> to Approve the apo or Return with Remarks.</div></div>';
    }
    else
    {
        $remarks=$crud->escape_string($_POST['remarks']);
        $level=$crud->escape_string($_POST['level']);
        $auth_id_remarks=$crud->escape_string($_POST['auth_id_remarks']);
        $auth_id_remarks_pre=$crud->escape_string($_POST['auth_id_remarks_pre']);
        $user_id_return_to=$crud->escape_string($_POST['user_id_return_to']);

        $isql="INSERT INTO remarks(type,ref_id,designation,name,department,remarks,added_by ) values('APO','$apo_id','".$_SESSION['ppmsRoleName']."','".$_SESSION['aname']."','".$_SESSION['department_name']."','$remarks','".$_SESSION['id']."')";

        $res = $crud->execute($isql);
        if($res != false)
        {
            if($user_id_return_to > 0)
            {
                $sql="UPDATE apo set level=".($level-2).",updated_by='".$_SESSION['id']."' where id=".$apo_id;             
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

                        $editapos_details=$crud->getData("SELECT apo.*,departments.title as departmentName,projects.title as projectName 
                                                    from apo   
                                                        left join departments ON apo.department_id=departments.id
                                                        left join projects ON apo.project_id=projects.id
                                                         where apo.id = $apo_id");
                        
                        $editapos_details = $editapos_details[0];

                        $AJKEmail = new AJKEmail('APO Rejected!',array(array($user['display_name'],$user['email'])));

                        $AJKEmail->email_body($user['display_name'],$_SESSION['aname'].'<br>'.$_SESSION['ppmsRoleName'].'<br>'.$_SESSION['department_name'],'An APO has been rejected with remarks. Please see details below:<br>
                            APO: '.$editapos_details['apo'].'<br>
                            Project: '.$editapos_details['projectName'].'<br>
                            Department: '.$editapos_details['departmentName'].'<br>
                            Remarks: '.$remarks.'<br>
                            <a href="https://ppms.ajk.gov.pk/print_apo.php?apo_id='.$editapos_details['id'].'">Click Here</a> to see the APO<br>'
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
                          Remark has been saved and APO has been sent back.
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


$approvals = $crud->getData("SELECT * FROM authorities where admin_id=".$_SESSION['id']." and status<>-1 and type='APO' and ref_id=".$apo_id);


if($approvals != false && count($approvals)>0)
{
    $apos_details=$crud->getData("select apo.*,projects.budget,projects.title from apo left join projects on apo.project_id=projects.id where apo.id = $apo_id");
}
else
{
    $apos_details=$crud->getData("select apo.*,projects.budget,projects.title from apo left join projects on apo.project_id=projects.id where $_my_projects_condition and apo.id = $apo_id");
}



if($apos_details != false && count($apos_details)>0)
{
    $editapos_details = $apos_details[0];


    $heads=$crud->getData("select * from heads  where project_id = ".$editapos_details['project_id']."  and parent_head=0 order by sort_order asc,id asc");


    $tcols=11;
    if($editapos_details['progress']==0)
    {
        $tcols=8;
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<title>Project APO Print | <?php echo  $site_title?></title>
        <?php include('include/head.php');?>
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
    <body>
    	<div class="wrapper" id="apopdf" style="background: none;width: 1000px;margin: 20px auto;padding: 0;box-shadow: none;border: 0;">
            <div class="row"><?php echo $alert;?></div>
            <table class="table table-bordered">
                <tr>
                    <th colspan="<?php echo $tcols;?>" class="align-center"><h4 style="margin: 0;padding: 0">Annual Plan of Operations(APO) <?php echo $editapos_details['apo'].' '.$department_name?></h4></th>
                </tr>
                <tr>
                    <td>1</td>
                    <th></th>
                    <th>Sector:</th>
                    <th colspan="<?php echo $tcols-3;?>"><?php echo $department_name?></th>
                </tr>
                <tr>
                    <td>2</td>
                    <th></th>
                    <th>Project Name:</th>
                    <th colspan="<?php echo $tcols-3;?>"><?php echo $editapos_details['title']?></th>
                </tr>
                <tr>
                    <td>3</td>
                    <th></th>
                    <th>Original Cost:</th>
                    <th colspan="<?php echo $tcols-3;?>"><?php echo $editapos_details['budget']+0?></th>
                </tr>
                <tr>
                    <td>4</td>
                    <th></th>
                    <th>Allocation <?php echo $editapos_details['apo']?>:</th>
                    <th colspan="<?php echo $tcols-3;?>"><?php echo $editapos_details['allocation']+0?></th>
                </tr>
                <tr>
                    <th colspan="6" class="align-center">PC-1 Project Inputs</th>
                    <?php if($editapos_details['progress']!=0){?><th colspan="3" class="align-center">Progress Upto 6/<?php echo $editapos_details['apo'];?></th><?php }?>
                    <th colspan="2" class="align-center">APO Plan Inputs</th>
                </tr>
                <tr>
                    <td></td>
                    <th class="bg_td">Code</th>
                    <th class="bg_td">Budget Input Description</th>
                    <th class="bg_td">Unit</th>
                    <th class="bg_td">Qty</th>
                    <th class="bg_td">Budget</th>
                    <?php if($editapos_details['progress']!=0){?>
                        <th class="bg_td">Qty</th>
                        <th class="bg_td">Exp.</th>
                        <th class="bg_td">Status</th>
                    <?php }?>
                    <th class="bg_td">Qty</th>
                    <th class="bg_td">Allocation</th>
                </tr>
                <?php $cnt=1; $grand_total=0; $progress_total=0; $allocation_total=0; foreach($heads as $head){
                    $subheads=$crud->getData("select heads.*,apo_heads.prog_qty,apo_heads.prog_expences,apo_heads.prog_status,apo_heads.quantity as apo_qty,apo_heads.revised as apo_revised from heads left join apo_heads on heads.id=apo_heads.head_id where  heads.project_id = ".$editapos_details['project_id']."  and heads.parent_head = ".$head['id']." and apo_heads.apo_id=".$editapos_details['id']." order by heads.sort_order asc,heads.id asc");
                    ?>
                    <tr>
                        <td><?php echo htmlentities($cnt);?></td>
                        <th class="verticalTableText bg_td" style="text-align: center;vertical-align: middle;" rowspan="<?php echo count($subheads)+2;?>"><?php echo $head['code']?></th>
                        <th class="bg_td" colspan="<?php echo $tcols-2;?>"><?php echo $head['head']?></th>
                    </tr>
                    <?php $subcnt=1; $grand_stotal=0; $progress_stotal=0; $allocation_stotal=0; foreach($subheads as $subrow){?>                            
                        <tr>
                            <td><?php echo htmlentities($cnt).'.'.htmlentities($subcnt);?></td>
                            <td><?php echo htmlentities($subrow['head']);?></td>
                            <td><?php echo htmlentities($subrow['unit']);?></td>
                            <td><?php echo htmlentities($subrow['quantity']);?></td>
                            <td><?php echo htmlentities($subrow['cost']+0);?></td>
                            <?php if($editapos_details['progress']!=0){?>
                                <td><?php echo $subrow['prog_qty']?></td>
                                <td><?php echo $subrow['prog_expences']+0?></td>
                                <td><?php echo $subrow['prog_status']?></td>
                            <?php }?>
                            <td><?php echo $subrow['apo_qty']?></td>
                            <td><?php echo $subrow['apo_revised']+0?></td>
                        </tr>                                     
                    <?php $subcnt++;$grand_stotal+=$subrow['cost']; $progress_stotal+=$subrow['prog_expences']; $allocation_stotal+=$subrow['apo_revised']; }?>

                    <tr>
                        <td></td>
                        <th>Sub Total</th>
                        <td></td>
                        <td></td>
                        <th><?php echo $grand_stotal;?></th>
                        <?php if($editapos_details['progress']!=0){?>
                            <td></td>
                            <th><?php echo $progress_stotal?></th>
                            <td></td>
                        <?php }?>
                        <td></td>
                        <th><?php echo $allocation_stotal?></th>
                    </tr> 
                <?php $cnt++;$grand_total+=$grand_stotal; $progress_total+=$progress_stotal; $allocation_total+=$allocation_stotal;}?>

                <tr>
                    <td class="bg_td"></td>
                    <td class="bg_td"></td>
                    <th class="bg_td">Grand Total</th>
                    <td class="bg_td"></td>
                    <td class="bg_td"></td>
                    <th class="bg_td"><?php echo $grand_total;?></th>
                    <?php if($editapos_details['progress']!=0){?>
                        <td class="bg_td"></td>
                        <th class="bg_td"><?php echo $progress_total?></th>
                        <td class="bg_td"></td>
                    <?php }?>
                    <td class="bg_td"></td>
                    <th class="bg_td"><?php echo $allocation_total?></th>
                </tr>
            </table>
            <div class="row">
                <?php 
                    $auth_details=$crud->getData("SELECT * from authorities where type='APO' and ref_id = ".$apo_id." order by sort_order DESC");
                    $alert='';$remarksFlag=false;
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
                                    $btns= '<center><a class="btn btn-success" href="print_apo.php?apo_id='.$apo_id.'&moveAPO='.($level).'" style="margin-top: 16px;" onclick="return confirm(\'Are you sure to sign and move forward?\');">'.$suc_btn_label.'</a><br>

                                        <a class="btn btn-primary showModule"  style="margin-top: 16px;" data-target="addRemarksModule">'.$remarks_label.'</a></center>';
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
                            <form class="form-horizontal row-fluid ajaxForm" name="addRemarkForm" method="post"  enctype="multipart/form-data" action="project_cycles_print.php?pid=<?php echo $pid?>&cycle=<?php echo $row['title']?>&cycle_id=<?php echo $my_cycles[$row['title']]['id'];?>">
                                <div class="control-group">
                                    <label class="control-label" for="basicinput">Remarks</label>
                                    <div class="controls">
                                        <textarea  name="remarks" class="span12 tip" required=""></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="controls">
                                        <input type="submit" name="addRemarkButton" value="Add Remark & Return" class="btn btn-primary submitbtn">
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
                <?php $apo_remarks = $crud->getData("SELECT remarks.*,admin.display_name,admin.admin_image  from remarks left join admin on remarks.added_by=admin.id where ref_id=".$apo_id." and type='APO' order by added_date DESC");?>
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
    	</div>


        <?php if(isset($_GET['action']) && $_GET['action']=='print'){?>
        	<script  type="text/javascript">
        		
        		$(function(){ 
                    window.print();
        		});
        		
        	</script>
        <?php }else if(isset($_GET['action']) && $_GET['action']=='pdf'){?>
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
        
    </body>
    </html>
<?php }?>