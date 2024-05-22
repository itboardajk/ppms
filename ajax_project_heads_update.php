<?php 
$module_name = 'heads';
include('classes/config.php');
authenticate_ajax();

$editFlag=authorizeAccess($module_name,'edit');
$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_POST['editHead']) && isset($_GET['updateHead']) && $_GET['updateHead']>0)
{
    if($editFlag)
    {
		$head_id=intval($crud->escape_string($_GET['updateHead']));


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

            $sql="UPDATE heads set head='$head',parent_head='$parent_head',unit='$unit',quantity='$quantity',cost='$cost',sort_order='$sort_order',updated_by='".$_SESSION['id']."',code='$code' where id=".$head_id;
             
            $result = $crud->execute($sql);

            if($result != false)
            {

            	if($parent_head>0)
                    $crud->execute("UPDATE heads set parent_head='$parent_head' where parent_head='$head_id'");

                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project head updated !!
                                    </div>';


                $crud->log('Head('.$result.') updated',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Unable to edit head.
                                </div>';
            }
        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  You do not have access to edit head.
                                </div>';
    }
}
echo json_encode($return);