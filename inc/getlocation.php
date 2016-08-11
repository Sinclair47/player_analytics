<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

//Database Info
require_once 'config.php';
?>
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Locations</h1>
				</div><!-- /.col-lg-12 -->
			</div><!-- /.row -->
			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> Active Countries
							<div class="pull-right">
								<div id="reportrange" class="pull-right">
									<i class="fa fa-calendar fa-lg"></i>
									<span><?php echo date("F j, Y", strtotime('-7 day')); ?> - <?php echo date("F j, Y"); ?></span> <b class="caret"></b>
								</div>
							</div>
						</div><!-- /.panel-heading -->
						<div class="panel-body">
							<div id="map" style="height:400px"></div>
						</div><!-- /.panel-body -->
					</div><!-- /.panel -->
				</div><!-- /.col-lg-12 -->
			</div><!-- /.row -->
			<div class="row">
				<div class="col-lg-12">
					<div id="result"></div>
				</div><!-- /.col-lg-12 -->
			</div><!-- /.row -->
<script type="text/javascript">
	$(document).ready(function() {
		$( "#map" ).load( "inc/getlocationrange.php" );
	});
</script>
<script type="text/javascript">
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
		$.ajax({
			type: "GET",
			url: "inc/getlocationrange.php",
			data: 'id=' + picker.startDate.format('YYYY-M-D')+","+picker.endDate.format('YYYY-M-D'),
			beforeSend: function(){
				$('#overlay').fadeIn("fast");
			},
			success: function(msg){
				$('#overlay').fadeOut("fast");
				$('#map').html(msg);
			}
		});
	});
</script>