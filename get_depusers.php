<?php
include('classes/config.php');
if(!empty($_REQUEST["did"])) 
{
	$id=intval($_REQUEST['did']);
	
	$users=$crud->getData("select admin.*,roles.title as roleName,roles.parent_id from admin left join roles on admin.role=roles.id where   admin.department_id=".$id." order by roles.sort_order ASC, roles.title ASC");

	if($users != false && count($users)>0)
    {
		$users_tree=parseTreeAdmin($users);
		if($users_tree != false && count($users_tree)>0)
    	{
		 	printTreeAdmin($users_tree,0);
		}
	}
}
?>