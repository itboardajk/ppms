<?php
include('classes/config.php');
authenticate();

if(isset($_POST['submit']))
{ 
	$password = $crud->escape_string($_POST['password']); 
	$new_password = $crud->escape_string($_POST['newpassword']);
    $confirmpassword = $crud->escape_string($_POST['confirmpassword']);
    
	$msg = $validation->check_empty($_POST, array(array('password','Current Password'), array('newpassword','New Password'), array('confirmpassword','Conrfirm New Password')));

	if($msg != null) {
		$errmsg = "Please correct the following errors:<br>".$msg;
	}    
	else if($new_password != $confirmpassword)
	{
	    $errmsg = 'New password and confirm password mismatch:';
	}
	else { 
	    
    	$password = md5($crud->escape_string($_POST['password']));
    	$new_password = md5($crud->escape_string($_POST['newpassword']));
    	
		$query ="SELECT password FROM  admin where password='".$password."' && id='".$_SESSION['id']."'";

		$result = $crud->getData($query);

		if($result != false && count($result)>0)
		{
			$query = "update admin set password='".$new_password."' where id='".$_SESSION['id']."'";
			if($crud->execute($query))
				$sucmsg="Password Changed Successfully !!";
			else
				$errmsg="Unable to update the password, please try again.";
		}
		else
		{
			$errmsg="Old Password not match !!";
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Change Password | <?php echo  $site_title?></title>
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

						<div class="module">
							<div class="module-head">
								<h3>Admin Change Password</h3>
							</div>
							<div class="module-body">
								<?php if(!empty($errmsg)){?>
									<div class="alert alert-error">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<strong>Error!</strong>	<?php echo htmlentities($errmsg);?>
									</div>
								<?php }else if(!empty($sucmsg)){?>
									<div class="alert alert-success">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<strong>Well done!</strong>	<?php echo htmlentities($sucmsg);?>
									</div>
								<?php }?>
								<form class="form-horizontal row-fluid" name="chngpwd" method="post" onSubmit="return valid();">
									
									<div class="control-group">
										<label class="control-label" for="basicinput">Current Password</label>
										<div class="controls">
											<input type="password" placeholder="Enter your current Password"  name="password" class="span8 tip" required>
										</div>
									</div>


									<div class="control-group">
										<label class="control-label" for="basicinput">New Password</label>
										<div class="controls">
											<input type="password" placeholder="Enter your new current Password"  name="newpassword" class="span8 tip" required>
										</div>
									</div>

									<div class="control-group">
										<label class="control-label" for="basicinput">Confirm New Password</label>
										<div class="controls">
											<input type="password" placeholder="Enter your new Password again"  name="confirmpassword" class="span8 tip" required>
										</div>
									</div>

									<div class="control-group">
										<div class="controls">
											<button type="submit" name="submit" class="btn btn-primary">Submit</button>
										</div>
									</div>
								</form>
							</div>
						</div>					
						
					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->

	<?php include('include/footer.php');?>

    <?php include_once('include/foot.php');?>
	<script type="text/javascript">
		function valid()
		{
			if(document.chngpwd.password.value=="")
			{
				alert("Current Password Filed is Empty !!");
				document.chngpwd.password.focus();
				return false;
			}
			else if(document.chngpwd.newpassword.value=="")
			{
				alert("New Password Filed is Empty !!");
				document.chngpwd.newpassword.focus();
				return false;
			}
			else if(document.chngpwd.confirmpassword.value=="")
			{
				alert("Confirm Password Filed is Empty !!");
				document.chngpwd.confirmpassword.focus();
				return false;
			}
			else if(document.chngpwd.newpassword.value!= document.chngpwd.confirmpassword.value)
			{
				alert("Password and Confirm Password Field do not match  !!");
				document.chngpwd.confirmpassword.focus();
				return false;
			}
			return true;
		}
	</script>
</body>
</html>