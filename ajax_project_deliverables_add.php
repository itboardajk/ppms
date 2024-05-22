<?php 
$module_name = 'deliverables';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));
//$paged = (isset($_GET['paged']) && $_GET['paged']>1)?$_GET['paged']:1;

$addFlag=authorizeAccess($module_name,'add');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_POST['addDeliverable']))
{
    if($addFlag)
    {
        $title=$crud->escape_string($_POST['title']);
        //$completed_percentage=$crud->escape_string($_POST['completed_percentage']);
        $msg = $validation->check_empty($_POST, array(array('title','Deliverable Title')));


        if($msg != null) {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Please correct the following errors:<br>'.$msg.'
                                </div>';
        }    
        else {

            $sql="INSERT INTO deliverables(title,added_by,project_id,department_id) values('$title','".$_SESSION['id']."','$pid','".$_SESSION['department_id']."')";
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {

                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Deliverable Created !!
                                    </div>';
                unset($_POST);
                $crud->log('Deliverable('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 Unable to create new deliverable.
                                </div>';
            }
        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to add deliverables
                                </div>';
    }
}

echo json_encode($return);