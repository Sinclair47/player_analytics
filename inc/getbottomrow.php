<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

//Database Info
require_once 'config.php';

// Include database class
require_once 'database.class.php';

if (!isset($_GET['id'])) {
	$_GET['id'] = date("Y-m-d", strtotime('-7 day')).",".date("Y-m-d");
}

$date = explode(",", $_GET['id']);

// Instantiate database.
$database = new Database();
$server_ip = Util::getCookie("server");
if (isset($server_ip)) {
	$database->query('SELECT `country` AS label, COUNT(*) AS value FROM `'.DB_TABLE_PA.'` WHERE `server_ip` = :ip AND `connect_date` BETWEEN  :start AND :end GROUP BY `country`'); #count(*) instead of count(country) bcs we need to count also NULL values 
	$database->bind(':start', $date[0]);
	$database->bind(':end', $date[1]);
	$database->bind(':ip', $server_ip);
	$country = $database->resultset();

	$database->query('SELECT `connect_method` AS label, COUNT(*) AS value FROM `'.DB_TABLE_PA.'` WHERE `server_ip` = :ip AND `connect_date` BETWEEN  :start AND :end GROUP BY `connect_method`');
	$database->bind(':start', $date[0]);
	$database->bind(':end', $date[1]);
	$database->bind(':ip', $server_ip);
	$method = $database->resultset();

	$database->query('SELECT `premium` AS label, COUNT(*) AS value FROM `'.DB_TABLE_PA.'` WHERE `server_ip` = :ip AND `connect_date` BETWEEN  :start AND :end GROUP BY `premium`');
	$database->bind(':start', $date[0]);
	$database->bind(':end', $date[1]);
	$database->bind(':ip', $server_ip);
	$premium = $database->resultset();
}

else {
	$database->query('SELECT `country` AS label, COUNT(*) AS value FROM `'.DB_TABLE_PA.'` WHERE `connect_date` BETWEEN  :start AND :end GROUP BY `country`');
	$database->bind(':start', $date[0]);
	$database->bind(':end', $date[1]);
	$country = $database->resultset();

	$database->query('SELECT `connect_method` AS label, COUNT(*) AS value FROM `'.DB_TABLE_PA.'` WHERE `connect_date` BETWEEN  :start AND :end GROUP BY `connect_method`');
	$database->bind(':start', $date[0]);
	$database->bind(':end', $date[1]);
	$method = $database->resultset();

	$database->query('SELECT `premium` AS label, COUNT(*) AS value FROM `'.DB_TABLE_PA.'` WHERE `connect_date` BETWEEN  :start AND :end GROUP BY `premium`');
	$database->bind(':start', $date[0]);
	$database->bind(':end', $date[1]);
	$premium = $database->resultset();
}


$country = processCountries($country, $Show_Max_Countries);
$methods = processConnectMethods($method);
$premium = processPremium($premium);

$country = json_encode($country);
$methods = json_encode(array_values($methods));
$premium = json_encode($premium);
?>
					<div class="col-lg-4">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> Top <?php echo $Show_Max_Countries ?> Countries
							</div>
							<div class="panel-body">
								<div id="country"></div>
								<a href="#/stats/locations" class="btn btn-default btn-block"><input type="hidden" value="getlocation"/>View Details</a>

							</div><!-- /.panel-body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-4 -->
					<div class="col-lg-4">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> Connection Method
							</div>
							<div class="panel-body">
								<div id="method"></div>
								<a href="#/stats/connections" class="btn btn-default btn-block"><input type="hidden" value="getconnections"/>View Details</a>
							</div><!-- /.panel-body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-4 -->
					<div class="col-lg-4">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> Premium
							</div>
							<div class="panel-body">
								<div id="premium"></div>
								<a href="#/stats/players" class="btn btn-default btn-block"><input type="hidden" value="getplayers"/>View Details</a>
							</div><!-- /.panel-body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-4 -->
<script type="text/javascript">
	Morris.Donut({
	  element: 'country',
	  data: <?php echo $country; ?>,
	  formatter: function (y) { return y + "%" ;}
	});
</script>
<script type="text/javascript">
	Morris.Donut({
	  element: 'method',
	  data: <?php echo $methods; ?>,
	  formatter: function (y) { return y + "%" ;}
	});
</script>
<script type="text/javascript">
	Morris.Donut({
	  element: 'premium',
	  data: <?php echo $premium; ?>,
	  formatter: function (y) { return y + "%" ;}
	});
