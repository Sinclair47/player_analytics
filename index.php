<?php

require_once 'inc/app.php';

$database->query('SELECT DISTINCT `server_ip` FROM `'.DB_TABLE_PA.'` ORDER BY server_ip');

$key = FileSystemCache::generateCacheKey(sha1(serialize(array($database->stmt(), $db))), 'SQL');
$servers = FileSystemCache::retrieve($key);
if($servers === false) {
	$servers = $database->resultset();
	FileSystemCache::store($key, $servers, 1200); #600 sec = 10 min
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="width=device-width, initial-scale=1" name="viewport">
	<title><?php echo $Title ?></title>
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="css/dataTables.bootstrap.css" rel="stylesheet" type="text/css">
	<link href="css/sb-admin-2.css" rel="stylesheet" type="text/css">
	<link href="css/morris.css" rel="stylesheet" type="text/css">
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="css/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css">
	<link href="css/daterangepicker.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">
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
		<nav class="navbar navbar-default navbar-static-top" style="margin-bottom: 0">
			<div class="navbar-header">


				<button class="navbar-toggle" data-target=".navbar-collapse" data-toggle="collapse" type="button">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#/">Player Analytics <span id="header_server_ip"></span></a>
				<div style="padding-top: 8px" class="nav navbar-top-links navbar-right">
					<select class="selectpicker form-control" multiple title="All Servers" data-size="auto" data-width="auto" data-header="Select a server" data-actions-box="true" data-selected-text-format="count > 3">
						<?php foreach ($servers as $server): ?>
							<option data-subtext="<?php echo KeyToValue($server['server_ip'], $server_sub_names, $return_empty = true); ?>"><?php echo ServerName($server['server_ip'], $server_names); ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div><!-- /.navbar-header -->


<?php if($db->count() > 1) { ?>
			<ul class="nav navbar-top-links navbar-right" style="cursor:pointer">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        DB-Switcher <i class="fa  fa-database fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul id="dbswitcher" class="dropdown-menu dropdown-alerts">
					<?php
						$i = 0;
						foreach($db->getDbIndices() as $index) {
							$i++
					?>
						<li>
                            <a data-db="<?php echo $index ?>">
                                <div>
                                    <i class="fa fa-table fa-fw"></i> <?php echo $db->getDbName($index) ?>
									<span class="pull-right text-muted small"><?php if($index ==  $db->getCurrentDbIndex()) echo "active" ?></span>
                                </div>
                            </a>
                        </li>
						<?php if($db->count() != $i) { ?>
                        	<li class="divider"></li>
						<?php } ?>
					<?php
						}
					?>
                    </ul>
                    <!-- /.dropdown-alerts -->
                </li>
                <!-- /.dropdown -->
            </ul>
<?php } ?>

			<div style="padding-top: 15px; margin-right: 24px;" class="pull-right">
				<div id="reportrange" class="pull-right">
					<i class="fa fa-calendar fa-lg"></i>
					<span><?php echo date("F j, Y", strtotime('-7 day')); ?> - <?php echo date("F j, Y"); ?></span> <b class="caret"></b>
				</div>
			</div>

			<div id="sidebar" class="navbar-default sidebar" role="navigation">
				<div class="sidebar-nav navbar-collapse">
					<ul class="nav" id="side-menu" style="cursor:pointer">
						<li class="menu">
							<a href="#/"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
						</li>
						<li class="menu">
							<a href="#/stats/locations"><i class="fa fa-globe fa-fw"></i> Locations</a>
						</li>
						<li class="menu">
							<a href="#/stats/connections"><i class="fa fa-chain fa-fw"></i> Connections</a>
						</li>
						<li class="menu">
							<a href="#/stats/players"><i class="fa fa-group fa-fw"></i> Players</a>
						</li>
						<li class="menu">
							<a href="#/stats/staff"><i class="fa fa-shield fa-fw"></i> Staff</a>
						</li>
						<li class="menu">
							<a href="#/stats/maps"><i class="fa fa-map-marker fa-fw"></i> Maps</a>
						</li>

					</ul>
				</div><!-- /.sidebar-collapse -->
			</div><!-- /.navbar-static-side -->
		</nav>
		<div id="page-wrapper">
			<div id="overlay"><i class="fa fa-spinner fa-spin fa-5x"></i></div>
			<div id="content">

			</div>
		</div><!-- /#page-wrapper -->
	</div><!-- /#wrapper -->
	<!-- Modal -->
	<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	</div><!-- /Modal -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/moment.min.js"></script>
	<script src="js/daterangepicker.js"></script>
	<script src="js/signals.min.js"></script>
	<script src="js/hasher.min.js"></script>
	<script src="js/crossroads.min.js"></script>
	<script src="js/plugins/morris/raphael.min.js"></script>
	<script src="js/plugins/morris/morris.min.js"></script>
	<script src="js/plugins/dataTables/jquery.dataTables.min.js"></script>
	<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
	<script src="js/plugins/jvectormaps/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="js/plugins/jvectormaps/jquery-jvectormap-world-merc-en.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
	<script src="js/app.js"></script>

	<script type="text/javascript">

		$(document).ready(function() {
			var c_servers 	= getCookie("server");
			if(c_servers) {
				c_servers 	= JSON.parse(c_servers);
				console.log(c_servers);
				$(".selectpicker").val(c_servers);
			}

			var selectpickerChanged = false;

			$('.selectpicker').selectpicker({
				multipleSeparator: " || ",
			});

			// Triggers after server select is closed
			$('.selectpicker').on('hide.bs.select', function (e) {
				if(selectpickerChanged)	{
					var server_ips = $(this).val();
					setCookie("server", JSON.stringify(server_ips), 60);
					crossroads.parse(hasher.getHash()); // reload current page (only content)
				}
			});
			$('.selectpicker').on('changed.bs.select', function (e) {
				selectpickerChanged = true;
			});


			$("#side-menu a").click(function() {
				$("#servers li a").removeClass('active fa fa-arrow-circle-right');
				$("#side-menu a").removeClass('active');
				$(this).addClass('active');
			});
			$("#servers li a").click(function() { // Server list
				$(this).addClass('active fa fa-arrow-circle-right');
			});

			$('.menu_server').on('click', function() {
				var server_ip = $(this).find("input").val()
				//console.log(server_ip);
				setCookie("server", server_ip, 60);
				//deleteCookie("server");
				//location.reload();
				crossroads.parse(hasher.getHash());
			});

			$('#dbswitcher a').on('click', function() {
				var db_index = $(this).attr('data-db');
				setCookie("db", db_index, 60);
				deleteCookie("server");
				//crossroads.parse(hasher.getHash());
				$('#overlay').fadeIn("fast");
				location.reload();
			});
		});

		function setCookie(cname,cvalue,exdays) {
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			//console.log(d);
			var expires = "expires=" + d.toGMTString();
			var c = cname+"="+cvalue+"; "+expires + "; "+"/player_analytics"
			document.cookie = c;
			console.log(c);
		}
		function getCookie(cname) {
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for(var i=0; i<ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		}
		function deleteCookie(cname) {
			setCookie(cname, "", -1, "/player_analytics");
		}
	</script>
	<script type="text/javascript">

		var drp_startDate = moment().subtract(6, 'days');
		var drp_startEnd = moment();

		// load date cookie
		var c_dates = getCookie("dates");
		if(c_dates) {
			c_dates = JSON.parse(c_dates);
			drp_startDate = moment(new Date(c_dates['start']));
			drp_startEnd   = moment(new Date(c_dates['end']));
			$('#reportrange span').html(drp_startDate.format('MMMM D, YYYY') + ' - ' + drp_startEnd.format('MMMM D, YYYY'));
		}

		$('#reportrange').daterangepicker(
			{
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'Last 90 Days': [moment().subtract(89, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				},
				startDate: drp_startDate,
				endDate: drp_startEnd
			},
			function(start, end) {
				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			}
		);

		$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
			//var dates = picker.startDate.format('YYYY-M-D')+","+picker.endDate.format('YYYY-M-D');
			var dates = {
				"start": picker.startDate.format('YYYY-M-D'),
				"end": picker.endDate.format('YYYY-M-D')
			}
			//console.log("dates:  " + JSON.stringify(dates));
			setCookie("dates", JSON.stringify(dates), 60);
			crossroads.parse(hasher.getHash()); // reload current page (only content)
		});
	</script>
</body>
</html>
