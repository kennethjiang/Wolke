<?
	require("src/prepend.inc.php");
    $display['load_extjs'] = true;

    //TODO: ONLY client
    
	$req_farmid = ($req_farmid) ? $req_farmid : $req_id;

	if ($req_saved) {
		$okmsg = _("Farm successfully saved");
		UI::Redirect("farms_builder.php?id=" . intval($req_farmid));
	}

   	$display['id'] = $req_farmid;
   	$display['role_id'] = $req_role_id ? intval($req_role_id) : 0;
   	$display['current_time_zone'] = @date_default_timezone_get();
   	$display['current_time'] = date("D h:i a");
   	$display['current_env_id'] = Scalr_Session::getInstance()->getEnvironmentId();
   	
   	$e_platforms = Scalr_Session::getInstance()->getEnvironment()->getEnabledPlatforms();
	$platforms = array();
	$l_platforms = SERVER_PLATFORMS::GetList();
	foreach ($e_platforms as $platform)
	{
		$platforms[$platform] = $l_platforms[$platform];
		$locations_list[$platform] = PlatformFactory::NewPlatform($platform)->getLocations();
	}
	
	if (empty($platforms))
	{
		$template = new Scalr_UI_Template();
		$template->messageError("Before building new farm you need to configure environment and setup cloud credentials");
		UI::Redirect("/#/environments/view");
	}
   	
   	$display['groups'] = json_encode(ROLE_GROUPS::GetName(null, true));
   	$display['platforms'] = json_encode($platforms);
   	$display['locations'] = json_encode($locations_list);

	require("src/append.inc.php");
?>