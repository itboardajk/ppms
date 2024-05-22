<?php 
$module_name = 'centers';
include('classes/config.php');
authenticate_ajax();

$deleteFlag=authorizeAccess($module_name,'delete');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_GET['deleteCenter']) && $_GET['deleteCenter']>0)
{

	if($deleteFlag)
	{
		$id=intval($crud->escape_string($_GET['deleteCenter']));
		$centers_details=$crud->getData("SELECT * from centers where id = $id");
		$centers_details = $centers_details[0];
		if(!empty($centers_details['fimage']))
		{
			@unlink($centers_details['fimage']);
		}

		if(!empty($centers_details['images']))
		{
			$center_images = explode(',',$centers_details['images']);
            foreach ($center_images as  $img) {
                if(!empty($img))
                    @unlink($img);
            }
		}

		$query = "delete from centers where id=$id";
		$result = $crud->execute($query);

		$return['status']=true;
        $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Project center deleted !!
                            </div>';
		$crud->log('Project center('.$id.') deleted',$_SESSION['id']);
	}
	else
	{
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to delete center.
                                </div>';
	}
}
echo json_encode($return);