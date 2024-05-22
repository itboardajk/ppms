<?php 
$module_name = 'APO';
include('classes/config.php');
authenticate_ajax();

$deleteFlag=authorizeAccess($module_name,'delete');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_GET['deleteAPO']) && $_GET['deleteAPO']>0)
{

	if($deleteFlag)
	{
		$id=intval($crud->escape_string($_GET['deleteAPO']));
    
    $apo_details=$crud->getData("SELECT * from apo where id = $id");
    $apo_details = $apo_details[0];
    if(!empty($apo_details['file']))
    {
      @unlink($apo_details['file']);
    }


    $query = "delete from apo where id=$id";
    $result = $crud->execute($query);
    $query = "delete from authorities where ref_id=$id and type='APO'";
    $result = $crud->execute($query);

    $query = "delete from remarks where  type='APO' and ref_id=$id";
    $result = $crud->execute($query);

    $query = "delete from apo_heads where apo_id=$id";
    $result = $crud->execute($query);


		$return['status']=true;
        $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Project APO deleted !!
                            </div>';
		$crud->log('Project APO('.$id.') deleted',$_SESSION['id']);
	}
	else
	{
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to delete APO.
                                </div>';
	}
}
echo json_encode($return);