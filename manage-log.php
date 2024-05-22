<?php
$module_name = 'log';
include('classes/config.php');
authenticate();
if(!authorizeAccess($module_name,'view')){("location:{$site_url}dashboard.php");exit();}

$query="select log.*,admin.display_name from log left join admin ON log.log_by=admin.id order by id DESC";//where log.log_by>1
$nodes = $crud->getData($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Logs | <?php echo  $site_title?></title>
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
								<h3>Manage Logs</h3>
							</div>
							<div class="module-body table">
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" width="100%">
									<thead>
										<tr>
											<th width="5%">#</th>
											<th>Details</th>
											<th width="5%">By</th>
											<th width="5%">Date</th>
										</tr>
									</thead>
									<tbody>
										<?php $count=1; foreach($nodes as $child){?>
									        <tr>
									            <td><?php echo htmlentities($count++);?></td>
									            <td><?php echo htmlentities($child['details']);?></td>
									            <td style="text-align: center"><?php echo htmlentities($child['display_name']);?></td>
									            <td style="text-align: center"><?php echo date('d.m.y g:iA',strtotime($child['log_date']));?></td>
									        </tr>
									    <?php }?>
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
</html>