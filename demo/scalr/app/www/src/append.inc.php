<?
	if ($_SESSION["okmsg"] || $okmsg)
	{
		$display["okmsg"] = $_SESSION["okmsg"] ? $_SESSION["okmsg"] : $okmsg;
		$_SESSION["okmsg"] = null;
	}
	elseif ($_SESSION["errmsg"] || $errmsg)
	{
		$display["errmsg"] = $_SESSION["errmsg"] ? $_SESSION["errmsg"] : $errmsg;
		$_SESSION["errmsg"] = null;
	}
	elseif ($_SESSION["mess"] || $mess)
	{
	    $display["mess"] = $_SESSION["mess"] ? $_SESSION["mess"] : $mess;
	    $_SESSION["mess"] = null;
	}
	
	if ($_SESSION["warnmsg"] || $warnmsg)
	{
	    $display["warnmsg"] = $_SESSION["warnmsg"] ? $_SESSION["warnmsg"] : $warnmsg;
	    $_SESSION["warnmsg"] = null;
	}
	
	if ($_SESSION["err"])
	{
	    $err = $_SESSION["err"];
	    $_SESSION["err"] = null;
	}
	
	if (is_array($err))
	{
		$display["errmsg"] = $errmsg ? $errmsg : "The following errors occured:";
		$display["err"] = $err;
	}
		
	$display["errmsg"] = preg_replace("/[\n\r]+/", "<br />", $display["errmsg"]);
	
	foreach ($display["err"] as &$r)
		$r = preg_replace("/[\n\r]+/", "<br />",$r);
		
	$Smarty->assign($display);
	
	if (!$template_name)
	   $template_name = NOW.".tpl";
	
	$Smarty->assign_by_ref("Scalr_Session", Scalr_Session::getInstance());
	
    $Smarty->display($template_name);
?>
