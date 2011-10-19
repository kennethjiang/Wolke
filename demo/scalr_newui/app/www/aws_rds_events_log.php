<? 
	require("src/prepend.inc.php"); 
	$display['load_extjs'] = true;
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
        
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon RDS&nbsp;&raquo;&nbsp;Events log");

	$display["grid_query_string"] = "&name={$req_name}&type={$req_type}";

	require("src/append.inc.php");
?>