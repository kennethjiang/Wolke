<? 
	require("src/prepend.inc.php");
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("index.php");
	}
	
	$display["title"] = _("Service log&nbsp;&raquo;&nbsp;Log event backtrace");
	
    if (!$get_logeventid)
	   UI::Redirect("logs_view.php");
		
	$info = $db->GetRow("SELECT * FROM syslog WHERE id=?", array($get_logeventid));
	if (!$info["backtrace"])
	{
		$errmsg = _("There are no backtrace found for selected log event");
		UI::Redirect("logs_view.php");
	}

	$display["backtrace"] = $info["backtrace"];
	
	require("src/append.inc.php");
?>