<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

//Database Info
require_once 'app.php';

//$server_ip = true;

#$database->query('SELECT COUNT(`auth`) AS cons, COUNT(DISTINCT(`auth`)) AS auth, COUNT(DISTINCT(`server_ip`)) AS server, COUNT(DISTINCT(`country_code`)) AS cc, SUM(`duration`) AS duration FROM `'.DB_TABLE_PA.'` '.getIpDatesSql($include_where = true));
$database->query('SELECT COUNT(*) AS cons, COUNT(DISTINCT(`auth`)) AS auth, COUNT(DISTINCT(`server_ip`)) AS server, COUNT(DISTINCT(`country_code`)) AS cc, count(*) AS duration FROM `'.DB_TABLE_PA.'` '.getIpDatesSql($include_where = true));
$key = FileSystemCache::generateCacheKey(sha1(serialize(array($database->stmt(), $db))), 'SQL');
$info = FileSystemCache::retrieve($key);
if($info === false) {
	$info = $database->single();
	FileSystemCache::store($key, $info, 1000);
}

$database->query('SELECT COUNT(*) as count FROM `'.DB_TABLE_PA.'`');
$key = FileSystemCache::generateCacheKey(sha1(serialize(array($database->stmt(), $db))), 'SQL');
$records = FileSystemCache::retrieve($key);
if($records === false) {
	$records = $database->single();
	FileSystemCache::store($key, $records, 2000);
}
?>
				<div class="row">
					<div class="col-lg-12">
<?php if (isset($server_ip)): ?>
	<h1 class="page-header">Dashboard - <?php echo $server_ip; ?></h1>
<?php else: ?>
	<h1 class="page-header">Dashboard</h1>
<?php endif ?>
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">
<?php if (isset($server_ip)): ?>
					<div id="unique" class="col-lg-3 col-md-6" style="cursor:pointer">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3 fa fa-child fa-5x"></div>
									<div class="col-xs-9 text-right">
										<div class="huge">
											<?php echo number_format($info['auth']); ?>
										</div>
										<div>
											Unique Players
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
<?php else: ?>
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3 fa fa-child fa-5x"></div>
									<div class="col-xs-9 text-right">
										<div class="huge">
											<?php echo number_format($info['auth']); ?>
										</div>
										<div>
											Unique Players
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
<?php endif ?>
<?php if (isset($server_ip)): ?>
					<div id="connections" class="col-lg-3 col-md-6" style="cursor:pointer">
						<div class="panel panel-green">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3 fa fa-child fa-5x"></div>
									<div class="col-xs-9 text-right">
										<div class="huge">
											<?php echo number_format($info['cons']); ?>
										</div>
										<div>
											Connections
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
<?php else: ?>
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-green">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3 fa fa-tasks fa-5x"></div>
									<div class="col-xs-9 text-right">
										<div class="huge">
											<?php echo $info['server']; ?>
										</div>
										<div>
											Servers
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
<?php endif ?>
<?php if (isset($server_ip)): ?>
					<div id="regions" class="col-lg-3 col-md-6" style="cursor:pointer">
						<div class="panel panel-yellow">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3 fa fa-globe fa-5x"></div>
									<div class="col-xs-9 text-right">
										<div class="huge">
											<?php echo $info['cc']; ?>
										</div>
										<div>
											Unique Regions
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
<?php else: ?>
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-yellow">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3 fa fa-globe fa-5x"></div>
									<div class="col-xs-9 text-right">
										<div class="huge">
											<?php echo $info['cc']; ?>
										</div>
										<div>
											Unique Regions
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
<?php endif ?>
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-red">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3 fa fa-database fa-5x"></div>
									<div class="col-xs-9 text-right">
										<div class="huge">
											<?php echo "~".number_format($records['count']) ?>
										</div>
										<div>
											Total DB records
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div><!-- /.row -->
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> Connections for
								
							</div><!-- /.panel-heading -->
							<div class="panel-body">
								<div id="chart" style="cursor:pointer;"></div> <!-- connections chart -->
							</div><!-- /.panel-body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">
					<div id="bottomrow">
						
					</div>
				</div><!-- /.row -->