</script>
<script type="text/javascript">
// TODO remove
	// $(document).ready(function() {
	// 	$('.btn-block').on('click', function() {
	// 		$.ajax({
	// 			type: "GET",
	// 			url: "inc/"+ $(this).find("input").val()+".php",
	// 			beforeSend: function(){
	// 				$('#overlay').fadeIn("fast");
	// 				$('#content').empty();
	// 				$('.daterangepicker').detach();
	// 			},
	// 			success: function(msg){
	// 				$('#content').delay(400).fadeIn("slow").html(msg);
	// 				$('#overlay').delay(400).fadeOut( "slow" );
	// 			}
	// 		});
	// 	});
	// });
</script>

<?php

function processCountries($country, $Show_Max_Countries) {

	if(empty($country)) {
		return NoChartData();
	}

	$c_total = 0;

	foreach ($country as $key => $value) {
		$c_total += $value['value'];
	}

	foreach ($country as $key => $value) {
		$country[$key]['value'] = number_format($value['value']/$c_total*100,2);
	}

	#show the top ten countries only
	$tmp_countries = $country;
	$countries_filtered = array();
	$c_percent = 0;
	$skip_other_country_calc = false;

	if(count($country) <= $Show_Max_Countries) {
		$Show_Max_Countries = count($country);
		$skip_other_country_calc = true;
	}


	if(count($country) > 1) {
		for($i = 1; $i <= $Show_Max_Countries; $i++) {
			$top = array_reduce($tmp_countries, function ($a, $b) {
				return @$a['value'] > $b['value'] ? $a : $b ;
			});
			foreach($tmp_countries as $key => &$c) {
				if($c['label'] == $top['label']) {
					unset($tmp_countries[$key]);
				}
			}

			$countries_filtered[] = $top;
			$c_percent += $top['value'];
		}

		$countries_filtered = array_reverse($countries_filtered); #reverse array order, so the donut is build up from big to small
		if(!$skip_other_country_calc) {
			$countries_filtered[] = array(
										'label' => 'Other Countries',
										'value' => number_format(100 - $c_percent,2),
			);
		}
		$country = $countries_filtered;
	}

	return $country;
}


function processConnectMethods($method) {

	if(empty($method)) {
		return NoChartData();
	}

	$m_total = 0;

	foreach ($method as $key => $value) {
		$m_total += $value['value'];
	}


	foreach ($method as $key => $value) {
		if (preg_match("/quickplay/", $value['label']) || preg_match("/quickpick/", $value['label'])) {
			
			if (preg_match("/quickplay/", $value['label'])) {
				if (!isset($id)) {
					$id = $key;
					
					$methods[$id]['label'] = 'quickplay';
					$methods[$id]['value'] = $value['value'];
				}
				else {
					$methods[$id]['label'] = 'quickplay';
					$methods[$id]['value'] += $value['value'];
				}
			}
			if (preg_match("/quickpick/", $value['label'])) {
				if (!isset($id2)) {
					$id2 = $key;
					$methods[$id2]['label'] = 'quickpick';
					$methods[$id2]['value'] = $value['value'];
				}
				else {
					$methods[$id2]['label'] = 'quickpick';
					$methods[$id2]['value'] += $value['value'];
				}
			}
		}
		else {
			$methods[$key]['label'] = $value['label'];
			$methods[$key]['value'] = $value['value'];	
		}
	}
	if(isset($methods)) {
		foreach ($methods as $key => $value) {
			$methods[$key]['label'] = ConnMethod($value['label']);
			$methods[$key]['value'] = number_format($value['value']/$m_total*100,2);
		}
	}

	return $methods;

}


function processPremium($premium) {
	if(empty($premium)) {
		return NoChartData();
	}

	$p_total = 0;

	foreach ($premium as $key => $value) {
		$p_total += $value['value'];
	}

	foreach ($premium as $key => $value) {
		if ($value['label'] == '1') {
			$value['label'] = 'Premium';
		} 
		elseif ($value['label'] == '0') {
			$value['label'] = 'F2P';
		}
		else {
			$value['label'] = 'Unknown';
		}
		$premium[$key]['label'] = $value['label'];
		if(!$p_total == 0)
			$premium[$key]['value'] = number_format($value['value']/$p_total*100,2);
		else
			$premium[$key]['value'] = 0;
	}

	return $premium;
}

function NoChartData() {
	return array(
			0 => array(
				'label' => 'No Data',
				'value' => '0',
			)
		);
}
