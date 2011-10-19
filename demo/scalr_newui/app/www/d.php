<?php

	@session_start();
	require_once(dirname(__FILE__) . "/../src/prepend.inc.php");

	Scalr_Session::restore();
	$session = Scalr_Session::getInstance();

	$headers = array();
	foreach (apache_request_headers() as $key => $value) {
		$headers[] = "$key: $value";
	}

	$db->Execute("INSERT INTO ui_debug_log (ipaddress, dtadded, url, request, response, env_id, client_id) VALUES(?, NOW(), ?, ?, ?, ?, ?)", array(
		$_SERVER['REMOTE_ADDR'], '/d.php',
		implode("\n", $headers) . "\n\n" . print_r($_REQUEST, true),
		'', $session->isAuthenticated() ? $session->getEnvironmentId() : 0, $session->getClientId()
	));

	echo "Success [ clientId: {$session->getClientId()}]";
