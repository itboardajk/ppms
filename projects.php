<?php
$module_name = 'projects';
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
		$title=$crud->escape_string($_POST['title']);
		$details=$crud->escape_string($_POST['details']);
		$bar_class=$crud->escape_string($_POST['bar_class']);

		$msg = $validation->check_empty($_POST, array(array('title','Project Title')));


		if($msg != null) {
			$errmsg = 'Please correct the following errors:<br>'.$msg;
		}    
		else {
			$attachment_error='';
			$imagefiles=array();
			if(!empty($_FILES['filesToUpload']))
			{
			  	$file_ary = reArrayFiles($_FILES['filesToUpload']);
			    if(!empty($file_ary[0]["name"]))
			    {
			      	$imageLinks='';
			      	foreach($file_ary as $file)
			      	{
			          
						$file_name = $file['name'];
						@$file_ext=strtolower(end(explode('.',$file_name)));
						@$mimetype = mime_content_type($file['tmp_name']);


                        $expensions= array("jpg","jpeg","png","mp3","mp4","pdf","doc","docx","xls","xlsx","ppt","pptx");
                        //array("jpeg","jpg","png");
                        $mimes = array('image/jpeg', 'image/png','audio/mpeg','video/mp4','audio/mp4','application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation');


                        if(!empty($file_ext) && !empty($file_name)  && !empty($mimetype) && in_array($file_ext,$expensions) && in_array($mimetype,$mimes)) //,"mp4","mp3","webm"
                        {
				            $folder_name='uploads/project_images';
				                
				            $imagefile =$folder_name."/project_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
				            if(move_uploaded_file($file["tmp_name"],$imagefile))
				            {
				            	$imagefiles[] = $imagefile;
				            }
			          	}
			      	}
			    }
			}
			$fimage='';
			if(!empty($_FILES['fimage']))
			{
				$file = $_FILES['fimage']; 
			  	$file_name = $file['name'];
				@$file_ext=strtolower(end(explode('.',$file_name)));
				@$mimetype = mime_content_type($_FILES['fimage']['tmp_name']);


		        $expensions= array("jpeg","jpg","png");
		        $mimes = array('image/jpeg',  'image/png');
	            
                if(!empty($file_ext) && !empty($file_name)  && !empty($mimetype) && in_array($file_ext,$expensions) && in_array($mimetype,$mimes) ){
		            $folder_name='uploads/project_images';
		                
		            $imagefile =$folder_name."/project_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
		            if(move_uploaded_file($file["tmp_name"],$imagefile))
		            {
		            	$fimage = $imagefile;
		            }
	          	}
			}

	        $sql="INSERT INTO projects(title,details,status,fimage,images,added_by,department_id) values('$title','$details','New','$fimage','".implode(",",$imagefiles)."','".$_SESSION['id']."','".$_SESSION['department_id']."')";
	         
	        $result = $crud->insert_and_get_id($sql);

			if($result != false)
			{
				$sucmsg="Project Created !!";
				unset($_POST);
				$crud->log('Project('.$result.') Added',$_SESSION['id']);
			}
			else
			{
				$errmsg = 'Unable to create new project.';
			}
		}
	}
	else
	{
		$errmsg = 'You do not have access to add project';
	}

}
else if(isset($_POST['edit']))
{
	if($editFlag)
	{
		$id=intval($crud->escape_string($_GET['view']));

		$title=$crud->escape_string($_POST['title']);
		$details=$crud->escape_string($_POST['details']);
		$start_date=$crud->escape_string($_POST['start_date']);
		$end_date=$crud->escape_string($_POST['end_date']);
		$budget=$crud->escape_string($_POST['budget']);
		$status=$crud->escape_string($_POST['status']);

		$bar_class=$crud->escape_string($_POST['bar_class']);

		$akdwp_approval_date=$crud->escape_string($_POST['akdwp_approval_date']);
		$pnd_noc_date=$crud->escape_string($_POST['pnd_noc_date']);
		$admin_approval_date=$crud->escape_string($_POST['admin_approval_date']);

		$revision_date=$crud->escape_string($_POST['revision_date']);
		$revision_budget=$crud->escape_string($_POST['revision_budget']);


		$msg = $validation->check_empty($_POST, array(array('title','Project Title')));

		$prev_images=$crud->escape_string($_POST['prev_images']);
		
		if(isset($_POST['rem_img']))
        {
            foreach($_POST['rem_img'] as $remove_img)
            {
                @unlink($remove_img);
                $prev_images = str_replace($remove_img.',', '', $prev_images);
                $prev_images = str_replace(','.$remove_img, '', $prev_images);
                $prev_images = str_replace($remove_img, '', $prev_images);
            }
        }

		if($msg != null) {
			$errmsg = 'Please correct the following errors:<br>'.$msg;
		}    
		else {

			$imagefiles=array();
			if(!empty($_FILES['filesToUpload']))
			{
			  	$file_ary = reArrayFiles($_FILES['filesToUpload']);
			    if(!empty($file_ary[0]["name"]))
			    {
			      	foreach($file_ary as $file)
			      	{
			          
						$file_name = $file['name'];
						@$file_ext=strtolower(end(explode('.',$file_name)));
						@$mimetype = mime_content_type($file['tmp_name']);


                        $expensions= array("jpg","jpeg","png","mp3","mp4","pdf","doc","docx","xls","xlsx","ppt","pptx");
                        //array("jpeg","jpg","png");
                        $mimes = array('image/jpeg', 'image/png','audio/mpeg','video/mp4','audio/mp4','application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation');


                        if(!empty($file_ext) && !empty($file_name)  && !empty($mimetype) && in_array($file_ext,$expensions) && in_array($mimetype,$mimes)) //,"mp4","mp3","webm"
                        {
				            $folder_name='uploads/project_images';
				                
				            $imagefile =$folder_name."/project_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
				            if(move_uploaded_file($file["tmp_name"],$imagefile))
				            {

				            	$imagefiles[] = $imagefile;
				            }
			          	}
			      	}

			      	if(empty($prev_images))
			      		$imgstr=implode(',', $imagefiles);
		      		else
			      		$imgstr=$prev_images.",".implode(',', $imagefiles);
			    }
				else
				{
					$imgstr=$prev_images;
				}
			}
			else
			{
				$imgstr=$prev_images;
			}

			$fimage='';
			if(!empty($_FILES['fimage']))
			{
				$file = $_FILES['fimage']; 
			  	$file_name = $file['name'];
				@$file_ext=strtolower(end(explode('.',$file_name)));
				@$mimetype = mime_content_type($_FILES['fimage']['tmp_name']);


		        $expensions= array("jpeg","jpg","png");
		        $mimes = array('image/jpeg',  'image/png');
	            
                if(!empty($file_ext) && !empty($file_name)  && !empty($mimetype) && in_array($file_ext,$expensions) && in_array($mimetype,$mimes) ){
		            $folder_name='uploads/project_images';
		                
		            $imagefile =$folder_name."/project_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
		            if(move_uploaded_file($file["tmp_name"],$imagefile))
		            {
		            	$fimage = $imagefile;
		            	$prev_fimage=$crud->escape_string($_POST['prev_fimage']);
                        if(!empty($prev_fimage))
                        {
                            @unlink($prev_fimage);
                        }
		            }
	          	}
			}

			$updb='';
			if(!empty($revision_date))
				$updb = ",revision_date='$revision_date'";
			if(!empty($admin_approval_date))
				$updb .= ",admin_approval_date='$admin_approval_date'";
			if(!empty($pnd_noc_date))
				$updb .= ",pnd_noc_date='$pnd_noc_date'";
			if(!empty($akdwp_approval_date))
				$updb .= ",akdwp_approval_date='$akdwp_approval_date'";
			if(!empty($start_date))
				$updb .= ",start_date='$start_date'";
			if(!empty($end_date))
				$updb .= ",end_date='$end_date'";
			if(!empty($fimage))
				$updb .= ",fimage='$fimage'";

		    $sql=$crud->execute("update projects set title='$title',details='$details',budget='$budget',status='$status',bar_class='$bar_class',images='$imgstr',updated_by='".$_SESSION['id']."' ".$updb." where id='$id'");
		    if($sql != false)
			{
				$sucmsg="Project Updated !!";
				unset($_POST);
				$crud->log('Project('.$id.') Updated',$_SESSION['id']);
			}
			else
			{
				$errmsg = 'Unable to updated  project.';
			}
		}

	}
	else
	{
		$errmsg = 'You do not have access to updated project';
	}

}
else if(isset($_GET['delete']) && $_GET['delete']>0)
{
	if($deleteFlag)
	{
		$id=intval($crud->escape_string($_GET['delete']));

		// delete centers
		$centers_details=$crud->getData("SELECT * from centers where project_id = $id");
		foreach($centers_details as $center)
		{
			if(!empty($center['fimage']))
			{
				@unlink($center['fimage']);
			}

			if(!empty($center['images']))
			{
				$center_images = explode(',',$center['images']);
	            foreach ($center_images as  $img) {
	                if(!empty($img))
	                    @unlink($img);
	            }
			}

		}
		$centers = $crud->execute("delete from centers where project_id=$id");

		// delete Cycles
		$cycles_details=$crud->getData("SELECT * from `project_cycles` where project_id='".$id."'");
		foreach($cycles_details as $cycle)
		{
			if($cycle['type']=='Manual')
			{
				@$blocks = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $cycle['defaults']),true);
				@unlink($blocks['url']);
			}
		}
		$result = $crud->execute("delete from project_cycles where project_id=$id");


		//delete expences
		$expenses_details=$crud->getData("SELECT * from `expenses` where project_id='".$id."'");
		foreach($expenses_details as $expense)
		{
			if(!empty($expense['images']))
			{
				$expense_images = explode(',',$expense['images']);
	            foreach ($expense_images as  $img) {
	                if(!empty($img))
	                    @unlink($img);
	            }
			}
		}
		$result = $crud->execute("delete from expenses where project_id=$id");

		//delete APO
		$apo_details=$crud->getData("SELECT * from `apo` where project_id='".$id."'");
		foreach($apo_details as $apo)
		{
			if(!empty($apo['file']))
			{
                @unlink($apo['file']);
			}

		    $query = "delete from authorities where ref_id=".$apo['id']." and type='APO'";
		    $result = $crud->execute($query);

		    $query = "delete from remarks where  ref_id=".$apo['id']."  and type='APO' ";
		    $result = $crud->execute($query);


		    $query = "delete from apo_heads where apo_id=".$apo['id'];
		    $result = $crud->execute($query);
		}
		$result = $crud->execute("delete from apo where project_id=$id");

		// delete heads
		$query = "delete from heads where project_id=$id";
		$result = $crud->execute($query);

		//delete Inspections
		$inspections_details=$crud->getData("SELECT * from `inspections` where project_id='".$id."'");
		foreach($inspections_details as $inspection)
		{
			if(!empty($inspection['images']))
			{
				$inspection_images = explode(',',$inspection['images']);
	            foreach ($inspection_images as  $img) {
	                if(!empty($img))
	                    @unlink($img);
	            }
			}
		}
		$result = $crud->execute("delete from inspections where project_id=$id");

		//delete Updates
		$updates_details=$crud->getData("SELECT * from `updates` where project_id='".$id."'");
		foreach($updates_details as $update)
		{
			if(!empty($update['images']))
			{
				$update_images = explode(',',$update['images']);
	            foreach ($update_images as  $img) {
	                if(!empty($img))
	                    @unlink($img);
	            }
			}
		}
		$result = $crud->execute("delete from updates where project_id=$id");


		//delete Deliverables
		$deliverables_details=$crud->getData("SELECT * from `deliverables` where project_id='".$id."'");
		foreach($deliverables_details as $deliverable)
		{
			if(!empty($deliverable['images']))
			{
				$deliverable_images = explode(',',$deliverable['images']);
	            foreach ($deliverable_images as  $img) {
	                if(!empty($img))
	                    @unlink($img);
	            }
			}
		}
		$result = $crud->execute("delete from deliverables where project_id=$id");


		//Delete Project

		$projects_details=$crud->getData("SELECT * from projects where id = $id");
		$projects_details = $projects_details[0];
		if(!empty($projects_details['fimage']))
		{
			@unlink($projects_details['fimage']);
		}

		if(!empty($projects_details['images']))
		{
			$project_images = explode(',',$projects_details['images']);
            foreach ($project_images as  $img) {
                if(!empty($img))
                    @unlink($img);
            }
		}
		$project = $crud->execute("delete from projects where id=$id");


	

		$sucmsg="Project deleted !!";
		$crud->log('Project('.$id.') deleted',$_SESSION['id']);
	}
	else
	{
		$errmsg = 'You do not have access to delete project';
	}
}
$status_proejcts=0;
$for_status = '';
if(isset($_GET['status']) && !empty($_GET['status']))
{
	$for_status = $crud->escape_string($_GET['status']);
	$status_proejcts=1;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Projects | <?php echo  $site_title?></title>
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
								$query="select projects.*  from projects where $_my_projects_condition and projects.id=".$_GET['view'];
								$enode = $crud->getData($query);
								if($enode != false && count($enode)>0)
								{
									$enode = $enode[0];
									?>
									<div class="module editModule">
										<div class="module-head">
											<h3><?php echo ($editFlag)?'Edit ':'View ';?> Project <span style="float:right"><a href="project_details.php?view=<?php echo htmlentities($enode['id'])?>"><i class="icon-eye-open"></i> View</a> | <a href="javascript:;" class="hideModule"  data-target="editModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
										</div>
										<div class="module-body">
											<form class="form-horizontal row-fluid" name="editadmin" method="post"  enctype="multipart/form-data" action="projects.php?view=<?php echo $_GET['view'];?>">

			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Title</label>
			    									<div class="controls">
			    										<input type="text" name="title" class="span12 tip" required="" value="<?php echo @$enode['title']?>" maxlength="100" >
			    									</div>
			    								</div>

			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Approval Dates</label>
			    									<div class="controls">
			    										<div class="input-prepend span4">
															<span class="add-on">AKDWP Approval</span><input type="date" name="akdwp_approval_date" class="span6 tip"  value="<?php echo @$enode['akdwp_approval_date']?>">       
														</div>
			    										<div class="input-prepend span4">
															<span class="add-on">PND NOC</span><input type="date" name="pnd_noc_date" class="span8 tip"  value="<?php echo @$enode['pnd_noc_date']?>">     
														</div>
			    										<div class="input-prepend span4">
															<span class="add-on">Admin Approval</span><input type="date" name="admin_approval_date" class="span6 tip"  value="<?php echo @$enode['admin_approval_date']?>">     
														</div>
			    									</div>
			    								</div>
			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Project Tenure</label>
			    									<div class="controls">
			    										<div class="input-prepend span6">
															<span class="add-on">Start</span><input type="date" name="start_date" class="span10 tip"   value="<?php echo @$enode['start_date']?>">       
														</div>
			    										<div class="input-prepend span6">
															<span class="add-on">End</span><input type="date" name="end_date" class="span10 tip" value="<?php echo @$enode['enode']?>">     
														</div>
			    									</div>
			    								</div>
			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Budget & Status</label>
			    									<div class="controls">
			    										<div class="input-prepend span6">
				    										<span class="add-on">Budget(m)</span><input type="number" name="budget"   class="tip" value="<?php echo @$enode['budget']+0?>" maxlength="1000"  step="any">
				    									</div>
			    										<div class="input-prepend span6"><span class="add-on">Status</span><select name="status" class="tip">
			    											<option value="New" <?php if($enode['status']=='New'){echo 'selected="selected"';}?>>New</option>
			    											<option value="UnderProcess" <?php if($enode['status']=='UnderProcess'){echo 'selected="selected"';}?>>Under Process</option>
			    											<option value="Closed" <?php if($enode['status']=='Closed'){echo 'selected="selected"';}?>>Closed</option>
			    										</select></div>
			    									</div>
			    								</div>


												<div class="control-group">
													<label class="control-label" for="basicinput">Details</label>
													<div class="controls">
														<textarea  name="details" class="span12 tip"><?php echo @$enode['details']?></textarea>
													</div>
												</div>
												<div class="control-group">
													<label class="control-label" for="basicinput">Project Bar Color</label>
													<div class="controls">
														<select name="bar_class" class="span3 tip">
			    											<option value="" <?php if($enode['bar_class']==''){echo 'selected="selected"';}?>>Blue</option>
			    											<option value="bar-success" <?php if($enode['bar_class']=='bar-success'){echo 'selected="selected"';}?>>Green</option>
			    											<option value="bar-warning" <?php if($enode['bar_class']=='bar-warning'){echo 'selected="selected"';}?>>Yellow</option>
			    											<option value="bar-danger" <?php if($enode['bar_class']=='bar-danger'){echo 'selected="selected"';}?>>Red</option>
			    										</select>
													</div>
												</div>
			    								<div class="control-group">
			    									<label class="control-label" for="basicinput">Project Revision</label>
			    									<div class="controls">
			    										<div class="input-prepend span6">
				    										<span class="add-on">Date</span><input type="date" name="revision_date" class="tip"  value="<?php echo @$enode['revision_date']?>">
				    									</div>

			    										<div class="input-prepend span6">
				    										<span class="add-on">Budget(m)</span><input type="number" name="revision_budget" class=" tip"  value="<?php echo @$enode['revision_budget']+0?>" step="any">
				    									</div>
			    									</div>
			    								</div>
		    									<div class="control-group">
													<label class="control-label" for="basicinput">Featured Image</label>
													<div class="controls">
														<?php 
															if(!empty($enode['fimage'])){
																echo '<a href="'.htmlentities($enode['fimage']).'" target="_blank">View Featured Image</a>';
															}
														?>
														<div class="fileswrapper" style="margin:0 0 20px 0;">
															<input  name="fimage" type="file"  accept="image/*">
                            								<input type="hidden" name="prev_fimage" value="<?php echo $enode['fimage'];?>">
														</div>
													</div>
												</div>
		    									<div class="control-group">
													<label class="control-label" for="basicinput">Files/Images<br><?php if(!empty($enode['images'])){?><small>Check to remove the image/file.</small><?php }?></label>
													<div class="controls">
														<?php 
															if(!empty($enode['images'])){
																$vimg = explode(',', $enode['images']);
																foreach ($vimg as $key => $value) {

																	echo '<label class="checkbox inline"><input type="checkbox" name="rem_img[]" value="'.htmlentities($value).'" title="Check to remove the image/file."> <a href="'.htmlentities($value).'" target="_blank">View Image/File</a> </label>';
																}
															}
														?>
														<div class="fileswrapper" style="margin: 0 0 20px 0;">
															<input  name="filesToUpload[]" type="file"  accept=".pdf|.xls|.xlsx|.doc|.docx|image/*">
														</div>
														<a href="javascript:;" class="addmorefile" data-types=".pdf|.xls|.xlsx|.doc|.docx|image/*">+ Add Another File</a>
														<input  name="prev_images" type="hidden"  value="<?php echo $enode['images'];?>">
													</div>
												</div>
												
																						
													<div class="control-group">
														<div class="controls">
															<?php if($editFlag){?>
																<button type="submit" name="edit" class="btn btn-primary">Update Project</button>
															<?php }?>
															<?php if($deleteFlag){?>
															 	<a class="btn btn-danger" href="projects.php?delete=<?php echo $_GET['view'];?>" onclick="return confirm('Are you sure to delete this project?')">Delete Project</a>
															<?php }?>
														</div>
													</div>
												
												<div class="control-group">
													<label class="control-label" for="basicinput">Log</label>
													<div class="controls">
														<?php 
															$log_dets = "SELECT log.*,admin.display_name FROM `log` left join admin ON log.log_by=admin.id WHERE `details` LIKE '%Project(".$_GET['view'].") %'"; 
															$log_dets = $crud->getData($log_dets);
															if($log_dets != false && count($log_dets)>0){
																foreach($log_dets as $row){?>
																	<blockquote><p><b><?php echo $row['display_name'];?></b>: <?php echo str_replace("Project(".$_GET['view'].") ", "", $row['details']);?> this record on <?php echo $row['log_date'];?></p></blockquote>

																<?php }
															}
														?>
													</div>
												</div>
											</form>
										</div>
									</div>

								<?php }?>
							<?php }?>
					      	<?php if($addFlag){
					      		?>
			                    <div class="module addModule" <?php if(isset($_POST['add']) || isset($_GET['create'])){echo 'style="display:block"';}?>>
									<div class="module-head">
										<h3>Add Project <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
									</div>
									<div class="module-body">
		    							<form class="form-horizontal row-fluid" name="addadmin" method="post"  enctype="multipart/form-data" action="projects.php">
		    							    
		    								<div class="control-group">
		    									<label class="control-label" for="basicinput">Title</label>
		    									<div class="controls">
		    										<input type="text" name="title" class="span12 tip" required="" value="<?php echo @$_POST['title']?>" maxlength="100" >
		    									</div>
		    								</div>
											<div class="control-group">
												<label class="control-label" for="basicinput">Details</label>
												<div class="controls">
													<textarea  name="details" class="span12 tip"><?php echo @$_POST['details']?></textarea>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="basicinput">Project Bar Color</label>
												<div class="controls">
													<select name="bar_class" class="span3 tip">
		    											<option value="">Blue</option>
		    											<option value="bar-success">Green</option>
		    											<option value="bar-warning">Yellow</option>
		    											<option value="bar-danger">Red</option>
		    										</select>
												</div>
											</div>
	    									<div class="control-group">
												<label class="control-label" for="basicinput">Featured Image</label>
												<div class="controls">
														<input  name="fimage" type="file"  accept="image/*">
												</div>
											</div>											
											<div class="control-group">
												<label class="control-label" for="basicinput">Files/Images</label>
												<div class="controls">
													<div class="fileswrapper" style="margin: 0 0 20px 0;">
														<input  name="filesToUpload[]" type="file"  accept=".pdf|.xls|.xlsx|.doc|.docx|image/*">
													</div>
													<a href="javascript:;" class="addmorefile" data-types=".pdf|.xls|.xlsx|.doc|.docx|image/*">+ Add Another File</a>
												</div>
											</div>
		                                    <div class="control-group">
		    									<div class="controls">
		    										<button type="submit" name="add" class="btn btn-primary">Create Project</button>
		    									</div>
		    								</div>
		    						    </form>
								    </div>
								</div>
							<?php }?>
						
	                    <div class="module">
							<div class="module-head">
								<h3>Manage<?php echo ' '.$for_status?> Projects <?php if($addFlag){?><span style="float:right"><a href="javascript:;" class="showModule" data-target="addModule"><i class="icon-plus"></i> Add Project</a></span><?php }?></h3>
							</div>
							<div class="module-body table">
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th  class="cell-title">Title</th>
											<th>AKDWP</th>
											<th>PND</th>
											<th>Admin</th>
											<th>Budget</th>
											<?php if(!$status_proejcts){?><th class="cell-status" style="text-align: center">Status</th><?php }?>
										</tr>
									</thead>
									<tbody>

	                                    <?php 
	                                    if($status_proejcts){
											$query=$crud->getData("select projects.* from projects where $_my_projects_condition and status='".$for_status."' order by added_date desc");
										}
										else
										{
											$query=$crud->getData("select projects.* from projects where $_my_projects_condition order by added_date desc");
										}
	                                    $cnt=1;
	                                    foreach($query as $row)
	                                    {
	                                    ?>									
											<tr>
	    										<td><?php echo htmlentities($cnt);?></td>
	    										<td><a href="project_details.php?view=<?php echo htmlentities($row['id'])?>" target="_blank"><?php echo htmlentities($row['title']);?></a></td>
	    										<td><?php echo (!empty($row['akdwp_approval_date']))?date("d M, Y", strtotime(htmlentities($row['akdwp_approval_date']))):'';?></td>
	    										<td><?php echo (!empty($row['pnd_noc_date']))?date("d M, Y", strtotime(htmlentities($row['pnd_noc_date']))):'';?></td>
	    										<td><?php echo (!empty($row['admin_approval_date']))?date("d M, Y", strtotime(htmlentities($row['admin_approval_date']))):'';?></td>
	    										<td><?php echo ($row['budget'])?number_format(htmlentities($row['budget']), 2):'0';?></td>
	    										<?php if(!$status_proejcts){?><td style="text-align: center"><b class="<?php echo htmlentities($row['status']);?>"><?php echo htmlentities($row['status'])?></b></td><?php }?>
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
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script> -->
	

	<script  type="text/javascript">
		$(document).ready(function() {
			$('.datatable-1').dataTable();
			$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');


			//$(".multi-select").select2();

		} );
		
	</script>
</body>
</html>