<?php 
$module_name = 'updates';
include('classes/config.php');
authenticate_ajax();

$deleteFlag=authorizeAccess($module_name,'delete');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_GET['deleteUpdate']) && $_GET['deleteUpdate']>0)
{

	if($deleteFlag)
	{
		$id=intval($crud->escape_string($_GET['deleteUpdate']));

		$updates_details=$crud->getData("SELECT * from updates where id = $id");
		$updates_details = $updates_details[0];
		
		if(!empty($updates_details['images']))
		{
			$update_images = explode(',',$updates_details['images']);
            foreach ($update_images as  $img) {
                if(!empty($img))
                    @unlink($img);
            }
		}


		$query = "delete from updates where id=$id";
		$result = $crud->execute($query);

		$return['status']=true;
        $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Project update deleted !!
                            </div>';
		$crud->log('Project update('.$id.') deleted',$_SESSION['id']);
	}
	else
	{
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to delete update.
                                </div>';
	}
}
echo json_encode($return);