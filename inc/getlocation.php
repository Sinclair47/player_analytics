<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

require_once 'app.php';
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
