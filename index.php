<?php 

//Database Info
include 'inc/config.php';

// Include database class
include 'inc/database.class.php';

// Instantiate database.
$database = new Database();

$database->query('SELECT DISTINCT `server_ip` FROM `player_analytics` ORDER BY server_ip');
$servers = $database->resultset();

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="width=device-width, initial-scale=1" name="viewport">
	<title>Player Analytics</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="css/dataTables.bootstrap.css" rel="stylesheet" type="text/css">
	<link href="css/sb-admin-2.css" rel="stylesheet" type="text/css">
	<link href="css/morris.css" rel="stylesheet" type="text/css">
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="css/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css">
	<link href="css/daterangepicker.css" rel="stylesheet" type="text/css">
	<style>
		#overlay {
		  position:absolute; 
		  width:100%;
		  height:100%;
		  margin-left:-300px;
		  margin-top:-50px;
		  background-color:rgba(255,255,255,.80);
		  text-align:center;
		  z-index:999;
		  display:none;
		}
		#overlay i {
			position: fixed;
			top: 49%;
			left: 49%;
			margin:auto;
			color:#3498db;
			filter: alpha(opacity=50);
			-moz-opacity: 0.50;
			opacity: 0.50;
			z-index:999;
		}
		#reportrange {
			cursor:pointer;
		}
		.refresh {
			color:#3498db;
			cursor:pointer;
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<!-- Navigation -->
		<nav class="navbar navbar-default navbar-fixed-top" style="margin-bottom: 0">
			<div class="navbar-header">
				<button class="navbar-toggle" data-target=".navbar-collapse" data-toggle="collapse" type="button"><span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span></button> <a class="navbar-brand" href="index.php">Player Analytics</a>
			</div><!-- /.navbar-header -->
			<div id="sidebar" class="navbar-default sidebar">
				<div class="sidebar-nav navbar-collapse">
					<ul class="nav" id="side-menu" style="cursor:pointer">
						<li class="menu">
							<a><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
							<input type="hidden" value="getdashboard"/>
						</li>
						<li class="menu">
							<a><i class="fa fa-globe fa-fw"></i> Locations</a>
							<input type="hidden" value="getlocation"/>
						</li>
						<li class="menu">
							<a><i class="fa fa-chain fa-fw"></i> Connections</a>
							<input type="hidden" value="getconnections"/>
						</li>
						<li class="menu">
							<a><i class="fa fa-group fa-fw"></i> Players</a>
							<input type="hidden" value="getplayers"/>
						</li>
						<li>
							<a data-toggle="collapse" data-target="#servers"><i class="fa fa-tasks fa-fw"></i> Servers <button type="button" class="btn btn-info btn-xs pull-right "><?php echo count($servers) ?></button></a>
							<ul id="servers" class="collapse nav">
							<?php foreach ($servers as $server): ?>
								<li class="menu_server">
									<a><?php echo ServerName($server['server_ip']); ?></a>
									<input type="hidden" value="<?php echo $server['server_ip']; ?>"/>
								</li>								
							<?php endforeach ?>
							</ul>
						</li>
					</ul>
				</div><!-- /.sidebar-collapse -->
			</div><!-- /.navbar-static-side -->
		</nav>
		<div id="page-wrapper" style="margin-top:25px">
			<div id="overlay"><i class="fa fa-spinner fa-spin fa-5x"></i></div>
			<div id="content">

			</div>
		</div><!-- /#page-wrapper -->
	</div><!-- /#wrapper -->
	<!-- Modal -->
	<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	</div><!-- /Modal -->

	<script src="js/jquery-1.11.0.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/moment.min.js"></script>
	<script src="js/daterangepicker.js"></script>
	<script src="js/plugins/morris/raphael.min.js"></script>
	<script src="js/plugins/morris/morris.min.js"></script>
	<script src="js/plugins/dataTables/jquery.dataTables.min.js"></script>
	<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
	<script src="js/plugins/jvectormaps/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="js/plugins/jvectormaps/jquery-jvectormap-world-merc-en.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			$( "#content" ).load( "inc/getdashboard.php" );
		});
		$(document).on("click",".menu",function(){
			$.ajax({
				type: "GET",
				url: "inc/"+ $(this).find("input").val()+".php",
				beforeSend: function(){
					$('#overlay').fadeIn("fast");
					$('#content').empty();
					$('.jvectormap-label').detach();
					$('.daterangepicker').detach();
				},
				success: function(msg){
					$('#content').delay(400).fadeIn("slow").html(msg);
					$('#overlay').delay(400).fadeOut( "slow" );
				}
			});
		});
		$(document).on("click",".menu_server",function(){
			$.ajax({
				type: "GET",
				url: "inc/getdashboard.php?server="+ $(this).find("input").val(),
				beforeSend: function(){
					$('#overlay').fadeIn("fast");
					$('#content').empty();
					$('.jvectormap-label').detach();
					$('.daterangepicker').detach();
				},
				success: function(msg){
					$('#content').delay(400).fadeIn("slow").html(msg);
					$('#overlay').delay(400).fadeOut( "slow" );
				}
			});
		});
	</script>
</body>
</html>
