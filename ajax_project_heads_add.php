<?php 
$module_name = 'heads';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));
//$paged = (isset($_GET['paged']) && $_GET['paged']>1)?$_GET['paged']:1;

$addFlag=authorizeAccess($module_name,'add');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_POST['addHead']))
{
    if($addFlag)
    {
        $head=$crud->escape_string($_POST['head']);
        $parent_head=$crud->escape_string($_POST['parent_head']);
        $code=$crud->escape_string($_POST['code']);
        $unit=$crud->escape_string($_POST['unit']);
        $quantity=$crud->escape_string($_POST['quantity']);
        $cost=$crud->escape_string($_POST['cost']);
        $sort_order=$crud->escape_string($_POST['sort_order']);
    
        if($parent_head==0)
        {
            $unit='';
            $quantity='';
            $cost='0';
        }
        else
        {
           $code=''; 
        }

        $msg = $validation->check_empty($_POST, array(array('head','Head')));


        if($msg != null) {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Please correct the following errors:<br>'.$msg.'
                                </div>';
        }    
        else {
            $cost = (empty($cost))?0:$cost;


            $sql="INSERT INTO heads(head,parent_head,unit,quantity,cost,sort_order,added_by,project_id,code,department_id) values('$head','$parent_head','$unit','$quantity','$cost','$sort_order','".$_SESSION['id']."','$pid','$code','".$_SESSION['department_id']."')";
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {

                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project head created !!
                                    </div>';
                unset($_POST);
                $crud->log('Head('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 Unable to create new head.
                                </div>';
            }
        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to add head
                                </div>';
    }
}

echo json_encode($return);