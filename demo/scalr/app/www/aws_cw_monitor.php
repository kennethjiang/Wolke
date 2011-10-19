<? 
	require("src/prepend.inc.php"); 
	$display['load_extjs'] = true;
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
    
    if ($post_cancel)
		UI::Redirect("#/servers/view?farmId={$farminfo['id']}");
    
	$AmazonCloudWatch = AmazonCloudWatch::GetInstance(
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
	);
	 
	$res = $AmazonCloudWatch->ListMetrics();
	
	$display['NameSpace'] = ($req_NameSpace && in_array($req_NameSpace, array_keys($res))) ? $req_NameSpace : "AWS/EC2";
	$display['ObjectId'] = $req_ObjectId;	
	$display['Object'] = $req_Object;
	$display['metrics'] = array_keys($res[$display['NameSpace']]); 
	
	$display["title"] = _("CloudWatch monitoring for {$display['NameSpace']}/{$display['Object']}: {$display['ObjectId']}");
	
	$display['units'] = array(
		'CPUUtilization' => '%',
		'NetworkIn' => 'KB',
		'NetworkOut' => 'KB',
		'DiskWriteBytes' => 'KB',
		'DiskReadBytes' => 'KB',
		'DiskWriteOps' => '%',
		'DiskReadOps' => '%',
	
		'WriteIOPS' => 'IOPS/s',
		'ReadThroughput' => 'KB/s',
		'WriteLatency' => 's',
		'FreeStorageSpace' => 'KB',
		'ReadIOPS' => 'IOPS/s',
		'DatabaseConnections' => '',
		'WriteThroughput' => 'KB/s',
		'ReadLatency' => 's'
	);
	
	require("src/append.inc.php");
	
?>