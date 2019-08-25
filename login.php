<?php
require_once 'inc/config.php';
require_once(__DIR__ . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "auth.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="width=device-width, initial-scale=1" name="viewport">
	<title><?php echo $Title ?></title>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/sb-admin-2.css" rel="stylesheet" type="text/css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>
<body>
	<div id="wrapper">
		<!-- Navigation -->
		<nav class="navbar navbar-default navbar-fixed-top" style="margin-bottom: 0">
			<div class="navbar-header">
				<button class="navbar-toggle" data-target=".navbar-collapse" data-toggle="collapse" type="button">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#/">Player Analytics <span id="header_server_ip"></span></a>
			</div><!-- /.navbar-header -->

			<div id="sidebar" class="navbar-default sidebar" role="navigation">
				<div class="sidebar-nav navbar-collapse">
					<ul class="nav" id="side-menu" style="cursor:pointer">
						<li class="menu">
							<a href="login.php"><i class="fa fa-key fa-fw"></i> Login</a>
						</li>
					</ul>
				</div><!-- /.sidebar-collapse -->
			</div><!-- /.navbar-static-side -->
		</nav>
		<div id="page-wrapper">
			<div id="content" style="margin-top: 24px">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Login</h1>
                    </div><!-- /.col-lg-12 -->
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if(!MUST_LOG_IN) { ?>
                            <p>This page is disabled.</p>
                        <?php } else { ?>
                            <?php if($Auth->IsUserLoggedIn()) { ?>
                                <p>
                                    You're logged in. Go to <a href="index.php">Dashboard</a>.
                                </p>
                            <?php } else { ?>
                            <p>
                                You must be logged in to view the Dashboard.
                            </p>
                            <p>
                                Log-in via steam:
                                <a href="<?= $Auth->GetLoginURL() ?>">
                                    <img src="assets/img/steam.png" alt="Steam Button">
                                </a>
                            </p>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
			</div>
		</div><!-- /#page-wrapper -->
	</div><!-- /#wrapper -->

</body>
</html>
