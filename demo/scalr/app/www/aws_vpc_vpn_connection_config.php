<?php 
	require("src/prepend.inc.php"); 
		
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon VPC&nbsp;&raquo;&nbsp;Create VPN connection");	
		
	$AmazonVPCClient = AmazonVPC::GetInstance(AWSRegions::GetAPIURL($_SESSION['aws_region'])); 
	$AmazonVPCClient->SetAuthKeys(
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
	);

	$display["vpnConnection"] = array();
		
	try
	{
		// Get  $req_id VPN gateway	
		$vpnConnection = new DescribeVpnConnections(null,array($req_id)); // filter
		$aws_response = $AmazonVPCClient->DescribeVpnConnections($vpnConnection);
		
		$rows = (array)$aws_response->vpnConnectionSet;				
				
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); // convert along  record to array
		
		foreach ($rows['item'] as $row)	
		{		
			$display["vpnConnection"]=(array)$row;
			$display["customerGatewayConfiguration"] = htmlspecialchars($row->customerGatewayConfiguration);			
		}		
	}
	catch(Exception $e)
	{					
		$err[] = $e->getMessage();//Incorrect VPN gateway ID %s: 
		UI::Redirect("/aws_vpc_gateways_view.php");
	}
			
		
	require("src/append.inc.php"); 

?>