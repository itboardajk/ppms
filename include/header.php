<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
					<i class="icon-reorder shaded"></i>
				</a>

			  	<a class="brand" href="dashboard.php">
			  	<i class="icon-time" style="font-size: 22px;margin-right: 10px;"></i> <?php echo $site_name?>
			  	</a>

				<div class="nav-collapse collapse navbar-inverse-collapse">
					<ul class="nav pull-right">
						<li><a href="profile.php"><?php echo $_SESSION['aname'];?></a></li>
						<li class="nav-user dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="<?php if(!empty($_SESSION['aimage'])){ echo $_SESSION['aimage']; }else{ echo 'images/user.png'; }?>" class="nav-avatar" />
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="change-password.php">Change Password</a></li>
								<li class="divider"></li>
								<li><a href="logout.php">Logout</a></li>
							</ul>
						</li>
					</ul>
				</div><!-- /.nav-collapse -->
			</div>
		</div><!-- /navbar-inner -->
	</div><!-- /navbar -->
