<?php
require_once 'app.php';



switch($_GET['id']) {
     case 1:
		#, COUNT(DISTINCT(`auth`)) AS players
		$database->query('SELECT COUNT(*) AS Comparison_Type, DATE_FORMAT(connect_date, "%Y%m%d") AS Date FROM `'.DB_TABLE_PA.'` WHERE '.getServerIpsSql(false, '', 'AND').' (year(connect_date) = "'.date("Y").'" OR year(connect_date) = "'.((int)date("Y") - 1).'" OR year(connect_date) = "'.((int)date("Y") - 2).'") GROUP BY YEAR(connect_date), month(connect_date), day(connect_date) ORDER BY `Date` ASC');
		$data = $database->resultset();

		echo json_encode($data);
		break;
	
	case 2:
		break;

	case 3:
		#$date_yesterday =  time() - 86400;
		$flags = " AND flags != '' ";
		if(isset($_GET['flags']) && $_GET['flags'] != 0) {
			$flags = "";
		}

		$database->query('SELECT id, auth, name, connect_time, map, duration  FROM `'.DB_TABLE_PA.'` WHERE '.getIpDatesSql(false).' '.$flags.'  ORDER BY connect_time ASC');
		$timeline_data_raw = $database->resultset();

		#$timeline_data['data'] = "";
		$timeline_data = array();
		$timeline_data_maps = array();

		$i = $map_i = 0;
		$last_map = "";
		$current_map = $next_map = "";
		$map_dateStart = "";

		foreach($timeline_data_raw as $r) {

			# new user
			$timeline_data[$i] = array(
				'id' 		=> $r['id'],
				'userid'	=> $r['auth'],
				'content' 	=> $r['name'],
				'start'	 	=> date("Y-m-d H:i:s", $r['connect_time']),
				'end' 		=> date("Y-m-d H:i:s", ($r['connect_time'] + $r['duration'])),
				'map' 		=> $r['map'],
				'title'		=> $r['auth']
			);

			$next_map = $r["map"];

			# new map
			$map_color = ($map_i % 2 == true ? "" : "'negative'");
			#if($last_map != $r['map']) { # we are on a new map
			if($current_map != $next_map) {
				
				$current_map = $next_map;
				$s = $map_dateStart;

				if(isset($map)) { # inset map delay
					$timeline_data_maps[$i] = array(
						'id' 		=> "m".$r['id'],
						'content' 	=> $r['map'],
						'start' 	=> date("Y-m-d H:i:s", $s),
						'end' 		=> date("Y-m-d H:i:s", ($r['connect_time'] + $r['duration'])),
						'map' 		=> $r['map'],
						'type' 		=> "background",
						'className' => $map_color,
					);
					$map_i++;
				}

				#$last_map =  $r['map'];
				$map = $current_map;
				$map_dateStart = $r['connect_time'];
			}

			$i++;

			# same map
			if($current_map != $next_map) {
				$current_map = $next_map;
			}
		}



		# merge users that are still on the server after a map change remove small time gaps between map changes
		$time_gap = 180; # in sec
		#$timeline_data_copy = $timeline_data;
		$cnt = count($timeline_data);
		for($i = 0; $i < $cnt; $i++)
		{
			for($j = $i + 1; $j < ($i + 40); $j++) #compare current user with the next X users. If it's the same user and we detect a map change => merge into one user & reset time.
			{
				if(isset($timeline_data[$j]))
				{
					if($timeline_data[$i]['userid'] == $timeline_data[$j]['userid']) #same user
					{
						$time_diff = strtotime($timeline_data[$j]['start']) - strtotime($timeline_data[$i]['end']); 
						if(abs($time_diff) < $time_gap) # small gap means mapchange (or quick reconnect of User)
						{
							$timeline_data[$j]['start'] = $timeline_data[$i]['start'];
							unset($timeline_data[$i]);
							break;
						}
						
					}	
				}
			} #for2
		}

		$final = array_merge($timeline_data,  $timeline_data_maps);
		echo json_encode($final); die;
		pr($timeline_data); die;
		break;

}
