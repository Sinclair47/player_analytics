<?php

include_once 'app.php';

$tab_id = 0;
if(isset($_GET['tab_id'])) {
	$tab_id = $_GET['tab_id'];
}
#pr($_GET);

$active_class = "active";
?>


<style>
	.calender-map {
	  font: 10px sans-serif;
	  shape-rendering: crispEdges;
	}
	.day {
	  stroke: #666;
	}
	.month {
	  fill: none;
	  stroke: #000;
	  stroke-width: 2px;
	}
	.RdYlGn .q0-11{fill:rgb(165,0,38)}
	.RdYlGn .q1-{fill:rgb(215,48,39)}
	.RdYlGn .q2-11{fill:rgb(244,109,67)}
	.RdYlGn .q3-11{fill:rgb(253,174,97)}
	.RdYlGn .q4-11{fill:rgb(254,224,139)}
	.RdYlGn .q5-11{fill:rgb(255,255,191)}
	.RdYlGn .q6-11{fill:rgb(217,239,139)}
	.RdYlGn .q7-11{fill:rgb(166,217,106)}
	.RdYlGn .q8-11{fill:rgb(102,189,99)}
	.RdYlGn .q9-11{fill:rgb(26,152,80)}
	.RdYlGn .q10-11{fill:rgb(0,104,55)}


	@keyframes blink {
		/**
		* At the start of the animation the dot
		* has an opacity of .2
		*/
		0% {
		opacity: .2;
		}
		/**
		* At 20% the dot is fully visible and
		* then fades out slowly
		*/
		20% {
		opacity: 1;
		}
		/**
		* Until it reaches an opacity of .2 and
		* the animation can start again
		*/
		100% {
		opacity: .2;
		}
	}

	.saving span {
		/**
		* Use the blink animation, which is defined above
		*/
		animation-name: blink;
		/**
		* The animation should take 1.4 seconds
		*/
		animation-duration: 1.0s;
		/**
		* It will repeat itself forever
		*/
		animation-iteration-count: infinite;
		/**
		* This makes sure that the starting style (opacity: .2)
		* of the animation is applied before the animation starts.
		* Otherwise we would see a short flash or would have
		* to set the default styling of the dots to the same
		* as the animation. Same applies for the ending styles.
		*/
		animation-fill-mode: both;
	}

	.saving span:nth-child(2) {
		/**
		* Starts the animation of the third dot
		* with a delay of .2s, otherwise all dots
		* would animate at the same time
		*/
		animation-delay: .2s;
	}

	.saving span:nth-child(3) {
		/**
		* Starts the animation of the third dot
		* with a delay of .4s, otherwise all dots
		* would animate at the same time
		*/
		animation-delay: .4s;
	}
