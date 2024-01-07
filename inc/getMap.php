<?php

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

require_once 'app.php';

#TODO limit Group By results
$database->query('SELECT server_ip, count(*) as total, sum(duration) as play_time_sum, avg(duration) as play_time_avg, AVG(numplayers) as numplayers_avg, max(numplayers), map FROM `'.DB_TABLE_PA.'` '.getIpDatesSql($include_where = true).' GROUP BY server_ip, map ORDER BY play_time_sum DESC');
$key = FileSystemCache::generateCacheKey(sha1(serialize(array($database->stmt(), $db))), 'SQL');
$map_stats = FileSystemCache::retrieve($key);
if($map_stats === false) {
	$map_stats = $database->resultset();
	FileSystemCache::store($key, $map_stats, 400);
}

$chart_data_final = "";

#pr($map_stats);

if(!empty($map_stats)) {
    foreach($map_stats as $map) {
        $chart_data[$map['server_ip']][] = $map;
    }


    $d = processData($chart_data, "sum");
    $chart_data_final = $d[0];
    $chart_data_final_category = $d[1];
    $max_sum = floor($d[2]);

    $d = processData($chart_data, "avg");
    $chart_data_final_avg = $d[0];
    $chart_data_final_category_avg = $d[1];
    $max_avg = floor($d[2]);

    $d = processData($chart_data, "avg_playtime");
    $chart_data_final_avg_playtime = $d[0];
    $chart_data_final_category_avg_playtime = $d[1];
    $max_avg_play_time = floor($d[2]);

    ksort($chart_data_final); # sort by IP (by key)
    #pr(($chart_data_final));
}



function processData($chart_data, $kind = "", $count = 4) {
    $chart_data_final = [];
    $max = 0;
    foreach($chart_data as $ip => $data) {
        if(is_array($data)) {


            $i = 0;
            $total_sum = 0;
            foreach($data as $x) {
                $total_sum += $x['play_time_sum'];

                if($kind == "sum") {
                    if(round($x['play_time_sum']/60/60, 1) > $max) {
                        $max = round($x['play_time_sum']/60/60, 1);
                    }
                }
                if($kind == "avg") {
                    if($x['numplayers_avg'] > $max) {
                        $max = $x['numplayers_avg'];
                    }
                }
                if($kind == "avg_playtime") {
                    if(round($x['play_time_avg']/60, 1) > $max) {
                        $max = round($x['play_time_avg']/60, 1);
                    }
                }
            }


            if($kind == "avg") {
                usort ($data,"sortByAvgPlayers");
            }
            if($kind == "avg_playtime") {
                usort ($data,"sortByAvgPlayTime");
            }
            #pr($data);

            foreach($data as &$value) {
                if($i++ > $count) {
                    continue;
                }

                if($kind == "sum") {
                    $chart_data_final[$ip]["Total Playtime (in hours)"][] = round($value['play_time_sum']/60/60, 1);# / $total_sum * 100;
                }
                if($kind == "avg") {
                    $chart_data_final[$ip]["AVG number of Players"][] = round($value['numplayers_avg'], 1);
                }
                if($kind == "avg_playtime") {
                    $chart_data_final[$ip]["AVG Playtime (in minutes)"][] = round($value['play_time_avg']/60, 1);
                }
                $chart_data_final_category[$ip][] = $value['map'];

            }
        }

    }
    return array($chart_data_final, $chart_data_final_category, $max);
}

function sortByAvgPlayers($a, $b){
    if ($a['numplayers_avg'] == $b['numplayers_avg']) return 0;
    return $a['numplayers_avg'] > $b['numplayers_avg'] ? -1 : 1;
}
function sortByAvgPlayTime($a, $b){
    if ($a['play_time_avg'] == $b['play_time_avg']) return 0;
    return $a['play_time_avg'] > $b['play_time_avg'] ? -1 : 1;
}


?>

				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">Maps</h1>
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> Maps
							</div><!-- /.panel-heading -->
							<div class="panel-body">
								<div style="padding-right:10px">

									<p>BETA STATUS</p>
                                    <p>In the left chart are the Top 5 maps sorted by avg play time. The right chart shows the Total play time by all players on a map.</p>
                                    <?php
                                        if(!empty($map_stats)) {
                                            foreach ($chart_data_final as $ip => $data) { ?>
                                                <div class="row" style="margin-bottom: 5px">
                                                    <h3 class="col-lg-12"><?php echo ServerName($ip, $server_names); ?></h3>
                                                    <div class="col-lg-4" id="chart_map_avg_<?php echo md5($ip) ?>"></div>
                                                    <div class="col-lg-4" id="chart_map_avg_play_<?php echo md5($ip) ?>"></div>
                                                    <div class="col-lg-4" id="chart_map_sum_<?php echo md5($ip) ?>"></div>
                                                </div>
                                    <?php }} else {
                                        echo '<p class="bg-warning" style="padding:15px">No Maps Data</p>';
                                    } ?>

								</div>
							</div><!-- /.panel-body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->

                <script type="text/javascript">
                    $(document).ready(function() {
                        <?php
                            foreach ($chart_data_final as $ip => $data) {
                                echo 'createChart("#chart_map_avg_'.md5($ip).'", '.json_encode($chart_data_final_avg[$ip]).', '.json_encode($chart_data_final_category_avg[$ip]).', "avg", '.$max_avg.');';
                                echo 'createChart("#chart_map_avg_play_'.md5($ip).'", '.json_encode($chart_data_final_avg_playtime[$ip]).', '.json_encode($chart_data_final_category_avg_playtime[$ip]).', "avg_play", '.$max_avg_play_time.');';
                                echo 'createChart("#chart_map_sum_'.md5($ip).'", '.json_encode($chart_data_final[$ip]).', '.json_encode($chart_data_final_category[$ip]).', "sum", '.$max_sum.');';
                            }
                        ?>
                    });

                    function createChart(chart_id, columns_data, category_data, type, max ) {
                        var chart = c3.generate({
                            bindto: chart_id,
                            data: {
                                json: columns_data,
                            // x : 'x',
                                type: 'bar'
                            },
                            size: {
                                height: 180,
                                //width: 260
                            },
                            axis: {
                                rotated: true,
                                x: {
                                    type: 'category',
                                    categories: category_data,
                                },
                            },
                            tooltip: {
                                format: {
                                    value: function (value, ratio, id) {
                                        if(type == "sum") {
                                            return value + "h";
                                        }
                                        if(type == "avg_play") {
                                            return value + " min";
                                        }
                                        return value;
                                    }
                                }
                            },
                            labels: true,
                            bar: {
                                width: {
                                    ratio: 0.5 // this makes bar width 50% of length between ticks
                                }
                            },
                        });

                    // if(type == "avg") {
                            chart.axis.max({y: max});
                    // }
                    }

                </script>
