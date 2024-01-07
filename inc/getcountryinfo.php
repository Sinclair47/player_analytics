<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

$id = $_GET['id']; 

?>

				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> Players From <?php echo $id; ?>
							</div><!-- /.panel-heading -->
							<div class="panel-body">
								<div style="padding:10px">
									<table id="players" class="table table-hover table-bordered table-striped table-condensed display" style="cursor:pointer">
										<thead>
											<tr>
												<th>ID</th>
												<th style="width:20%">Name</th>
												<th>Auth</th>
												<th>Duration</th>
												<th>Premium</th>
												<th>HTML</th>
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
			"processing": true,
			"serverSide": true,
			"ajax": "inc/server_processing.php?type=getcountryinfo&id=<?php echo $id; ?>",
			"pagingType": "full",
			"columns": [
				{ "data": "id", "visible" : false },
				{ "data": "name" },
				{ "data": "auth", "visible" : false },
				{ "data": "duration" },
				{ "data": "premium" },
				{ "data": "html_motd_disabled" },
				{ "data": "os" },
				{ "data": "server_ip", "visible" : false, "searchable" : true }
			],
			"order": [[0, 'desc']]
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
