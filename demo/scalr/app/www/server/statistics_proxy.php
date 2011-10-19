<?
	$_REQUEST['role_name'] = $_REQUEST['role'];

	if ($_REQUEST['version'] != 2)
		$STATS_URL = 'http://stats.scalr.net';    
	else
		$STATS_URL = 'http://monitoring.scalr.net';

	print @file_get_contents("{$STATS_URL}/server/statistics.php?".http_build_query($_REQUEST));
?>