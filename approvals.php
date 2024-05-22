<?php
$module_name = 'approvals';
include('classes/config.php');
authenticate();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Approvals | <?php echo  $site_title?></title>
	
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
						
	                    <div class="module">
							<div class="module-head">
								<h3>Manage Approvals</h3>
							</div>
							<div class="module-body table">
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Title</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>

		                                <?php 
										
										$APOs=$crud->getData("SELECT authorities.*,apo.apo as apoTitle,departments.title as departmentTitle,projects.title as projectTitle 
											from authorities 
												left join apo ON authorities.ref_id=apo.id 
	                                            left join departments ON apo.department_id=departments.id
	                                            left join projects ON apo.project_id=projects.id

											where authorities.admin_id=".$_SESSION['id']." and authorities.status <> -1 and authorities.type='APO'");

										$PCs=$crud->getData("SELECT authorities.*,project_cycles.title as pcTitle,departments.title as departmentTitle,projects.title as projectTitle , project_cycles.project_id
											from authorities 
												left join project_cycles ON authorities.ref_id=project_cycles.id  
	                                            left join departments ON project_cycles.department_id=departments.id
	                                            left join projects ON project_cycles.project_id=projects.id
											where authorities.admin_id=".$_SESSION['id']." and authorities.status <> -1 and authorities.type='PC'");
										$cnt=1;
										if($APOs != false && count($APOs)>0)
										{
											foreach($APOs as $row)
		                                	{?>
		                                		<tr>
													<td><?php echo htmlentities($cnt);?></td>
													<td><a href="print_apo.php?apo_id=<?php echo $row['ref_id'] ?>" target="_blank">APO <?php echo htmlentities($row['apoTitle']);?></a><br><b>Project:</b> <?php echo htmlentities($row['projectTitle']);?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Department:</b> <?php echo htmlentities($row['departmentTitle']);?></td>
													<td><?php if($row['status']==0){?><a class="btn btn-mini btn-primary">Pending</a><?php }elseif($row['status']==1){?><a class="btn btn-mini btn-success"><?php echo str_replace(' By', '', $row['label']);?></a><?php }else{?><a class="btn btn-mini btn-danger">Rejected</a><?php }?></td>
												</tr>
		                                		<?php $cnt=$cnt+1;
		                                	}
										}	
										if($PCs != false && count($PCs)>0)
										{
											foreach($PCs as $row)
		                                	{?>
		                                		<tr>
													<td><?php echo htmlentities($cnt);?></td>
													<td><a href="project_cycles_print.php?cycle=<?php echo $row['pcTitle'] ?>&cycle_id=<?php echo $row['ref_id'] ?>&pid=<?php echo $row['project_id'] ?>" target="_blank"><?php echo htmlentities($row['pcTitle']);?></a><br><b>Project:</b> <?php echo htmlentities($row['projectTitle']);?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Department:</b> <?php echo htmlentities($row['departmentTitle']);?></td>
													<td><?php if($row['status']==0){?><a class="btn btn-mini btn-primary">Pending</a><?php }elseif($row['status']==1){?><a class="btn btn-mini btn-success"><?php echo str_replace(' By', '', $row['label']);?></a><?php }else{?><a class="btn btn-mini btn-danger">Rejected</a><?php }?></td>
												</tr>
		                                		<?php $cnt=$cnt+1;
		                                	}
										}
		                                ?>	
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