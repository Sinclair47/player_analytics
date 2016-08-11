<?php

/*@license MIT - http://datatables.net/license_mit/
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

require_once 'config.php';

$db_conn = array(
    'user' => DB_USER,
    'pass' => DB_PASS,
    'db'   => DB_NAME,
    'host' => DB_HOST,
    'port' => DB_PORT
);

$table = DB_TABLE_PA;

if (isset($_GET['type']) && $_GET['type'] == 'getconnections') {

    $primaryKey = 'id';
     
    $columns = array(
        array(
            'db'        => 'id',
            'dt'        => 'id'
        ),
        array(
            'db'        => 'name',
            'dt'        => 'name',
            'formatter' => function( $d, $row ) {
                return htmlentities($d);
            }
        ),
        array(
            'db'        => 'auth',
            'dt'        => 'auth'
        ),
        array(
            'db'        => 'connect_time',
            'dt'        => 'connect_time',
            'formatter' => function( $d, $row ) {
                return date('h:i:s a m/d/Y',$d);
            }
        ),
        array(
            'db'        => 'connect_method',
            'dt'        => 'connect_method',
            'formatter' => function( $d, $row ) {
                return ConnMethod($d);
            }
        ),
        array(
            'db'        => 'duration',
            'dt'        => 'duration',
            'formatter' => function( $d, $row ) {
                return PlaytimeCon($d);
            }
        ),
        array(
            'db'        => 'country',
            'dt'        => 'country'
        ),
        array(
            'db'        => 'premium',
            'dt'        => 'premium'
        ),
        array(
            'db'        => 'html_motd_disabled',
            'dt'        => 'html_motd_disabled'
        ),
        array(
            'db'        => 'os',
            'dt'        => 'os'
        ),
        array(
            'db'        => 'server_ip',
            'dt'        => 'server_ip'
        )
    );

    require('ssp.class.php');

    echo json_encode(
        SSP::simple( $_GET, $db_conn, $table, $primaryKey, $columns, $joinQuery = '')
    );
}

if (isset($_GET['type']) && ($_GET['type'] == 'getplayers' || $_GET['type'] == 'getstaff')) {

    $primaryKey = 'id';
    #print_r($_GET); die;
    $columns = array(
        array(
            'db'        => 'id',
            'dt'        => 'id'
        ),
        array(
            'db'        => 'name',
            'dt'        => 'name',
            'formatter' => function( $d, $row ) {
                return htmlentities($d);
            }
        ),
        array(
            'db'        => 'auth',
            'dt'        => 'auth'
        ),
        array(
            'db'        => 'SUM(duration)',
            'dt'        => 'duration',
            'formatter' => function( $d, $row ) {
                return PlaytimeCon($d);
            }
        ),
        array(
            'db'        => 'COUNT(auth)',
            'dt'        => 'total',
            'as'        => 'total',
            'formatter' => function( $d, $row ) {
                return number_format($d);
            }
        ),
        array(
            'db'        => 'MAX(connect_time)',
            'dt'        => 'connect_time',
            'as'        => 'connect_time',
            'formatter' => function( $d, $row ) {
                return date('h:i:s a m/d/Y',$d);
            }
        ),
        array(
            'db'        => 'country',
            'dt'        => 'country'
        ),
        array(
            'db'        => 'premium',
            'dt'        => 'premium'
        ),
        array(
            'db'        => 'html_motd_disabled',
            'dt'        => 'html_motd_disabled'
        ),
        array(
            'db'        => 'os',
            'dt'        => 'os'
        ),
        array(
            'db'        => 'flags',
            'dt'        => 'flags',
            'formatter' => function( $d, $row ) use (&$staff_group_names) {
                return FlagToName($d, $staff_group_names);
            }
        ),
    );

    $where = '';
    $groupBy = "GROUP BY auth";

    if($_GET['type'] == 'getstaff') {
        $where = 'flags != "" ';
        if(!empty($staff_whitelist)) {
            $where .= " AND (";
            foreach($staff_whitelist as $flag) {
                $where .= "flags = '".$flag."' OR ";
            }
            $where = rtrim(rtrim($where, " "), "OR");
            $where .= ")";
        }
        $groupBy = "GROUP BY auth, flags";
    }   

    require('ssp.class.php');

    echo json_encode(
        SSP::simple( $_GET, $db_conn, $table, $primaryKey, $columns, $joinQuery = '', $extraCondition = '', $groupBy, $where)
    );
}

if (isset($_GET['type']) && $_GET['type'] == 'getcountryinfo') {

    $primaryKey = 'id';
     
    $columns = array(
        array(
            'db'        => 'id',
            'dt'        => 'id'
        ),
        array(
            'db'        => 'name',
            'dt'        => 'name',
            'formatter' => function( $d, $row ) {
                return htmlentities($d);
            }
        ),
        array(
            'db'        => 'auth',
            'dt'        => 'auth'
        ),
        array(
            'db'        => 'duration',
            'dt'        => 'duration',
            'formatter' => function( $d, $row ) {
                return PlaytimeCon($d);
            }
        ),
        array(
            'db'        => 'premium',
            'dt'        => 'premium'
        ),
        array(
            'db'        => 'html_motd_disabled',
            'dt'        => 'html_motd_disabled'
        ),
        array(
            'db'        => 'os',
            'dt'        => 'os'
        ),
        array(
            'db'        => 'server_ip',
            'dt'        => 'server_ip'
        )
    );

    $extraCondition = "`country_code` = '".$_GET['id']."'";

    require('ssp.class.php');

    echo json_encode(
        SSP::simple( $_GET, $db_conn, $table, $primaryKey, $columns, $joinQuery = '', $extraCondition)
    );
}

if (isset($_GET['type']) && $_GET['type'] == 'c') { // connections for single server View

    $primaryKey = 'id';
     
    $columns = array(
        array(
            'db'        => 'id',
            'dt'        => 'id'
        ),
        array(
            'db'        => 'name',
            'dt'        => 'name',
            'formatter' => function( $d, $row ) {
                return htmlentities($d);
            }
        ),
        array(
            'db'        => 'auth',
            'dt'        => 'auth'
        ),
        array(
            'db'        => 'connect_time',
            'dt'        => 'connect_time',
            'formatter' => function( $d, $row ) {
                return date('h:i:s a m/d/Y',$d);
            }
        ),
        array(
            'db'        => 'connect_method',
            'dt'        => 'connect_method',
            'formatter' => function( $d, $row ) {
                return ConnMethod($d);
            }
        ),
        array(
            'db'        => 'duration',
            'dt'        => 'duration',
            'formatter' => function( $d, $row ) {
                return PlaytimeCon($d);
            }
        ),
        array(
            'db'        => 'country',
            'dt'        => 'country'
        ),
        array(
            'db'        => 'premium',
            'dt'        => 'premium'
        ),
        array(
            'db'        => 'html_motd_disabled',
            'dt'        => 'html_motd_disabled'
        ),
        array(
            'db'        => 'os',
            'dt'        => 'os'
        ),
        array(
            'db'        => 'server_ip',
            'dt'        => 'server_ip'
        )
    );

    $extraCondition = "`server_ip` = '".$_GET['server']."'";

    require('ssp.class.php');

    echo json_encode(
        SSP::simple( $_GET, $db_conn, $table, $primaryKey, $columns, $joinQuery = '', $extraCondition, $groupBy = '', $where = '')
    );
}

if (isset($_GET['type']) && $_GET['type'] == 'u') { // Unique Players for single server View

    $primaryKey = 'id';
    $columns = array(
        array(
            'db'        => 'id',
            'dt'        => 'id'
        ),
        array(
            'db'        => 'name',
            'dt'        => 'name',
            'formatter' => function( $d, $row ) {
                return htmlentities($d);
            }
        ),
        array(
            'db'        => 'auth',
            'dt'        => 'auth'
        ),
        array(
            'db'        => 'SUM(duration)',
            'dt'        => 'duration',
            'formatter' => function( $d, $row ) {
                return PlaytimeCon($d);
            }
        ),
        array(
            'db'        => 'COUNT(auth)',
            'dt'        => 'total',
            'as'        => 'total',
            'formatter' => function( $d, $row ) {
                return number_format($d);
            }
        ),
        array(
            'db'        => 'MAX(connect_time)',
            'dt'        => 'connect_time',
            'as'        => 'connect_time',
            'formatter' => function( $d, $row ) {
                return date('h:i:s a m/d/Y',$d);
            }
        ),
        array(
            'db'        => 'country',
            'dt'        => 'country'
        ),
        array(
            'db'        => 'premium',
            'dt'        => 'premium'
        ),
        array(
            'db'        => 'html_motd_disabled',
            'dt'        => 'html_motd_disabled'
        ),
        array(
            'db'        => 'os',
            'dt'        => 'os'
        ),
        array(
            'db'        => 'server_ip',
            'dt'        => 'server_ip'
        )
    );

    $where = "`server_ip` = '".$_GET['server']."'";
    $groupBy = "GROUP BY auth";

    require('ssp.class.php');

    echo json_encode(
        SSP::simple( $_GET, $db_conn, $table, $primaryKey, $columns, $joinQuery = '', $extraCondition = '', $groupBy, $where)
    );
}
