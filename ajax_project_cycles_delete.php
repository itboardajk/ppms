<?php 
$module_name = 'heads';
include('classes/config.php');
authenticate_ajax();

$deleteFlag=authorizeAccess($module_name,'delete');

$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_GET['deleteCycle']) && $_GET['deleteCycle']>0)
{

	if($deleteFlag)
	{
		$id=intval($crud->escape_string($_GET['deleteCycle']));
		$cycles_details=$crud->getData("SELECT * from `project_cycles` where id='".$id."' order by id ASC");
		$cycles_details = $cycles_details[0];

		if($cycles_details['type']=='Manual')
		{
			@$blocks = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $cycles_details['defaults']),true);
			@unlink($blocks['url']);
		}


		$query = "delete from project_cycles where id=$id";
		$result = $crud->execute($query);

		$query = "delete from authorities where type='PC' and ref_id=$id";
		$result = $crud->execute($query);


		$query = "delete from remarks where  type='PC' and ref_id=$id";
		$result = $crud->execute($query);


		$return['status']=true;
        $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Project cycle deleted !!
                            </div>';
		$crud->log('Project cycle('.$id.') deleted',$_SESSION['id']);
	}
	else
	{
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to delete Project Cycles.
                                </div>';
	}
}
echo json_encode($return);