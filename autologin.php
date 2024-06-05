<?php
include("classes/config.php");

if(isset($_GET['u']) && isset($_GET['p']))
{
	$username = $crud->escape_string(base64_decode($_GET['u']));
	$password = md5($crud->escape_string(base64_decode($_GET['p']))); 

	// $msg = $validation->check_empty($_GET, array(array('username','Username'), array('password','Password')));
	// if($msg != null) {
	// 	$errmsg = 'Please correct the following errors:<br>'.$msg;
	// }    
	// else { 
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
            //    echo "$site_url$extra";exit;
                
				header("location:$site_url$extra");
				exit();
			}
		}
		else
		{
			$errmsg="Invalid username or password";
		}

	// }
}

if(@strlen($_SESSION['alogin'])>0 && isset($_SESSION['ppms']) && $_SESSION['ppms']=='ppms')
{
	header("location:{$site_url}dashboard.php");
	exit;
} else {
    header("location:{$site_url}index.php");
	exit;
}
?>