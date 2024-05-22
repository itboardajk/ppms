<?php

$module_name = 'departments';
include('classes/config.php');
authenticate();

if($_SESSION['id']!=1)
{
	exit;
}
$viewFlag=authorizeAccess($module_name,'view');
$addFlag=authorizeAccess($module_name,'add');
$editFlag=authorizeAccess($module_name,'edit');
$deleteFlag=authorizeAccess($module_name,'delete');

if(!$viewFlag){header("location:{$site_url}/dashboard.php");exit();}


if(isset($_POST['add']))
{
	if($addFlag)
	{
		$name=$crud->escape_string($_POST['name']);
		$name_urdu=$crud->escape_string($_POST['name_urdu']);
		$parent_id=$crud->escape_string($_POST['parent_id']);
		$displayName=$crud->escape_string($_POST['displayName']);
		$displayName_urdu=$crud->escape_string($_POST['displayName_urdu']);
		$email=$crud->escape_string($_POST['email']);
		$username=$crud->escape_string($_POST['username']);
		$officeContact=$crud->escape_string($_POST['officeContact']);
		$mobileContact=$crud->escape_string($_POST['mobileContact']);
		$password=md5($crud->escape_string($_POST['password']));

		$msg = $validation->check_empty($_POST, array(array('name','Department Name'),array('displayName','Focal Person Display Name'),array('username','Focal Person Username'),array('password','Focal Person Password')));


		$enode = $crud->getData("SELECT * from admin where username='$username'");
		if($enode != false && count($enode)>0)
		{
			$errmsg = 'Username already exist!!';
		}
		else if($msg != null) {
			$errmsg = 'Please correct the following errors:<br>'.$msg;
		}    
		else {
			if(!empty($_FILES['image']['name']))
			{
		    	$file_name = $_FILES['image']['name'];
		        $file_size =$_FILES['image']['size'];
		        $file_tmp =$_FILES['image']['tmp_name'];
		        $file_type=$_FILES['image']['type'];

		        @$file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
		       
		        $allowed_extensions= array("jpeg","jpg","png");
		        
		        if(in_array($file_ext,$allowed_extensions)=== false){
		        	$errmsg="Department Picture: Extension not allowed, please choose a JPEG or PNG file.";
		        }
		        
		        if($file_size > 1097152){
		        	$errmsg='Department Picture: File size must be less then 1 MB';
		        }
			}
			      
		    if(empty($errmsg))
		    {
		    	$imagefile="";
		        if(!empty($_FILES['image']['name']))
			    {
		        	$imagefile="department_images/department_".date('YmdHis').'.'.$file_ext;
		        	move_uploaded_file($_FILES["image"]["tmp_name"],'uploads/'.$imagefile);
			    }

			    $add_dep = "insert into departments(name,name_urdu,logo,parent_id,createdBy) values('$name','$name_urdu','$imagefile','$parent_id','".$_SESSION['id']."')";
		        $result = $crud->insert($add_dep);

		       
		        $rowD=$crud->getData("select * from departments where name ='".$name."'");
				$rowD=$rowD[0];
		        
		        $add_role = "insert into roles(title,role_urdu,parent_id,sort_order,department_id,accesses) values('Administrator','ایڈمنسٹریٹر','-1','0',".$rowD['id'].",'\"all\"')";
				$crud->insert($add_role);

				$rowR=$crud->getData("select * from roles where title ='Administrator' and department_id=".$rowD['id']);
				$rowR = $rowR[0];

				$add_fp="insert into admin(displayName,displayName_urdu,email,role,username,password,department_id,officeContact,mobileContact,status) values('$displayName','$displayName_urdu','$email','".$rowR['id']."','$username','$password','".$rowD['id']."','$officeContact','$mobileContact','1')";
				$crud->insert($add_fp);

				$rowA=$crud->getData("select * from admin where username ='".$username."' and department_id=".$rowD['id']);
				$rowA=$rowA[0];

				$update_dep = "update departments set focal_person='".$rowA['id']."' where id=".$rowD['id'];
				$crud->execute($update_dep);

		    	 

				if($result != false)
				{
					$sucmsg="Department Created !!";
					unset($_POST);
					$crud->log('Department('.$result.') Added',$_SESSION['id']);
				}
				else
				{
					$errmsg = 'Unable to create new department.';
				}
		    }    
		}
	}
	else
	{
		$errmsg = 'You do not have access to add department';
	}

}
else if(isset($_POST['edit']))
{
	if($editFlag)
	{
		$id=intval($crud->escape_string($_GET['view']));


		$name=$crud->escape_string($_POST['name']);
		$name_urdu=$crud->escape_string($_POST['name_urdu']);
		$focal_person=$crud->escape_string($_POST['focal_person']);
		$parent_id=$crud->escape_string($_POST['parent_id']);

		$msg = $validation->check_empty($_POST, array(array('name','Department Name')));

		if($msg != null) {
			$errmsg = 'Please correct the following errors:<br>'.$msg;
		}    
		else {
			if(!empty($_FILES['image']['name']))
			{
		    	$file_name = $_FILES['image']['name'];
		        $file_size =$_FILES['image']['size'];
		        $file_tmp =$_FILES['image']['tmp_name'];
		        $file_type=$_FILES['image']['type'];
		        @$file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
		       
		        $expensions= array("jpeg","jpg","png");
		        
		        if(in_array($file_ext,$expensions)=== false){
		         $errmsg="Department Picture: extension not allowed, please choose a JPEG or PNG file.";
		        }
		        
		        if($file_size > 1097152){
		         $errmsg='Department Picture: File size must be less then 1 MB';
		        }
			}
			
		      
		    if(empty($errmsg))
		    {
		    	$updb="";
		        if(!empty($_FILES['image']['name']))
			    {
		        	$imagefile="department_images/department_".date('YmdHis').'.'.$file_ext;
		        	move_uploaded_file($_FILES["image"]["tmp_name"],'uploads/'.$imagefile);
		        	$updb .=",logo='$imagefile'";
		        	
			    }
			    $sql=$crud->execute("update departments set name='$name',name_urdu='$name_urdu',focal_person='$focal_person',parent_id='$parent_id' $updb where id='$id'");

	    	}

		    if($sql != false)
			{
				$sucmsg="Department Updated !!";
				unset($_POST);
				$crud->log('Department('.$id.') Updated',$_SESSION['id']);
			}
			else
			{
				$errmsg = 'Unable to updated  department.';
			}
		}

	}
	else
	{
		$errmsg = 'You do not have access to updated department';
	}

}
else if(isset($_GET['delete']) && $_GET['delete']>0)
{
	if($deleteFlag)
	{
		$id=intval($crud->escape_string($_GET['delete']));

		$query = "delete from departments where id=$id";
		$result = $crud->execute($query);
		$query = "delete from admin where department_id=$id";
		$result = $crud->execute($query);
		$query = "delete from roles where department_id=$id";
		$result = $crud->execute($query);

		$sucmsg="Department and all of its users and roles deleted !!";
		$crud->log('Department('.$id.') deleted',$_SESSION['id']);
	}
	else
	{
		$errmsg = 'You do not have access to delete department';
	}
}

