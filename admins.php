<?php
$module_name = 'admin';
include('classes/config.php');
authenticate();



$viewFlag=authorizeAccess($module_name,'view');
$addFlag=authorizeAccess($module_name,'add');
$editFlag=authorizeAccess($module_name,'edit');
$deleteFlag=authorizeAccess($module_name,'delete');

if(!$viewFlag){header("location:{$site_url}/dashboard.php");exit();}

if(isset($_POST['add']))
{
	if($addFlag)
	{
		$display_name=$crud->escape_string($_POST['display_name']);
		$email=$crud->escape_string($_POST['email']);
		$cnic=$crud->escape_string($_POST['cnic']);
		$role=$crud->escape_string($_POST['role']);
		$username=$crud->escape_string($_POST['username']);
		$jurisdiction=$crud->escape_string($_POST['jurisdiction']);
		$office_contact=$crud->escape_string($_POST['office_contact']);
		$mobile_contact=$crud->escape_string($_POST['mobile_contact']);
		$status=$crud->escape_string($_POST['status']);
		$password=md5($crud->escape_string($_POST['password']));

		$msg = $validation->check_empty($_POST, array(array('display_name','Name'),array('mobile_contact','Cell Number'),array("username","Username"),array("password","Password")));



		if($msg != null) {
			$errmsg = 'Please correct the following errors:<br>'.$msg;
		}    
		else {
			if(!empty($_FILES['admin_image']['name']))
			{
		    	$file_name = $_FILES['admin_image']['name'];
		        $file_size =$_FILES['admin_image']['size'];
		        $file_tmp =$_FILES['admin_image']['tmp_name'];
		        $file_type=$_FILES['admin_image']['type'];
		        $file_ext=@strtolower(end(explode('.',$_FILES['admin_image']['name'])));
		       

				$mimetype = mime_content_type($_FILES['admin_image']['tmp_name']);


		        $expensions= array("jpeg","jpg","png");
		        $mimes = array('image/jpeg',  'image/png');

		        if(in_array($file_ext,$expensions)=== false || in_array($mimetype,$mimes)=== false){
		         $errmsg ="extension not allowed, please choose a JPEG, JPG or PNG file.";
		        }
		        
		        if($file_size > 1097152){
		         $errmsg ='File size must be less then 1 MB';
		        }

				if(in_array($mimetype, array('image/jpeg', 'image/gif', 'image/png'))) 
				{

				}
			}
			if(!empty($_FILES['admin_sign']['name']))
	        {
	            $file_name = $_FILES['admin_sign']['name'];
	            $file_size =$_FILES['admin_sign']['size'];
	            $file_tmp =$_FILES['admin_sign']['tmp_name'];
	            $file_type=$_FILES['admin_sign']['type'];
	            $file_ext_sig =@strtolower(end(explode('.',$_FILES['admin_sign']['name'])));	           
				$mimetype = mime_content_type($_FILES['admin_sign']['tmp_name']);


		        $expensions= array("jpeg","jpg","png");
		        $mimes = array('image/jpeg',  'image/png');
	            
		        if(in_array($file_ext_sig,$expensions)=== false || in_array($mimetype,$mimes)=== false){
	             $errmsg="Signature: extension not allowed, please choose a JPEG or PNG file.";
	            }
	            
	            if($file_size > 1097152){
	             $errmsg='Signature: File size must be less then 1 MB';
	            }
	        }
		      
		    if(empty($errmsg ))
		    {
	        	$imagefile = '';
		        if(!empty($_FILES['admin_image']['name']))
			    {
		        	$imagefile="uploads/admin_images/admin_".date('YmdHis').'.'.$file_ext;
		        	move_uploaded_file($_FILES["admin_image"]["tmp_name"],$imagefile);
			    }
			    $imagefile_sig ='';
	            if(!empty($_FILES['admin_sign']['name']))
	            {
	                $imagefile_sig="uploads/admin_images/signs/signs_".date('YmdHis').'.'.$file_ext_sig;
	                move_uploaded_file($_FILES["admin_sign"]["tmp_name"],$imagefile_sig);	                
	            }

		        $sql="INSERT INTO admin(display_name,email,cnic,role,username,password,admin_image,admin_sign,office_contact,mobile_contact,status,department_id,added_by,jurisdiction) values('$display_name','$email','$cnic','$role','$username','$password','$imagefile','$imagefile_sig','$office_contact','$mobile_contact','$status','".$_SESSION['department_id']."','".$_SESSION['id']."','$jurisdiction')";
		         
		        $result = $crud->insert_and_get_id($sql);

				if($result != false)
				{
					$sucmsg="Admin Created !!";
					unset($_POST);
					$crud->log('Admin('.$result.') Added',$_SESSION['id']);
				}
				else
				{
					$errmsg = 'Unable to create new admin.';
				}
		    }
		}
	}
	else
	{
		$errmsg = 'You do not have access to add admin';
	}

}
else if(isset($_POST['edit']))
{
	if($editFlag)
	{
		$id=intval($crud->escape_string($_GET['view']));

		$display_name=$crud->escape_string($_POST['display_name']);
		$email=$crud->escape_string($_POST['email']);
		$cnic=$crud->escape_string($_POST['cnic']);
		$role=$crud->escape_string($_POST['role']);
		$jurisdiction=$crud->escape_string($_POST['jurisdiction']);
		$office_contact=$crud->escape_string($_POST['office_contact']);
		$mobile_contact=$crud->escape_string($_POST['mobile_contact']);
		$username=$crud->escape_string($_POST['username']);
		$status=$crud->escape_string($_POST['status']);

		$msg = $validation->check_empty($_POST, array(array('display_name','Name'),array('mobile_contact','Cell Number'),array("username","Username")));


		if($msg != null) {
			$errmsg = 'Please correct the following errors:<br>'.$msg;
		}    
		else {
			$updb='';
			if(!empty($_POST['password']))
			{
			    $password=md5($crud->escape_string($_POST['password']));
			    $updb =",password='$password'";
			}
		
			if(!empty($_FILES['admin_image']['name']))
			{
		    	$file_name = $_FILES['admin_image']['name'];
		        $file_size =$_FILES['admin_image']['size'];
		        $file_tmp =$_FILES['admin_image']['tmp_name'];
		        $file_type=$_FILES['admin_image']['type'];
		        $file_ext=@strtolower(end(explode('.',$_FILES['admin_image']['name'])));
		       
				$mimetype = mime_content_type($_FILES['admin_image']['tmp_name']);


		        $expensions= array("jpeg","jpg","png");
		        $mimes = array('image/jpeg',  'image/png');
	            
		        if(in_array($file_ext,$expensions)=== false || in_array($mimetype,$mimes)=== false){
		         $errmsg="Admin Image: extension not allowed, please choose a JPEG or PNG file.";
		        }
		        
		        if($file_size > 1097152){
		         $errmsg='Admin Image: File size must be less then 1 MB';
		        }
			}
	        if(!empty($_FILES['admin_sign']['name']))
	        {
	            $file_name = $_FILES['admin_sign']['name'];
	            $file_size =$_FILES['admin_sign']['size'];
	            $file_tmp =$_FILES['admin_sign']['tmp_name'];
	            $file_type=$_FILES['admin_sign']['type'];
	            $file_ext_sig =@strtolower(end(explode('.',$_FILES['admin_sign']['name'])));
	           
				$mimetype = mime_content_type($_FILES['admin_sign']['tmp_name']);


		        $expensions= array("jpeg","jpg","png");
		        $mimes = array('image/jpeg', 'image/png');
	            
		        if(in_array($file_ext_sig,$expensions)=== false || in_array($mimetype,$mimes)=== false){
	             $errmsg="Signature: extension not allowed, please choose a JPEG or PNG file.";
	            }
	            
	            if($file_size > 1097152){
	             $errmsg='Signature: File size must be less then 1 MB';
	            }
	        }
		      
		    if(empty($errmsg))
		    {
		        if(!empty($_FILES['admin_image']['name']))
			    {
		        	$imagefile="uploads/admin_images/admin_".date('YmdHis').'.'.$file_ext;
		        	move_uploaded_file($_FILES["admin_image"]["tmp_name"],$imagefile);
		        	$updb .=",admin_image='$imagefile'";
		        	
			    }

	            if(!empty($_FILES['admin_sign']['name']))
	            {
	                $imagefile_sig="uploads/admin_images/signs/signs_".date('YmdHis').'.'.$file_ext_sig;
	                move_uploaded_file($_FILES["admin_sign"]["tmp_name"],$imagefile_sig);
	                $updb .=",admin_sign='$imagefile_sig'";
	                
	            }
			    $sql=$crud->execute("update admin set display_name='$display_name',email='$email',cnic='$cnic',role='$role',office_contact='$office_contact',mobile_contact='$mobile_contact',username='$username',updated_by='".$_SESSION['id']."',status='$status', jurisdiction='$jurisdiction' $updb where id='$id'");
			    if($sql != false)
				{
					$sucmsg="Admin Updated !!";
					unset($_POST);
					$crud->log('Admin('.$sql.') Updated',$_SESSION['id']);
				}
				else
				{
					$errmsg = 'Unable to updated  admin.';
				}
		       
		    }
		}

	}
	else
	{
		$errmsg = 'You do not have access to updated admin';
	}

}
else if(isset($_GET['delete']) && $_GET['delete']>0)
{
	if($deleteFlag)
	{
		$id=intval($crud->escape_string($_GET['delete']));

		$query = "delete from admin where id=$id";
		$result = $crud->execute($query);

		$sucmsg="Admin deleted !!";
		$crud->log('Admin('.$id.') deleted',$_SESSION['id']);
	}
	else
	{
		$errmsg = 'You do not have access to delete admin';
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Admins | <?php echo  $site_title?></title>
	
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

					      	<?php if(isset($_GET['view'])){
								$query="select * from admin where department_id='".$_SESSION['department_id']."' and id=".$_GET['view'];
								$enode = $crud->getData($query);
								if($enode != false && count($enode)>0)
								{
									$enode = $enode[0];
									?>
									<div class="module editModule">
										<div class="module-head">
											<h3><?php echo ($editFlag)?'Edit ':'View ';?> Admin <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="editModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
										</div>
										<div class="module-body">
											<form class="form-horizontal row-fluid" name="editadmin" method="post"  enctype="multipart/form-data" action="admins.php?view=<?php echo $_GET['view'];?>">
												<div class="control-group">
			    									<label class="control-label" for="basicinput">Display Name</label>
			    									<div class="controls">
			    										<input type="text" name="display_name" class="span8 tip" required="" value="<?php echo @$enode['display_name']?>">
			    									</div>
			    								</div>
			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Email</label>
			    									<div class="controls">
			    										<input type="email" name="email" class="span8 tip" required="" value="<?php echo @$enode['email']?>">
			    									</div>
			    								</div>
												<div class="control-group">
													<label class="control-label" for="basicinput">CNIC</label>
													<div class="controls">
														<input type="text" name="cnic" class="span8 tip" required="" value="<?php echo @$enode['cnic']?>">
													</div>
												</div>
			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Role</label>
			    									<div class="controls">
			    										<select name="role">
			    										<?php 
			    											$nodes=$crud->getData("select * from roles where department_id='".$_SESSION['department_id']."' order by sort_order ASC, title ASC");

			                                                			                                                
			                                               $tree=parseTree($nodes);
			                                               printTree($tree,$enode['role']);
			                                              ?>
			                                            </select>
			    									</div>
			    								</div>
			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Projects Jurisdiction</label>
			    									<div class="controls">
			    										<select name="jurisdiction">
			    											<?php if(isset($_SESSION['jurisdiction']) && $_SESSION['jurisdiction'] == 'All'){?>
			    												<option value="All" <?php if($enode['jurisdiction']=='All'){echo 'selected="selected"';}?>>All</option>
			    											<?php }?>
			    											<?php if(isset($_SESSION['jurisdiction']) && ($_SESSION['jurisdiction'] == 'All' || $_SESSION['jurisdiction']=='Departmental & Sub-Departmentals')){?>
			    												<option value="Departmental & Sub-Departmentals" <?php if($enode['jurisdiction']=='Departmental & Sub-Departmentals'){echo 'selected="selected"';}?>>Departmental & Sub-Departmentals</option>
			    											<?php }?>
			    											<?php if(isset($_SESSION['jurisdiction']) && ($_SESSION['jurisdiction'] == 'All' || $_SESSION['jurisdiction']=='Departmental & Sub-Departmentals' || $_SESSION['jurisdiction']=='Departmental')){?>
			    												<option value="Departmental" <?php if($enode['jurisdiction']=='Departmental'){echo 'selected="selected"';}?>>Departmental</option>
			    											<?php }?>
		    												<option value="Created" <?php if($enode['jurisdiction']=='Created'){echo 'selected="selected"';}?>>Created</option>
			                                            </select>
			    									</div>
			    								</div>

			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Phone(office)</label>
			    									<div class="controls">
			    										<input type="text" name="office_contact" class="span8 tip" required="" value="<?php echo @$enode['office_contact']?>">
			    									</div>
			    								</div>
			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Mobile</label>
			    									<div class="controls">
			    										<input type="text" name="mobile_contact" class="span8 tip" required="" value="<?php echo @$enode['mobile_contact']?>">
			    									</div>
			    								</div>
			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Login Username</label>
			    									<div class="controls">
			    										<input type="text" name="username" class="span8 tip" required="" value="<?php echo @$enode['username']?>">
			    									</div>
			    								</div>
			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Login Password</label>
			    									<div class="controls">
			    										<input type="password" name="password" class="span8 tip" value="">
			    									</div>
			    								</div>
			    								<div class="control-group">
													<label class="control-label" for="basicinput">Image</label>
													<div class="controls">
														<input type="file" name="admin_image">

														<?php if(!empty($enode['admin_image'])){?><a href="<?php echo htmlentities($enode['admin_image']);?>" target="_blank"><img src="<?php echo htmlentities($enode['admin_image']);?>" width="32" height="auto"></a><?php }?>
													</div>
												</div>
		    									<div class="control-group">
													<label class="control-label" for="basicinput">Sign</label>
													<div class="controls">
														<input type="file" name="admin_sign">
														<?php if(!empty($enode['admin_sign'])){?><a href="<?php echo htmlentities($enode['admin_sign']);?>" target="_blank"><img src="<?php echo htmlentities($enode['admin_sign']);?>" width="32" height="auto"></a><?php }?>
													</div>
												</div>
	    										<div class="control-group">
													<label class="control-label" for="basicinput">Status</label>
													<div class="controls">
														<select name="status">
														    <option value="1">Enabled</option>
														    <option value="0" <?php if($enode['status']==0){ echo 'selected="selected"';}?>>Disabled</option>
														</select>
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

								<?php }else{
									echo 'You are not allowed to see this record.';
								}?>
							<?php }?>
					      	<?php if($addFlag){
					      		?>
			                    <div class="module addModule" <?php if(isset($_POST['add'])){echo 'style="display:block"';}?>>
									<div class="module-head">
										<h3>Add Admin <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
									</div>
									<div class="module-body">
		    							<form class="form-horizontal row-fluid" name="addadmin" method="post"  enctype="multipart/form-data" action="admins.php">
		    							    <div class="control-group">
		    									<label class="control-label" for="basicinput">Display Name</label>
		    									<div class="controls">
		    										<input type="text" name="display_name" class="span8 tip" required="" value="<?php echo @$_POST['display_name']?>">
		    									</div>
		    								</div>
		    								<div class="control-group">
		    									<label class="control-label" for="basicinput">Email</label>
		    									<div class="controls">
		    										<input type="email" name="email" class="span8 tip" required="" value="<?php echo @$_POST['email']?>">
		    									</div>
		    								</div>
											<div class="control-group">
												<label class="control-label" for="basicinput">CNIC</label>
												<div class="controls">
													<input type="text" name="cnic" class="span8 tip" required="" value="<?php echo @$_POST['cnic']?>">
												</div>
											</div>
		    								<div class="control-group">
		    									<label class="control-label" for="basicinput">Role</label>
		    									<div class="controls">
		    										<select name="role">
		    										<?php 
		    											$nodes=$crud->getData("select * from roles where department_id='".$_SESSION['department_id']."'  order by sort_order ASC, title ASC");
		                                                
		                                               $tree=parseTree($nodes);
		                                               printTree($tree);
		                                              ?>
		                                            </select>
		    									</div>
		    								</div>
		    								<div class="control-group">
		    									<label class="control-label" for="basicinput">Projects Jurisdiction</label>
		    									<div class="controls">
		    										<select name="jurisdiction">
		    											<?php if(isset($_SESSION['jurisdiction']) && $_SESSION['jurisdiction'] == 'All'){?>
			    												<option value="All" <?php if(@$_POST['jurisdiction']=='All'){echo 'selected="selected"';}?>>All</option>
			    											<?php }?>
			    											<?php if(isset($_SESSION['jurisdiction']) && ($_SESSION['jurisdiction'] == 'All' || $_SESSION['jurisdiction']=='Departmental & Sub-Departmentals')){?>
			    												<option value="Departmental & Sub-Departmentals" <?php if(@$_POST['jurisdiction']=='Departmental & Sub-Departmentals'){echo 'selected="selected"';}?>>Departmental & Sub-Departmentals</option>
			    											<?php }?>
			    											<?php if(isset($_SESSION['jurisdiction']) && ($_SESSION['jurisdiction'] == 'All' || $_SESSION['jurisdiction']=='Departmental & Sub-Departmentals' || $_SESSION['jurisdiction']=='Departmental')){?>
			    												<option value="Departmental" <?php if(@$_POST['jurisdiction']=='Departmental'){echo 'selected="selected"';}?>>Departmental</option>
			    											<?php }?>
		    											<option value="Created">Created</option>
		                                            </select>
		    									</div>
		    								</div>
		    								
		    								<div class="control-group">
		    									<label class="control-label" for="basicinput">Phone(office)</label>
		    									<div class="controls">
		    										<input type="text" name="office_contact" class="span8 tip" value="<?php echo @$_POST['office_contact']?>">
		    									</div>
		    								</div>
		    								<div class="control-group">
		    									<label class="control-label" for="basicinput">Mobile</label>
		    									<div class="controls">
		    										<input type="text" name="mobile_contact" class="span8 tip"  value="<?php echo @$_POST['mobile_contact']?>">
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
												<label class="control-label" for="basicinput">Image</label>
												<div class="controls">
													<input type="file" name="admin_image">
												</div>
											</div>
    										<div class="control-group">
												<label class="control-label" for="basicinput">Sign</label>
												<div class="controls">
													<input type="file" name="admin_sign">
												</div>
											</div>
    										<div class="control-group">
												<label class="control-label" for="basicinput">Status</label>
												<div class="controls">
													<select name="status">
													    <option value="1">Enabled</option>
													    <option value="0" <?php if(@$_POST['status']==0){ echo 'selected="selected"';}?>>Disabled</option>
													</select>
												</div>
											</div>
		                                    <div class="control-group">
		    									<div class="controls">
		    										<button type="submit" name="add" class="btn btn-primary">Create</button>
		    									</div>
		    								</div>
		    						    </form>
								    </div>
								</div>
							<?php }?>
						
	                    <div class="module">
							<div class="module-head">
								<h3>Manage Admins <?php if($addFlag){?><span style="float:right"><a href="javascript:;" class="showModule" data-target="addModule"><i class="icon-plus"></i> Add Admin</a></span><?php }?></h3>
							</div>
							<div class="module-body table">
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Name</th>
											<th>Role</th>
											<th>Email </th>
											<th>Last Login</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>

		                                <?php 
										
											$query=$crud->getData("select admin.*,roles.title as roleName from admin left join roles on admin.role=roles.id where  admin.department_id=".$_SESSION['department_id']);

		                                $cnt=1;
		                                foreach($query as $row)
		                                {
		                                ?>									
											<tr>
												<td><?php echo htmlentities($cnt);?></td>
												<td><?php if(!empty($row['admin_image'])){?><a href="<?php echo htmlentities($row['admin_image']);?>" target="_blank"><img src="<?php echo htmlentities($row['admin_image']);?>" width="32" height="auto"></a><?php }?> <a href="admins.php?view=<?php echo htmlentities($row['id'])?>"><?php echo htmlentities($row['display_name']);?></a></td>
												<td><?php echo htmlentities($row['roleName']);?></td>
												<td> <?php echo htmlentities($row['email']);?></td>
												<td><?php echo htmlentities($row['last_login']);?></td>
												<td>
													<a href="admins.php?delete=<?php echo htmlentities($row['id'])?>" onclick="return confirm('Are you sure you want to delete?')"><i class="icon-remove-sign"></i></a></td>
												</td>
											</tr>	
										<?php $cnt=$cnt+1; } ?>
									</tbody>
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>

	<script  type="text/javascript">
		$(document).ready(function() {
			$('.datatable-1').dataTable();
			$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');


			$(".multi-select").select2();
		} );
	</script>
</body>
</html>