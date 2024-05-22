<?php
include("classes/config.php");
if(isset($_POST['submit']))
{

	$username = $crud->escape_string($_POST['username']);
	$password = md5($crud->escape_string($_POST['password'])); 

	$msg = $validation->check_empty($_POST, array(array('username','Username'), array('password','Password')));
	if($msg != null) {
		$errmsg = 'Please correct the following errors:<br>'.$msg;
	}    
	else { 
		$query ="
		SELECT admin.*,roles.accesses, roles.title as role_name,roles.parent_id as role_parent,departments.title as department_name,departments.focal_person as dfp
		FROM admin 
		left join roles on admin.role=roles.id 
		left join departments on admin.department_id=departments.id 
		WHERE (admin.username='$username' OR admin.email='$username' OR admin.cnic='$username') and admin.password='$password' 
		limit 1";

		$result = $crud->getData($query);
		if($result != false && count($result)>0)
		{

			if($result[0]['status']==0)
			{
				$errmsg="Your account has been disabled, please contact administrator.";
			}
			else
			{
				if(!empty($result[0]['last_login']))
				{
					if(isset($_SESSION['myreffer']) && !empty($_SESSION['myreffer']))
						$extra = $_SESSION['myreffer'];
					else
						$extra="dashboard.php";
				}
				else
					$extra="change-password.php";

				$_SESSION['ppms']='ppms';
				$_SESSION['alogin']=$username;
				$_SESSION['aname']=$result[0]['display_name'];
				$_SESSION['aimage']=$result[0]['admin_image'];
				$_SESSION['asign']=$result[0]['admin_sign'];
				
				$_SESSION['department_id'] = $result[0]['department_id'];
				$_SESSION['department_name'] = $result[0]['department_name'];
				

				$_SESSION['jurisdiction'] = $result[0]['jurisdiction'];
				if($_SESSION['jurisdiction']=='Departmental & Sub-Departmentals')
				{
					$_SESSION['sub_departments_id'] = getSubDepartments($_SESSION['department_id']);
					$_SESSION['sub_departments_id'][]=$_SESSION['department_id'];
				}

				//$_SESSION['accesses']=json_decode($result[0]['accesses'],true);

				$_SESSION['ppmsRole']=$result[0]['role'];
				$_SESSION['ppmsRoleName']=$result[0]['role_name'];
				$_SESSION['ppmsRoleParent']=$result[0]['role_parent'];


				$_SESSION['id']=$result[0]['id'];
				$crud->execute("update admin set last_login=NOW() where id='".$result[0]['id']."'");

				$crud->log('Logged into system.');
               // echo "$site_url$extra";exit;
                
				header("location:$site_url$extra");
				exit();
			}
		}
		else
		{
			$errmsg="Invalid username or password";
		}

	}
}

if(@strlen($_SESSION['alogin'])>0 && isset($_SESSION['ppms']) && $_SESSION['ppms']=='ppms')
{
	header("location:{$site_url}dashboard.php");
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login | <?php echo  $site_title?></title>
	<?php include_once('include/head.php');?>
</head>
<body class="login_body">

	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
					<i class="icon-reorder shaded"></i>
				</a>

				<a class="brand" href="index.php">
					<i class="icon-time" style="font-size: 22px;margin-right: 10px;"></i> <?php echo $site_name;?><!-- icon-legal -->
				</a>

				<div class="nav-collapse collapse navbar-inverse-collapse">

					<ul class="nav pull-right">
						<li><a href="https://ajk.gov.pk/">Back to website</a></li>
					</ul>
				</div><!-- /.nav-collapse -->
			</div>
		</div><!-- /navbar-inner -->
	</div><!-- /navbar -->



	<div class="wrapper">
		<div class="container">
			<div class="row">
				<div class="col-md-12 align-center">
					<img src="images/ajklogo.png" width="100">
					<div class="login-title"><b><?php echo $site_name;?></b><br>Government of AJ&K</div></div>
					<div class="module module-login span4 offset4">
						<form class="form-vertical" method="post">
							<div class="module-head">
								<h3>Sign In</h3>
							</div>
							<?php if(!empty($errmsg)){?><div style="color:red;padding: 16px 16px 4px;" ><?php echo htmlentities($errmsg);$errmsg="";?></div><?php }?>
							<div class="module-body">
								<div class="control-group">
									<div class="controls row-fluid">
										<input class="span12" type="text" id="inputEmail" name="username" placeholder="Username / Email / CNIC">
									</div>
								</div>
								<div class="control-group">
									<div class="controls row-fluid">
										<input class="span12" type="password" id="inputPassword" name="password" placeholder="Password">
									</div>
								</div>
							</div>
							<div class="module-foot">
								<div class="control-group">
									<div class="controls clearfix">
										<button type="submit" class="btn btn-primary pull-right" name="submit">Login</button>

									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div><!--/.wrapper-->

		<div class="footer">
			<div class="container" style="text-align:center;">
				<b class="copyright">&copy; <?php echo date("Y");?> - All rights reserved. Brought to you by <a href="http://itb.ajk.gov.pk" target="_blank">Information Technology Board of Azad Jammu & Kashmir</a>.</b>
			</div>
		</div>
		<?php include_once('include/foot.php');?>
	</body>
</html>