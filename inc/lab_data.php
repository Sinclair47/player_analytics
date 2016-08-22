<?php
require_once 'app.php';



switch($_GET['id']) {
     case 1:
        #, COUNT(DISTINCT(`auth`)) AS players
        $database->query('SELECT COUNT(*) AS Comparison_Type, DATE_FORMAT(connect_date, "%Y%m%d") AS Date FROM `'.DB_TABLE_PA.'` WHERE '.getServerIpsSql(false, '', 'AND').' (year(connect_date) = "2016" OR year(connect_date) = "2015" OR year(connect_date) = "2014") GROUP BY YEAR(connect_date), month(connect_date), day(connect_date) ORDER BY `Date` ASC');
        $data = $database->resultset();

        echo json_encode($data);
        break;

}