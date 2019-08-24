<?php
require_once 'inc/app.php';

if(MUST_LOG_IN) {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "auth.php");
    if (!$Auth->IsUserLoggedIn()) {
        header("Location: ./auth.php?action=login");
        exit;
    }
}

$hide_servers = "";
if($hide_inactive_servers_days > 0) {
	$hide_servers = " WHERE connect_date > DATE_SUB(NOW(), INTERVAL $hide_inactive_servers_days DAY) ";
}
$database->query('SELECT DISTINCT `server_ip` FROM `'.DB_TABLE_PA.'` '. $hide_servers .' ORDER BY server_ip');

$key = FileSystemCache::generateCacheKey(sha1(serialize(array($database->stmt(), $db))), 'SQL');
$servers = FileSystemCache::retrieve($key);
if($servers === false) {
	$servers = $database->resultset();
	FileSystemCache::store($key, $servers, 100000); #300k sec = 3.4 days
}

$force_recache = "?t2";  # change to some other random string after modding js files
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="width=device-width, initial-scale=1" name="viewport">
	<title><?php echo $Title ?></title>
	<link href="css/bootstrap.min.css<?php echo $force_recache ?>" rel="stylesheet" type="text/css">
	<link href="css/dataTables.bootstrap.css<?php echo $force_recache ?>" rel="stylesheet" type="text/css">
	<link href="css/sb-admin-2.css<?php echo $force_recache ?>" rel="stylesheet" type="text/css">
	<link href="css/morris.css<?php echo $force_recache ?>" rel="stylesheet" type="text/css">
	<link href="font-awesome/css/font-awesome.min.css<?php echo $force_recache ?>" rel="stylesheet" type="text/css">
	<link href="css/jquery-jvectormap-1.2.2.css<?php echo $force_recache ?>" rel="stylesheet" type="text/css">
	<link href="css/daterangepicker.css<?php echo $force_recache ?>" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.16.1/vis.css">
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


				<button class="navbar-toggle" data-target=".navbar-collapse" data-toggle="collapse" type="button">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#/">Player Analytics <span id="header_server_ip"></span></a>
				<div style="padding-top: 8px" class="nav navbar-top-links navbar-right">
					<select class="selectpicker form-control" multiple title="All Servers" data-size="auto" data-width="auto" data-header="Select a server" data-actions-box="true" data-count-selected-text="{0} of {1} Servers selected" data-selected-text-format="count > 2">
						<?php foreach ($servers as $server): ?>
							<option value="<?php echo $server['server_ip']; ?>" data-subtext="<?php echo KeyToValue($server['server_ip'], $server_sub_names, $return_empty = true); ?>"><?php echo ServerName($server['server_ip'], $server_names); ?></option>
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
                    <?php if ( MUST_LOG_IN) { ?>
                        <a href="auth.php?action=logout"><i class="fa fa-user"></i> Logout</a> |
                    <?php } ?>
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
						<!--<li class="menu">
							<a href="#/stats/servers"><i class="fa fa-map-marker fa-fw"></i> Servers</a>
						</li>-->
						<li class="menu">
							<a href="#/stats/maps"><i class="fa fa-map-marker fa-fw"></i> Maps</a>
						</li>
						<li class="menu">
							<a href="#/lab"><i class="fa fa-flask fa-fw"></i> Laboratory</a>
						</li>

					</ul>
				</div><!-- /.sidebar-collapse -->
			</div><!-- /.navbar-static-side -->
		</nav>
		<div id="page-wrapper">
			<div id="overlay"><i class="fa fa-spinner fa-spin fa-5x"></i></div>
			<div id="content" style="margin-top: 24px">

			</div>
		</div><!-- /#page-wrapper -->
	</div><!-- /#wrapper -->
	<!-- Modal -->
	<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	</div><!-- /Modal -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="js/bootstrap.min.js<?php echo $force_recache ?>"></script>
	<script src="js/moment.min.js<?php echo $force_recache ?>"></script>
	<script src="js/daterangepicker.js<?php echo $force_recache ?>"></script>
	<script src="js/signals.min.js<?php echo $force_recache ?>"></script>
	<script src="js/hasher.min.js<?php echo $force_recache ?>"></script>
	<script src="js/crossroads.min.js<?php echo $force_recache ?>"></script>
	<script src="js/plugins/morris/raphael.min.js<?php echo $force_recache ?>"></script>
	<script src="js/plugins/morris/morris.min.js<?php echo $force_recache ?>"></script>
	<script src="js/plugins/dataTables/jquery.dataTables.min.js<?php echo $force_recache ?>"></script>
	<script src="js/plugins/dataTables/dataTables.bootstrap.js<?php echo $force_recache ?>"></script>
	<script src="js/plugins/jvectormaps/jquery-jvectormap-1.2.2.min.js<?php echo $force_recache ?>"></script>
	<script src="js/plugins/jvectormaps/jquery-jvectormap-world-merc-en.js<?php echo $force_recache ?>"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
	<script src="js/app.js<?php echo $force_recache ?>"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.1/Chart.bundle.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.16.1/vis.min.js"></script>

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
				setCookie("db", db_index, 30);
				deleteCookie("server");
				//crossroads.parse(hasher.getHash());
				$('#overlay').fadeIn("fast");
				location.reload();
			});
		});

		function setCookie(cname, cvalue, ex_minutes) {
			var d = new Date();
			d.setTime(d.getTime() + (ex_minutes*60*1000));//(ex_minutes*24*60*60*1000));
			console.log("exp: " + d);
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
		$(document).ready(function() {
			var drp_startDate = moment().subtract(6, 'days');
			var drp_startEnd = moment();

			// load date cookie
			var c_dates = getCookie("dates");
			if(c_dates) {
				c_dates = JSON.parse(c_dates);
				drp_startDate = moment(new Date(c_dates['start']));
				drp_startEnd  = moment(new Date(c_dates['end']));

				$('#reportrange span').html(drp_startDate.format('MMMM D, YYYY') + ' - ' + drp_startEnd.format('MMMM D, YYYY'));
			} else {
				var s = drp_startDate;
				var e = drp_startEnd;
				var dates = {
					"start": s.format('YYYY-M-D'),
					"end": e.format('YYYY-M-D')
				}
				setCookie("dates", JSON.stringify(dates), 30);
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
				setCookie("dates", JSON.stringify(dates), 30);
				crossroads.parse(hasher.getHash()); // reload current page (only content)
			});
		});
	</script>
</body>
</html>
