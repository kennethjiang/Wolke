<?php
	require("src/prepend.inc.php"); 
	$display['load_extjs'] = true;	
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	$display["title"] = _("Tools&nbsp;&raquo;&nbsp;Amazon Web Services&nbsp;&raquo;&nbsp;Amazon VPC&nbsp;&raquo;&nbsp;Amazon VPC gateways list");

	$AmazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
		$_SESSION['aws_region'], 
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
	);
	
	if ($_POST)
	{	
		$i = 0;
		if($post_action_customer == 'delete' )
		{
			foreach ($post_customer_id as $id)
			{
				try
				{				
					$AmazonVPCClient->DeleteCustomerGateway($id);
					$i++;				
				}
				catch(Exception $e)
				{
					$err[] = $e->getMessage(); //Can't delete Gateways %s: %s
					UI::Redirect("aws_vpc_gateways_view.php");
				}
			}	
			
			if ($i > 0)
				$okmsg = sprintf(_("%s Customer gateways(s) successfully removed"), $i);
			
			UI::Redirect("aws_vpc_gateways_view.php");
		}
		
		if($post_action_vpn == 'delete')
		{
			foreach ($post_vpn_id as $id)
			{
				try
				{				
					$AmazonVPCClient->DeleteVpnGateway($id);
					$i++;				
				}
				catch(Exception $e)
				{
					$err[] = $e->getMessage(); //Can't delete Gateways %s: %s
					UI::Redirect("aws_vpc_gateways_view.php");
				}
			}	
			if ($i > 0)
				$okmsg = sprintf(_("%s VPN gateways(s) successfully removed"), $i);
			
			UI::Redirect("aws_vpc_gateways_view.php");
		}
		
		if($post_action_conn == 'delete')
		{
			foreach ($post_conn_id as $id)
			{
				try
				{				
					$AmazonVPCClient->DeleteVpnConnection($id);
					$i++;				
				}
				catch(Exception $e)
				{
					$err[] = $e->getMessage(); //Can't delete connection(s) %s: %s
					UI::Redirect("aws_vpc_gateways_view.php");
				}
			}
			if ($i > 0)
				$okmsg = sprintf(_("%s VPN connection(s) successfully removed"), $i);
			
			UI::Redirect("aws_vpc_gateways_view.php");			
		}	

	}
	
	if($_GET)
	{	
		// detaching selected item			
		if($req_action === 'detach')	
		{					
			try
			{		
				if (!$req_vpcId)
				{
					$errmsg = "VPC ID not found. Please select atteched VPN";
					UI::Redirect("/aws_vpc_gateways_view.php");
				}
				if (!$req_vpnId)
				{
					$errmsg = "VPN ID not found. Please select atteched VPN";
					UI::Redirect("/aws_vpc_gateways_view.php");
				}	
									
				$AmazonVPCClient->DetachVpnGateway(new DetachVpnGateway($req_vpcId,$req_vpnId));
				$okmsg = "Vpn gateway deteched successfully";	
				UI::Redirect("/aws_vpc_gateways_view.php");					
			}
			catch(Exception $e)
			{					
				$err[] = $e->getMessage(); //"Can't detach VPN gateway %s from VPC %s : %s
				UI::Redirect("/aws_vpc_gateways_view.php");
			}
		}
		else
		{
			$err[] = sprintf(_("The incorrect action with VPN %s"),$req_vpnId);
			UI::Redirect("/aws_vpc_gateways_view.php");	
		}
		
	}

	require("src/append.inc.php"); 	

?>
