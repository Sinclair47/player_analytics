<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

require_once 'app.php';


// if (!isset($_GET['id'])) {
// 	$_GET['id'] = date("Y-m-d", strtotime('-7 day')).",".date("Y-m-d");
// }


$database->query('SELECT `country_code`, COUNT(`auth`) AS total FROM `'.DB_TABLE_PA.'` '.getIpDatesSql($include_where = true).' GROUP BY `country_code`');
$map = $database->resultset();


$maps = array();
if(!empty($map)) {
	foreach ($map as $key => $value) {
		if ($value['country_code'] != NULL) {
			$maps[$value['country_code']] = $value['total'];
		}
	}
} else {
	echo '<p class="bg-warning" style="padding:15px">No data available.</p>';
}

$maps = json_encode($maps);
?>

<script type="text/javascript">
	var cdata = <?php print_r($maps); ?>;
	$(function(){
		$('#map').vectorMap({
			map: 'world_merc_en',
			backgroundColor: 'transparent',
			zoomButtons: false,
			regionsSelectable: true,
			regionsSelectableOne: true,
			regionStyle: {
				initial: {
					fill: 'white',
					"fill-opacity": 1,
					stroke: '#7f8c8d',
					"stroke-width": 1,
					"stroke-opacity": 1
				},
				selected: {
					fill: '#2c3e50'
	      }
	    },
			series: {
				regions: [{
					scale: ['#75b9e7', '#1d6fa5'],
					normalizeFunction: 'linear',
					attribute: 'fill',
					values: cdata
				}]
			},
			onRegionOver: function(e, code) {
				if (cdata.hasOwnProperty(code)) {
					document.body.style.cursor = 'pointer';
				}
			},
			onRegionOut: function(e, code) {
				// return to normal cursor
				document.body.style.cursor = 'default';
			},
			onRegionLabelShow: function(e, el, code){
				if (cdata.hasOwnProperty(code)) {
	      	el.html(el.html()+' - '+cdata[code]);
	      }
	    },
			onRegionClick: function (event, code) {
				if (cdata.hasOwnProperty(code)) {
					var map = $('#map').vectorMap('get', 'mapObject');
					var name = map.getRegionName(code);
					$.ajax({
						type: "GET",
						url: "inc/getcountryinfo.php",
						data: 'id=' + code,
						success: function(msg){
							$('#result').html(msg);
						}
					});
				}
			}
		});
	})
</script>
