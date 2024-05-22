<?php 
$module_name = 'deliverables';
include('classes/config.php');
authenticate_ajax();

$deleteFlag=authorizeAccess($module_name,'delete');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_GET['deleteDeliverable']) && $_GET['deleteDeliverable']>0)
{

	if($deleteFlag)
	{
		$id=intval($crud->escape_string($_GET['deleteDeliverable']));

		$editDeliverable_details=$crud->getData("select * from deliverables where id = $id");
    $editDeliverable_details = $editDeliverable_details[0];

    if(!empty($editDeliverable_details['images']))
    {
      $deliverable_images = explode(',',$editDeliverable_details['images']);
      foreach ($deliverable_images as  $img) {
          if(!empty($img))
              @unlink($img);
      }
    }


		$query = "delete from deliverables where id=$id";
		$result = $crud->execute($query);



    $project_complete=$crud->getData("select SUM((status/100)*weight) as total from deliverables where project_id = ".$editDeliverable_details['project_id']);
    if($project_complete != false && count($project_complete)>0)
      $project_complete = $project_complete[0];
    else
      $project_complete = 0;

    $crud->execute("UPDATE projects set completed_percentage='".round($project_complete['total'],0)."' where id=".$editDeliverable_details['project_id']);



		$return['status']=true;
        $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Project deliverable deleted !!
                            </div>';
		$crud->log('Project deliverable('.$id.') deleted',$_SESSION['id']);
	}
	else
	{
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to delete deliverables.
                                </div>';
	}
}
echo json_encode($return);