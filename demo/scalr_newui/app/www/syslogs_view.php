<? 
	require("src/prepend.inc.php");
	$display['load_extjs'] = true;	
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("index.php");
	}
			
	$display["title"] = _("Logs");
	$display["help"] = _("Almost all Scalr activity being logged. You should check logs in case of any issues.");
	$display["table_title_text"] = sprintf(_("Current time: %s"), date("M j, Y H:i:s"));
		
	$farms = $db->GetAll("SELECT id, name FROM farms");
	$disp_farms = array(array('',''));
	foreach ($farms as $farm)
		$disp_farms[] = array($farm['id'], $farm['name']);
		
	$display['farms'] = json_encode($disp_farms);
	
	$display["severities"] = array (
               'FATAL'          => 'Fatal error',
               'ERROR'     		=> 'Error',
               'WARN'     		=> 'Warning',
               'INFO'        	=> 'Information',                              
               'DEBUG'   		=> 'Debug'
               );
               
    $severities = array(
		array('hideLabel' => true, 'boxLabel'=> 'Fatal error', 'name' => 'severity[]', 'inputValue' => 'FATAL', 'checked'=> true),
		array('hideLabel' => true, 'boxLabel'=> 'Error', 'name' => 'severity[]','inputValue'=> 'ERROR', 'checked'=> true),
		array('hideLabel' => true, 'boxLabel'=> 'Warning', 'name' => 'severity[]', 'inputValue'=> 'WARN', 'checked'=> true),
		array('hideLabel' => true, 'boxLabel'=> 'Information','name' => 'severity[]', 'inputValue'=> 'INFO', 'checked'=> true),
		array('hideLabel' => true, 'boxLabel'=> 'Debug', 'name' => 'severity[]', 'inputValue'=> 'DEBUG', 'checked'=> false)
	);
	$display["severities"] = json_encode($severities);
	
	require("src/append.inc.php");
?>