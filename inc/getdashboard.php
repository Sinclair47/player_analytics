<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

//Database Info
include 'config.php';

// Include database class
include 'database.class.php';

// Instantiate database.
$database = new Database();

$database->query('SELECT COUNT(DISTINCT(`auth`)) AS auth, COUNT(DISTINCT(`server_ip`)) AS server, COUNT(DISTINCT(`country_code`)) AS cc, SUM(`duration`) AS duration FROM `player_analytics`');
$info = $database->single();

?>
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">Dashboard</h1>
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">
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
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-red">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3 fa fa-clock-o fa-5x"></div>
									<div class="col-xs-9 text-right">
										<div class="huge">
											<?php echo PlaytimeCon($info['duration']) ?>
										</div>
										<div>
											Hours Played
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
								<div class="pull-right">
									<div id="reportrange" class="pull-right">
										<i class="fa fa-calendar fa-lg"></i>
										<span><?php echo date("F j, Y", strtotime('-7 day')); ?> - <?php echo date("F j, Y"); ?></span> <b class="caret"></b>
									</div>
								</div>
							</div><!-- /.panel-heading -->
							<div class="panel-body">
								<div id="chart" style="cursor:pointer;"></div>
							</div><!-- /.panel-body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">
					<div id="bottomrow">
						
					</div>
				</div><!-- /.row -->
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
			url: "inc/getbottomrow.php",
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
			url: "inc/getdateinfo.php",
			data: 'id='+row['d'],
			success: function(msg){
				$('#modal').html(msg);
			}
		});
	});
</script>