<?php if (isset($server_ip)): ?>
	<script type="text/javascript">
	var query = "<?php echo $server_ip; ?>";
		$(document).ready(function() {
			$("#bottomrow").load("inc/getbottomrow.php);


			function data(dates){
					$.ajax({
						type: "GET",
						dataType: 'json',
						url: "inc/getdashboardrange.php?server="+query, // This is the URL to the API
						data: "id=" + dates,
						beforeSend: function(){
							$('#overlay').fadeIn("fast");
						},
						success: function(msg){
							$('#overlay').fadeOut("fast");
							chart.setData(msg);
						}
					})
				}
			$('#reportrange').daterangepicker(
				{
					ranges: {
						'Today': [moment(), moment()],
						'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
						'Last 7 Days': [moment().subtract(6, 'days'), moment()],
						'Last 30 Days': [moment().subtract(29, 'days'), moment()],
						'This Month': [moment().startOf('month'), moment().endOf('month')],
						'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
					},
					startDate: moment().subtract(6, 'days'),
					endDate: moment()
				},
				function(start, end) {
					$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
				}
			);
			$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
				var dates = picker.startDate.format('YYYY-M-D')+","+picker.endDate.format('YYYY-M-D');
				data(dates);
				$.ajax({
					type: "GET",
					url: "inc/getbottomrow.php?server="+query,
					data: 'id=' + picker.startDate.format('YYYY-M-D')+","+picker.endDate.format('YYYY-M-D'),
					success: function(msg){
						$('#bottomrow').empty();
						$('#bottomrow').html(msg);
					}
				});
			});
			
			var chart =  Morris.Area ({
				element: 'chart',
				data: data(moment().subtract('days', 6).format('YYYY-M-D')+","+moment().format('YYYY-M-D')),
				xkey: 'time',
				ykeys: ['total'],
				labels: ['Total'],
				barRatio: 0.4,
				xLabelAngle: 0,
				hideHover: 'auto',
				parseTime: false,
				resize: true
			}).on('click', function(i, row){
				$('#modal').modal('show');
				$.ajax({
					type: "GET",
					url: "inc/getdateinfo.php?server="+query,
					data: 'id='+row['d'],
					success: function(msg){
						$('#modal').html(msg);
					}
				});
			});
			$('#unique').on('click', function() {
				$('#modal').modal('show');
				$.ajax({
					type: "GET",
					url: "inc/getserverinfo.php?id=u&server="+query,
					success: function(msg){
						$('#modal').empty();
						$('#modal').html(msg);
					}
				});
			});
			$('#connections').on("click",function(){
				$('#modal').modal('show');
				$.ajax({
					type: "GET",
					url: "inc/getserverinfo.php?id=c&server="+query,
					success: function(msg){
						$('#modal').empty();
						$('#modal').html(msg);
					}
				});
			});
			$('#regions').on("click",function(){
				$('#modal').modal('show');
				$.ajax({
					type: "GET",
					url: "inc/getserverinfo.php?id=l&server="+query,
					success: function(msg){
						$('#modal').empty();
						$('#modal').html(msg);
					}
				});
			});
		});
	</script>
<?php else: ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#bottomrow").load("inc/getbottomrow.php");
		});
		function data(dates){
				$.ajax({
					type: "GET",
					dataType: 'json',
					url: "inc/getdashboardrange.php", // This is the URL to the API
					data: "id=" + dates,
					beforeSend: function(){
						$('#overlay').fadeIn("fast");
					},
					success: function(msg){
						$('#overlay').fadeOut("fast");
						chart.setData(msg);
					}
				})
			}
		
		var chart =  Morris.Area ({
			element: 'chart',
			data: data(moment().subtract('days', 6).format('YYYY-M-D')+","+moment().format('YYYY-M-D')),
			xkey: 'time',
			ykeys: ['total'],
			labels: ['Total'],
			barRatio: 0.4,
			xLabelAngle: 0,
			hideHover: 'auto',
			parseTime: false,
			resize: true
		}).on('click', function(i, row){
			$('#modal').modal('show');
			$.ajax({
				type: "GET",
				url: "inc/getdateinfo.php",
				data: 'id='+row['d'],
				success: function(msg){
					$('#modal').html(msg);
				}
			});
		});
	</script>
<?php endif ?>