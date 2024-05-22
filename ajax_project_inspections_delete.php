<?php 
$module_name = 'inspections';
include('classes/config.php');
authenticate_ajax();

$deleteFlag=authorizeAccess($module_name,'delete');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_GET['deleteInspection']) && $_GET['deleteInspection']>0)
{

	if($deleteFlag)
	{
		$id=intval($crud->escape_string($_GET['deleteInspection']));
		
		$inspections_details=$crud->getData("SELECT * from inspections where id = $id");
		$inspections_details = $inspections_details[0];
		
		if(!empty($inspections_details['images']))
		{
			$inspections_images = explode(',',$inspections_details['images']);
            foreach ($inspections_images as  $img) {
                if(!empty($img))
                    @unlink($img);
            }
		}


		$query = "delete from inspections where id=$id";
		$result = $crud->execute($query);

		$return['status']=true;
        $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Project inspection deleted !!
                            </div>';
		$crud->log('Project inspection('.$id.') deleted',$_SESSION['id']);
	}
	else
	{
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to delete inspection.
                                </div>';
	}
}
echo json_encode($return);