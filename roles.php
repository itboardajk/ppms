<?php
$module_name = 'roles';
include('classes/config.php');
authenticate();
if(!authorizeAccess($module_name,'view')){("location:{$site_url}/dashboard.php");exit();}



$accesses = $perms = "";
		
if(isset($_POST['submit']))
{
	if(authorizeAccess($module_name,'add'))
	{
		if(isset($_POST['all_permisions']) && $_POST['all_permisions']=='all')
		{
			$perms=$_POST['all_permisions'];
			$accesses=json_encode($perms);
		}
		else if(isset($_POST['permisions']))
		{
			$perms=$_POST['permisions'];
			$accesses=json_encode($perms);
		}

		$role = $crud->escape_string($_POST['role']);
		$parent_role = $crud->escape_string($_POST['parent_role']); 
		$sort_order = $crud->escape_string($_POST['sort_order']); 

		$msg = $validation->check_empty($_POST, array(array('role','Role')));

		if($msg != null) {
			$errmsg = 'Please correct the following errors:<br>'.$msg;
		}    
		else {


		 	$query = "insert into roles(title,parent_id,sort_order,accesses,department_id,added_by) values('$role','$parent_role','$sort_order','$accesses','".$_SESSION['department_id']."','".$_SESSION['id']."')";
		 	$result = $crud->insert_and_get_id($query);

			if($result != false)
			{
				$sucmsg="Role Created !!";
				unset($_POST);
				$crud->log('Role('.$result.') Added');
			}
			else
			{
				$errmsg = 'Unable to create new role.';
			}
		}
	}
	else
	{
		$errmsg = 'You do not have access to add new role';
	}

}
else if(isset($_POST['edit']))
{
	if(authorizeAccess($module_name,'edit'))
	{		
		$id=intval($crud->escape_string($_GET['view']));

		$role = $crud->escape_string($_POST['role']);
		$parent_role = $crud->escape_string($_POST['parent_role']); 
		$sort_order = $crud->escape_string($_POST['sort_order']); 

		$msg = $validation->check_empty($_POST, array(array('role','Role')));

		if($msg != null) {
			$errmsg = 'Please correct the following errors:<br>'.$msg;
		}    
		else {
			$accesses="";
			if(isset($_POST['all_permisions']) && $_POST['all_permisions']=='all')
				$accesses=json_encode($_POST['all_permisions']);
			else if(isset($_POST['permisions']))
				$accesses=json_encode($_POST['permisions']);

			$accesses = $crud->escape_string($accesses); 

			$query = "update roles set title='$role',parent_id='$parent_role',sort_order='$sort_order',accesses='$accesses', updated_by='".$_SESSION['id']."' where id='$id'";
		 	$result = $crud->execute($query);

			if($result != false)
			{
				$sucmsg="Role Updated !!";
				unset($_POST);
				$crud->log('Role('.$id.') Updated');
			}
			else
			{
				$errmsg = 'Unable to update role.';
			}
		} 
	}


}
else if(isset($_GET['delete']))
{
	if(authorizeAccess($module_name,'delete'))
	{
		$id=intval($crud->escape_string($_GET['delete']));

		$query = "delete from roles where id=$id or parent_id=$id";
		$result = $crud->execute($query);

		$sucmsg="Role deleted !!";
		$crud->log('Role('.$id.') deleted');
	}
	else
	{
		$errmsg = 'You do not have access to delete role';
	}
}
$query="select * from roles where parent_id>=0 and department_id='".$_SESSION['department_id']."' order by sort_order ASC, title ASC";
$nodes = $crud->getData($query);
if($nodes != false && count($nodes)>0)
	$tree=parseTree($nodes);


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Hierarchy | <?php echo  $site_title?></title>
    <?php include_once('include/head.php');?>
