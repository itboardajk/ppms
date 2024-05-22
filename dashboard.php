<?php
include('classes/config.php');
authenticate();
/*
echo json_encode(array(
            'users'=>array('view','add','update','delete'),
            'settings'=>array('view','add','update','delete'),
            'noc_internation_passport'=>array('view','add','update','delete'),
        ));
exit;
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dashboard | <?php echo  $site_title?></title>
	
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

                        <div class="btn-controls">
                            <div class="btn-box-row row-fluid">
                                <a href="projects.php" class="btn-box big span4"><i class="icon-list-alt"></i><b><?php echo $ccounts["Total"];?></b>
                                    <p class="text-muted">
                                        Total Projects</p>
                                </a><a href="projects.php?status=Closed" class="btn-box big span4"><i class="icon-book"></i><b><?php echo $ccounts["Closed"];?></b>
                                    <p class="text-muted">
                                        Completed</p>
                                </a><a href="projects.php?status=UnderProcess" class="btn-box big span4"><i class="icon-folder-open"></i><b><?php echo $ccounts["UnderProcess"];?></b>
                                    <p class="text-muted">
                                        Under Process</p>
                                </a>
                            </div>
                            <div class="btn-box-row row-fluid">
                                <div class="span8">
                                    <div class="row-fluid">
                                        <div class="span12">
                                             <?php if(authorizeAccess('users','view')){?><a href="admins.php" class="btn-box small span6"><i class="icon-group"></i><b>Users</b></a><?php }?>
                                            <?php if(authorizeAccess('roles','view')){?><a href="roles.php" class="btn-box small span6"><i class="icon-sitemap"></i><b>Roles</b></a><?php }?>
                                        </div>
                                    </div>
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <a href="profile.php" class="btn-box small span6"><i class="icon-user"></i><b>Profile</b></a>
                                            <a href="logout.php" class="btn-box small span6"><i class="icon-signout"></i><b>Logout</b></a>
                                        </div>
                                    </div>
                                </div>
                                <ul class="widget widget-usage unstyled span4">
                                    <?php $query=$crud->getData("select projects.* from projects where $_my_projects_condition and status='UnderProcess' order by added_date desc");

                                    $cnt=1;
                                    foreach($query as $row)
                                    {?>
                                        <li>
                                            <p>
                                                <strong><a href="project_details.php?view=<?php echo $row['id']?>"><?php echo $row['title']?></a></strong> <span class="pull-right small muted"><?php echo $row['completed_percentage']?>%</span>
                                            </p>
                                            <div class="progress tight">
                                                <div class="bar <?php echo $row['bar_class']?>" style="width: <?php echo $row['completed_percentage']?>%;">
                                                </div>
                                            </div>
                                        </li>
                                    <?php }?>
                                </ul>
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