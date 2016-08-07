<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

 ?>
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">Connections</h1>
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> Connections
							</div><!-- /.panel-heading -->
							<div class="panel-body">
								<div style="padding:10px">
									<table id="players" class="table table-bordered table-striped table-condensed display" style="cursor:pointer">
										<thead>
											<tr>
												<th>ID</th>
												<th style="width:20%">Name</th>
												<th>Auth</th>
												<th>Time</th>
												<th>Method</th>
												<th>Duration</th>
												<th>Country</th>
												<th><i class="fa fa-usd"></i></th>
												<th><i class="fa fa-html5"></i></th>
												<th>OS</th>
												<th>Server</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
							</div><!-- /.panel-body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->
<script type="text/javascript">
	$(document).ready(function() {
		var players = $('#players').DataTable( {
			"processing": false,
			"serverSide": true,
			"ajax": "inc/server_processing.php?type=getconnections",
			"columns": [
				{ "data": "id", "visible" : false },
				{ "data": "name" },
				{ "data": "auth", "visible" : false },
				{ "data": "connect_time" },
				{ "data": "connect_method" },
				{ "data": "duration" },
				{ "data": "country" },
				{ "data": "premium" },
				{ "data": "html_motd_disabled" },
				{ "data": "os" },
				{ "data": "server_ip", "visible" : false, "searchable" : true }
			],
			"order": [[0, 'desc']]
		});
		$('#players tbody').on('click', 'tr', function () {
			$.ajax({
				type: "GET",
				url: "inc/getplayerinfo.php",
				data: 'id='+players.cell(this, 2).data(),
				beforeSend: function(){
					$('#overlay').fadeIn();
				},
				success: function(msg){
					$('#content').html(msg);
					$('#overlay').fadeOut();
				}
			});
		});
	});
</script>
