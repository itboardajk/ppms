<?php
include('classes/config.php');
authenticate();

$id=intval($crud->escape_string($_SESSION['id']));

if(isset($_POST['submit']))
{
    $display_name = $crud->escape_string($_POST['display_name']);
    $cnic = $crud->escape_string($_POST['cnic']); 
    $email = $crud->escape_string($_POST['email']); 
    $username = $crud->escape_string($_POST['username']); 
    $office_contact = $crud->escape_string($_POST['office_contact']); 
    $mobile_contact = $crud->escape_string($_POST['mobile_contact']); 
     $updb ='';
    if(!empty($_POST['password']))
    {
        $password = md5($crud->escape_string($_POST['password'])); 
        $updb =",password='$password'";
    }

   


    $msg = $validation->check_empty($_POST, array(array('display_name','Display Name'),array('cnic','CNIC'),array('username','Username')));

    if($msg != null || !$validation->is_email_valid($email)) {
        $errmsg = 'Please correct the following errors:<br>'.$msg;
        if(!$validation->is_email_valid($email))
            $errmsg .= '<br>Invalid email format.';
    }  
    else {
        if(!empty($_FILES['image']['name']))
        {
            $file_name = $_FILES['image']['name'];
            $file_size =$_FILES['image']['size'];
            $file_tmp =$_FILES['image']['tmp_name'];
            $file_type=$_FILES['image']['type'];
            $file_ext=@strtolower(end(explode('.',$_FILES['image']['name'])));

            $mimetype = mime_content_type($_FILES['image']['tmp_name']);


            $expensions= array("jpeg","jpg","png");
            $mimes = array('image/jpeg',  'image/png');
            
            if(in_array($file_ext,$expensions)=== false || in_array($mimetype,$mimes)=== false){
                $errmsg="extension not allowed, please choose a JPEG or PNG file.";
            }

            if($file_size > 1097152){
                $errmsg='File size must be less then 1 MB';
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

        if(empty($errmsg))
        {
            if(!empty($_FILES['image']['name']))
            {
                $imagefile="uploads/admin_images/admin_".date('YmdHis').'.'.$file_ext;
                move_uploaded_file($_FILES["image"]["tmp_name"],$imagefile);
                $updb .=",admin_image='$imagefile'";
                $_SESSION['aimage']=$imagefile;
                
            }
            if(!empty($_FILES['admin_sign']['name']))
            {
                $imagefile_sig="uploads/admin_images/signs/signs_".date('YmdHis').'.'.$file_ext_sig;
                move_uploaded_file($_FILES["admin_sign"]["tmp_name"],$imagefile_sig);
                $updb .=",admin_sign='$imagefile_sig'";
                $_SESSION['asign']=$imagefile_sig;
                
            }

            $sql="update admin set display_name='$display_name',email='$email',cnic='$cnic',office_contact='$office_contact',mobile_contact='$mobile_contact',username='$username' $updb, updated_by=".$_SESSION['id']." where id='$id'";

            $result = $crud->execute($sql);

            if($result != false)
            {
                $_SESSION['aname']=$display_name;
               

                $sucmsg="Profile Updated !!";
                unset($_POST);
                $crud->log('Profile('.$id.') Updated');
            }
            else
            {
                $errmsg = 'Unable to update profile.';
            }
        }
    }
}

$querym="select * from admin where id='$id'";
$user = $crud->getData($querym);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit Profile | <?php echo  $site_title?></title>
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
								<h3>Edit Profile</h3>
							</div>
							<div class="module-body">
                                <?php if(!empty($errmsg)){?>
                                    <div class="alert alert-error">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Error!</strong> <?php echo $errmsg;?>
                                    </div>
                                <?php }else if(!empty($sucmsg)){?>
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Well done!</strong> <?php echo $sucmsg;?>
                                    </div>
                                <?php } ?>

    							<form class="form-horizontal row-fluid" name="subcategory" method="post"  enctype="multipart/form-data">
    							    <?php
                                    foreach($user as $row)
                                    {
                                    ?>	
    							    <div class="control-group">
    									<label class="control-label" for="basicinput">Display Name</label>
    									<div class="controls">
    										<input type="text" name="display_name" class="span8 tip" required="" value="<?php echo $row['display_name']?>">
    									</div>
    								</div>
    								<div class="control-group">
    									<label class="control-label" for="basicinput">Email</label>
    									<div class="controls">
    										<input type="email" name="email" class="span8 tip" required="" value="<?php echo $row['email']?>">
    									</div>
    								</div>
    								<div class="control-group">
    									<label class="control-label" for="basicinput">CNIC</label>
    									<div class="controls">
    										<input type="text" name="cnic" class="span8 tip" required="" value="<?php echo $row['cnic']?>">
    									</div>
    								</div>
    								<div class="control-group">
    									<label class="control-label" for="basicinput">Phone(office)</label>
    									<div class="controls">
    										<input type="text" name="office_contact" class="span8 tip" required="" value="<?php echo $row['office_contact']?>">
    									</div>
    								</div>
    								<div class="control-group">
    									<label class="control-label" for="basicinput">Mobile</label>
    									<div class="controls">
    										<input type="text" name="mobile_contact" class="span8 tip" required="" value="<?php echo $row['mobile_contact']?>">
    									</div>
    								</div>    								
    								<div class="control-group">
    									<label class="control-label" for="basicinput">Username</label>
    									<div class="controls">
    										<input type="text" name="username" class="span8 tip" required="" value="<?php echo $row['username']?>">
    									</div>
    								</div>
    								<div class="control-group">
											<label class="control-label" for="basicinput">Image</label>
											<div class="controls">
												<input type="file" name="image">
												<?php if(!empty($row['admin_image'])){?><a href="<?php echo htmlentities($row['admin_image']);?>" target="_blank"><img src="<?php echo htmlentities($row['admin_image']);?>" width="32" height="auto"></a><?php }?>
											</div>
										</div>
										
    								<div class="control-group">
											<label class="control-label" for="basicinput">Sign</label>
											<div class="controls">
												<input type="file" name="admin_sign">
												<?php if(!empty($row['admin_sign'])){?><a href="<?php echo htmlentities($row['admin_sign']);?>" target="_blank"><img src="<?php echo htmlentities($row['admin_sign']);?>" width="32" height="auto"></a><?php }?>
											</div>
										</div>
									<?php } ?>
                                    <div class="control-group">
    									<div class="controls">
    										<button type="submit" name="submit" class="btn btn-primary">Update</button>
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
</body>
</html>