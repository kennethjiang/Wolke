<?php

	//@TODO: optimize
	$path = trim(str_replace("?{$_SERVER['QUERY_STRING']}", "", $_SERVER['REQUEST_URI']), '/');

	if (empty($path))
	{
		session_start();

		if (!isset($_SESSION['Scalr_Session']['clientId']) && !isset($_COOKIE['scalr_signature'])) {
			require("login.php");
			exit();
		}
	}

	require("src/prepend.inc.php");

	Scalr_UI_Response::getInstance()->template->assignParam('menuitems', $display['menuitems']); // hack @TODO: move to Core/Default
	Scalr_UI_Controller::handleRequest(explode('/', $path), $_REQUEST);