</style>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header"> Laboratory</h1>
	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-bar-chart-o fa-fw"></i> Experiments
			</div><!-- /.panel-heading -->
			<div class="panel-body">
				<div style="padding:10px">
					<p>Fun with charts</p>


					<ul class="nav nav-tabs">
						<li class="<?php if($tab_id == 1) echo $active_class ?>"><a href="#/lab/1">Calender View</a></li>
						<!--<li class="<?php if($tab_id == 2) echo $active_class ?>"><a href="#/lab/2">Hlststs chart</a></li>-->
						<li class="<?php if($tab_id == 3) echo $active_class ?>"><a href="#/lab/3">Timeline</a></li>
						<!--<li class="<?php if($tab_id == 4) echo $active_class ?>"><a href="#/lab/4">Menu 3</a></li>-->
					</ul>
					<div id="chart_content" class="panel-body">
						<?php

							switch ($tab_id) {
# case 1
								case 1: ?>
									<h2>Calender heatmap</h2>
									<p class="">Number of connections per day. Yellow (0%) => Dark Green (100%)</p>
									<div class="calender-map"></div>
									<script type="text/javascript">


										//https://github.com/mohans-ca/d3js-heatmap
										var width = 900,
										height = 105,
										cellSize = 12; // cell size
										week_days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']
										month = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']

									var day = d3.time.format("%w"),
										week = d3.time.format("%U"),
										percent = d3.format(".1%"),
										format = d3.time.format("%Y%m%d");
										parseDate = d3.time.format("%Y%m%d").parse;

									var color = d3.scale.linear().range(["#ffff61", 'darkgreen'])
										.domain([0, 1])

									var svg = d3.select(".calender-map").selectAll("svg")
										.data(d3.range(<?php echo date("Y") - 2 ?>, <?php echo date("Y") + 1 ?>))
									.enter().append("svg")
										.attr("width", '100%')
										.attr("data-height", '0.5678')
										.attr("viewBox",'0 0 900 105')
										.attr("class", "RdYlGn")
									.append("g")
										.attr("transform", "translate(" + ((width - cellSize * 53) / 2) + "," + (height - cellSize * 7 - 1) + ")");

									svg.append("text")
										.attr("transform", "translate(-38," + cellSize * 3.5 + ")rotate(-90)")
										.style("text-anchor", "middle")
										.text(function(d) { return d; });

									for (var i=0; i<7; i++)
									{
									svg.append("text")
										.attr("transform", "translate(-5," + cellSize*(i+1) + ")")
										.style("text-anchor", "end")
										.attr("dy", "-.25em")
										.text(function(d) { return week_days[i]; });
									}

									var rect = svg.selectAll(".day")
										.data(function(d) { return d3.time.days(new Date(d, 0, 1), new Date(d + 1, 0, 1)); })
									.enter()
										.append("rect")
										.attr("class", "day")
										.attr("width", cellSize)
										.attr("height", cellSize)
										.attr("x", function(d) { return week(d) * cellSize; })
										.attr("y", function(d) { return day(d) * cellSize; })
										.attr("fill",'#fff')
										.datum(format);

									var legend = svg.selectAll(".legend")
										.data(month)
										.enter().append("g")
										.attr("class", "legend")
										.attr("transform", function(d, i) { return "translate(" + (((i+1) * 50)+8) + ",0)"; });

									legend.append("text")
									.attr("class", function(d,i){ return month[i] })
									.style("text-anchor", "end")
									.attr("dy", "-.25em")
									.text(function(d,i){ return month[i] });

									svg.selectAll(".month")
										.data(function(d) { return d3.time.months(new Date(d, 0, 1), new Date(d + 1, 0, 1)); })
									.enter().append("path")
										.attr("class", "month")
										.attr("id", function(d,i){ return month[i] })
										.attr("d", monthPath);

									//d3.csv("inc/data.csv", function(error, csv) {
									d3.json("inc/lab_data.php?id=1", function(error, csv) {

										csv.forEach(function(d) {
											d.Comparison_Type = parseInt(d.Comparison_Type);
										});

										var Comparison_Type_Max = d3.max(csv, function(d) { return d.Comparison_Type; });

										var data = d3.nest()
											.key(function(d) { return d.Date; })
											.rollup(function(d) { return  Math.sqrt(d[0].Comparison_Type / Comparison_Type_Max); })
											.map(csv);

										   // console.log(csv);
									   // console.log( JSON.stringify(csv));
										rect.filter(function(d, i) { return d in data; })
											.attr("fill", function(d) { return color(data[d]); })
											.attr("data-title", function(d, i) { return "date: "+ csv[i].Comparison_Type +  " " +csv[i].players+ " " +csv[i].Date+" d: "+d+ " ("+Math.round(data[d]*100)+"% of max value)"});
											$("rect").tooltip({container: 'body', html: true, placement:'top'});
									});

									function numberWithCommas(x) {
										x = x.toString();
										var pattern = /(-?\d+)(\d{3})/;
										while (pattern.test(x))
											x = x.replace(pattern, "$1,$2");
										return x;
									}

									function monthPath(t0) {
									var t1 = new Date(t0.getFullYear(), t0.getMonth() + 1, 0),
										d0 = +day(t0), w0 = +week(t0),
										d1 = +day(t1), w1 = +week(t1);
									return "M" + (w0 + 1) * cellSize + "," + d0 * cellSize
										+ "H" + w0 * cellSize + "V" + 7 * cellSize
										+ "H" + w1 * cellSize + "V" + (d1 + 1) * cellSize
										+ "H" + (w1 + 1) * cellSize + "V" + 0
										+ "H" + (w0 + 1) * cellSize + "Z";
									}
									</script>

									<?php break;
#case 2
								case 2:
									echo "hlstatsx like chart [NOT READY]";
									return;
									?>
									<div id="hlstats-chart"></div>
									<script type="text/javascript">
										var chart = c3.generate({
											bindto: '#hlstats-chart',
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
									</script>
									<?php
									break;
# case 3
									case 3:




									?>

									  <style>
										.vis-item.vis-background.negative {
										background-color: rgba(255, 0, 0, 0.2);
										}

										.vis-item .vis-item-overflow {
										overflow: visible;
									}
									</style>



									<h2>Timeline</h2>
									<p class="bg-warning" style="padding:15px">Select only ONE server from the menu. Otherwise the result will look strange/overlap</p>

									<select id="timeline-select">
										<option value="0">Only Staff</option>
										<option value="1">All Players</option>
									</select>

									<input type="button" id="fit" value="Fit all items">
									<br><br>
									<div class="timeline-menu">
										<input type="button" id="zoomIn" value="Zoom in"/>
										<input type="button" id="zoomOut" value="Zoom out"/>
										<input type="button" id="moveLeft" value="Move left"/>
										<input type="button" id="moveRight" value="Move right"/>
									</div>

									<div id="visualization"></div>
									<p id="loading" class="saving" style="font-size:15pt">Loading<span>.</span><span>.</span><span>.</span></p>

									<script type="text/javascript">
										$( document ).ready(function() {

											var timeline;

											$( "#timeline-select" ).change(function() {
												if (timeline != undefined){
													timeline.destroy();
													timeline = null;
												}
												LoadTimelineDataAsync($( "#timeline-select" ).val());
											});



											document.getElementById('fit').onclick = function() {
												timeline.fit();
											};

											/**
											* Move the timeline a given percentage to left or right
											* @param {Number} percentage   For example 0.1 (left) or -0.1 (right)
											*/
											function move (percentage) {
												var range = timeline.getWindow();
												var interval = range.end - range.start;

												timeline.setWindow({
													start: range.start.valueOf() - interval * percentage,
													end:   range.end.valueOf()   - interval * percentage
												});
											}

											/**
											* Zoom the timeline a given percentage in or out
											* @param {Number} percentage   For example 0.1 (zoom out) or -0.1 (zoom in)
											*/
											function zoom (percentage) {
												var range = timeline.getWindow();
												var interval = range.end - range.start;

												timeline.setWindow({
													start: range.start.valueOf() - interval * percentage,
													end:   range.end.valueOf()   + interval * percentage
												});
											}

											// attach events to the navigation buttons
											document.getElementById('zoomIn').onclick    = function () { zoom(-0.2); };
											document.getElementById('zoomOut').onclick   = function () { zoom( 0.2); };
											document.getElementById('moveLeft').onclick  = function () { move( 0.2); };
											document.getElementById('moveRight').onclick = function () { move(-0.2); };


											LoadTimelineDataAsync();

											function LoadTimelineDataAsync(flags) {
												document.getElementById('loading').style.display = 'block';
												// load data via an ajax request. When the data is in, load the timeline
												$.ajax({
													url: 'inc/lab_data.php?id=3&flags='+flags,
													dataType: 'json',
													success: function (data) {
														// hide the "loading..." message
														document.getElementById('loading').style.display = 'none';

														// DOM element where the Timeline will be attached
														var container = document.getElementById('visualization');

														// Create a DataSet (allows two way data-binding)
														var items = new vis.DataSet(data);

														// Configuration for the Timeline
														var options = {
															showCurrentTime: true,
															minHeight: '400px',
														};

														// Create a Timeline
														timeline = new vis.Timeline(container, items, options);
														timeline.setWindow({
															start: timeline.getWindow().start.valueOf(),
															end: timeline.getWindow().start.valueOf() + (60*60*12*1000)
														});
													},
													error: function (err) {
														document.getElementById('loading').style.display = 'none';
														console.log('Error', err);
														if (err.status === 0) {
															alert('Failed to load data.\nPlease run this example on a server.');
														}
														else {
															alert('Failed to load data.');
														}
													}
												});
											}

										});
									</script>

									<?php
									break;
							}
						?>
					</div>
				</div>
			</div><!-- /.panel-body -->
		</div><!-- /.panel -->
	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->
