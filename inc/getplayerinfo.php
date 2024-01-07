<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

require_once 'app.php';

$database->query('SELECT `server_ip`, `name`, `auth`, `connect_time`, `connect_method`, `numplayers`, `map`, `duration`, `flags`, `country`, `os`  FROM `'.DB_TABLE_PA.'` WHERE auth = :auth AND '.getIpDatesSql().' ORDER BY `connect_time` DESC');
$key = FileSystemCache::generateCacheKey(sha1(serialize(array($database->stmt(), $_GET['id'], $db))), 'SQL');
$info = FileSystemCache::retrieve($key);
if($info === false) {
	$database->bind(':auth', $_GET['id']);
	$info = $database->resultset();
	FileSystemCache::store($key, $info, 1000);
}

$profile = GetPlayerInformation(SteamTo64($_GET['id']));

?>

				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo htmlentities($profile['personaname']); ?>
							<strong class="pull-right refresh"><i id="getplayerinfo" class="fa fa-refresh fa-fw"></i></strong>
						</h1>
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">
					<div class="col-lg-3">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> Profile
							</div><!-- /.panel-heading -->
							<div class="panel-body">
								<div style="padding:10px">
									<a href="<?php echo $profile['profileurl']; ?>">
										<img src="<?php echo $profile['avatarfull']; ?>" alt="<?php echo $profile['personaname']; ?>">
									</a>
									<h4>SteamID</h4>
									<span class="badge-profile"><?php echo @$info[0]['auth']; ?></span>
									<h4>Last Location</h4>
									<span class="badge-profile"><?php echo @$info[0]['country']; ?></span>
									<h4>OS</h4>
									<span class="badge-profile"><?php echo @$info[0]['os']; ?></span>
								</div>
							</div><!-- /.panel-body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-3 -->		
					<div class="col-lg-9">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> History
							</div><!-- /.panel-heading -->
							<div class="panel-body">
								<div style="padding:10px">
									<table class="table table-hover table-bordered table-striped table-condensed" style="table-layout:fixed;width:100%;">
										<thead>
											<tr>
												<th style="text-align:left;">Server </th>
												<th style="text-align:center;width:8%;">Date </th>
												<th style="text-align:center;width:10%;">Duration </th>
												<th style="text-align:center;width:5%;"><i class="fa fa-group fa-fw"> </i></th>
												<th style="text-align:right;">Map </th>
												<th style="text-align:right;">Method </th>
												<th style="text-align:right;">Flags </th>
											</tr>
										</thead>
										<tbody>
						<?php foreach ($info as $info): ?>
											<tr>
												<td style="text-align:left;max-width:100%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo ServerName($info['server_ip'], $server_names); ?></td>
												<td style="text-align:center;"><?php echo date('Y-m-d', $info['connect_time']); ?></td>
												<td style="text-align:center;"><?php echo PlaytimeCon($info['duration']); ?></td>
												<td style="text-align:center;"><?php echo $info['numplayers']; ?></td>
												<td style="text-align:right;max-width:100%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo $info['map']; ?></td>
												<td style="text-align:right;"><?php echo ConnMethod($info['connect_method']); ?></td>
												<td style="text-align:right;"><?php echo FlagToName($info['flags'], $staff_group_names); ?></td>
											</tr>
						<?php endforeach ?>
										</tbody>
									</table>
								</div>
							</div><!-- /.panel-body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-9 -->
					<div class="row">
						<div class="col-lg-3">

						</div>
					</div>
				</div><!-- /.row -->
<script type="text/javascript">
$(document).ready(function(){
	$('.table').DataTable({
		"retrieve": true,
		"pagingType": "full",
		"order": [[1, 'desc']]
	});

});
</script>
<script>
function getData() {
	$.ajax({
		url: "inc/getplayerinfo.php",
		data: 'id=<?php echo $_GET["id"]; ?>',
		beforeSend: function(){
			$('#overlay').fadeIn("fast");
		},
		success: function(data) {
			$('#content').empty();
			$('#content').delay(400).fadeIn("slow").append(data);;
			$('#overlay').delay(400).fadeOut( "slow" );
		}
	});
}
$('#getplayerinfo').on('click', getData);
</script>