$query="select * from departments order by title ASC";
$nodes = $crud->getData($query);
if($nodes != false && count($nodes)>0)
	$tree=parseTree($nodes);


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Departemnts | <?php echo  $site_title?></title>
    <?php include_once('include/head.php');?>

	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
</head>
<body>	
	<?php include('include/header.php');?>
	<div class="wrapper">
		<div class="container">
			<div class="row">
			<?php include('include/sidebar.php');?>				
			<div class="span9">
					<div class="content">
						<?php if(!empty(@$errmsg)){?>
							<div class="alert alert-danger alert-dismissible fade in" role="alert">
							  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
							  <?php echo $errmsg;?>
							</div>
						<?php } else if(!empty(@$sucmsg)){?>
							<div class="alert alert-success alert-dismissible fade in" role="alert">
							  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
							  <?php echo $sucmsg;?>
							</div>
						<?php }?>
						<?php if($addFlag){
				      		?>
		                    <div class="module addModule" <?php if(isset($_POST['add']) || isset($_GET['create'])){echo 'style="display:block"';}?>>
								<div class="module-head">
									<h3>Add Department <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
								</div>
								<div class="module-body">
	    							<form class="form-horizontal row-fluid" name="addadmin" method="post"  enctype="multipart/form-data" action="departments.php">
	    							    <div class="control-group">
											<label class="control-label" for="basicinput">Name</label>
											<div class="controls">
												<input type="text" name="name" class="span8 tip" required="" value="<?php echo @$_POST['name']?>">
											</div>
	    								</div>
	    							    <div class="control-group">
											<label class="control-label" for="basicinput">Name in Urdu</label>
											<div class="controls">
												<input type="text" name="name_urdu" class="span8 tip typeInrtl" required="" value="<?php echo @$_POST['name_urdu']?>">
											</div>
	    								</div>
                                        <div class="control-group">
                                            <label class="control-label" for="basicinput">Parent Department</label>
                                            <div class="controls">
                                            	<select name="parent_id">
                                            		<option value="0">Root(No Parent)</option>
													<?php if($nodes != false && count($nodes)>0) printTree($tree);?>
                                            	</select>
                                            </div>
                                        </div>
	    								<div class="control-group">
											<label class="control-label" for="basicinput">Logo Image</label>
											<div class="controls">
												<input type="file" name="image">
											</div>
										</div>
	    								<div class="control-group"><h3>Focal Person</h3></div>
	    								<div class="control-group">
	    									<label class="control-label" for="basicinput">Display Name</label>
	    									<div class="controls">
	    										<input type="text" name="displayName" class="span8 tip" required="" value="<?php echo @$_POST['displayName']?>">
	    									</div>
	    								</div>
	    								<div class="control-group">
	    									<label class="control-label" for="basicinput">Display Name in Urdu</label>
	    									<div class="controls">
	    										<input type="text" name="displayName_urdu" class="span8 tip typeInrtl" required="" value="<?php echo @$_POST['displayName_urdu']?>">
	    									</div>
	    								</div>
	    								<div class="control-group">
	    									<label class="control-label" for="basicinput">Email</label>
	    									<div class="controls">
	    										<input type="email" name="email" class="span8 tip" required="" value="<?php echo @$_POST['email']?>">
	    									</div>
	    								</div>
	    								<div class="control-group">
	    									<label class="control-label" for="basicinput">Phone(office)</label>
	    									<div class="controls">
	    										<input type="text" name="officeContact" class="span8 tip" required="" value="<?php echo @$_POST['officeContact']?>">
	    									</div>
	    								</div>
	    								<div class="control-group">
	    									<label class="control-label" for="basicinput">Mobile</label>
	    									<div class="controls">
	    										<input type="text" name="mobileContact" class="span8 tip" required="" value="<?php echo @$_POST['mobileContact']?>">
	    									</div>
	    								</div>
	    								<div class="control-group">
	    									<label class="control-label" for="basicinput">Login Username</label>
	    									<div class="controls">
	    										<input type="text" name="username" class="span8 tip" required="" value="<?php echo @$_POST['username']?>">
	    									</div>
	    								</div>
	    								<div class="control-group">
	    									<label class="control-label" for="basicinput">Login Password</label>
	    									<div class="controls">
	    										<input type="password" name="password" class="span8 tip" required="" value="<?php echo @$_POST['password']?>">
	    									</div>
	    								</div>
	                                    <div class="control-group">
	    									<div class="controls">
	    										<button type="submit" name="add" class="btn btn-primary">Create Department</button>
	    									</div>
	    								</div>
	    						    </form>
							    </div>
							</div>
						<?php }?>
				      	<?php if(isset($_GET['view'])){
							$query="select *  from departments   where id=".$_GET['view'];
							$enode = $crud->getData($query);
							if($enode != false && count($enode)>0)
							{
								$row = $enode[0];
								?>
								<div class="module editModule">
									<div class="module-head">
										<h3><?php echo ($editFlag)?'Edit ':'View ';?> Department <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="editModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
									</div>
									<div class="module-body">
										<form class="form-horizontal row-fluid" name="editadmin" method="post"  enctype="multipart/form-data" action="projects.php?view=<?php echo $_GET['view'];?>">
											
	        							    <div class="control-group">
	        									<label class="control-label" for="basicinput">Name</label>
	        									<div class="controls">
	        										<input type="text" name="name" class="span8 tip" required="" value="<?php echo $row['title']?>">
	        									</div>
	        								</div>
	                                        <div class="control-group">
	                                            <label class="control-label" for="basicinput">Name in Urdu</label>
	                                            <div class="controls">
	                                                <input type="text" name="name_urdu" class="span8 tip typeInrtl" required="" value="<?php echo $row['name_urdu']?>">
	                                            </div>
	                                        </div>
	                                        <div class="control-group">
	                                            <label class="control-label" for="basicinput">Parent Department</label>
	                                            <div class="controls">
	                                            	<select name="parent_id">
	                                            		<option value="0">Root(No Parent)</option>
	                                            		<?php $query="select * from departments where id<>'".$row['id']."' and parent_id<>".$row['id']." order by  title ASC";
					                                        $edit_nodes=$crud->getData($query);

					                                        if($edit_nodes != false && count($edit_nodes)>0)
					                                        {
					                                        	$edit_tree=parseTree($edit_nodes);
					                                        	printTree($edit_tree,$row['parent_id']);
					                                        } 
					                                     ?>
	                                            	</select>
	                                            </div>
	                                        </div>
	        								<div class="control-group">
	        									<label class="control-label" for="basicinput">Focal Person</label>
	        									<div class="controls">
	        										<select name="focal_person">
	        										<?php 
	                                                    $fp_nodes=$crud->getData("select admin.*,roles.title as roleName,roles.parent_id  from admin left join roles on admin.role=roles.id where roles.parent_id>=0 and  admin.department_id=".$row['id']." order by roles.sort_order ASC, roles.title ASC");
	                                                      	if($fp_nodes != false && count($fp_nodes)>0)
					                                        {
					                                        	$fp_tree=parseTreeAdmin($fp_nodes);
					                                        	//var_dump($fp_tree);
	                                                     		printTreeAdmin($fp_tree,$row['focal_person']);
	                                                     	}
	                                                ?>
	                                                </select>
	        									</div>
	        								</div>
	        								
	        								<div class="control-group">
												<label class="control-label" for="basicinput">Image</label>
												<div class="controls">
													<input type="file" name="image">
													<?php if(!empty($row['logo'])){?><a href="uploads/<?php echo htmlentities($row['logo']);?>" target="_blank"><img src="uploads/<?php echo htmlentities($row['logo']);?>" width="32" height="auto"></a><?php }?>
												</div>
											</div>
											
											<?php if($editFlag){?>										
												<div class="control-group">
													<div class="controls">
														<button type="submit" name="edit" class="btn btn-primary">Update</button>
													</div>
												</div>
											<?php }?>
										</form>
									</div>
								</div>

							<?php }?>
						<?php }?>
				      	


						<div class="module">
							<div class="module-head">
								<h3>Manage Departments <?php if(authorizeAccess($module_name,'add')){?><span style="float:right"><a href="javascript:;" class="showModule" data-target="addModule"><i class="icon-plus"></i> Add Depertment</a></span><?php }?></h3>
							</div>
							<div class="module-body table">
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" width="100%">
									<thead>
										<tr>
											<th width="5%">#</th>
											<th>Department</th>
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

	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	
	<script src="scripts/datatables/jquery.dataTables.js"></script>
	<script src="scripts/common.js"></script>
	<script>
		$(document).ready(function() {
			$('.datatable-1').dataTable();
			$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
			
		} );
	</script>
</body>