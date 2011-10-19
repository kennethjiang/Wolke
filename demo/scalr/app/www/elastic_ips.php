<?
	require_once('src/prepend.inc.php');
    $display['load_extjs'] = true;	    
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	if ($req_farmid)
	{
		$display['grid_query_string'] .= "&farmid={$req_farmid}";
	}
	
	$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
		$_SESSION['aws_region'],
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
	);

	if ($req_task == 'associate')
	{
		if (!$req_server_id)
		{
			$servers = $db->GetAll("SELECT * FROM servers WHERE env_id=? AND status IN(?,?) AND platform=?", 
				array(Scalr_Session::getInstance()->getEnvironmentId(), SERVER_STATUS::RUNNING, SERVER_STATUS::INIT, SERVER_PLATFORMS::EC2)
			);
			$display['servers'] = array();
			foreach ($servers as $server)
			{
				if ($db->GetOne("SELECT value FROM server_properties WHERE server_id=? AND name=?", array($server['server_id'], EC2_SERVER_PROPERTIES::REGION)) == $_SESSION['aws_region'])
				{
					$server['instance_id'] = $db->GetOne("SELECT value FROM server_properties WHERE server_id=? AND name=?", array($server['server_id'], EC2_SERVER_PROPERTIES::INSTANCE_ID));
					$server['farm_name'] = $db->GetOne("SELECT name FROM farms WHERE id=?", array($server['farm_id']));
					$server['role_name'] = $db->GetOne("SELECT name FROM roles WHERE id=?", array($server['role_id']));
					$display['servers'][] = $server;
				}
			}
			
			$display['ip'] = $req_ip;
			$template_name = 'aws_ec2_eip_associate.tpl';
			require_once ("src/append.inc.php");
			exit();
		}
		else
		{
			try
			{
				$DBServer = DBServer::LoadByID($req_server_id);
				if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($DBServer->envId))
					throw new Exception("Server not found");
			}
			catch(Exception $e)
			{
				UI::Redirect("/elastic_ips.php");
			}
			
			try
			{
				$AmazonEC2Client->AssociateAddress(
					$DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID),
					$req_ip
				);
				
				$okmsg = _("Elastic IP address successfullly associated with instance");
			}
			catch(Exception $e)
			{
				$errmsg = sprintf(_("Cannot associate Elastic IP with instance: %s"), $e->getMessage());
			}
			
			UI::Redirect("/elastic_ips.php");
		}
	}
	elseif ($req_task == 'release')
	{
		try
		{
			$AmazonEC2Client->ReleaseAddress($req_ip);
			$db->Execute("DELETE FROM elastic_ips WHERE ipaddress=?", array($req_ip));
		}
		catch(Exception $e)
		{
			$errmsg = sprintf(_("Cannot release elastic IP: %s"), $e->getMessage());
		}
		
		if (!$errmsg)
		{
			$okmsg = _("Elastic IP successfully released");
			UI::Redirect("elastic_ips.php");
		}
	}
	
	$display['title'] = 'Elastic IPs';
		
	require_once ("src/append.inc.php");
?>