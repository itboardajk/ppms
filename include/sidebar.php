<div class="span3">
	<div class="sidebar">
		<ul class="widget widget-menu unstyled">
			<li><a href="dashboard.php"><i class="menu-icon icon-dashboard"></i> Dashboard </a></li>
		</ul><!--/.widget-nav-->
		
		<?php if(authorizeAccess('projects','add')){
			$approvals = $crud->getData("SELECT count(*) as total FROM authorities where admin_id=".$_SESSION['id']." and status<>-1");
			if($approvals != false && count($approvals)>0)
			{$approvals=$approvals[0];?>
			<ul class="widget widget-menu unstyled">
				<li><a href="approvals.php"><i class="menu-icon icon-ok"></i>Approvals<b class="label red pull-right"><?php echo  $approvals['total']; ?></b></a></li>
			</ul>
			<?php
			}
		}?>

		<?php if(authorizeAccess('projects','add')){?>
			<ul class="widget widget-menu unstyled">
				<li><a href="projects.php?create"><i class="menu-icon icon-plus"></i>Create New Project</a></li>
			</ul>
		<?php }?>
		<?php if(authorizeAccess('projects','view')){?>
			<ul class="widget widget-menu unstyled">
				<li>
					<a data-toggle="collapse" href="#toggleProjects">
						<i class="menu-icon icon-bullhorn"></i>
						<i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
						Manage Projects
					</a>
					<?php 
					
					//echo "SELECT status,COUNT(*) as total FROM projects where $_my_projects_condition GROUP by status";

					$proects = $crud->getData("SELECT status,COUNT(*) as total FROM projects where $_my_projects_condition GROUP by status");
					
					$ccounts = array("New"=>0, "UnderProcess"=>0,"Closed"=>0,"Total"=>0);
				
					foreach($proects as $proect)
					{
					    $ccounts[$proect['status']]=$proect['total']; 
				    	$ccounts["Total"] += $proect['total'];
					}
					//var_dump($ccounts);


					/** -----Var Dump--------- */
					$inventory_items = $crud->getData("SELECT it.*,
                                SUM(CASE WHEN t.transaction_type = 'stockIn' THEN t.quantity ELSE 0 END)  as total_stock_in,
                                SUM(CASE WHEN t.transaction_type = 'stockOut' THEN t.quantity ELSE 0 END)  as total_stock_out  
                                from inventory_items it  join inventory_transactions t on t.item_id = it.item_id 
                                GROUP BY it.item_id
                                ORDER BY it.item_id DESC");
					
					?>
					<ul id="toggleProjects" class="collapse in unstyled" style="height: auto;">										
						<li><a href="projects.php?status=New">New Projects<b class="label red pull-right"><?php echo (!empty($ccounts['New']))?htmlentities($ccounts['New']):'0'; ?></b></a></li>
						<li><a href="projects.php?status=UnderProcess">Under Process Projects<b class="label yellow pull-right"><?php echo (!empty($ccounts['UnderProcess']))?htmlentities($ccounts['UnderProcess']):'0'; ?></b></a></li>
						<li><a href="projects.php?status=Closed">Closed Projects<b class="label green pull-right"><?php echo (!empty($ccounts['Closed']))?htmlentities($ccounts['Closed']):'0'; ?></b></a></li>
						<li><a href="projects.php">Total Projects<b class="label blue pull-right"><?php echo htmlentities($ccounts['Total']); ?></b></a></li>
					</ul>
				</li>
			</ul>
		<?php }?>
		
		
		<ul class="widget widget-menu unstyled">
			<?php if($_SESSION['id']==1){?><li><a href="departments.php"><i class="menu-icon icon-building"></i>Manage Departments</a></li><?php }?>
			<?php if(authorizeAccess('users','view')){?><li><a href="admins.php"><i class="menu-icon icon-group"></i>Manage Users</a></li><?php }?>
			<?php if(authorizeAccess('roles','view')){?><li><a href="roles.php"><i class="menu-icon icon-sitemap"></i>Role Hierarchy</a></li><?php }?>
		</ul>
	
	
		<ul class="widget widget-menu unstyled">
			<?php if(authorizeAccess('log','view')){?><li><a href="manage-log.php"><i class="menu-icon icon-edit"></i>User Logs</a></li><?php }?>
		</ul>
		

		<ul class="widget widget-menu unstyled">
			<li><a href="logout.php"><i class="menu-icon icon-signout"></i>Logout</a></li>
		</ul>

	</div><!--/.sidebar-->
</div><!--/.span3-->