</head>
<body>
	<?php include('include/header.php');?>

	<div class="wrapper">
		<div class="container">
			<div class="row">
				<?php include('include/sidebar.php');?>				
				<div class="span9">
					<div class="content">

						<?php if(!empty($errmsg)){?>
							<div class="alert alert-error">
								<button type="button" class="close" data-dismiss="alert">×</button>
								<strong>Error!</strong>	<?php echo $errmsg;?>
							</div>
						<?php }else if(!empty($sucmsg)){?>
							<div class="alert alert-success">
								<button type="button" class="close" data-dismiss="alert">×</button>
								<strong>Well done!</strong>	<?php echo $sucmsg;?>
							</div>
						<?php } ?>
						

						<?php if(authorizeAccess($module_name,'add')){?>
							<div class="module addModule" <?php if(!empty($errmsg) && $_POST){echo 'style="display:block"';}?>>
								<div class="module-head">
									<h3>Add Role <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
								</div>
								<div class="module-body">
									<form class="form-horizontal row-fluid" name="subcategory" method="post"  enctype="multipart/form-data" action="roles.php">
										<div class="control-group">
											<label class="control-label" for="basicinput">Role</label>
											<div class="controls">
												<input type="text" name="role" class="span8 tip" required="" value="<?php echo @$_POST['role']?>">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label" for="basicinput">Parent</label>
											<div class="controls">
												<select name="parent_role">
													<option value="0">Root(No Parent)</option>
													<?php if($nodes != false && count($nodes)>0) printTree($tree);?>
												</select>
											</div>
										</div>
    							    <div class="control-group">
    									<label class="control-label" for="basicinput">Permisions</label>
    									<div class="controls">
    										<table  width="100%" class="table table-bordered table-striped perms">
    											<thead>
	    											<tr>
	    												<th><label><input type="checkbox" name="all_permisions" value="all" class="all_permisions" <?php if($perms=='all'){echo 'checked="checked"';}?>> Modules</label></td></th>
	    												<th class="align-center">Add</th>
	    												<th class="align-center">Edit</th>
	    												<th class="align-center">Delete</th>
	    											</tr>
	    										</thead>
	    										<tbody>
	    											<tr>
	    												<td><label><input type="checkbox" name="permisions[projects][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('projects',$perms) && in_array('view',$perms['projects']))){echo 'checked="checked"';}?>> Projects</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[projects][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('projects',$perms) && in_array('add',$perms['projects']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[projects][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('projects',$perms) && in_array('edit',$perms['projects']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[projects][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('projects',$perms) && in_array('delete',$perms['projects']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													<tr>
	    												<td><label><input type="checkbox" name="permisions[deliverables][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('deliverables',$perms) && in_array('view',$perms['deliverables']))){echo 'checked="checked"';}?>> Project Deliverables</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[deliverables][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('deliverables',$perms) && in_array('add',$perms['deliverables']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[deliverables][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('deliverables',$perms) && in_array('edit',$perms['deliverables']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[deliverables][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('deliverables',$perms) && in_array('delete',$perms['deliverables']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													<tr>
	    												<td><label><input type="checkbox" name="permisions[updates][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('updates',$perms) && in_array('view',$perms['updates']))){echo 'checked="checked"';}?>> Project Updates</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[updates][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('updates',$perms) && in_array('add',$perms['updates']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[updates][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('updates',$perms) && in_array('edit',$perms['updates']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[updates][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('updates',$perms) && in_array('delete',$perms['updates']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													 <tr>
	    												<td><label><input type="checkbox" name="permisions[expenses][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('expenses',$perms) && in_array('view',$perms['expenses']))){echo 'checked="checked"';}?>> Project Expenses</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[expenses][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('expenses',$perms) && in_array('add',$perms['expenses']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[expenses][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('expenses',$perms) && in_array('edit',$perms['expenses']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[expenses][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('expenses',$perms) && in_array('delete',$perms['expenses']))){echo 'checked="checked"';}?>></td>
	    											</tr> 
													 <tr>
	    												<td><label><input type="checkbox" name="permisions[heads][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('heads',$perms) && in_array('view',$perms['heads']))){echo 'checked="checked"';}?>> Project Heads</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[heads][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('heads',$perms) && in_array('add',$perms['heads']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[heads][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('heads',$perms) && in_array('edit',$perms['heads']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[heads][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('heads',$perms) && in_array('delete',$perms['heads']))){echo 'checked="checked"';}?>></td>
	    											</tr> 
													 <tr>
	    												<td><label><input type="checkbox" name="permisions[apo][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('apo',$perms) && in_array('view',$perms['apo']))){echo 'checked="checked"';}?>> Project APOs</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[apo][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('apo',$perms) && in_array('add',$perms['apo']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[apo][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('apo',$perms) && in_array('edit',$perms['apo']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[apo][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('apo',$perms) && in_array('delete',$perms['apo']))){echo 'checked="checked"';}?>></td>
	    											</tr> 
													<tr>
	    												<td><label><input type="checkbox" name="permisions[inspections][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inspections',$perms) && in_array('view',$perms['inspections']))){echo 'checked="checked"';}?>> Project Inspections</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inspections][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inspections',$perms) && in_array('add',$perms['inspections']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inspections][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inspections',$perms) && in_array('edit',$perms['inspections']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inspections][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inspections',$perms) && in_array('delete',$perms['inspections']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													<tr>
	    												<td><label><input type="checkbox" name="permisions[centers][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('centers',$perms) && in_array('view',$perms['centers']))){echo 'checked="checked"';}?>> Project Centers</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[centers][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('centers',$perms) && in_array('add',$perms['centers']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[centers][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('centers',$perms) && in_array('edit',$perms['centers']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[centers][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('centers',$perms) && in_array('delete',$perms['centers']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													<tr>
	    												<td><label><input type="checkbox" name="permisions[inventory][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inventory',$perms) && in_array('view',$perms['inventory']))){echo 'checked="checked"';}?>> Project Inventory</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inventory][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inventory',$perms) && in_array('add',$perms['inventory']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inventory][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inventory',$perms) && in_array('edit',$perms['inventory']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inventory][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inventory',$perms) && in_array('delete',$perms['inventory']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													<tr>
	    												<td><label><input type="checkbox" name="permisions[project_cycles][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('project_cycles',$perms) && in_array('view',$perms['project_cycles']))){echo 'checked="checked"';}?>> Project Cycles</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[project_cycles][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('project_cycles',$perms) && in_array('add',$perms['project_cycles']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[project_cycles][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('project_cycles',$perms) && in_array('edit',$perms['project_cycles']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[project_cycles][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('project_cycles',$perms) && in_array('delete',$perms['project_cycles']))){echo 'checked="checked"';}?>></td>
	    											</tr>
	    											<tr>
	    												<td><label><input type="checkbox" name="permisions[users][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('users',$perms) && in_array('view',$perms['users']))){echo 'checked="checked"';}?>> Users</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[users][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('users',$perms) && in_array('add',$perms['users']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[users][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('users',$perms) && in_array('edit',$perms['users']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[users][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('users',$perms) && in_array('delete',$perms['users']))){echo 'checked="checked"';}?>></td>
	    											</tr>
	    											<tr>
	    												<td><label><input type="checkbox" name="permisions[roles][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('roles',$perms) && in_array('view',$perms['roles']))){echo 'checked="checked"';}?>> Roles</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[roles][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('roles',$perms) && in_array('add',$perms['roles']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[roles][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('roles',$perms) && in_array('edit',$perms['roles']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[roles][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('roles',$perms) && in_array('delete',$perms['roles']))){echo 'checked="checked"';}?>></td>
	    											</tr>    
	    											<tr>
	    												<td><label><input type="checkbox" name="permisions[log][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('log',$perms) && in_array('view',$perms['log']))){echo 'checked="checked"';}?>> Log</label></td>
	    												<td> </td>
	    												<td> </td>
	    												<td> </td>
	    											</tr>
	    										</tbody>
    										</table>
    										
    										   
    									</div>
    								</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">Order</label>
											<div class="controls">
												<input type="number" name="sort_order" class="span8 tip" required="" value="1">
											</div>
										</div>
										<div class="control-group">
											<div class="controls">
												<button type="submit" name="submit" class="btn btn-primary">Create</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						<?php }?>


						<?php if(isset($_GET['view'])){
							$querym="select * from roles where id='".$_GET['view']."'";
							$roles = $crud->getData($querym);
							?>							
		                    <div class="module">
								<div class="module-head">
									<h3>Edit Role <span style="float:right"><a href="roles.php"><i class="icon-remove-circle"></i> Close</a></span></h3>
								</div>
								<div class="module-body">
									<form class="form-horizontal row-fluid" name="subcategory" method="post"  enctype="multipart/form-data">
								    <?php
		                           
		                            foreach($roles as $role)
		                            {
		                            	$perms = json_decode($role['accesses'],true);
		                            ?>	
								    <div class="control-group">
										<label class="control-label" for="basicinput">Role</label>
										<div class="controls">
											<input type="text" name="role" class="span8 tip" required="" value="<?php echo $role['title']?>">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="basicinput">Parent</label>
										<div class="controls">
											<select name="parent_role">
												<option value="0">Root(No Parent)</option>
												<?php $query="select * from roles where  department_id='".$_SESSION['department_id']."' and  id<>".$role['id']." and parent_id>=0 and parent_id<>".$role['id']."  order by sort_order ASC, title ASC";
		                                        $nodes=$crud->getData($query);

		                                        if($nodes != false && count($nodes)>0)
		                                        {
		                                        	$edit_tree=parseTree($nodes);
		                                        	printTree($edit_tree,$role['parent_id']);
		                                        } 
		                                     ?>
		                                    </select>
										</div>
									</div>
								    <div class="control-group">
										<label class="control-label" for="basicinput">Permisions</label>
										<div class="controls">
											<table width="100%" class="table table-bordered table-striped perms">
												<thead>
													<tr>
														<th><label><input type="checkbox" name="all_permisions" value="all" class="all_permisions" <?php if($perms=='all'){echo 'checked="checked"';}?>> Modules</label></td></th>
														<th class="align-center">Add</th>
														<th class="align-center">Edit</th>
														<th class="align-center">Delete</th>
													</tr>
												</thead>
												<tbody>
													<tr>
	    												<td><label><input type="checkbox" name="permisions[projects][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('projects',$perms) && in_array('view',$perms['projects']))){echo 'checked="checked"';}?>> Projects</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[projects][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('projects',$perms) && in_array('add',$perms['projects']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[projects][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('projects',$perms) && in_array('edit',$perms['projects']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[projects][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('projects',$perms) && in_array('delete',$perms['projects']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													<tr>
	    												<td><label><input type="checkbox" name="permisions[deliverables][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('deliverables',$perms) && in_array('view',$perms['deliverables']))){echo 'checked="checked"';}?>> Project Deliverables</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[deliverables][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('deliverables',$perms) && in_array('add',$perms['deliverables']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[deliverables][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('deliverables',$perms) && in_array('edit',$perms['deliverables']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[deliverables][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('deliverables',$perms) && in_array('delete',$perms['deliverables']))){echo 'checked="checked"';}?>></td>
	    											</tr>													 
													<tr>
	    												<td><label><input type="checkbox" name="permisions[updates][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('updates',$perms) && in_array('view',$perms['updates']))){echo 'checked="checked"';}?>> Project Updates</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[updates][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('updates',$perms) && in_array('add',$perms['updates']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[updates][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('updates',$perms) && in_array('edit',$perms['updates']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[updates][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('updates',$perms) && in_array('delete',$perms['updates']))){echo 'checked="checked"';}?>></td>
	    											</tr>
	    											<tr>
	    												<td><label><input type="checkbox" name="permisions[expenses][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('expenses',$perms) && in_array('view',$perms['expenses']))){echo 'checked="checked"';}?>> Project Expenses</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[expenses][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('expenses',$perms) && in_array('add',$perms['expenses']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[expenses][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('expenses',$perms) && in_array('edit',$perms['expenses']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[expenses][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('expenses',$perms) && in_array('delete',$perms['expenses']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													 <tr>
	    												<td><label><input type="checkbox" name="permisions[heads][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('heads',$perms) && in_array('view',$perms['heads']))){echo 'checked="checked"';}?>> Project Heads</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[heads][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('heads',$perms) && in_array('add',$perms['heads']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[heads][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('heads',$perms) && in_array('edit',$perms['heads']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[heads][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('heads',$perms) && in_array('delete',$perms['heads']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													 <tr>
	    												<td><label><input type="checkbox" name="permisions[apo][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('apo',$perms) && in_array('view',$perms['apo']))){echo 'checked="checked"';}?>> Project APOs</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[apo][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('apo',$perms) && in_array('add',$perms['apo']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[apo][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('apo',$perms) && in_array('edit',$perms['apo']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[apo][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('apo',$perms) && in_array('delete',$perms['apo']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													<tr>
	    												<td><label><input type="checkbox" name="permisions[inspections][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inspections',$perms) && in_array('view',$perms['inspections']))){echo 'checked="checked"';}?>> Project Inspections</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inspections][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inspections',$perms) && in_array('add',$perms['inspections']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inspections][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inspections',$perms) && in_array('edit',$perms['inspections']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inspections][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inspections',$perms) && in_array('delete',$perms['inspections']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													<tr>
	    												<td><label><input type="checkbox" name="permisions[centers][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('centers',$perms) && in_array('view',$perms['centers']))){echo 'checked="checked"';}?>> Project Centers</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[centers][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('centers',$perms) && in_array('add',$perms['centers']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[centers][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('centers',$perms) && in_array('edit',$perms['centers']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[centers][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('centers',$perms) && in_array('delete',$perms['centers']))){echo 'checked="checked"';}?>></td>
	    											</tr>
													<tr>
	    												<td><label><input type="checkbox" name="permisions[inventory][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inventory',$perms) && in_array('view',$perms['inventory']))){echo 'checked="checked"';}?>> Project Inventory</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inventory][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inventory',$perms) && in_array('add',$perms['inventory']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inventory][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inventory',$perms) && in_array('edit',$perms['inventory']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[inventory][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('inventory',$perms) && in_array('delete',$perms['inventory']))){echo 'checked="checked"';}?>></td>
	    											</tr>													
													<tr>
	    												<td><label><input type="checkbox" name="permisions[project_cycles][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('project_cycles',$perms) && in_array('view',$perms['project_cycles']))){echo 'checked="checked"';}?>> Project Cycles</label></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[project_cycles][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('project_cycles',$perms) && in_array('add',$perms['project_cycles']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[project_cycles][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('project_cycles',$perms) && in_array('edit',$perms['project_cycles']))){echo 'checked="checked"';}?>></td>
	    												<td class="align-center"><input type="checkbox" name="permisions[project_cycles][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('project_cycles',$perms) && in_array('delete',$perms['project_cycles']))){echo 'checked="checked"';}?>></td>
	    											</tr>
	    											<tr>
														<td><label><input type="checkbox" name="permisions[users][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('users',$perms) && in_array('view',$perms['users']))){echo 'checked="checked"';}?>> Users</label></td>
														<td class="align-center"><input type="checkbox" name="permisions[users][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('users',$perms) && in_array('add',$perms['users']))){echo 'checked="checked"';}?>></td>
														<td class="align-center"><input type="checkbox" name="permisions[users][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('users',$perms) && in_array('edit',$perms['users']))){echo 'checked="checked"';}?>></td>
														<td class="align-center"><input type="checkbox" name="permisions[users][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('users',$perms) && in_array('delete',$perms['users']))){echo 'checked="checked"';}?>></td>
													</tr>
													<tr>
														<td><label><input type="checkbox" name="permisions[roles][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('roles',$perms) && in_array('view',$perms['roles']))){echo 'checked="checked"';}?>> Roles</label></td>
														<td class="align-center"><input type="checkbox" name="permisions[roles][]" value="add" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('roles',$perms) && in_array('add',$perms['roles']))){echo 'checked="checked"';}?>></td>
														<td class="align-center"><input type="checkbox" name="permisions[roles][]" value="edit" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('roles',$perms) && in_array('edit',$perms['roles']))){echo 'checked="checked"';}?>></td>
														<td class="align-center"><input type="checkbox" name="permisions[roles][]" value="delete" class="in_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('roles',$perms) && in_array('delete',$perms['roles']))){echo 'checked="checked"';}?>></td>
													</tr>    											
													
													<tr>
														<td><label><input type="checkbox" name="permisions[log][]" value="view" class="view_perm" <?php if($perms=='all' || (!empty($perms) && array_key_exists('log',$perms) && in_array('view',$perms['log']))){echo 'checked="checked"';}?>> Log</label></td>
														<td> </td>
														<td> </td>
														<td> </td>
													</tr>
												</tbody>
											</table>
											
											   
										</div>
									</div>
									
								    <div class="control-group">
										<label class="control-label" for="basicinput">Order</label>
										<div class="controls">
											<input type="number" name="sort_order" class="span8 tip" required="" value="<?php echo $role['sort_order']?>">
										</div>
									</div>
									<?php }?>
									<?php if(authorizeAccess($module_name,'edit')){?>
			                            <div class="control-group">
											<div class="controls">
												<button type="submit" name="edit" class="btn btn-primary">Update</button>
											</div>
										</div>
			    					<?php }?>
							    </form>
							</div>
						<?php }?>
						<div class="module">
							<div class="module-head">
								<h3>Manage Roles <?php if(authorizeAccess($module_name,'add')){?><span style="float:right"><a href="javascript:;" class="showModule" data-target="addModule"><i class="icon-plus"></i> Add Role</a></span><?php }?></h3>
							</div>
							<div class="module-body table">
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" width="100%">
									<thead>
										<tr>
											<th width="5%">#</th>
											<th>Role</th>
											<th width="5%">Action</th>
										</tr>
									</thead>
									<tbody><?php if($nodes != false && count($nodes)>0) printTreeTable($tree,'',$module_name);?></tbody>
								</table>
							</div>
						</div>					



					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->

	<?php include('include/footer.php');?>

	<?php include_once('include/foot.php');?>
	
	<script src="scripts/datatables/jquery.dataTables.js"></script>
	<script>
		$(document).ready(function() {
			$('.datatable-1').dataTable();
			$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
		} );
	</script>

	<script type="text/javascript">
		$(function(){
			$('.all_permisions').click(function () {
			    var checked = $(this).prop('checked');
    			$(this).closest('.perms').find('input:checkbox').prop('checked', checked);
			});
			$('.view_perm').click(function () {
			    var checked = $(this).prop('checked');
    			$(this).closest('tr').find('input:checkbox').prop('checked', checked);


    			var totalCheckBoxes = $(this).closest('.perms').find('input.view_perm').length + $(this).closest('.perms').find('input.in_perm').length;
    			var totalChecked = $(this).closest('.perms').find('input.view_perm:checkbox:checked').length + $(this).closest('.perms').find('input.in_perm:checkbox:checked').length;
    			if( totalCheckBoxes == totalChecked)
    				$(this).closest('.perms').find('.all_permisions').prop('checked', true);
				else
    				$(this).closest('.perms').find('.all_permisions').prop('checked', false);
			});
			$('.in_perm').click(function () {
			    var checked = $(this).prop('checked');

			    if ($(this).closest('tr').find("input.in_perm:checkbox:checked").length > 0)
			    {
			    	$(this).closest('tr').find('.view_perm').prop('checked', true);
			    }


    			var totalCheckBoxes = $(this).closest('.perms').find('input.view_perm').length + $(this).closest('.perms').find('input.in_perm').length;
    			var totalChecked = $(this).closest('.perms').find('input.view_perm:checkbox:checked').length + $(this).closest('.perms').find('input.in_perm:checkbox:checked').length;
    			if( totalCheckBoxes == totalChecked)
    				$(this).closest('.perms').find('.all_permisions').prop('checked', true);
				else
    				$(this).closest('.perms').find('.all_permisions').prop('checked', false);

			});
		});
	</script>
</body>
</html>