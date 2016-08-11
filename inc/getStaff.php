<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    #header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    #die();
}

 ?>
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">Staff</h1>
					</div><!-- /.col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bar-chart-o fa-fw"></i> Staff

                                <div id="staff-filter" class="pull-right hide">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            Staff filter (not working yet)
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <li><a href="#">Action</a>
                                            </li>
                                            <li><a href="#">Another action</a>
                                            </li>
                                            <li><a href="#">Something else here</a>
                                            </li>
                                            <li class="divider"></li>
                                            <li><a href="#">Separated link</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>


							</div><!-- /.panel-heading -->
							<div class="panel-body">
								<div style="padding:10px">
									<div class="panel-body">
										<p>Here you can see your flagged Users. The Users are grouped by steam id and flags. Click on a row to see all records for a specific User.</p>
									</div>

									<table id="staff" class="table table-bordered table-striped table-condensed display" style="cursor:pointer">
										<thead>
											<tr>
												<th>ID</th>
												<th style="width:20%">Name</th>
												<th>Auth</th>
												<th>Total</th>
												<th>Duration</th>
												<th>Last On</th>
												<th>Country</th>
												<th><i class="fa fa-usd"></i></th>
												<th><i class="fa fa-html5"></i></th>
                                                <th>Flags</th>
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
		var staff = $('#staff').DataTable( {
			"processing": true,
			"serverSide": true,
			"ajax": "inc/server_processing.php?type=getstaff",
			"columns": [
				{ "data": "id", "visible" : false },
				{ "data": "name" },
				{ "data": "auth", "visible" : true, "searchable" : true },
				{ "data": "total", "searchable" : false },
				{ "data": "duration", "searchable" : false },
				{ "data": "connect_time", "searchable" : false },
				{ "data": "country", "searchable" : false },
				{ "data": "premium", "searchable" : false },
				{ "data": "html_motd_disabled", "searchable" : false },
                { "data": "flags" },
				{ "data": "os", "searchable" : false },
			],
			"order": [[9, 'desc']]
		});
		$('#staff tbody').on('click', 'tr', function () {
			$.ajax({
				type: "GET",
				url: "inc/getplayerinfo.php",
				data: 'id='+staff.cell(this, 2).data(),
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
