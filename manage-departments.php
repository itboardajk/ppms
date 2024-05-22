<?php
session_start();
include('include/config.php');
authenticate();

if($_SESSION['id']!=1)
	header("location:{$site_url}dashboard.php");

if(isset($_POST['submit']))
{
	$name=$_POST['name'];
	$name_urdu=$_POST['name_urdu'];

	$displayName=$_POST['displayName'];
	$displayName_urdu=$_POST['displayName_urdu'];

	$email=$_POST['email'];
	$username=$_POST['username'];
	$officeContact=$_POST['officeContact'];
	$mobileContact=$_POST['mobileContact'];

	$password=md5($_POST['password']);
	
	$sql=$mysqli->query("SELECT * from admin where username='$username'");
	if($sql->num_rows>0)
	{
		$_SESSION['emsg']="Username already exist!!";
	}
	else
	{
		if(!empty($_FILES['image']['name']))
		{
	    	$file_name = $_FILES['image']['name'];
	        $file_size =$_FILES['image']['size'];
	        $file_tmp =$_FILES['image']['tmp_name'];
	        $file_type=$_FILES['image']['type'];
	        @$file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
	       
	        $expensions= array("jpeg","jpg","png");
	        
	        if(in_array($file_ext,$expensions)=== false){
	        	$_SESSION['emsg']="Department Picture: Extension not allowed, please choose a JPEG or PNG file.";
	        }
	        
	        if($file_size > 1097152){
	        	$_SESSION['emsg']='Department Picture: File size must be less then 1 MB';
	        }
		}
		      
	    if(empty($_SESSION['emsg']))
	    {
	        $imagefile="";
	        if(!empty($_FILES['image']['name']))
		    {
	        	$imagefile="department_images/department_".date('YmdHis').'.'.$file_ext;
	        	move_uploaded_file($_FILES["image"]["tmp_name"],$imagefile);
		    }

		    $add_dep = "insert into departments(name,name_urdu,logo,createdBy) values('$name','$name_urdu','$imagefile','".$_SESSION['id']."')";
	        $mysqli->query($add_dep);
	       
	        $query=$mysqli->query("select * from departments where name ='".$name."'");
			$rowD=$query->fetch_assoc();
	        
	        $add_role = "insert into roles(role,role_urdu,pRole,sortOrder,department_id) values('Administrator','ایڈمنسٹریٹر','0','0',".$rowD['id'].")";
			$mysqli->query($add_role);

			$query=$mysqli->query("select * from roles where role ='Administrator' and department_id=".$rowD['id']);
			$rowR=$query->fetch_assoc();

			$add_fp="insert into admin(displayName,displayName_urdu,email,role,username,password,department_id,officeContact,mobileContact,status) values('$displayName','$displayName_urdu','$email','".$rowR['id']."','$username','$password','".$rowD['id']."','$officeContact','$mobileContact','1')";
			$mysqli->query($add_fp);

			$query=$mysqli->query("select * from admin where username ='".$username."' and department_id=".$rowD['id']);
			$rowA=$query->fetch_assoc();

			$update_dep = "update departments set focal_person='".$rowA['id']."' where id=".$rowD['id'];
			$mysqli->query($update_dep);

	        $_SESSION['msg']="Department Created !!";
	         unset($_POST);
	    }
	}


	
	

}

if(isset($_GET['del']))
{
      $mysqli->query("delete from departments where id = '".$_GET['id']."'");
      $mysqli->query("delete from admin where department_id = '".$_GET['id']."'");
      $mysqli->query("delete from roles where department_id = '".$_GET['id']."'");
      $_SESSION['delmsg']="Department deleted !!";
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo  $title?> | Manage Departments</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>

	<link rel="apple-touch-icon" sizes="57x57" href="images/favicon/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="images/favicon/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="images/favicon/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="images/favicon/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="images/favicon/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="images/favicon/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="images/favicon/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="images/favicon/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="images/favicon/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="images/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="images/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="images/favicon/favicon-16x16.png">
	<link rel="manifest" href="images/favicon/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="images/favicon/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
</head>
<body>
<?php include('include/header.php');?>

	<div class="wrapper">
		<div class="container">
			<div class="row">
<?php include('include/sidebar.php');?>				
			<div class="span9">
					<div class="content">
					    <?php if(isset($_POST['submit'])){?>
							<?php if(!empty($_SESSION['emsg'])){?>
							    <div class="alert alert-error">
								    <button type="button" class="close" data-dismiss="alert">×</button>
								    <strong>Error!</strong>	<?php echo htmlentities($_SESSION['emsg']);?><?php echo htmlentities($_SESSION['emsg']="");?>
								</div>
							<?php }else{?>
							    <div class="alert alert-success">
								    <button type="button" class="close" data-dismiss="alert">×</button>
								    <strong>Well done!</strong>	<?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
								</div>
							<?php }?>
						<?php } ?>


						<?php if(isset($_GET['del'])){?>
							<div class="alert alert-error">
								<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>Oh snap!</strong> 	<?php echo htmlentities(@$_SESSION['delmsg']);?><?php echo htmlentities(@$_SESSION['delmsg']="");?>
							</div>
                        <?php } ?>

	                    <div class="module addModule">
							<div class="module-head">
								<h3>Add Department <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
							</div>
							<div class="module-body">
    							<form class="form-horizontal row-fluid" name="subcategory" method="post"  enctype="multipart/form-data" action="manage-departments.php">
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
    										<button type="submit" name="submit" class="btn btn-primary">Create</button>
    									</div>
    								</div>
    						    </form>
						    </div>
						</div>
						
	                    <div class="module">
							<div class="module-head">
								<h3>Manage Departments <span style="float:right"><a href="javascript:;" class="showModule" data-target="addModule"><i class="icon-plus"></i> Add Department</a></span></h3>
							</div>
							<div class="module-body table">
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Name</th>
											<th>Focal Person</th>
											<th>Action</th>
										
										</tr>
									</thead>
									<tbody>

                                    <?php $query2=$mysqli->query("select departments.*,admin.displayName from departments left join admin on departments.focal_person=admin.id");
                                    $cnt=1;
                                    while($row=$query2->fetch_assoc())
                                    {
                                    ?>									
										<tr>
    										<td><?php echo htmlentities($cnt);?></td>
    										<td><?php if(!empty($row['logo'])){?><a href="<?php echo htmlentities($row['logo']);?>" target="_blank"><img src="<?php echo htmlentities($row['logo']);?>" width="64" height="auto"></a><?php }?> <?php echo htmlentities($row['name']);?></td>
    										<td><?php echo htmlentities($row['displayName']);?></td>
    										<td>
    											<a href="edit-department.php?id=<?php echo htmlentities($row['id'])?>"><i class="icon-edit"></i></a>
    											<a href="manage-departments.php?id=<?php echo htmlentities($row['id'])?>&del=delete" onclick="return confirm('Are you sure you want to delete?')"><i class="icon-remove-sign"></i></a></td>
    										</td>
										</tr>	
									<?php $cnt=$cnt+1; } ?>
										
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