<? 
	require("src/prepend.inc.php"); 
	 	
	$display["title"] = _("Dashboard");
		
	if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
	{
		$display['clients'] = array(
			'total' 	=> (int)$db->GetOne("SELECT COUNT(*) FROM clients"),
			'active'	=> (int)$db->GetOne("SELECT COUNT(*) FROM clients WHERE isactive='1'"),
			'inactive'	=> (int)$db->GetOne("SELECT COUNT(*) FROM clients WHERE isactive='0'")
		);
	}
	else
	{
		UI::Redirect("/client_dashboard.php");
	}
	
	require("src/append.inc.php"); 
?>
