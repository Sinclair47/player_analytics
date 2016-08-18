<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

require_once 'app.php';



if (!isset($_GET['id'])) {
	die;
}

if (!empty(getServerIpsSql())) {
	$server_ip = $_GET['server'];
	$database->query('SELECT `server_ip`, COUNT(`auth`) AS connections, COUNT(DISTINCT(`auth`)) AS players, DATE_FORMAT(FROM_UNIXTIME(`connect_time`), "%p %I:00") AS time FROM `'.DB_TABLE_PA.'` WHERE '.getServerIpsSql(false, '', 'AND').' `connect_date` = :id GROUP BY `server_ip`, DATE_FORMAT(FROM_UNIXTIME(`connect_time`), "%H") ORDER BY DATE_FORMAT(FROM_UNIXTIME(`connect_time`), "%H")');
	$database->bind(':id', $_GET['id']);
	$connections = $database->resultset();
}
else {
	$database->query('SELECT `server_ip`, COUNT(`auth`) AS connections, COUNT(DISTINCT(`auth`)) AS players, DATE_FORMAT(FROM_UNIXTIME(`connect_time`), "%p %I:00") AS time FROM `'.DB_TABLE_PA.'` WHERE `connect_date` = :id GROUP BY `server_ip`, DATE_FORMAT(FROM_UNIXTIME(`connect_time`), "%H") ORDER BY DATE_FORMAT(FROM_UNIXTIME(`connect_time`), "%H")');
	$database->bind(':id', $_GET['id']);
	$connections = $database->resultset();
}
?>

<div class="modal-dialog modal-lg">
 	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">
				<span aria-hidden="true">&times;</span>
				<span class="sr-only">Close</span>
			</button>
			<h4 class="modal-title" id="myModalLabel"><?php echo $_GET['id']; ?></h4>
		</div>
		<div class="modal-body">
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>Time</th>
						<th>Server</th>
						<th style="text-align:center;">Connections</th>
						<th style="text-align:center;">Unique Players</th>
					</tr>
				</thead>
				<tbody>
<?php foreach ($connections as $connections): ?>
					<tr>
						<td><?php echo $connections['time']; ?></td>
						<td><?php echo ServerName($connections['server_ip'], $server_names); ?></td>
						<td style="text-align:center;"><?php echo $connections['connections']; ?></td>
						<td style="text-align:center;"><?php echo $connections['players']; ?></td>
					</tr>
<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('.table').dataTable({
		"pagingType": "full"
	});
});
</script>
