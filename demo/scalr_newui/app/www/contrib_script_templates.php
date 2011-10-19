<? 
	require("src/prepend.inc.php"); 
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	$display["title"] = _("Contrinuted script templates");
	if (isset($post_cancel))
		UI::Redirect("script_templates.php");
	
	$display['grid_query_string'] = "&approval_state={$req_approval_state}";
		
	require("src/append.inc.php");
?>