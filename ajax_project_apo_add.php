<?php 
$module_name = 'APO';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));
//$paged = (isset($_GET['paged']) && $_GET['paged']>1)?$_GET['paged']:1;

$addFlag=authorizeAccess($module_name,'add');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_POST['addAPO']))
{
    if($addFlag)
    {
        $apo=$crud->escape_string($_POST['apo']);
        $allocation=$crud->escape_string($_POST['allocation']);

        $msg = $validation->check_empty($_POST, array(array('apo','APO'),array('allocation','Allocation')));


        if($msg != null) {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Please correct the following errors:<br>'.$msg.'
                                </div>';
        }    
        else {
            $progress=0;
            if(isset($_POST['prog_qty']))
                $progress=1;


            $sql="INSERT INTO apo(apo,allocation,progress,added_by,project_id,department_id) values('$apo','$allocation','$progress','".$_SESSION['id']."','$pid','".$_SESSION['department_id']."')";
             
            $apo_id = $crud->insert_and_get_id($sql);

            if($apo_id != false)
            {
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
                //if(!empty($_SESSION['asign']))
                $crud->execute("INSERT INTO authorities(`label`, `designation`, `name`,`sign`, `status`,  `type`, `ref_id`, `dep_id`, `admin_id`,`added_by`) values('Created By','".$_SESSION['ppmsRoleName']."','".$_SESSION['aname']."','','-1','APO','$apo_id','".$_SESSION['department_id']."','".$_SESSION['id']."','".$_SESSION['id']."')");

                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project APO created !!
                                    </div>';
                                    
                $crud->log('APO('.$apo_id.') Added',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 Unable to create new APO.
                                </div>';
            }
        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to add APO
                                </div>';
    }
}

echo json_encode($return);