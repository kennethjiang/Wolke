<? 
	require("src/prepend.inc.php"); 
	$display['load_extjs'] = true;
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
    
        
	$display["title"] = _("CloudWatch Request Builder");
	
	$AmazonCloudWatch = AmazonCloudWatch::GetInstance(
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
	); 
	$res = $AmazonCloudWatch->ListMetrics();
	
	
	$display['namespaces'] = array_keys($res);
	$display['measures'] = array();
	$display['dimensions'] = array();
	$display['dimension_values'] = array();
	
	foreach ($display['namespaces'] as $metric)
	{
		ksort($res[$metric]);
		$display['measures'][$metric] = array_keys($res[$metric]);
		foreach ($display['measures'][$metric] as $dimension)
		{
			ksort($res[$metric][$dimension]);
			$display['dimensions']["{$metric}:{$dimension}"] = array_keys($res[$metric][$dimension]);
			foreach ($display['dimensions']["{$metric}:{$dimension}"] as $dd)
			{
				sort($res[$metric][$dimension][$dd]);
				$display['dimension_values']["{$metric}:{$dimension}:{$dd}"] = array_values($res[$metric][$dimension][$dd]);
			}	
		}
	}
	
	$display['measures'] = json_encode($display['measures']);
	$display['dimensions'] = json_encode($display['dimensions']);
	$display['dimension_values'] = json_encode($display['dimension_values']);
		
	require("src/append.inc.php"); 
	
?>