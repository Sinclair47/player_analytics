<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

 ?>
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">Players</h1>
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> Players
							</div><!-- /.panel-heading -->
							<div class="panel-body">
								<div style="padding:10px">
									<table id="players" class="table table-hover table-bordered table-striped table-condensed display" style="cursor:pointer">
										<thead>
											<tr>
												<th>ID</th>
												<th style="width:20%">Name</th>
												<th>Auth</th>
												<th>Total</th>
												<th>Duration</th>
												<th>Last On</th>
												<th>Country</th>
												<th title="Premium status. 0 = F2P, 1 = Premium aka you bought the game"><i class="fa fa-usd"></i></th>
												<th title="Html MOTD disabled status"><i class="fa fa-html5"></i></th>
												<th>OS</th>
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
			"processing": true,
			"serverSide": true,
			"ajax": "inc/server_processing.php?type=getplayers",
			"columns": [
				{ "data": "id", "visible" : false },
				{ "data": "name" },
				{ "data": "auth", "visible" : false },
				{ "data": "total" },
				{ "data": "duration", "searchable" : false },
				{ "data": "connect_time" },
				{ "data": "country" },
				{ "data": "premium" },
				{ "data": "html_motd_disabled" },
				{ "data": "os" }
			],
			"order": [[3, 'desc']]
		});
		$('#players tbody').on('click', 'tr', function () {
			window.location.href = '#/stats/players/info/' + players.cell(this, 2).data();
			// $.ajax({
			// 	type: "GET",
			// 	url: "inc/getplayerinfo.php",
			// 	data: 'id='+players.cell(this, 2).data(),
			// 	beforeSend: function(){
			// 		$('#overlay').fadeIn();
			// 	},
			// 	success: function(msg){
			// 		$('#content').html(msg);
			// 		$('#overlay').fadeOut();
			// 	}
			// });
		});
	});
</script>
