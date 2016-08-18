<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

require_once 'app.php';
?>

<?php if ($id == "c"): ?>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"><?php echo $server; ?></h4>
			</div>
			<div class="modal-body">
				<table id="players" class="table table-hover table-bordered table-striped table-condensed display" style="cursor:pointer">
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
		</div>
	</div>
<?php endif ?>
<?php if ($id == "u"): ?>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"><?php echo $server; ?></h4>
			</div>
			<div class="modal-body">
				<table id="players" class="table table-bordered table-striped table-condensed display" style="cursor:pointer">
					<thead>
						<tr>
							<th>ID</th>
							<th style="width:20%">Name</th>
							<th>Auth</th>
							<th>Total</th>
							<th>Playtime</th>
							<th>Last On</th>
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
		</div>
	</div>
<?php endif ?>
<?php if ($id == "l"): ?>
<?php 

	$database->query('SELECT COUNT(`auth`) AS total, `country` FROM `'.DB_TABLE_PA.'` WHERE `server_ip` = :ip GROUP BY `country` ORDER BY total DESC');
	$database->bind(':ip', $server);
	$regions = $database->resultset();

?>
	<div class="modal-dialog modal-lg">
	 	<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"><?php echo $server; ?></h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered table-striped table-condensed display">
					<thead>
						<tr>
							<th>Country</th>
							<th style="text-align:right;">Total</th>
						</tr>
					</thead>
					<tbody>
	<?php foreach ($regions as $regions): ?>
						<tr>
							<td><?php echo $regions['country']; ?></td>
							<td style="text-align:right;"><?php echo $regions['total']; ?></td>
						</tr>
	<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php endif ?>

<?php if ($id == "c"): ?>
	<script type="text/javascript">
		$(document).ready(function() {
			var query = "<?php echo $_GET['server']; ?>";
			var players = $('#players').DataTable( {
				"processing": true,
				"serverSide": true,
				"ajax": "inc/server_processing.php?type=c&server="+query,
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
					{ "data": "server_ip", "visible" : false }
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
<?php endif ?>
<?php if ($id == "u"): ?>
	<script type="text/javascript">
		$(document).ready(function() {
			var query = "<?php echo $_GET['server']; ?>";
			var players = $('#players').DataTable( {
				"processing": false,
				"serverSide": true,
				"ajax": "inc/server_processing.php?type=u&server="+query,
				"columns": [
					{ "data": "id", "visible" : false },
					{ "data": "name" },
					{ "data": "auth", "visible" : false },
					{ "data": "total" },
					{ "data": "duration" },
					{ "data": "connect_time" },
					{ "data": "country" },
					{ "data": "premium" },
					{ "data": "html_motd_disabled" },
					{ "data": "os" },
					{ "data": "server_ip", "visible" : false }
				],
				"order": [[3, 'desc']]
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
<?php endif ?>
<?php if ($id == "l"): ?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.table').dataTable({
				"pagingType": "full",
				"order": [[1, 'desc']]
			});
		});
	</script>
<?php endif ?>
